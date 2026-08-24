<?php

namespace App\Models;

use CodeIgniter\Model;

class AttendanceModel extends Model
{
    protected $table            = 'attendance';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'employee_id', 'attendance_date', 'check_in', 'check_out',
        'status', 'source', 'notes',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Cutoff time after which a check-in is marked "late".
    protected string $lateCutoff = '10:15:00';

    // Minimum hours worked to count as a full "present" day; below this is "half_day".
    protected float $halfDayThresholdHours = 4.0;

    /**
     * Get today's attendance row for an employee, if it exists.
     */
    public function findToday(int $employeeId): ?array
    {
        return $this->where('employee_id', $employeeId)
            ->where('attendance_date', date('Y-m-d'))
            ->first();
    }

    /**
     * Record a check-in. Creates today's row if it doesn't exist,
     * or updates check_in if the employee somehow re-checks-in.
     */
    public function checkIn(int $employeeId, string $source = 'manual'): array
    {
        $existing = $this->findToday($employeeId);
        $now      = date('Y-m-d H:i:s');
        $status   = (date('H:i:s') > $this->lateCutoff) ? 'late' : 'present';

        if ($existing) {
            $this->update($existing['id'], [
                'check_in' => $existing['check_in'] ?? $now,
                'status'   => $existing['check_in'] ? $existing['status'] : $status,
            ]);

            return $this->find($existing['id']);
        }

        $id = $this->insert([
            'employee_id'     => $employeeId,
            'attendance_date' => date('Y-m-d'),
            'check_in'        => $now,
            'status'          => $status,
            'source'          => $source,
        ], true);

        return $this->find($id);
    }

    /**
     * Record a check-out for today. Recalculates status to half_day
     * if total hours worked fall below the threshold.
     */
    public function checkOut(int $employeeId): ?array
    {
        $existing = $this->findToday($employeeId);

        if (! $existing || ! $existing['check_in']) {
            return null;
        }

        $now         = date('Y-m-d H:i:s');
        $hoursWorked = (strtotime($now) - strtotime($existing['check_in'])) / 3600;

        $status = $existing['status'];
        if ($hoursWorked < $this->halfDayThresholdHours) {
            $status = 'half_day';
        }

        $this->update($existing['id'], [
            'check_out' => $now,
            'status'    => $status,
        ]);

        return $this->find($existing['id']);
    }

    /**
     * Admin/HR: manually set or correct an attendance record for any date.
     */
    public function setManual(int $employeeId, string $date, array $data): bool
    {
        $existing = $this->where('employee_id', $employeeId)
            ->where('attendance_date', $date)
            ->first();

        $data['source'] = 'admin';

        if ($existing) {
            return (bool) $this->update($existing['id'], $data);
        }

        $data['employee_id']     = $employeeId;
        $data['attendance_date'] = $date;

        return (bool) $this->insert($data);
    }

    /**
     * Daily grid: all employees with their attendance status for a given date,
     * optionally filtered by department. Employees with no record show as
     * "absent" (not yet marked) via LEFT JOIN.
     */
    public function dailyGrid(string $date, ?int $departmentId = null): array
    {
        $builder = $this->db->table('employees')
            ->select('employees.id as employee_id, employees.employee_code, employees.first_name, employees.last_name,
                      departments.name as department_name,
                      attendance.id as attendance_id,
                      attendance.check_in, attendance.check_out, attendance.status, attendance.source')
            ->join('departments', 'departments.id = employees.department_id', 'left')
            ->join('attendance', "attendance.employee_id = employees.id AND attendance.attendance_date = " . $this->db->escape($date), 'left')
            ->where('employees.status', 'active');

        if ($departmentId) {
            $builder->where('employees.department_id', $departmentId);
        }

        return $builder->orderBy('employees.first_name', 'ASC')->get()->getResultArray();
    }

    /**
     * All attendance rows for one employee in a given month, keyed by day number.
     */
    public function monthRows(int $employeeId, int $year, int $month): array
    {
        $rows = $this->where('employee_id', $employeeId)
            ->where('YEAR(attendance_date)', $year)
            ->where('MONTH(attendance_date)', $month)
            ->orderBy('attendance_date', 'ASC')
            ->findAll();

        $byDay = [];
        foreach ($rows as $row) {
            $day         = (int) date('j', strtotime($row['attendance_date']));
            $byDay[$day] = $row;
        }

        return $byDay;
    }

    /**
     * The single source of truth for "what happened on each day of this
     * month" -- merges actual attendance rows with holidays, weekends, and
     * (critically) days that have already passed with NO record at all,
     * which count as an implicit absence. Without this, an employee who
     * simply never checks in on a working day was invisible to both the
     * calendar and payroll's LOP calculation -- this closes that gap.
     *
     * Returns, keyed by day number:
     *   ['status' => 'present'|'late'|'half_day'|'absent'|'on_leave'
     *               |'holiday'|'weekend'|'future',
     *    'row'    => the attendance row array, or null,
     *    'holiday_name' => string|null]
     */
    public function dayStatusMap(int $employeeId, int $year, int $month): array
    {
        $rows     = $this->monthRows($employeeId, $year, $month);
        $holidays = (new HolidayModel())->forMonth($year, $month);

        $today       = date('Y-m-d');
        $daysInMonth = (int) date('t', mktime(0, 0, 0, $month, 1, $year));

        $map = [];

        for ($d = 1; $d <= $daysInMonth; $d++) {
            $date      = sprintf('%04d-%02d-%02d', $year, $month, $d);
            $dayOfWeek = (int) date('N', mktime(0, 0, 0, $month, $d, $year));

            if ($date > $today) {
                $map[$d] = ['status' => 'future', 'row' => null, 'holiday_name' => null];
                continue;
            }

            if (isset($holidays[$d])) {
                $map[$d] = ['status' => 'holiday', 'row' => $rows[$d] ?? null, 'holiday_name' => $holidays[$d]];
                continue;
            }

            if ($dayOfWeek >= 6) { // Sat=6, Sun=7
                $map[$d] = ['status' => 'weekend', 'row' => $rows[$d] ?? null, 'holiday_name' => null];
                continue;
            }

            if (isset($rows[$d])) {
                $map[$d] = ['status' => $rows[$d]['status'], 'row' => $rows[$d], 'holiday_name' => null];
                continue;
            }

            // Working day, already passed, no record at all -- implicit absence.
            $map[$d] = ['status' => 'absent', 'row' => null, 'holiday_name' => null];
        }

        return $map;
    }

    /**
     * Monthly summary counts, now derived from dayStatusMap() so implicit
     * absences (no check-in, no leave, not a holiday/weekend) are correctly
     * counted -- this is what payroll's LOP calculation reads from.
     */
    public function monthlySummary(int $employeeId, int $year, int $month): array
    {
        $map = $this->dayStatusMap($employeeId, $year, $month);

        $summary = [
            'present'  => 0,
            'late'     => 0,
            'half_day' => 0,
            'absent'   => 0,
            'on_leave' => 0,
        ];

        foreach ($map as $day) {
            if (isset($summary[$day['status']])) {
                $summary[$day['status']]++;
            }
        }

        return $summary;
    }

    /**
     * Compute login / break / productive hours for one attendance record.
     * Works for both a finished day (check_out set) and a live in-progress
     * day (check_out null -> counts up to "now"), same for an open break.
     */
    public function computeHours(array $attendance, int $breakSeconds): array
    {
        if (! $attendance['check_in']) {
            return ['login_hours' => 0.0, 'break_hours' => 0.0, 'productive_hours' => 0.0];
        }

        $start = strtotime($attendance['check_in']);
        $end   = $attendance['check_out'] ? strtotime($attendance['check_out']) : time();

        $loginSeconds      = max(0, $end - $start);
        $productiveSeconds = max(0, $loginSeconds - $breakSeconds);

        return [
            'login_hours'      => round($loginSeconds / 3600, 2),
            'break_hours'      => round($breakSeconds / 3600, 2),
            'productive_hours' => round($productiveSeconds / 3600, 2),
        ];
    }
}

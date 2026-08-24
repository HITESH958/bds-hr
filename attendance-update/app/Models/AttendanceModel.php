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
        'status', 'worked_minutes', 'notes',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Work-day rules — adjust to match actual office policy.
    public const WORK_START    = '09:30:00'; // check-in after this = "late"
    public const GRACE_MINUTES = 0;          // minutes of grace before marking late
    public const HALF_DAY_MINUTES = 240;     // worked less than this (4 hrs) = "half_day"

    public function todayRecord(int $employeeId): ?array
    {
        return $this->where('employee_id', $employeeId)
            ->where('attendance_date', date('Y-m-d'))
            ->first();
    }

    /**
     * Create today's check-in record. Fails silently (returns false) if
     * already checked in today — caller should check todayRecord() first.
     */
    public function checkIn(int $employeeId): bool
    {
        if ($this->todayRecord($employeeId)) {
            return false;
        }

        $now    = date('Y-m-d H:i:s');
        $status = (date('H:i:s') > self::WORK_START) ? 'late' : 'present';

        return (bool) $this->insert([
            'employee_id'     => $employeeId,
            'attendance_date' => date('Y-m-d'),
            'check_in'        => $now,
            'status'          => $status,
        ]);
    }

    /**
     * Close out today's check-out, compute worked minutes, and finalize status.
     */
    public function checkOut(int $employeeId): bool
    {
        $record = $this->todayRecord($employeeId);

        if (! $record || $record['check_out']) {
            return false; // no check-in yet, or already checked out
        }

        $checkInTime  = new \DateTime($record['check_in']);
        $checkOutTime = new \DateTime();
        $minutes      = (int) (($checkOutTime->getTimestamp() - $checkInTime->getTimestamp()) / 60);

        $status = $record['status'];
        if ($minutes < self::HALF_DAY_MINUTES) {
            $status = 'half_day';
        }

        return $this->update($record['id'], [
            'check_out'      => $checkOutTime->format('Y-m-d H:i:s'),
            'worked_minutes' => $minutes,
            'status'         => $status,
        ]);
    }

    /**
     * Attendance list joined with employee name/code, with optional filters.
     */
    public function listWithEmployee(?string $dateFrom = null, ?string $dateTo = null, ?int $employeeId = null, int $perPage = 20)
    {
        $builder = $this->select('attendance.*, employees.first_name, employees.last_name, employees.employee_code')
            ->join('employees', 'employees.id = attendance.employee_id', 'left');

        if ($dateFrom) {
            $builder->where('attendance.attendance_date >=', $dateFrom);
        }
        if ($dateTo) {
            $builder->where('attendance.attendance_date <=', $dateTo);
        }
        if ($employeeId) {
            $builder->where('attendance.employee_id', $employeeId);
        }

        return $builder->orderBy('attendance.attendance_date', 'DESC')->paginate($perPage);
    }

    public function monthlySummary(int $employeeId, string $yearMonth): array
    {
        return $this->where('employee_id', $employeeId)
            ->where("DATE_FORMAT(attendance_date, '%Y-%m') =", $yearMonth)
            ->orderBy('attendance_date', 'ASC')
            ->findAll();
    }
}

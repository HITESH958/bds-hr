<?php

namespace App\Controllers;

use App\Models\AttendanceBreakModel;
use App\Models\AttendanceModel;
use App\Models\DepartmentModel;
use App\Models\EmployeeModel;
use App\Models\HolidayModel;
use CodeIgniter\Controller;

class Attendance extends Controller
{
    protected AttendanceModel      $attendanceModel;
    protected AttendanceBreakModel $breakModel;

    public function __construct()
    {
        $this->attendanceModel = new AttendanceModel();
        $this->breakModel      = new AttendanceBreakModel();
    }

    public function myAttendance()
    {
        $employeeId = session()->get('employee_id');

        if (! $employeeId) {
            return redirect()->to('/dashboard')->with('error', 'Your account is not linked to an employee record.');
        }

        $employeeId = (int) $employeeId;
        $today      = $this->attendanceModel->findToday($employeeId);

        $todayBreaks = [];
        $openBreak   = null;
        $todayHours  = ['login_hours' => 0.0, 'break_hours' => 0.0, 'productive_hours' => 0.0];

        if ($today) {
            $todayBreaks = $this->breakModel->forAttendance((int) $today['id']);
            $openBreak   = $this->breakModel->openBreak((int) $today['id']);
            $breakSecs   = $this->breakModel->totalBreakSeconds((int) $today['id']);
            $todayHours  = $this->attendanceModel->computeHours($today, $breakSecs);
        }

        $year  = (int) ($this->request->getGet('year') ?: date('Y'));
        $month = (int) ($this->request->getGet('month') ?: date('n'));

        $dayMap = $this->attendanceModel->dayStatusMap($employeeId, $year, $month);

        foreach ($dayMap as $day => $entry) {
            if ($entry['row']) {
                $secs = $this->breakModel->totalBreakSeconds((int) $entry['row']['id']);
                $dayMap[$day]['hours'] = $this->attendanceModel->computeHours($entry['row'], $secs);
            } else {
                $dayMap[$day]['hours'] = ['login_hours' => 0.0, 'break_hours' => 0.0, 'productive_hours' => 0.0];
            }
        }

        return view('attendance/my_attendance', [
            'today'       => $today,
            'todayBreaks' => $todayBreaks,
            'openBreak'   => $openBreak,
            'todayHours'  => $todayHours,
            'dayMap'      => $dayMap,
            'year'        => $year,
            'month'       => $month,
            'summary'     => $this->attendanceModel->monthlySummary($employeeId, $year, $month),
        ]);
    }

    public function checkIn()
    {
        $employeeId = session()->get('employee_id');

        if (! $employeeId) {
            return redirect()->to('/attendance')->with('error', 'Your account is not linked to an employee record.');
        }

        $this->attendanceModel->checkIn((int) $employeeId, 'manual');

        return redirect()->to('/attendance')->with('success', 'Checked in successfully.');
    }

    public function checkOut()
    {
        $employeeId = session()->get('employee_id');

        if (! $employeeId) {
            return redirect()->to('/attendance')->with('error', 'Your account is not linked to an employee record.');
        }

        $today = $this->attendanceModel->findToday((int) $employeeId);
        if ($today) {
            $open = $this->breakModel->openBreak((int) $today['id']);
            if ($open) {
                $this->breakModel->endBreak((int) $open['id']);
            }
        }

        $result = $this->attendanceModel->checkOut((int) $employeeId);

        if (! $result) {
            return redirect()->to('/attendance')->with('error', 'You have not checked in yet today.');
        }

        return redirect()->to('/attendance')->with('success', 'Checked out successfully.');
    }

    public function startBreak()
    {
        $employeeId = session()->get('employee_id');
        $today      = $employeeId ? $this->attendanceModel->findToday((int) $employeeId) : null;

        if (! $today || ! $today['check_in']) {
            return redirect()->to('/attendance')->with('error', 'Check in before starting a break.');
        }
        if ($today['check_out']) {
            return redirect()->to('/attendance')->with('error', 'You have already checked out for today.');
        }
        if ($this->breakModel->openBreak((int) $today['id'])) {
            return redirect()->to('/attendance')->with('error', 'A break is already in progress.');
        }

        $this->breakModel->startBreak((int) $today['id']);

        return redirect()->to('/attendance')->with('success', 'Break started.');
    }

    public function endBreak()
    {
        $employeeId = session()->get('employee_id');
        $today      = $employeeId ? $this->attendanceModel->findToday((int) $employeeId) : null;
        $open       = $today ? $this->breakModel->openBreak((int) $today['id']) : null;

        if (! $open) {
            return redirect()->to('/attendance')->with('error', 'No break is currently in progress.');
        }

        $this->breakModel->endBreak((int) $open['id']);

        return redirect()->to('/attendance')->with('success', 'Break ended.');
    }

    public function dailyView()
    {
        $date         = $this->request->getGet('date') ?: date('Y-m-d');
        $departmentId = $this->request->getGet('department');

        $departmentModel = new DepartmentModel();
        $holidayModel    = new HolidayModel();
        $grid            = $this->attendanceModel->dailyGrid($date, $departmentId ? (int) $departmentId : null);

        foreach ($grid as &$row) {
            if (! empty($row['attendance_id'])) {
                $secs         = $this->breakModel->totalBreakSeconds((int) $row['attendance_id']);
                $row['hours'] = $this->attendanceModel->computeHours($row, $secs);
            } else {
                $row['hours'] = ['login_hours' => 0.0, 'break_hours' => 0.0, 'productive_hours' => 0.0];
            }
        }
        unset($row);

        return view('attendance/daily', [
            'date'         => $date,
            'departmentId' => $departmentId,
            'departments'  => $departmentModel->findAll(),
            'grid'         => $grid,
            'holidayName'  => $holidayModel->isHoliday($date),
        ]);
    }

    public function manualUpdate()
    {
        $employeeId = (int) $this->request->getPost('employee_id');
        $date       = $this->request->getPost('attendance_date');
        $status     = $this->request->getPost('status');

        $this->attendanceModel->setManual($employeeId, $date, ['status' => $status]);

        return redirect()->to('/attendance/daily?date=' . $date)->with('success', 'Attendance updated.');
    }

    public function monthlyReport(int $employeeId)
    {
        $year  = (int) ($this->request->getGet('year') ?: date('Y'));
        $month = (int) ($this->request->getGet('month') ?: date('n'));

        $employeeModel = new EmployeeModel();
        $employee      = $employeeModel->find($employeeId);

        if (! $employee) {
            return redirect()->to('/attendance/daily')->with('error', 'Employee not found.');
        }

        return view('attendance/monthly_report', [
            'employee' => $employee,
            'year'     => $year,
            'month'    => $month,
            'summary'  => $this->attendanceModel->monthlySummary($employeeId, $year, $month),
        ]);
    }

    /**
     * Admin/HR: export the daily attendance grid as CSV.
     */
    public function exportDailyCsv()
    {
        $date         = $this->request->getGet('date') ?: date('Y-m-d');
        $departmentId = $this->request->getGet('department');

        $grid = $this->attendanceModel->dailyGrid($date, $departmentId ? (int) $departmentId : null);

        foreach ($grid as &$row) {
            if (! empty($row['attendance_id'])) {
                $secs         = $this->breakModel->totalBreakSeconds((int) $row['attendance_id']);
                $row['hours'] = $this->attendanceModel->computeHours($row, $secs);
            } else {
                $row['hours'] = ['login_hours' => 0.0, 'break_hours' => 0.0, 'productive_hours' => 0.0];
            }
        }
        unset($row);

        $filename = 'attendance-' . $date . '.csv';

        $output = fopen('php://temp', 'w');
        fputcsv($output, ['Employee Code', 'Name', 'Department', 'Check In', 'Check Out', 'Login Hrs', 'Productive Hrs', 'Break Hrs', 'Status']);

        foreach ($grid as $r) {
            fputcsv($output, [
                $r['employee_code'],
                $r['first_name'] . ' ' . $r['last_name'],
                $r['department_name'] ?? '-',
                $r['check_in'] ?? '-',
                $r['check_out'] ?? '-',
                $r['hours']['login_hours'],
                $r['hours']['productive_hours'],
                $r['hours']['break_hours'],
                $r['status'] ?? 'absent',
            ]);
        }

        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        return $this->response
            ->setHeader('Content-Type', 'text/csv')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setBody($csv);
    }
}

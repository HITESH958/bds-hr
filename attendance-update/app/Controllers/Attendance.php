<?php

namespace App\Controllers;

use App\Models\AttendanceModel;
use App\Models\EmployeeModel;
use CodeIgniter\Controller;

class Attendance extends Controller
{
    protected AttendanceModel $attendanceModel;
    protected EmployeeModel   $employeeModel;

    public function __construct()
    {
        $this->attendanceModel = new AttendanceModel();
        $this->employeeModel   = new EmployeeModel();
    }

    /**
     * Logged-in employee checks themselves in for today.
     */
    public function checkIn()
    {
        $employeeId = session()->get('employee_id');

        if (! $employeeId) {
            return redirect()->to('/dashboard')->with('error', 'No employee profile is linked to your account. Contact HR.');
        }

        if ($this->attendanceModel->todayRecord($employeeId)) {
            return redirect()->to('/dashboard')->with('error', 'You have already checked in today.');
        }

        $this->attendanceModel->checkIn($employeeId);

        return redirect()->to('/dashboard')->with('success', 'Checked in successfully.');
    }

    /**
     * Logged-in employee checks themselves out for today.
     */
    public function checkOut()
    {
        $employeeId = session()->get('employee_id');

        if (! $employeeId) {
            return redirect()->to('/dashboard')->with('error', 'No employee profile is linked to your account. Contact HR.');
        }

        if (! $this->attendanceModel->checkOut($employeeId)) {
            return redirect()->to('/dashboard')->with('error', 'You need to check in first, or you already checked out today.');
        }

        return redirect()->to('/dashboard')->with('success', 'Checked out successfully.');
    }

    /**
     * The logged-in employee's own attendance history.
     */
    public function myAttendance()
    {
        $employeeId = session()->get('employee_id');

        if (! $employeeId) {
            return redirect()->to('/dashboard')->with('error', 'No employee profile is linked to your account. Contact HR.');
        }

        $month = $this->request->getGet('month') ?: date('Y-m');

        return view('attendance/my', [
            'records' => $this->attendanceModel->monthlySummary((int) $employeeId, $month),
            'month'   => $month,
        ]);
    }

    /**
     * Admin/HR: attendance report across all employees, with filters.
     */
    public function index()
    {
        $dateFrom   = $this->request->getGet('date_from');
        $dateTo     = $this->request->getGet('date_to');
        $employeeId = $this->request->getGet('employee_id');

        $records = $this->attendanceModel->listWithEmployee(
            $dateFrom ?: null,
            $dateTo ?: null,
            $employeeId ? (int) $employeeId : null
        );

        return view('attendance/index', [
            'records'    => $records,
            'pager'      => $this->attendanceModel->pager,
            'employees'  => $this->employeeModel->orderBy('first_name', 'ASC')->findAll(),
            'dateFrom'   => $dateFrom,
            'dateTo'     => $dateTo,
            'employeeId' => $employeeId,
        ]);
    }
}

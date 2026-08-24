<?php

namespace App\Controllers;

use App\Models\AttendanceBreakModel;
use App\Models\AttendanceModel;
use App\Models\DepartmentModel;
use App\Models\EmployeeModel;
use App\Models\LeaveRequestModel;
use CodeIgniter\Controller;

class Dashboard extends Controller
{
    public function index()
    {
        $role = session()->get('role');

        if (in_array($role, ['admin', 'hr'], true)) {
            return $this->adminDashboard();
        }

        return $this->employeeDashboard();
    }

    private function adminDashboard()
    {
        $employeeModel     = new EmployeeModel();
        $departmentModel   = new DepartmentModel();
        $leaveRequestModel = new LeaveRequestModel();

        return view('dashboard_admin', [
            'totalEmployees'   => $employeeModel->where('status', 'active')->countAllResults(),
            'totalDepartments' => $departmentModel->countAllResults(),
            'recentEmployees'  => $employeeModel->orderBy('id', 'DESC')->findAll(5),
            'onLeaveToday'     => $leaveRequestModel->onLeaveToday(),
        ]);
    }

    private function employeeDashboard()
    {
        $employeeId = session()->get('employee_id');

        if (! $employeeId) {
            // Logged in but not linked to an employee record -- nothing
            // personal to show, fall back to a minimal version of the page.
            return view('dashboard_employee', [
                'linked'     => false,
                'today'      => null,
                'openBreak'  => null,
                'todayHours' => ['login_hours' => 0.0, 'break_hours' => 0.0, 'productive_hours' => 0.0],
                'summary'    => ['present' => 0, 'late' => 0, 'half_day' => 0, 'absent' => 0, 'on_leave' => 0],
            ]);
        }

        $employeeId           = (int) $employeeId;
        $attendanceModel      = new AttendanceModel();
        $breakModel           = new AttendanceBreakModel();

        $today      = $attendanceModel->findToday($employeeId);
        $openBreak  = null;
        $todayHours = ['login_hours' => 0.0, 'break_hours' => 0.0, 'productive_hours' => 0.0];

        if ($today) {
            $openBreak  = $breakModel->openBreak((int) $today['id']);
            $breakSecs  = $breakModel->totalBreakSeconds((int) $today['id']);
            $todayHours = $attendanceModel->computeHours($today, $breakSecs);
        }

        return view('dashboard_employee', [
            'linked'     => true,
            'today'      => $today,
            'openBreak'  => $openBreak,
            'todayHours' => $todayHours,
            'summary'    => $attendanceModel->monthlySummary($employeeId, (int) date('Y'), (int) date('n')),
        ]);
    }
}

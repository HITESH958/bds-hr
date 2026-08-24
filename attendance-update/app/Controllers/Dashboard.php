<?php

namespace App\Controllers;

use App\Models\AttendanceModel;
use App\Models\DepartmentModel;
use App\Models\EmployeeModel;
use CodeIgniter\Controller;

class Dashboard extends Controller
{
    public function index()
    {
        $employeeModel   = new EmployeeModel();
        $departmentModel = new DepartmentModel();
        $attendanceModel = new AttendanceModel();

        $employeeId    = session()->get('employee_id');
        $todayRecord   = $employeeId ? $attendanceModel->todayRecord((int) $employeeId) : null;

        return view('dashboard', [
            'totalEmployees'   => $employeeModel->where('status', 'active')->countAllResults(),
            'totalDepartments' => $departmentModel->countAllResults(),
            'recentEmployees'  => $employeeModel->orderBy('id', 'DESC')->findAll(5),
            'todayRecord'      => $todayRecord,
        ]);
    }
}

<?php

namespace App\Controllers;

use App\Models\AttendanceModel;
use App\Models\LeaveBalanceModel;
use App\Models\LeaveRequestModel;
use App\Models\LeaveTypeModel;
use CodeIgniter\Controller;

class Leave extends Controller
{
    protected LeaveRequestModel $leaveRequestModel;
    protected LeaveBalanceModel $leaveBalanceModel;
    protected LeaveTypeModel    $leaveTypeModel;

    public function __construct()
    {
        $this->leaveRequestModel = new LeaveRequestModel();
        $this->leaveBalanceModel = new LeaveBalanceModel();
        $this->leaveTypeModel    = new LeaveTypeModel();
    }

    /**
     * Employee self-service: view own requests + balances, apply for new leave.
     */
    public function index()
    {
        $employeeId = session()->get('employee_id');

        if (! $employeeId) {
            return redirect()->to('/dashboard')->with('error', 'Your account is not linked to an employee record.');
        }

        $year = (int) date('Y');

        return view('leave/index', [
            'requests'   => $this->leaveRequestModel->forEmployee((int) $employeeId),
            'balances'   => $this->leaveBalanceModel->forEmployee((int) $employeeId, $year),
            'leaveTypes' => $this->leaveTypeModel->findAll(),
        ]);
    }

    public function apply()
    {
        $employeeId = session()->get('employee_id');

        if (! $employeeId) {
            return redirect()->to('/leave')->with('error', 'Your account is not linked to an employee record.');
        }

        $rules = [
            'leave_type_id' => 'required|is_natural_no_zero',
            'start_date'    => 'required|valid_date',
            'end_date'      => 'required|valid_date',
            'reason'        => 'permit_empty|max_length[500]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $startDate   = $this->request->getPost('start_date');
        $endDate     = $this->request->getPost('end_date');
        $leaveTypeId = (int) $this->request->getPost('leave_type_id');

        if (strtotime($endDate) < strtotime($startDate)) {
            return redirect()->back()->withInput()->with('error', 'End date cannot be before start date.');
        }

        $days = $this->leaveRequestModel->calculateDays($startDate, $endDate);

        if ($days <= 0) {
            return redirect()->back()->withInput()->with('error', 'Selected range has no working days.');
        }

        // Check balance up front so the employee gets immediate feedback,
        // even though the actual deduction only happens on approval.
        $balance = $this->leaveBalanceModel->getBalance((int) $employeeId, $leaveTypeId, (int) date('Y'));
        $remaining = $balance ? ((float) $balance['allocated_days'] - (float) $balance['used_days']) : 0;

        if ($days > $remaining) {
            return redirect()->back()->withInput()->with('error',
                "Insufficient balance: requesting {$days} day(s), only {$remaining} remaining.");
        }

        $this->leaveRequestModel->insert([
            'employee_id'   => $employeeId,
            'leave_type_id' => $leaveTypeId,
            'start_date'    => $startDate,
            'end_date'      => $endDate,
            'days'          => $days,
            'reason'        => $this->request->getPost('reason'),
            'status'        => 'pending',
        ]);

        return redirect()->to('/leave')->with('success', 'Leave request submitted.');
    }

    public function cancel(int $id)
    {
        $employeeId = session()->get('employee_id');
        $request    = $this->leaveRequestModel->find($id);

        if (! $request || (int) $request['employee_id'] !== (int) $employeeId) {
            return redirect()->to('/leave')->with('error', 'Request not found.');
        }

        if ($request['status'] !== 'pending') {
            return redirect()->to('/leave')->with('error', 'Only pending requests can be cancelled.');
        }

        $this->leaveRequestModel->update($id, ['status' => 'cancelled']);

        return redirect()->to('/leave')->with('success', 'Leave request cancelled.');
    }

    /**
     * Admin/HR: list of pending requests awaiting approval.
     */
    public function pending()
    {
        return view('leave/pending', [
            'requests' => $this->leaveRequestModel->pending(),
        ]);
    }

    /**
     * Admin/HR: all requests, any status (history view).
     */
    public function allRequests()
    {
        return view('leave/all', [
            'requests' => $this->leaveRequestModel->all(),
        ]);
    }

    public function approve(int $id)
    {
        $request = $this->leaveRequestModel->find($id);

        if (! $request || $request['status'] !== 'pending') {
            return redirect()->back()->with('error', 'Request not found or already reviewed.');
        }

        $year = (int) date('Y', strtotime($request['start_date']));

        $deducted = $this->leaveBalanceModel->deduct(
            (int) $request['employee_id'],
            (int) $request['leave_type_id'],
            $year,
            (float) $request['days']
        );

        if (! $deducted) {
            return redirect()->back()->with('error', 'Cannot approve: insufficient leave balance.');
        }

        $this->leaveRequestModel->update($id, [
            'status'      => 'approved',
            'reviewed_by' => session()->get('user_id'),
            'reviewed_at' => date('Y-m-d H:i:s'),
        ]);

        // Mark attendance as "on_leave" for each working day in the range.
        $attendanceModel = new AttendanceModel();
        $current = strtotime($request['start_date']);
        $end     = strtotime($request['end_date']);

        while ($current <= $end) {
            $dayOfWeek = (int) date('N', $current);
            if ($dayOfWeek < 6) { // skip weekends
                $attendanceModel->setManual(
                    (int) $request['employee_id'],
                    date('Y-m-d', $current),
                    ['status' => 'on_leave']
                );
            }
            $current = strtotime('+1 day', $current);
        }

        return redirect()->back()->with('success', 'Leave approved.');
    }

    public function reject(int $id)
    {
        $request = $this->leaveRequestModel->find($id);

        if (! $request || $request['status'] !== 'pending') {
            return redirect()->back()->with('error', 'Request not found or already reviewed.');
        }

        $this->leaveRequestModel->update($id, [
            'status'       => 'rejected',
            'reviewed_by'  => session()->get('user_id'),
            'reviewed_at'  => date('Y-m-d H:i:s'),
            'review_notes' => $this->request->getPost('review_notes'),
        ]);

        return redirect()->back()->with('success', 'Leave rejected.');
    }
}
<?php

namespace App\Controllers;

use App\Models\EmployeeModel;
use App\Models\UserModel;
use CodeIgniter\Controller;

class Users extends Controller
{
    protected UserModel     $userModel;
    protected EmployeeModel $employeeModel;

    public function __construct()
    {
        $this->userModel     = new UserModel();
        $this->employeeModel = new EmployeeModel();
    }

    public function index()
    {
        return view('users/index', [
            'users' => $this->userModel->listWithEmployees(),
        ]);
    }

    public function create()
    {
        return view('users/create', [
            'employees' => $this->employeeModel->withoutLogin(),
        ]);
    }

    public function store()
    {
        $rules = [
            'employee_id' => 'required|is_natural_no_zero',
            'username'    => 'required|min_length[3]|max_length[100]|is_unique[users.username]',
            'email'       => 'required|valid_email|is_unique[users.email]',
            'password'    => 'required|min_length[8]',
            'role'        => 'required|in_list[admin,hr,employee]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->userModel->insert([
            'employee_id' => $this->request->getPost('employee_id'),
            'username'    => $this->request->getPost('username'),
            'email'       => $this->request->getPost('email'),
            'password'    => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'role'        => $this->request->getPost('role'),
            'status'      => 'active',
        ]);

        return redirect()->to('/users')->with('success', 'Login created successfully.');
    }

    public function resetPasswordForm(int $id)
    {
        $user = $this->userModel->find($id);

        if (! $user) {
            return redirect()->to('/users')->with('error', 'User not found.');
        }

        return view('users/reset_password', ['userAccount' => $user]);
    }

    public function resetPassword(int $id)
    {
        $rules = [
            'password'         => 'required|min_length[8]',
            'password_confirm' => 'required|matches[password]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $user = $this->userModel->find($id);

        if (! $user) {
            return redirect()->to('/users')->with('error', 'User not found.');
        }

        $this->userModel->update($id, [
            'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
        ]);

        return redirect()->to('/users')->with('success', 'Password reset successfully.');
    }

    /**
     * Toggle a login between active/inactive -- disables sign-in without
     * deleting the account or touching the linked employee record.
     */
    public function toggleStatus(int $id)
    {
        $user = $this->userModel->find($id);

        if (! $user) {
            return redirect()->to('/users')->with('error', 'User not found.');
        }

        // Guard: never let the currently logged-in admin lock themselves out.
        if ((int) $user['id'] === (int) session()->get('user_id')) {
            return redirect()->to('/users')->with('error', 'You cannot deactivate your own account.');
        }

        $newStatus = $user['status'] === 'active' ? 'inactive' : 'active';
        $this->userModel->update($id, ['status' => $newStatus]);

        return redirect()->to('/users')->with('success', "Account marked {$newStatus}.");
    }
}

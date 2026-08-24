<?php

namespace App\Controllers;

use App\Models\EmployeeModel;
use CodeIgniter\Controller;

class Profile extends Controller
{
    protected EmployeeModel $employeeModel;

    public function __construct()
    {
        $this->employeeModel = new EmployeeModel();
    }

    public function index()
    {
        $employeeId = session()->get('employee_id');

        if (! $employeeId) {
            return redirect()->to('/dashboard')->with('error', 'Your account is not linked to an employee record.');
        }

        $employee = $this->employeeModel->find((int) $employeeId);

        if (! $employee) {
            return redirect()->to('/dashboard')->with('error', 'Employee record not found.');
        }

        return view('profile/index', ['employee' => $employee]);
    }

    public function uploadPhoto()
    {
        $employeeId = session()->get('employee_id');

        if (! $employeeId) {
            return redirect()->to('/dashboard')->with('error', 'Your account is not linked to an employee record.');
        }

        $file = $this->request->getFile('profile_photo');

        $rules = [
            'profile_photo' => [
                'label' => 'Profile photo',
                'rules' => 'uploaded[profile_photo]|is_image[profile_photo]|max_size[profile_photo,2048]'
                    . '|ext_in[profile_photo,jpg,jpeg,png]',
            ],
        ];

        if (! $this->validate($rules)) {
            return redirect()->to('/profile')->with('errors', $this->validator->getErrors());
        }

        $employee = $this->employeeModel->find((int) $employeeId);
        $newName  = 'emp_' . $employeeId . '_' . time() . '.' . $file->getExtension();

        // Move into public/uploads/profiles so it's directly web-accessible.
        $file->move(FCPATH . 'uploads/profiles', $newName);

        // Clean up the old photo file, if any, so we don't accumulate orphans.
        if (! empty($employee['profile_photo'])) {
            $oldPath = FCPATH . 'uploads/profiles/' . $employee['profile_photo'];
            if (is_file($oldPath)) {
                @unlink($oldPath);
            }
        }

        $this->employeeModel->update((int) $employeeId, ['profile_photo' => $newName]);

        return redirect()->to('/profile')->with('success', 'Profile photo updated.');
    }
}

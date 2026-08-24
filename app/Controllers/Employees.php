<?php

namespace App\Controllers;

use App\Models\DepartmentModel;
use App\Models\EmployeeModel;
use CodeIgniter\Controller;

class Employees extends Controller
{
    protected EmployeeModel   $employeeModel;
    protected DepartmentModel $departmentModel;

    public function __construct()
    {
        $this->employeeModel   = new EmployeeModel();
        $this->departmentModel = new DepartmentModel();
    }

    public function index()
    {
        $search       = $this->request->getGet('q');
        $departmentId = $this->request->getGet('department');

        $employees = $this->employeeModel->listWithDepartment($search, $departmentId ? (int) $departmentId : null);

        return view('employees/index', [
            'employees'    => $employees,
            'pager'        => $this->employeeModel->pager,
            'departments'  => $this->departmentModel->findAll(),
            'search'       => $search,
            'departmentId' => $departmentId,
        ]);
    }

    public function create()
    {
        return view('employees/create', [
            'departments' => $this->departmentModel->findAll(),
        ]);
    }

    public function store()
    {
        $data = $this->request->getPost([
            'employee_code', 'first_name', 'last_name', 'email', 'phone',
            'department_id', 'designation', 'date_of_joining', 'date_of_birth',
            'gender', 'address', 'status',
        ]);

        if (! $this->employeeModel->save($data)) {
            return redirect()->back()->withInput()->with('errors', $this->employeeModel->errors());
        }

        return redirect()->to('/employees')->with('success', 'Employee added successfully.');
    }

    public function edit(int $id)
    {
        $employee = $this->employeeModel->find($id);

        if (! $employee) {
            return redirect()->to('/employees')->with('error', 'Employee not found.');
        }

        return view('employees/edit', [
            'employee'    => $employee,
            'departments' => $this->departmentModel->findAll(),
        ]);
    }

    public function update(int $id)
    {
        $employee = $this->employeeModel->find($id);

        if (! $employee) {
            return redirect()->to('/employees')->with('error', 'Employee not found.');
        }

        $data = $this->request->getPost([
            'employee_code', 'first_name', 'last_name', 'email', 'phone',
            'department_id', 'designation', 'date_of_joining', 'date_of_birth',
            'gender', 'address', 'status',
        ]);

        if (! $this->employeeModel->update($id, $data)) {
            return redirect()->back()->withInput()->with('errors', $this->employeeModel->errors());
        }

        return redirect()->to('/employees')->with('success', 'Employee updated successfully.');
    }

    /**
     * Soft-delete: marks the employee as "resigned" rather than removing
     * the row, preserving their attendance/leave/payroll history.
     */
    public function delete(int $id)
    {
        $employee = $this->employeeModel->find($id);

        if (! $employee) {
            return redirect()->to('/employees')->with('error', 'Employee not found.');
        }

        $this->employeeModel->update($id, ['status' => 'resigned']);

        return redirect()->to('/employees')->with('success', 'Employee marked as resigned. Their records have been kept for history.');
    }

    // ---------- CSV Import ----------

    public function importForm()
    {
        return view('employees/import');
    }

    public function import()
    {
        $file = $this->request->getFile('csv_file');

        if (! $file || ! $file->isValid()) {
            return redirect()->to('/employees/import')->with('error', 'Please choose a valid CSV file.');
        }

        $handle = fopen($file->getTempName(), 'r');
        if (! $handle) {
            return redirect()->to('/employees/import')->with('error', 'Could not read the uploaded file.');
        }

        $header = fgetcsv($handle);
        if (! $header) {
            fclose($handle);
            return redirect()->to('/employees/import')->with('error', 'The file appears to be empty.');
        }

        $header = array_map(static fn ($h) => strtolower(trim($h)), $header);

        $required = ['employee_code', 'first_name', 'last_name', 'email'];
        foreach ($required as $col) {
            if (! in_array($col, $header, true)) {
                fclose($handle);
                return redirect()->to('/employees/import')->with('error', "Missing required column: {$col}");
            }
        }

        $imported = 0;
        $skipped  = 0;
        $rowNum   = 1;
        $errors   = [];

        while (($row = fgetcsv($handle)) !== false) {
            $rowNum++;

            if (count($row) < count($header)) {
                $skipped++;
                $errors[] = "Row {$rowNum}: column count mismatch, skipped.";
                continue;
            }

            $data = array_combine($header, $row);

            $employeeCode = trim($data['employee_code'] ?? '');
            $firstName    = trim($data['first_name'] ?? '');
            $lastName     = trim($data['last_name'] ?? '');
            $email        = trim($data['email'] ?? '');

            if ($employeeCode === '' || $firstName === '' || $lastName === '' || $email === '') {
                $skipped++;
                $errors[] = "Row {$rowNum}: missing required field(s), skipped.";
                continue;
            }

            // Resolve department by name, creating it if it doesn't exist yet.
            $departmentId = null;
            $deptName     = trim($data['department'] ?? '');
            if ($deptName !== '') {
                $dept = $this->departmentModel->where('name', $deptName)->first();
                if (! $dept) {
                    $departmentId = $this->departmentModel->insert(['name' => $deptName], true);
                } else {
                    $departmentId = $dept['id'];
                }
            }

            $record = [
                'employee_code'   => $employeeCode,
                'first_name'      => $firstName,
                'last_name'       => $lastName,
                'email'           => $email,
                'phone'           => trim($data['phone'] ?? '') ?: null,
                'department_id'   => $departmentId,
                'designation'     => trim($data['designation'] ?? '') ?: null,
                'date_of_joining' => trim($data['date_of_joining'] ?? '') ?: null,
                'gender'          => trim($data['gender'] ?? '') ?: null,
                'status'          => trim($data['status'] ?? '') ?: 'active',
            ];

            if (! $this->employeeModel->save($record)) {
                $skipped++;
                $rowErrors = implode(', ', $this->employeeModel->errors());
                $errors[]  = "Row {$rowNum} ({$employeeCode}): {$rowErrors}";
                continue;
            }

            $imported++;
        }

        fclose($handle);

        $message = "{$imported} employee(s) imported.";
        if ($skipped > 0) {
            $message .= " {$skipped} row(s) skipped.";
        }

        session()->setFlashdata('import_errors', array_slice($errors, 0, 20));

        return redirect()->to('/employees')->with('success', $message);
    }

    // ---------- CSV Export ----------

    public function exportCsv()
    {
        $search       = $this->request->getGet('q');
        $departmentId = $this->request->getGet('department');

        $builder = $this->employeeModel->select('employees.*, departments.name as department_name')
            ->join('departments', 'departments.id = employees.department_id', 'left');

        if ($search) {
            $builder->groupStart()
                ->like('employees.first_name', $search)
                ->orLike('employees.last_name', $search)
                ->orLike('employees.employee_code', $search)
                ->orLike('employees.email', $search)
                ->groupEnd();
        }
        if ($departmentId) {
            $builder->where('employees.department_id', (int) $departmentId);
        }

        $rows = $builder->orderBy('employees.first_name', 'ASC')->findAll();

        $filename = 'employees-' . date('Y-m-d') . '.csv';

        $output = fopen('php://temp', 'w');
        fputcsv($output, ['Employee Code', 'First Name', 'Last Name', 'Email', 'Phone', 'Department', 'Designation', 'Date of Joining', 'Status']);

        foreach ($rows as $r) {
            fputcsv($output, [
                $r['employee_code'], $r['first_name'], $r['last_name'], $r['email'], $r['phone'],
                $r['department_name'], $r['designation'], $r['date_of_joining'], $r['status'],
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

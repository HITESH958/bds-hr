<?php

namespace App\Models;

use CodeIgniter\Model;

class EmployeeModel extends Model
{
    protected $table            = 'employees';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'employee_code', 'first_name', 'last_name', 'email', 'phone',
        'department_id', 'designation', 'date_of_joining', 'date_of_birth',
        'gender', 'address', 'profile_photo', 'status',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'employee_code' => 'required|max_length[20]|is_unique[employees.employee_code,id,{id}]',
        'first_name'    => 'required|max_length[100]',
        'last_name'     => 'required|max_length[100]',
        'email'         => 'required|valid_email|is_unique[employees.email,id,{id}]',
        'status'        => 'required|in_list[active,inactive,resigned]',
    ];

    /**
     * Employees joined with department name, with optional search/filter/pagination.
     */
    public function listWithDepartment(?string $search = null, ?int $departmentId = null, int $perPage = 20)
    {
        $builder = $this->select('employees.*, departments.name as department_name')
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
            $builder->where('employees.department_id', $departmentId);
        }

        return $builder->orderBy('employees.id', 'DESC')->paginate($perPage);
    }

    /**
     * Active employees who don't have a login (users) row yet -- the pool
     * of candidates HR can create an account for.
     */
    public function withoutLogin(): array
    {
        return $this->select('employees.*')
            ->join('users', 'users.employee_id = employees.id', 'left')
            ->where('employees.status', 'active')
            ->where('users.id', null)
            ->orderBy('employees.first_name', 'ASC')
            ->findAll();
    }
}

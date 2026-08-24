<?php

namespace App\Models;

use CodeIgniter\Model;

class SalaryStructureModel extends Model
{
    protected $table            = 'salary_structures';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['employee_id', 'basic', 'hra', 'allowances', 'effective_from'];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function forEmployee(int $employeeId): ?array
    {
        return $this->where('employee_id', $employeeId)->first();
    }

    /**
     * Create or update the (single) salary structure for an employee.
     */
    public function setForEmployee(int $employeeId, array $data): bool
    {
        $existing = $this->forEmployee($employeeId);

        if ($existing) {
            return (bool) $this->update($existing['id'], $data);
        }

        $data['employee_id'] = $employeeId;

        return (bool) $this->insert($data);
    }

    /**
     * All active employees joined with their salary structure (if any),
     * for the admin salary management list.
     */
    public function listWithEmployees(): array
    {
        return $this->db->table('employees')
            ->select('employees.id as employee_id, employees.employee_code, employees.first_name, employees.last_name,
                      salary_structures.basic, salary_structures.hra, salary_structures.allowances, salary_structures.effective_from')
            ->join('salary_structures', 'salary_structures.employee_id = employees.id', 'left')
            ->where('employees.status', 'active')
            ->orderBy('employees.first_name', 'ASC')
            ->get()->getResultArray();
    }
}

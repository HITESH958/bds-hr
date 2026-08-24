<?php

namespace App\Models;

use CodeIgniter\Model;

class PayslipModel extends Model
{
    protected $table            = 'payslips';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'payroll_period_id', 'employee_id', 'basic', 'hra', 'allowances',
        'gross_earnings', 'working_days', 'lop_days', 'per_day_rate',
        'lop_deduction', 'net_pay',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function forPeriod(int $periodId): array
    {
        return $this->select('payslips.*, employees.employee_code, employees.first_name, employees.last_name')
            ->join('employees', 'employees.id = payslips.employee_id')
            ->where('payroll_period_id', $periodId)
            ->orderBy('employees.first_name', 'ASC')
            ->findAll();
    }

    public function forEmployee(int $employeeId): array
    {
        return $this->select('payslips.*, payroll_periods.month, payroll_periods.year')
            ->join('payroll_periods', 'payroll_periods.id = payslips.payroll_period_id')
            ->where('payslips.employee_id', $employeeId)
            ->orderBy('payroll_periods.year', 'DESC')
            ->orderBy('payroll_periods.month', 'DESC')
            ->findAll();
    }

    public function existsForPeriodAndEmployee(int $periodId, int $employeeId): bool
    {
        return (bool) $this->where('payroll_period_id', $periodId)
            ->where('employee_id', $employeeId)
            ->first();
    }
}

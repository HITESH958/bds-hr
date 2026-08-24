<?php

namespace App\Models;

use CodeIgniter\Model;

class PayrollPeriodModel extends Model
{
    protected $table            = 'payroll_periods';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['month', 'year', 'status', 'generated_by', 'generated_at'];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function findByMonthYear(int $month, int $year): ?array
    {
        return $this->where('month', $month)->where('year', $year)->first();
    }

    public function allOrdered(): array
    {
        return $this->orderBy('year', 'DESC')->orderBy('month', 'DESC')->findAll();
    }
}

<?php

namespace App\Models;

use CodeIgniter\Model;

class LeaveBalanceModel extends Model
{
    protected $table            = 'leave_balances';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['employee_id', 'leave_type_id', 'year', 'allocated_days', 'used_days'];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * All balances for an employee for a given year, joined with leave type name.
     */
    public function forEmployee(int $employeeId, int $year): array
    {
        return $this->select('leave_balances.*, leave_types.name as leave_type_name')
            ->join('leave_types', 'leave_types.id = leave_balances.leave_type_id')
            ->where('leave_balances.employee_id', $employeeId)
            ->where('leave_balances.year', $year)
            ->orderBy('leave_types.name', 'ASC')
            ->findAll();
    }

    public function getBalance(int $employeeId, int $leaveTypeId, int $year): ?array
    {
        return $this->where('employee_id', $employeeId)
            ->where('leave_type_id', $leaveTypeId)
            ->where('year', $year)
            ->first();
    }

    /**
     * Increase used_days by $days. Returns false if it would exceed allocated_days.
     */
    public function deduct(int $employeeId, int $leaveTypeId, int $year, float $days): bool
    {
        $balance = $this->getBalance($employeeId, $leaveTypeId, $year);

        if (! $balance) {
            return false;
        }

        if ((float) $balance['used_days'] + $days > (float) $balance['allocated_days']) {
            return false;
        }

        return (bool) $this->update($balance['id'], [
            'used_days' => (float) $balance['used_days'] + $days,
        ]);
    }

    /**
     * Reverse a deduction (e.g. on rejection after prior approval, or cancellation).
     */
    public function restore(int $employeeId, int $leaveTypeId, int $year, float $days): bool
    {
        $balance = $this->getBalance($employeeId, $leaveTypeId, $year);

        if (! $balance) {
            return false;
        }

        $newUsed = max(0, (float) $balance['used_days'] - $days);

        return (bool) $this->update($balance['id'], ['used_days' => $newUsed]);
    }
}
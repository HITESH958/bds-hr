<?php

namespace App\Models;

use CodeIgniter\Model;

class LeaveRequestModel extends Model
{
    protected $table            = 'leave_requests';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'employee_id', 'leave_type_id', 'start_date', 'end_date', 'days',
        'reason', 'status', 'reviewed_by', 'reviewed_at', 'review_notes',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Count of weekdays (Mon-Fri) between two dates inclusive.
     */
    public function calculateDays(string $startDate, string $endDate): float
    {
        $start = new \DateTime($startDate);
        $end   = new \DateTime($endDate);
        $end->modify('+1 day'); // make range inclusive

        $interval = new \DateInterval('P1D');
        $period   = new \DatePeriod($start, $interval, $end);

        $days = 0;
        foreach ($period as $date) {
            if ((int) $date->format('N') < 6) { // 1-5 = Mon-Fri
                $days++;
            }
        }

        return (float) $days;
    }

    public function forEmployee(int $employeeId): array
    {
        return $this->select('leave_requests.*, leave_types.name as leave_type_name')
            ->join('leave_types', 'leave_types.id = leave_requests.leave_type_id')
            ->where('leave_requests.employee_id', $employeeId)
            ->orderBy('leave_requests.created_at', 'DESC')
            ->findAll();
    }

    public function pending(): array
    {
        return $this->select('leave_requests.*, leave_types.name as leave_type_name,
                               employees.employee_code, employees.first_name, employees.last_name')
            ->join('leave_types', 'leave_types.id = leave_requests.leave_type_id')
            ->join('employees', 'employees.id = leave_requests.employee_id')
            ->where('leave_requests.status', 'pending')
            ->orderBy('leave_requests.created_at', 'ASC')
            ->findAll();
    }

    public function all(): array
    {
        return $this->select('leave_requests.*, leave_types.name as leave_type_name,
                               employees.employee_code, employees.first_name, employees.last_name')
            ->join('leave_types', 'leave_types.id = leave_requests.leave_type_id')
            ->join('employees', 'employees.id = leave_requests.employee_id')
            ->orderBy('leave_requests.created_at', 'DESC')
            ->findAll();
    }

    /**
     * Employees currently on approved leave today -- powers the "Who's Out
     * Today" dashboard widget so HR can see team availability at a glance.
     */
    public function onLeaveToday(): array
    {
        $today = date('Y-m-d');

        return $this->select('leave_requests.*, leave_types.name as leave_type_name,
                               employees.id as employee_id, employees.employee_code,
                               employees.first_name, employees.last_name')
            ->join('leave_types', 'leave_types.id = leave_requests.leave_type_id')
            ->join('employees', 'employees.id = leave_requests.employee_id')
            ->where('leave_requests.status', 'approved')
            ->where('leave_requests.start_date <=', $today)
            ->where('leave_requests.end_date >=', $today)
            ->orderBy('employees.first_name', 'ASC')
            ->findAll();
    }
}

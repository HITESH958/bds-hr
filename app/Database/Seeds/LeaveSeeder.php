<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class LeaveSeeder extends Seeder
{
    public function run()
    {
        $types = [
            ['name' => 'Casual Leave', 'default_days_per_year' => 12],
            ['name' => 'Sick Leave',   'default_days_per_year' => 8],
            ['name' => 'Earned Leave', 'default_days_per_year' => 15],
        ];

        foreach ($types as $type) {
            // Skip if already seeded (safe to re-run).
            $exists = $this->db->table('leave_types')->where('name', $type['name'])->get()->getRow();
            if ($exists) {
                continue;
            }

            $this->db->table('leave_types')->insert([
                'name'                  => $type['name'],
                'default_days_per_year' => $type['default_days_per_year'],
                'created_at'            => date('Y-m-d H:i:s'),
                'updated_at'            => date('Y-m-d H:i:s'),
            ]);
        }

        // Allocate current-year balances for every active employee, for every leave type.
        $year      = (int) date('Y');
        $employees = $this->db->table('employees')->where('status', 'active')->get()->getResultArray();
        $leaveTypes = $this->db->table('leave_types')->get()->getResultArray();

        foreach ($employees as $employee) {
            foreach ($leaveTypes as $leaveType) {
                $exists = $this->db->table('leave_balances')
                    ->where('employee_id', $employee['id'])
                    ->where('leave_type_id', $leaveType['id'])
                    ->where('year', $year)
                    ->get()->getRow();

                if ($exists) {
                    continue;
                }

                $this->db->table('leave_balances')->insert([
                    'employee_id'    => $employee['id'],
                    'leave_type_id'  => $leaveType['id'],
                    'year'           => $year,
                    'allocated_days' => $leaveType['default_days_per_year'],
                    'used_days'      => 0,
                    'created_at'     => date('Y-m-d H:i:s'),
                    'updated_at'     => date('Y-m-d H:i:s'),
                ]);
            }
        }
    }
}
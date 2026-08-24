<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePayslips extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'payroll_period_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'employee_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'basic' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
            ],
            'hra' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
            ],
            'allowances' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
            ],
            'gross_earnings' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
            ],
            'working_days' => [
                'type'       => 'INT',
                'constraint' => 2,
            ],
            'lop_days' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,1',
                'default'    => 0,
            ],
            'per_day_rate' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
            ],
            'lop_deduction' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => 0,
            ],
            'net_pay' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
            ],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['payroll_period_id', 'employee_id']);
        $this->forge->addForeignKey('payroll_period_id', 'payroll_periods', 'id', '', 'CASCADE');
        $this->forge->addForeignKey('employee_id', 'employees', 'id', '', 'CASCADE');
        $this->forge->createTable('payslips');
    }

    public function down()
    {
        $this->forge->dropTable('payslips');
    }
}

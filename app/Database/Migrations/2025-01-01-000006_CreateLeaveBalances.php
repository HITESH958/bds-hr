<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLeaveBalances extends Migration
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
            'employee_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'leave_type_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'year' => [
                'type'       => 'INT',
                'constraint' => 4,
            ],
            'allocated_days' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,1',
                'default'    => 0,
            ],
            'used_days' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,1',
                'default'    => 0,
            ],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['employee_id', 'leave_type_id', 'year']);
        $this->forge->addForeignKey('employee_id', 'employees', 'id', '', 'CASCADE');
        $this->forge->addForeignKey('leave_type_id', 'leave_types', 'id', '', 'CASCADE');
        $this->forge->createTable('leave_balances');
    }

    public function down()
    {
        $this->forge->dropTable('leave_balances');
    }
}
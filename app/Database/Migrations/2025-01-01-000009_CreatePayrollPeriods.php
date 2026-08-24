<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePayrollPeriods extends Migration
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
            'month' => [
                'type'       => 'INT',
                'constraint' => 2,
            ],
            'year' => [
                'type'       => 'INT',
                'constraint' => 4,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['draft', 'finalized'],
                'default'    => 'draft',
            ],
            'generated_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'generated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['month', 'year']);
        $this->forge->createTable('payroll_periods');
    }

    public function down()
    {
        $this->forge->dropTable('payroll_periods');
    }
}

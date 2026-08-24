<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateEmployees extends Migration
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
            'employee_code'    => ['type' => 'VARCHAR', 'constraint' => 20],
            'first_name'       => ['type' => 'VARCHAR', 'constraint' => 100],
            'last_name'        => ['type' => 'VARCHAR', 'constraint' => 100],
            'email'            => ['type' => 'VARCHAR', 'constraint' => 150],
            'phone'            => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'department_id'    => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'designation'      => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'date_of_joining'  => ['type' => 'DATE', 'null' => true],
            'date_of_birth'    => ['type' => 'DATE', 'null' => true],
            'gender'           => ['type' => 'ENUM', 'constraint' => ['male', 'female', 'other'], 'null' => true],
            'address'          => ['type' => 'TEXT', 'null' => true],
            'profile_photo'    => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'status'           => ['type' => 'ENUM', 'constraint' => ['active', 'inactive', 'resigned'], 'default' => 'active'],
            'created_at'       => ['type' => 'DATETIME', 'null' => true],
            'updated_at'       => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('employee_code');
        $this->forge->addUniqueKey('email');
        $this->forge->addForeignKey('department_id', 'departments', 'id', '', 'SET NULL');
        $this->forge->createTable('employees');
    }

    public function down()
    {
        $this->forge->dropTable('employees');
    }
}

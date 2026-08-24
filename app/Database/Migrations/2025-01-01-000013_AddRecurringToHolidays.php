<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddRecurringToHolidays extends Migration
{
    public function up()
    {
        $this->forge->addColumn('holidays', [
            'is_recurring' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
                'after'      => 'name',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('holidays', 'is_recurring');
    }
}

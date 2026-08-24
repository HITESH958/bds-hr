<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class InitialSeeder extends Seeder
{
    public function run()
    {
        $departments = ['HR', 'Finance', 'IT', 'Operations', 'Sales', 'Legal'];
        foreach ($departments as $name) {
            $this->db->table('departments')->insert([
                'name'       => $name,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }

        // Default admin login - CHANGE PASSWORD AFTER FIRST LOGIN
        $this->db->table('users')->insert([
            'username'   => 'admin',
            'email'      => 'admin@yourcompany.com',
            'password'   => password_hash('Admin@123', PASSWORD_DEFAULT),
            'role'       => 'admin',
            'status'     => 'active',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }
}

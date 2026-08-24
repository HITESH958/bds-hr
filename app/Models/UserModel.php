<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'employee_id', 'username', 'email', 'password', 'role',
        'status', 'reset_token', 'reset_expires', 'last_login',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'username' => 'required|min_length[3]|max_length[100]|is_unique[users.username,id,{id}]',
        'email'    => 'required|valid_email|is_unique[users.email,id,{id}]',
        'role'     => 'required|in_list[admin,hr,employee]',
    ];

    /**
     * Find an active user by username or email.
     */
    public function findByLogin(string $login): ?array
    {
        return $this->groupStart()
            ->where('username', $login)
            ->orWhere('email', $login)
            ->groupEnd()
            ->first();
    }

    public function updateLastLogin(int $id): void
    {
        $this->update($id, ['last_login' => date('Y-m-d H:i:s')]);
    }

    /**
     * All user logins joined with the employee they're linked to (if any),
     * for the HR "manage logins" screen.
     */
    public function listWithEmployees(): array
    {
        return $this->select('users.*, employees.employee_code, employees.first_name, employees.last_name')
            ->join('employees', 'employees.id = users.employee_id', 'left')
            ->orderBy('users.created_at', 'DESC')
            ->findAll();
    }
}

<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\Controller;

class Auth extends Controller
{
    protected UserModel $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function loginForm()
    {
        if (session()->get('isLoggedIn')) {
            return redirect()->to('/dashboard');
        }

        return view('auth/login');
    }

    public function attemptLogin()
    {
        $rules = [
            'login'    => 'required',
            'password' => 'required',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $login    = $this->request->getPost('login');
        $password = $this->request->getPost('password');

        $user = $this->userModel->findByLogin($login);

        if (! $user || ! password_verify($password, $user['password'])) {
            return redirect()->back()->withInput()->with('error', 'Invalid username/email or password.');
        }

        if ($user['status'] !== 'active') {
            return redirect()->back()->withInput()->with('error', 'Your account is inactive. Contact HR.');
        }

        // Regenerate session ID to prevent session fixation.
        session()->regenerate();

        session()->set([
            'user_id'     => $user['id'],
            'username'    => $user['username'],
            'role'        => $user['role'],
            'employee_id' => $user['employee_id'],
            'isLoggedIn'  => true,
        ]);

        $this->userModel->updateLastLogin($user['id']);

        return redirect()->to('/dashboard')->with('success', 'Welcome back, ' . $user['username'] . '!');
    }

    public function logout()
    {
        session()->destroy();

        return redirect()->to('/login')->with('success', 'You have been logged out.');
    }

    public function forgotForm()
    {
        return view('auth/forgot');
    }

    public function sendReset()
    {
        $email = $this->request->getPost('email');
        $user  = $this->userModel->where('email', $email)->first();

        $message = 'If that email is registered, a reset link has been sent.';

        if ($user) {
            $token   = bin2hex(random_bytes(32));
            $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

            $this->userModel->update($user['id'], [
                'reset_token'   => $token,
                'reset_expires' => $expires,
            ]);

            // TODO: send email with reset link containing $token via PHPMailer/SMTP.
            // Example link: base_url('reset-password/' . $token)
            log_message('info', 'Password reset token for ' . $email . ': ' . $token);
        }

        return redirect()->to('/login')->with('success', $message);
    }

    public function resetForm(string $token)
    {
        $user = $this->userModel->where('reset_token', $token)
            ->where('reset_expires >=', date('Y-m-d H:i:s'))
            ->first();

        if (! $user) {
            return redirect()->to('/login')->with('error', 'That reset link is invalid or has expired.');
        }

        return view('auth/reset', ['token' => $token]);
    }

    public function resetPassword(string $token)
    {
        $rules = [
            'password'         => 'required|min_length[8]',
            'password_confirm' => 'required|matches[password]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $user = $this->userModel->where('reset_token', $token)
            ->where('reset_expires >=', date('Y-m-d H:i:s'))
            ->first();

        if (! $user) {
            return redirect()->to('/login')->with('error', 'That reset link is invalid or has expired.');
        }

        $this->userModel->update($user['id'], [
            'password'      => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'reset_token'   => null,
            'reset_expires' => null,
        ]);

        return redirect()->to('/login')->with('success', 'Password updated. Please log in.');
    }

    /**
     * Self-service password change for any logged-in user (any role),
     * requires knowing the current password.
     */
    public function changePasswordForm()
    {
        return view('auth/change_password');
    }

    public function changePassword()
    {
        $rules = [
            'current_password' => 'required',
            'password'         => 'required|min_length[8]',
            'password_confirm' => 'required|matches[password]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $userId = session()->get('user_id');
        $user   = $this->userModel->find($userId);

        if (! $user || ! password_verify($this->request->getPost('current_password'), $user['password'])) {
            return redirect()->back()->with('error', 'Current password is incorrect.');
        }

        $this->userModel->update($userId, [
            'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
        ]);

        return redirect()->to('/dashboard')->with('success', 'Password changed successfully.');
    }
}

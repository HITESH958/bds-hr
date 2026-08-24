<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= $title ?? 'BDS HR' ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Source+Serif+4:wght@600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
</head>
<body>

<?php if (session()->get('isLoggedIn')): ?>
<header class="topbar">
    <div class="brand">
        <img src="<?= base_url('assets/images/bdslogo.png') ?>" alt="BDS Services" class="brand-logo">
        <span>HR</span>
    </div>
    <nav>
        <a href="<?= site_url('dashboard') ?>">
            <svg class="nav-icon" viewBox="0 0 20 20" fill="none"><rect x="3" y="3" width="6" height="6" rx="1" stroke="currentColor" stroke-width="1.5"/><rect x="11" y="3" width="6" height="6" rx="1" stroke="currentColor" stroke-width="1.5"/><rect x="3" y="11" width="6" height="6" rx="1" stroke="currentColor" stroke-width="1.5"/><rect x="11" y="11" width="6" height="6" rx="1" stroke="currentColor" stroke-width="1.5"/></svg>
            Dashboard
        </a>
        <?php if (in_array(session()->get('role'), ['admin', 'hr'], true)): ?>
            <a href="<?= site_url('employees') ?>">
                <svg class="nav-icon" viewBox="0 0 20 20" fill="none"><circle cx="7" cy="6" r="2.5" stroke="currentColor" stroke-width="1.5"/><path d="M2.5 16c0-2.5 2-4 4.5-4s4.5 1.5 4.5 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/><circle cx="14" cy="6.5" r="2" stroke="currentColor" stroke-width="1.5"/><path d="M12.5 12.2c1.8.3 3 1.6 3 3.8" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                Employees
            </a>
            <a href="<?= site_url('attendance/daily') ?>">
                <svg class="nav-icon" viewBox="0 0 20 20" fill="none"><circle cx="10" cy="10" r="7" stroke="currentColor" stroke-width="1.5"/><path d="M10 6v4l3 2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                Attendance
            </a>
            <a href="<?= site_url('leave/pending') ?>">
                <svg class="nav-icon" viewBox="0 0 20 20" fill="none"><rect x="3" y="4" width="14" height="13" rx="1.5" stroke="currentColor" stroke-width="1.5"/><path d="M3 8h14M7 2.5v3M13 2.5v3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                Leave
            </a>
            <a href="<?= site_url('payroll/periods') ?>">
                <svg class="nav-icon" viewBox="0 0 20 20" fill="none"><rect x="2.5" y="5" width="15" height="10" rx="1.5" stroke="currentColor" stroke-width="1.5"/><path d="M2.5 8.5h15" stroke="currentColor" stroke-width="1.5"/><circle cx="6" cy="11.5" r="1" fill="currentColor"/></svg>
                Payroll
            </a>
            <a href="<?= site_url('holidays') ?>">
                <svg class="nav-icon" viewBox="0 0 20 20" fill="none"><circle cx="10" cy="10" r="3.5" stroke="currentColor" stroke-width="1.5"/><path d="M10 2.5v2M10 15.5v2M17.5 10h-2M4.5 10h-2M15.3 4.7l-1.4 1.4M6.1 13.9l-1.4 1.4M15.3 15.3l-1.4-1.4M6.1 6.1L4.7 4.7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                Holidays
            </a>
            <a href="<?= site_url('users') ?>">
                <svg class="nav-icon" viewBox="0 0 20 20" fill="none"><circle cx="8" cy="7" r="2.5" stroke="currentColor" stroke-width="1.5"/><path d="M3 16c0-2.5 2.2-4 5-4s5 1.5 5 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/><path d="M14.5 8.5l1.2 1.2 2.3-2.3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                Users
            </a>
        <?php else: ?>
            <a href="<?= site_url('attendance') ?>">
                <svg class="nav-icon" viewBox="0 0 20 20" fill="none"><circle cx="10" cy="10" r="7" stroke="currentColor" stroke-width="1.5"/><path d="M10 6v4l3 2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                Attendance
            </a>
            <a href="<?= site_url('leave') ?>">
                <svg class="nav-icon" viewBox="0 0 20 20" fill="none"><rect x="3" y="4" width="14" height="13" rx="1.5" stroke="currentColor" stroke-width="1.5"/><path d="M3 8h14M7 2.5v3M13 2.5v3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                Leave
            </a>
            <a href="<?= site_url('payroll/my-payslips') ?>">
                <svg class="nav-icon" viewBox="0 0 20 20" fill="none"><rect x="2.5" y="5" width="15" height="10" rx="1.5" stroke="currentColor" stroke-width="1.5"/><path d="M2.5 8.5h15" stroke="currentColor" stroke-width="1.5"/><circle cx="6" cy="11.5" r="1" fill="currentColor"/></svg>
                Payslips
            </a>
            <a href="<?= site_url('profile') ?>">
                <svg class="nav-icon" viewBox="0 0 20 20" fill="none"><circle cx="10" cy="7" r="3" stroke="currentColor" stroke-width="1.5"/><path d="M4 17c0-3 2.7-5 6-5s6 2 6 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                My Profile
            </a>
        <?php endif; ?>
        <a href="<?= site_url('change-password') ?>">
            <svg class="nav-icon" viewBox="0 0 20 20" fill="none"><rect x="4.5" y="9" width="11" height="8" rx="1.5" stroke="currentColor" stroke-width="1.5"/><path d="M6.5 9V6a3.5 3.5 0 0 1 7 0v3" stroke="currentColor" stroke-width="1.5"/></svg>
            Change Password
        </a>
        <a href="<?= site_url('logout') ?>">
            <svg class="nav-icon" viewBox="0 0 20 20" fill="none"><path d="M8 3H4.5A1.5 1.5 0 0 0 3 4.5v11A1.5 1.5 0 0 0 4.5 17H8M13 6l4 4-4 4M17 10H7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
            Logout (<?= esc(session()->get('username')) ?>)
        </a>
    </nav>
</header>
<?php endif; ?>

<main class="container">

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-error"><?= esc(session()->getFlashdata('error')) ?></div>
<?php endif; ?>

<?php if (session()->getFlashdata('errors')): ?>
    <div class="alert alert-error">
        <ul>
        <?php foreach (session()->getFlashdata('errors') as $err): ?>
            <li><?= esc($err) ?></li>
        <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<?= $this->renderSection('content') ?>

</main>
</body>
</html>

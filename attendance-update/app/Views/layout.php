<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= $title ?? 'Spine HR' ?></title>
<link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
</head>
<body>

<?php if (session()->get('isLoggedIn')): ?>
<header class="topbar">
    <div class="brand">Spine HR</div>
    <nav>
        <a href="<?= site_url('dashboard') ?>">Dashboard</a>
        <?php if (in_array(session()->get('role'), ['admin', 'hr'], true)): ?>
            <a href="<?= site_url('employees') ?>">Employees</a>
            <a href="<?= site_url('attendance') ?>">Attendance</a>
        <?php endif; ?>
        <?php if (session()->get('employee_id')): ?>
            <a href="<?= site_url('my-attendance') ?>">My Attendance</a>
        <?php endif; ?>
        <a href="<?= site_url('logout') ?>">Logout (<?= esc(session()->get('username')) ?>)</a>
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

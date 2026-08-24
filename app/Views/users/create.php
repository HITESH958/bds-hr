<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<h1>Create Login</h1>

<?php if (empty($employees)): ?>
    <p>Every active employee already has a login. <a href="<?= site_url('employees/create') ?>">Add a new employee</a> first if you need one.</p>
<?php else: ?>
<form action="<?= site_url('users') ?>" method="post" class="employee-form">
    <?= csrf_field() ?>

    <label for="employee_id">Employee</label>
    <select id="employee_id" name="employee_id" required>
        <option value="">-- Select --</option>
        <?php foreach ($employees as $emp): ?>
            <option value="<?= esc($emp['id']) ?>"><?= esc($emp['employee_code'] . ' - ' . $emp['first_name'] . ' ' . $emp['last_name']) ?></option>
        <?php endforeach; ?>
    </select>

    <label for="username">Username</label>
    <input type="text" id="username" name="username" required value="<?= esc(old('username')) ?>">

    <label for="email">Login Email</label>
    <input type="email" id="email" name="email" required value="<?= esc(old('email')) ?>">

    <label for="password">Password</label>
    <input type="text" id="password" name="password" required minlength="8" value="<?= esc(old('password')) ?>">
    <p class="text-muted">Shown in plain text so you can share it with the employee. Minimum 8 characters.</p>

    <label for="role">Role</label>
    <select id="role" name="role" required>
        <option value="employee" selected>Employee</option>
        <option value="hr">HR</option>
        <option value="admin">Admin</option>
    </select>

    <button type="submit" class="btn-primary">Create Login</button>
    <a href="<?= site_url('users') ?>" class="btn-secondary">Cancel</a>
</form>
<?php endif; ?>

<?= $this->endSection() ?>

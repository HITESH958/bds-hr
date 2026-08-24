<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<h1>Dashboard</h1>

<div class="stat-cards">
    <div class="stat-card">
        <span class="stat-number"><?= esc($totalEmployees) ?></span>
        <span class="stat-label">Active Employees</span>
    </div>
    <div class="stat-card">
        <span class="stat-number"><?= esc($totalDepartments) ?></span>
        <span class="stat-label">Departments</span>
    </div>
</div>

<?php if (in_array(session()->get('role'), ['admin', 'hr'], true)): ?>
<h2>Recently Added Employees</h2>
<table class="data-table">
    <thead>
        <tr><th>Code</th><th>Name</th><th>Email</th><th>Status</th></tr>
    </thead>
    <tbody>
        <?php foreach ($recentEmployees as $emp): ?>
        <tr>
            <td><?= esc($emp['employee_code']) ?></td>
            <td><?= esc($emp['first_name'] . ' ' . $emp['last_name']) ?></td>
            <td><?= esc($emp['email']) ?></td>
            <td><span class="badge badge-<?= esc($emp['status']) ?>"><?= esc($emp['status']) ?></span></td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($recentEmployees)): ?>
        <tr><td colspan="4">No employees yet.</td></tr>
        <?php endif; ?>
    </tbody>
</table>
<a href="<?= site_url('employees') ?>" class="btn-primary">View All Employees</a>
<?php endif; ?>

<?= $this->endSection() ?>

<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<h1>Dashboard</h1>

<?php if (session()->get('employee_id')): ?>
<div class="attendance-widget">
    <?php if (! $todayRecord): ?>
        <p>You haven't checked in today.</p>
        <form action="<?= site_url('attendance/check-in') ?>" method="post">
            <?= csrf_field() ?>
            <button type="submit" class="btn-primary">Check In</button>
        </form>
    <?php elseif (! $todayRecord['check_out']): ?>
        <p>Checked in at <?= esc(date('h:i A', strtotime($todayRecord['check_in']))) ?>
            (<span class="badge badge-<?= $todayRecord['status'] === 'late' ? 'inactive' : 'active' ?>"><?= esc($todayRecord['status']) ?></span>)</p>
        <form action="<?= site_url('attendance/check-out') ?>" method="post">
            <?= csrf_field() ?>
            <button type="submit" class="btn-primary">Check Out</button>
        </form>
    <?php else: ?>
        <p>
            Checked in at <?= esc(date('h:i A', strtotime($todayRecord['check_in']))) ?>,
            checked out at <?= esc(date('h:i A', strtotime($todayRecord['check_out']))) ?>
            (<span class="badge badge-<?= $todayRecord['status'] === 'half_day' ? 'inactive' : 'active' ?>"><?= esc($todayRecord['status']) ?></span>)
        </p>
    <?php endif; ?>
    <a href="<?= site_url('my-attendance') ?>" class="link-muted">View my attendance history</a>
</div>
<?php endif; ?>

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

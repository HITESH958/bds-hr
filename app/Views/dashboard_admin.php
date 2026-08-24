<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<div class="page-greeting">
    <h1>Good <?= (int) date('G') < 12 ? 'morning' : ((int) date('G') < 17 ? 'afternoon' : 'evening') ?>, <?= esc(session()->get('username')) ?></h1>
    <p class="text-muted"><?= esc(date('l, j F Y')) ?></p>
</div>

<div class="stat-cards">
    <div class="stat-card">
        <div class="stat-card-icon">
            <svg viewBox="0 0 20 20" fill="none"><circle cx="7" cy="6" r="2.5" stroke="currentColor" stroke-width="1.5"/><path d="M2.5 16c0-2.5 2-4 4.5-4s4.5 1.5 4.5 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/><circle cx="14" cy="6.5" r="2" stroke="currentColor" stroke-width="1.5"/><path d="M12.5 12.2c1.8.3 3 1.6 3 3.8" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
        </div>
        <div class="stat-card-text">
            <span class="stat-number"><?= esc($totalEmployees) ?></span>
            <span class="stat-label">Active Employees</span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-card-icon">
            <svg viewBox="0 0 20 20" fill="none"><rect x="4" y="3" width="12" height="14" rx="1.5" stroke="currentColor" stroke-width="1.5"/><path d="M7 7h6M7 10h6M7 13h3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
        </div>
        <div class="stat-card-text">
            <span class="stat-number"><?= esc($totalDepartments) ?></span>
            <span class="stat-label">Departments</span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-card-icon">
            <svg viewBox="0 0 20 20" fill="none"><rect x="3" y="4" width="14" height="13" rx="1.5" stroke="currentColor" stroke-width="1.5"/><path d="M3 8h14" stroke="currentColor" stroke-width="1.5"/></svg>
        </div>
        <div class="stat-card-text">
            <span class="stat-number"><?= esc(count($onLeaveToday)) ?></span>
            <span class="stat-label">Out Today</span>
        </div>
    </div>
</div>

<h2>Quick Actions</h2>
<div class="quick-actions">
    <a href="<?= site_url('employees/create') ?>" class="quick-action-tile">
        <div class="quick-action-icon"><svg viewBox="0 0 20 20" fill="none"><path d="M10 4v12M4 10h12" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg></div>
        <span>Add Employee</span>
    </a>
    <a href="<?= site_url('attendance/daily') ?>" class="quick-action-tile">
        <div class="quick-action-icon"><svg viewBox="0 0 20 20" fill="none"><circle cx="10" cy="10" r="7" stroke="currentColor" stroke-width="1.5"/><path d="M10 6v4l3 2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
        <span>Today's Attendance</span>
    </a>
    <a href="<?= site_url('leave/pending') ?>" class="quick-action-tile">
        <div class="quick-action-icon"><svg viewBox="0 0 20 20" fill="none"><rect x="3" y="4" width="14" height="13" rx="1.5" stroke="currentColor" stroke-width="1.5"/><path d="M3 8h14" stroke="currentColor" stroke-width="1.5"/></svg></div>
        <span>Pending Leave</span>
    </a>
    <a href="<?= site_url('payroll/periods') ?>" class="quick-action-tile">
        <div class="quick-action-icon"><svg viewBox="0 0 20 20" fill="none"><rect x="2.5" y="5" width="15" height="10" rx="1.5" stroke="currentColor" stroke-width="1.5"/><path d="M2.5 8.5h15" stroke="currentColor" stroke-width="1.5"/></svg></div>
        <span>Run Payroll</span>
    </a>
    <a href="<?= site_url('holidays') ?>" class="quick-action-tile">
        <div class="quick-action-icon"><svg viewBox="0 0 20 20" fill="none"><circle cx="10" cy="10" r="3.5" stroke="currentColor" stroke-width="1.5"/><path d="M10 2.5v2M10 15.5v2M17.5 10h-2M4.5 10h-2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg></div>
        <span>Holidays</span>
    </a>
    <a href="<?= site_url('users/create') ?>" class="quick-action-tile">
        <div class="quick-action-icon"><svg viewBox="0 0 20 20" fill="none"><circle cx="8" cy="7" r="2.5" stroke="currentColor" stroke-width="1.5"/><path d="M3 16c0-2.5 2.2-4 5-4s5 1.5 5 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg></div>
        <span>Create Login</span>
    </a>
</div>

<?php $avatarColors = ['#142440', '#6a4c93', '#1e7e34', '#9c6b00', '#b02a2a', '#0f6674']; ?>

<h2>Who's Out Today</h2>
<?php if (empty($onLeaveToday)): ?>
    <div class="empty-state" style="background: var(--surface); border-radius: var(--radius-lg); box-shadow: var(--shadow-sm); margin-bottom: 8px;">
        <svg viewBox="0 0 20 20" fill="none"><path d="M4 10l4 4 8-8" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
        <p>Everyone's in today — no approved leave on record.</p>
    </div>
<?php else: ?>
<table class="data-table">
    <thead><tr><th>Employee</th><th>Leave Type</th><th>Returns</th></tr></thead>
    <tbody>
        <?php foreach ($onLeaveToday as $l): ?>
        <tr>
            <td>
                <div class="name-cell">
                    <span class="avatar-circle" style="background: <?= esc($avatarColors[$l['employee_id'] % count($avatarColors)]) ?>;">
                        <?= esc(strtoupper(substr($l['first_name'], 0, 1) . substr($l['last_name'], 0, 1))) ?>
                    </span>
                    <div>
                        <div><?= esc($l['first_name'] . ' ' . $l['last_name']) ?></div>
                        <div class="text-muted" style="font-size: 0.78rem;"><?= esc($l['employee_code']) ?></div>
                    </div>
                </div>
            </td>
            <td><?= esc($l['leave_type_name']) ?></td>
            <td><?= esc(date('d M', strtotime($l['end_date'] . ' +1 day'))) ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php endif; ?>

<h2>Recently Added Employees</h2>
<table class="data-table">
    <thead>
        <tr><th>Employee</th><th>Email</th><th>Status</th></tr>
    </thead>
    <tbody>
        <?php foreach ($recentEmployees as $emp): ?>
        <tr>
            <td>
                <div class="name-cell">
                    <span class="avatar-circle" style="background: <?= esc($avatarColors[$emp['id'] % count($avatarColors)]) ?>;">
                        <?= esc(strtoupper(substr($emp['first_name'], 0, 1) . substr($emp['last_name'], 0, 1))) ?>
                    </span>
                    <div>
                        <div><?= esc($emp['first_name'] . ' ' . $emp['last_name']) ?></div>
                        <div class="text-muted" style="font-size: 0.78rem;"><?= esc($emp['employee_code']) ?></div>
                    </div>
                </div>
            </td>
            <td><?= esc($emp['email']) ?></td>
            <td><span class="badge badge-<?= esc($emp['status']) ?>"><?= esc($emp['status']) ?></span></td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($recentEmployees)): ?>
        <tr><td colspan="3">No employees yet.</td></tr>
        <?php endif; ?>
    </tbody>
</table>
<p style="margin-top: 16px;"><a href="<?= site_url('employees') ?>" class="btn-primary">View All Employees</a></p>

<?= $this->endSection() ?>

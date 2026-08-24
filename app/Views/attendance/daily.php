<?php helper('hr'); ?>
<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<div class="page-header">
    <h1>Daily Attendance</h1>
    <a href="<?= site_url('attendance/export?date=' . esc($date) . '&department=' . urlencode((string) ($departmentId ?? ''))) ?>" class="btn-secondary">Export CSV</a>
</div>

<?php if ($holidayName): ?>
    <div class="alert alert-success" style="border-left-color: var(--gold); background: #fdf7e8; color: #7a5c00;">
        🎉 <?= esc($holidayName) ?> &mdash; this is a declared company holiday.
    </div>
<?php endif; ?>

<form method="get" action="<?= site_url('attendance/daily') ?>" class="filter-bar">
    <input type="date" name="date" value="<?= esc($date) ?>">
    <select name="department">
        <option value="">All Departments</option>
        <?php foreach ($departments as $dept): ?>
            <option value="<?= esc($dept['id']) ?>" <?= (string) ($departmentId ?? '') === (string) $dept['id'] ? 'selected' : '' ?>>
                <?= esc($dept['name']) ?>
            </option>
        <?php endforeach; ?>
    </select>
    <button type="submit" class="btn-secondary">View</button>
</form>

<table class="data-table">
    <thead>
        <tr>
            <th>Code</th><th>Name</th><th>Department</th>
            <th>Check In</th><th>Check Out</th>
            <th>Login</th><th>Productive</th><th>Break</th>
            <th>Status</th><th>Correct</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($grid as $row): ?>
        <tr>
            <td><?= esc($row['employee_code']) ?></td>
            <td><?= esc($row['first_name'] . ' ' . $row['last_name']) ?></td>
            <td><?= esc($row['department_name'] ?? '-') ?></td>
            <td><?= $row['check_in'] ? esc(date('h:i A', strtotime($row['check_in']))) : '-' ?></td>
            <td><?= $row['check_out'] ? esc(date('h:i A', strtotime($row['check_out']))) : '-' ?></td>
            <td><?= esc(format_hours($row['hours']['login_hours'])) ?></td>
            <td><?= esc(format_hours($row['hours']['productive_hours'])) ?></td>
            <td><?= esc(format_hours($row['hours']['break_hours'])) ?></td>
            <td>
                <span class="badge badge-<?= esc($row['status'] ?? 'absent') ?>">
                    <?= esc(format_status($row['status'] ?? 'absent')) ?>
                </span>
            </td>
            <td>
                <form action="<?= site_url('attendance/manual-update') ?>" method="post" class="inline-form">
                    <?= csrf_field() ?>
                    <input type="hidden" name="employee_id" value="<?= esc($row['employee_id']) ?>">
                    <input type="hidden" name="attendance_date" value="<?= esc($date) ?>">
                    <select name="status" onchange="this.form.submit()">
                        <?php foreach (['present', 'absent', 'half_day', 'late', 'on_leave'] as $s): ?>
                            <option value="<?= $s ?>" <?= ($row['status'] ?? '') === $s ? 'selected' : '' ?>>
                                <?= esc(format_status($s)) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($grid)): ?>
        <tr><td colspan="10">No employees found.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

<?= $this->endSection() ?>

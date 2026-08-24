<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<div class="page-title-bar">
    <div class="page-title-icon">
        <svg viewBox="0 0 20 20" fill="none"><rect x="2.5" y="5" width="15" height="10" rx="1.5" stroke="currentColor" stroke-width="1.5"/><path d="M2.5 8.5h15" stroke="currentColor" stroke-width="1.5"/></svg>
    </div>
    <div style="flex: 1;">
        <h1>Salary Structures</h1>
        <p class="text-muted">Basic, HRA, and allowances per employee.</p>
    </div>
    <a href="<?= site_url('payroll/periods') ?>" class="btn-secondary">Payroll Periods</a>
</div>

<?php $avatarColors = ['#142440', '#6a4c93', '#1e7e34', '#9c6b00', '#b02a2a', '#0f6674']; ?>

<table class="data-table">
    <thead>
        <tr><th>Employee</th><th>Basic</th><th>HRA</th><th>Allowances</th><th>Gross</th><th>Action</th></tr>
    </thead>
    <tbody>
        <?php foreach ($employees as $emp): ?>
        <tr>
            <td>
                <div class="name-cell">
                    <span class="avatar-circle" style="background: <?= esc($avatarColors[$emp['employee_id'] % count($avatarColors)]) ?>;">
                        <?= esc(strtoupper(substr($emp['first_name'], 0, 1) . substr($emp['last_name'], 0, 1))) ?>
                    </span>
                    <div>
                        <div><?= esc($emp['first_name'] . ' ' . $emp['last_name']) ?></div>
                        <div class="text-muted" style="font-size: 0.78rem;"><?= esc($emp['employee_code']) ?></div>
                    </div>
                </div>
            </td>
            <td><?= $emp['basic'] !== null ? esc($emp['basic']) : '-' ?></td>
            <td><?= $emp['hra'] !== null ? esc($emp['hra']) : '-' ?></td>
            <td><?= $emp['allowances'] !== null ? esc($emp['allowances']) : '-' ?></td>
            <td><?= $emp['basic'] !== null ? esc((float) $emp['basic'] + (float) $emp['hra'] + (float) $emp['allowances']) : '-' ?></td>
            <td><a href="<?= site_url('payroll/salary/' . $emp['employee_id'] . '/edit') ?>">
                <?= $emp['basic'] !== null ? 'Edit' : 'Set Salary' ?>
            </a></td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($employees)): ?>
        <tr><td colspan="6">No employees found.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

<?= $this->endSection() ?>

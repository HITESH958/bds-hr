<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<div class="page-title-bar">
    <div class="page-title-icon">
        <svg viewBox="0 0 20 20" fill="none"><rect x="2.5" y="5" width="15" height="10" rx="1.5" stroke="currentColor" stroke-width="1.5"/><path d="M2.5 8.5h15" stroke="currentColor" stroke-width="1.5"/></svg>
    </div>
    <div style="flex: 1;">
        <h1><?= esc(date('F Y', mktime(0, 0, 0, $period['month'], 1, $period['year']))) ?> Payslips</h1>
        <p class="text-muted">Generated payroll for this period.</p>
    </div>
    <a href="<?= site_url('payroll/periods/' . $period['id'] . '/export') ?>" class="btn-secondary">Export CSV</a>
    <a href="<?= site_url('payroll/periods') ?>" class="btn-secondary">Back to Periods</a>
</div>

<?php $avatarColors = ['#142440', '#6a4c93', '#1e7e34', '#9c6b00', '#b02a2a', '#0f6674']; ?>

<table class="data-table">
    <thead>
        <tr><th>Employee</th><th>Gross</th><th>LOP Days</th><th>LOP Deduction</th><th>Net Pay</th><th>Action</th></tr>
    </thead>
    <tbody>
        <?php foreach ($payslips as $p): ?>
        <tr>
            <td>
                <div class="name-cell">
                    <span class="avatar-circle" style="background: <?= esc($avatarColors[$p['employee_id'] % count($avatarColors)]) ?>;">
                        <?= esc(strtoupper(substr($p['first_name'], 0, 1) . substr($p['last_name'], 0, 1))) ?>
                    </span>
                    <div>
                        <div><?= esc($p['first_name'] . ' ' . $p['last_name']) ?></div>
                        <div class="text-muted" style="font-size: 0.78rem;"><?= esc($p['employee_code']) ?></div>
                    </div>
                </div>
            </td>
            <td><?= esc($p['gross_earnings']) ?></td>
            <td><?= esc($p['lop_days']) ?></td>
            <td><?= esc($p['lop_deduction']) ?></td>
            <td><strong><?= esc($p['net_pay']) ?></strong></td>
            <td>
                <a href="<?= site_url('payroll/payslip/' . $p['id']) ?>">View</a>
                &nbsp;|&nbsp;
                <a href="<?= site_url('payroll/payslip/' . $p['id'] . '/pdf') ?>">PDF</a>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($payslips)): ?>
        <tr><td colspan="6">
            <div class="empty-state">
                <svg viewBox="0 0 20 20" fill="none"><rect x="2.5" y="5" width="15" height="10" rx="1.5" stroke="currentColor" stroke-width="1.5"/></svg>
                <p>No payslips generated for this period yet.</p>
            </div>
        </td></tr>
        <?php endif; ?>
    </tbody>
</table>

<?= $this->endSection() ?>

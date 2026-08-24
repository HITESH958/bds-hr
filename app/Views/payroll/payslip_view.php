<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<div class="page-title-bar">
    <div class="page-title-icon">
        <svg viewBox="0 0 20 20" fill="none"><rect x="2.5" y="5" width="15" height="10" rx="1.5" stroke="currentColor" stroke-width="1.5"/><path d="M2.5 8.5h15" stroke="currentColor" stroke-width="1.5"/></svg>
    </div>
    <div style="flex: 1;">
        <h1>Payslip</h1>
        <p class="text-muted"><?= esc(date('F Y', mktime(0, 0, 0, $period['month'], 1, $period['year']))) ?></p>
    </div>
    <a href="<?= site_url('payroll/payslip/' . $payslip['id'] . '/pdf') ?>" class="btn-primary">Download PDF</a>
</div>

<div class="employee-form">
    <p><strong>Employee:</strong> <?= esc($employee['first_name'] . ' ' . $employee['last_name']) ?> (<?= esc($employee['employee_code']) ?>)</p>
    <p><strong>Department:</strong> <?= esc($employee['designation'] ?? '-') ?></p>

    <h2>Earnings</h2>
    <table class="data-table">
        <tr><td>Basic</td><td><?= esc($payslip['basic']) ?></td></tr>
        <tr><td>HRA</td><td><?= esc($payslip['hra']) ?></td></tr>
        <tr><td>Allowances</td><td><?= esc($payslip['allowances']) ?></td></tr>
        <tr><td><strong>Gross Earnings</strong></td><td><strong><?= esc($payslip['gross_earnings']) ?></strong></td></tr>
    </table>

    <h2>Attendance & Deductions</h2>
    <table class="data-table">
        <tr><td>Working Days (this month)</td><td><?= esc($payslip['working_days']) ?></td></tr>
        <tr><td>LOP Days (unpaid absence)</td><td><?= esc($payslip['lop_days']) ?></td></tr>
        <tr><td>Per-Day Rate</td><td><?= esc($payslip['per_day_rate']) ?></td></tr>
        <tr><td>LOP Deduction</td><td>-<?= esc($payslip['lop_deduction']) ?></td></tr>
    </table>

    <h2>Net Pay</h2>
    <p style="font-size: 1.5rem; font-weight: 700; color: var(--primary);">
        <?= esc($payslip['net_pay']) ?>
    </p>
</div>

<?= $this->endSection() ?>

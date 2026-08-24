<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<h1>My Payslips</h1>

<table class="data-table">
    <thead>
        <tr><th>Period</th><th>Gross</th><th>LOP Deduction</th><th>Net Pay</th><th>Action</th></tr>
    </thead>
    <tbody>
        <?php foreach ($payslips as $p): ?>
        <tr>
            <td><?= esc(date('F Y', mktime(0, 0, 0, $p['month'], 1, $p['year']))) ?></td>
            <td><?= esc($p['gross_earnings']) ?></td>
            <td><?= esc($p['lop_deduction']) ?></td>
            <td><strong><?= esc($p['net_pay']) ?></strong></td>
            <td>
                <a href="<?= site_url('payroll/payslip/' . $p['id']) ?>">View</a>
                &nbsp;|&nbsp;
                <a href="<?= site_url('payroll/payslip/' . $p['id'] . '/pdf') ?>">Download PDF</a>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($payslips)): ?>
        <tr><td colspan="5">No payslips yet.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

<?= $this->endSection() ?>

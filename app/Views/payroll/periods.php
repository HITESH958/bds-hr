<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<div class="page-title-bar">
    <div class="page-title-icon">
        <svg viewBox="0 0 20 20" fill="none"><rect x="2.5" y="5" width="15" height="10" rx="1.5" stroke="currentColor" stroke-width="1.5"/><path d="M2.5 8.5h15" stroke="currentColor" stroke-width="1.5"/></svg>
    </div>
    <div style="flex: 1;">
        <h1>Payroll Periods</h1>
        <p class="text-muted">Generate and review monthly payroll runs.</p>
    </div>
    <a href="<?= site_url('payroll/salary') ?>" class="btn-secondary">Salary Structures</a>
</div>

<form action="<?= site_url('payroll/generate') ?>" method="post" class="filter-bar">
    <?= csrf_field() ?>
    <select name="month" required>
        <?php for ($m = 1; $m <= 12; $m++): ?>
            <option value="<?= $m ?>" <?= $m === (int) date('n') ? 'selected' : '' ?>>
                <?= date('F', mktime(0, 0, 0, $m, 1)) ?>
            </option>
        <?php endfor; ?>
    </select>
    <select name="year" required>
        <?php for ($y = (int) date('Y'); $y >= (int) date('Y') - 2; $y--): ?>
            <option value="<?= $y ?>"><?= $y ?></option>
        <?php endfor; ?>
    </select>
    <button type="submit" class="btn-primary">Generate Payroll</button>
</form>

<table class="data-table">
    <thead>
        <tr><th>Period</th><th>Status</th><th>Generated</th><th>Action</th></tr>
    </thead>
    <tbody>
        <?php foreach ($periods as $p): ?>
        <tr>
            <td><?= esc(date('F Y', mktime(0, 0, 0, $p['month'], 1, $p['year']))) ?></td>
            <td><span class="badge badge-<?= $p['status'] === 'finalized' ? 'active' : 'inactive' ?>"><?= esc(ucfirst($p['status'])) ?></span></td>
            <td><?= $p['generated_at'] ? esc(date('d M Y, h:i A', strtotime($p['generated_at']))) : '-' ?></td>
            <td><a href="<?= site_url('payroll/periods/' . $p['id']) ?>">View Payslips</a></td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($periods)): ?>
        <tr><td colspan="4">
            <div class="empty-state">
                <svg viewBox="0 0 20 20" fill="none"><rect x="2.5" y="5" width="15" height="10" rx="1.5" stroke="currentColor" stroke-width="1.5"/></svg>
                <p>No payroll periods generated yet.</p>
            </div>
        </td></tr>
        <?php endif; ?>
    </tbody>
</table>

<?= $this->endSection() ?>

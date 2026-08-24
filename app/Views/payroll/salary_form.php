<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<div class="page-title-bar">
    <div class="page-title-icon">
        <svg viewBox="0 0 20 20" fill="none"><rect x="2.5" y="5" width="15" height="10" rx="1.5" stroke="currentColor" stroke-width="1.5"/><path d="M2.5 8.5h15" stroke="currentColor" stroke-width="1.5"/></svg>
    </div>
    <div>
        <h1>Salary Structure</h1>
        <p class="text-muted"><?= esc($employee['first_name'] . ' ' . $employee['last_name']) ?> (<?= esc($employee['employee_code']) ?>)</p>
    </div>
</div>

<form action="<?= site_url('payroll/salary/' . $employee['id']) ?>" method="post" class="employee-form">
    <?= csrf_field() ?>

    <label for="basic">Basic (monthly)</label>
    <input type="number" step="0.01" id="basic" name="basic" required
           value="<?= esc(old('basic', $salary['basic'] ?? '')) ?>">

    <label for="hra">HRA (monthly)</label>
    <input type="number" step="0.01" id="hra" name="hra"
           value="<?= esc(old('hra', $salary['hra'] ?? '0')) ?>">

    <label for="allowances">Other Allowances (monthly)</label>
    <input type="number" step="0.01" id="allowances" name="allowances"
           value="<?= esc(old('allowances', $salary['allowances'] ?? '0')) ?>">

    <button type="submit" class="btn-primary">Save</button>
    <a href="<?= site_url('payroll/salary') ?>" class="btn-secondary">Cancel</a>
</form>

<?= $this->endSection() ?>

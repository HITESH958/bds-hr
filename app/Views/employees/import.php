<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<div class="page-title-bar">
    <div class="page-title-icon">
        <svg viewBox="0 0 20 20" fill="none"><path d="M10 3v10M6 9l4 4 4-4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/><path d="M4 16h12" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
    </div>
    <div>
        <h1>Import Employees</h1>
        <p class="text-muted">Bulk-add employees from a CSV file.</p>
    </div>
</div>

<div class="employee-form" style="margin-bottom: 24px;">
    <h2 style="margin-top: 0;">CSV Format</h2>
    <p>Your file needs a header row with these column names (order doesn't matter, extra columns are ignored):</p>
    <table class="data-table" style="margin-bottom: 16px;">
        <thead><tr><th>Column</th><th>Required?</th><th>Notes</th></tr></thead>
        <tbody>
            <tr><td>employee_code</td><td>Yes</td><td>Must be unique</td></tr>
            <tr><td>first_name</td><td>Yes</td><td></td></tr>
            <tr><td>last_name</td><td>Yes</td><td></td></tr>
            <tr><td>email</td><td>Yes</td><td>Must be unique</td></tr>
            <tr><td>phone</td><td>No</td><td></td></tr>
            <tr><td>department</td><td>No</td><td>Matched by name; created automatically if it doesn't exist yet</td></tr>
            <tr><td>designation</td><td>No</td><td></td></tr>
            <tr><td>date_of_joining</td><td>No</td><td>Format: YYYY-MM-DD</td></tr>
            <tr><td>gender</td><td>No</td><td>male / female / other</td></tr>
            <tr><td>status</td><td>No</td><td>Defaults to "active" if left blank</td></tr>
        </tbody>
    </table>
    <p class="text-muted">Example row: <code>BDS-2001,Anita,Sharma,anita@bdsserv.co.in,9876543210,IT,Developer,2026-01-15,female,active</code></p>
</div>

<form action="<?= site_url('employees/import') ?>" method="post" enctype="multipart/form-data" class="employee-form">
    <?= csrf_field() ?>
    <label for="csv_file">CSV File</label>
    <input type="file" id="csv_file" name="csv_file" accept=".csv" required>
    <button type="submit" class="btn-primary">Import</button>
    <a href="<?= site_url('employees') ?>" class="btn-secondary">Cancel</a>
</form>

<?php $importErrors = session()->getFlashdata('import_errors'); ?>
<?php if (! empty($importErrors)): ?>
<div class="employee-form" style="margin-top: 20px; border-top-color: var(--error);">
    <h2 style="margin-top: 0; color: var(--error);">Rows Skipped</h2>
    <ul>
        <?php foreach ($importErrors as $err): ?>
            <li><?= esc($err) ?></li>
        <?php endforeach; ?>
    </ul>
</div>
<?php endif; ?>

<?= $this->endSection() ?>

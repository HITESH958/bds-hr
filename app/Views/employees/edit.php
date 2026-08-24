<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<h1>Edit Employee</h1>

<form action="<?= site_url('employees/' . $employee['id']) ?>" method="post" class="employee-form">
    <?= csrf_field() ?>
    <?= $this->include('employees/_form') ?>
    <button type="submit" class="btn-primary">Update Employee</button>
    <a href="<?= site_url('employees') ?>" class="btn-secondary">Cancel</a>
</form>

<?= $this->endSection() ?>

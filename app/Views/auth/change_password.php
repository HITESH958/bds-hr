<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<div class="page-title-bar">
    <div class="page-title-icon">
        <svg viewBox="0 0 20 20" fill="none"><rect x="4.5" y="9" width="11" height="8" rx="1.5" stroke="currentColor" stroke-width="1.5"/><path d="M6.5 9V6a3.5 3.5 0 0 1 7 0v3" stroke="currentColor" stroke-width="1.5"/></svg>
    </div>
    <div>
        <h1>Change Password</h1>
        <p class="text-muted">Update your login password.</p>
    </div>
</div>

<form action="<?= site_url('change-password') ?>" method="post" class="employee-form">
    <?= csrf_field() ?>

    <label for="current_password">Current Password</label>
    <input type="password" id="current_password" name="current_password" required>

    <label for="password">New Password</label>
    <input type="password" id="password" name="password" required minlength="8">

    <label for="password_confirm">Confirm New Password</label>
    <input type="password" id="password_confirm" name="password_confirm" required minlength="8">

    <button type="submit" class="btn-primary">Change Password</button>
</form>

<?= $this->endSection() ?>

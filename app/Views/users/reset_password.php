<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<h1>Reset Password &mdash; <?= esc($userAccount['username']) ?></h1>

<form action="<?= site_url('users/' . $userAccount['id'] . '/reset-password') ?>" method="post" class="employee-form">
    <?= csrf_field() ?>

    <label for="password">New Password</label>
    <input type="text" id="password" name="password" required minlength="8">
    <p class="text-muted">Shown in plain text so you can share it with the employee. Minimum 8 characters.</p>

    <label for="password_confirm">Confirm New Password</label>
    <input type="text" id="password_confirm" name="password_confirm" required minlength="8">

    <button type="submit" class="btn-primary">Reset Password</button>
    <a href="<?= site_url('users') ?>" class="btn-secondary">Cancel</a>
</form>

<?= $this->endSection() ?>

<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<div class="auth-split">
    <div class="auth-brand-panel">
        <div class="auth-brand-logo">
            <img src="<?= base_url('assets/images/bdslogo.png') ?>" alt="BDS Services">
        </div>
        <h2>Set a new password.</h2>
        <p>Choose something you'll remember — at least 8 characters.</p>
    </div>

    <div class="auth-form-side">
        <div class="auth-box">
            <h1>Reset Password</h1>
            <p class="auth-split-tagline">Enter and confirm your new password.</p>

            <form action="<?= site_url('reset-password/' . $token) ?>" method="post">
                <?= csrf_field() ?>

                <label for="password">New Password</label>
                <input type="password" id="password" name="password" minlength="8" required autofocus>

                <label for="password_confirm">Confirm New Password</label>
                <input type="password" id="password_confirm" name="password_confirm" minlength="8" required>

                <button type="submit" class="btn-primary" style="width: 100%; box-sizing: border-box; justify-content: center; display: flex; align-items: center;">Update Password</button>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

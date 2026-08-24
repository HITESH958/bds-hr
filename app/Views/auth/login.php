<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<div class="auth-split">
    <div class="auth-brand-panel">
        <div class="auth-brand-logo">
            <img src="<?= base_url('assets/images/bdslogo.png') ?>" alt="BDS Services">
        </div>
        <h2>Attendance, leave, and payroll — all in one place.</h2>
        <p>BDS HR brings check-ins, approvals, and payslips together for the whole team, built around how BDS Services actually works.</p>
    </div>

    <div class="auth-form-side">
        <div class="auth-box">
            <h1>Welcome back</h1>
            <p class="auth-split-tagline">Sign in to your BDS HR account.</p>

            <form action="<?= site_url('login') ?>" method="post">
                <?= csrf_field() ?>

                <label for="login">Username or Email</label>
                <input type="text" id="login" name="login" value="<?= esc(old('login')) ?>" required autofocus>

                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>

                <button type="submit" class="btn-primary" style="width: 100%; box-sizing: border-box; justify-content: center; display: flex; align-items: center;">Log In</button>
            </form>
            <a href="<?= site_url('forgot-password') ?>" class="link-muted">Forgot password?</a>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

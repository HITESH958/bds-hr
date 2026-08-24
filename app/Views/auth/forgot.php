<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<div class="auth-split">
    <div class="auth-brand-panel">
        <div class="auth-brand-logo">
            <img src="<?= base_url('assets/images/bdslogo.png') ?>" alt="BDS Services">
        </div>
        <h2>Forgot your password?</h2>
        <p>No problem — enter your email and we'll help you get back in.</p>
    </div>

    <div class="auth-form-side">
        <div class="auth-box">
            <h1>Forgot Password</h1>
            <p class="auth-split-tagline">Enter your email and we'll send you a reset link.</p>

            <form action="<?= site_url('forgot-password') ?>" method="post">
                <?= csrf_field() ?>
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required autofocus>
                <button type="submit" class="btn-primary" style="width: 100%; box-sizing: border-box; justify-content: center; display: flex; align-items: center;">Send Reset Link</button>
            </form>
            <a href="<?= site_url('login') ?>" class="link-muted">Back to login</a>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

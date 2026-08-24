<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<div class="page-title-bar">
    <div class="page-title-icon">
        <svg viewBox="0 0 20 20" fill="none"><circle cx="8" cy="7" r="2.5" stroke="currentColor" stroke-width="1.5"/><path d="M3 16c0-2.5 2.2-4 5-4s5 1.5 5 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/><path d="M14.5 8.5l1.2 1.2 2.3-2.3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
    </div>
    <div style="flex: 1;">
        <h1>User Logins</h1>
        <p class="text-muted">Manage who can sign in to BDS HR.</p>
    </div>
    <a href="<?= site_url('users/create') ?>" class="btn-primary">
        <svg class="btn-icon" viewBox="0 0 20 20" fill="none"><path d="M10 4v12M4 10h12" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
        Create Login
    </a>
</div>

<?php $avatarColors = ['#142440', '#6a4c93', '#1e7e34', '#9c6b00', '#b02a2a', '#0f6674']; ?>

<table class="data-table">
    <thead>
        <tr><th>Login</th><th>Employee</th><th>Role</th><th>Status</th><th>Last Login</th><th>Action</th></tr>
    </thead>
    <tbody>
        <?php foreach ($users as $u): ?>
        <tr>
            <td>
                <div class="name-cell">
                    <span class="avatar-circle" style="background: <?= esc($avatarColors[$u['id'] % count($avatarColors)]) ?>;">
                        <?= esc(strtoupper(substr($u['username'], 0, 2))) ?>
                    </span>
                    <div><?= esc($u['username']) ?></div>
                </div>
            </td>
            <td><?= $u['employee_code'] ? esc($u['employee_code'] . ' - ' . $u['first_name'] . ' ' . $u['last_name']) : '<span class="text-muted">Not linked</span>' ?></td>
            <td><?= esc(ucfirst($u['role'])) ?></td>
            <td><span class="badge badge-<?= $u['status'] === 'active' ? 'active' : 'inactive' ?>"><?= esc(ucfirst($u['status'])) ?></span></td>
            <td><?= $u['last_login'] ? esc(date('d M Y, h:i A', strtotime($u['last_login']))) : 'Never' ?></td>
            <td>
                <a href="<?= site_url('users/' . $u['id'] . '/reset-password') ?>">Reset Password</a>
                &nbsp;|&nbsp;
                <form action="<?= site_url('users/' . $u['id'] . '/toggle-status') ?>" method="post" class="inline-form"
                      onsubmit="return confirm('<?= $u['status'] === 'active' ? 'Deactivate' : 'Activate' ?> this login?');">
                    <?= csrf_field() ?>
                    <button type="submit" class="link-danger"><?= $u['status'] === 'active' ? 'Deactivate' : 'Activate' ?></button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($users)): ?>
        <tr><td colspan="6">No user logins yet.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

<?= $this->endSection() ?>

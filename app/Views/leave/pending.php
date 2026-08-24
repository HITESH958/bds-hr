<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<div class="page-title-bar">
    <div class="page-title-icon">
        <svg viewBox="0 0 20 20" fill="none"><rect x="3" y="4" width="14" height="13" rx="1.5" stroke="currentColor" stroke-width="1.5"/><path d="M3 8h14M7 2.5v3M13 2.5v3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
    </div>
    <div style="flex: 1;">
        <h1>Pending Leave Approvals</h1>
        <p class="text-muted">Requests waiting for your review.</p>
    </div>
    <a href="<?= site_url('leave/all') ?>" class="btn-secondary">View All Requests</a>
</div>

<?php $avatarColors = ['#142440', '#6a4c93', '#1e7e34', '#9c6b00', '#b02a2a', '#0f6674']; ?>

<table class="data-table">
    <thead>
        <tr><th>Employee</th><th>Type</th><th>From</th><th>To</th><th>Days</th><th>Reason</th><th>Action</th></tr>
    </thead>
    <tbody>
        <?php foreach ($requests as $req): ?>
        <tr>
            <td>
                <div class="name-cell">
                    <span class="avatar-circle" style="background: <?= esc($avatarColors[$req['employee_id'] % count($avatarColors)]) ?>;">
                        <?= esc(strtoupper(substr($req['first_name'], 0, 1) . substr($req['last_name'], 0, 1))) ?>
                    </span>
                    <div>
                        <div><?= esc($req['first_name'] . ' ' . $req['last_name']) ?></div>
                        <div class="text-muted" style="font-size: 0.78rem;"><?= esc($req['employee_code']) ?></div>
                    </div>
                </div>
            </td>
            <td><?= esc($req['leave_type_name']) ?></td>
            <td><?= esc(date('d M', strtotime($req['start_date']))) ?></td>
            <td><?= esc(date('d M', strtotime($req['end_date']))) ?></td>
            <td><?= esc($req['days']) ?></td>
            <td><?= esc($req['reason'] ?? '-') ?></td>
            <td>
                <form action="<?= site_url('leave/' . $req['id'] . '/approve') ?>" method="post" class="inline-form">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn-primary">Approve</button>
                </form>
                <form action="<?= site_url('leave/' . $req['id'] . '/reject') ?>" method="post" class="inline-form"
                      onsubmit="return confirm('Reject this request?');">
                    <?= csrf_field() ?>
                    <button type="submit" class="link-danger">Reject</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($requests)): ?>
        <tr><td colspan="7">
            <div class="empty-state">
                <svg viewBox="0 0 20 20" fill="none"><path d="M4 10l4 4 8-8" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                <p>No pending requests — all caught up.</p>
            </div>
        </td></tr>
        <?php endif; ?>
    </tbody>
</table>

<?= $this->endSection() ?>

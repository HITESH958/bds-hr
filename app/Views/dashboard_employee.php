<?php helper('hr'); ?>
<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<div class="page-greeting">
    <h1>Good <?= (int) date('G') < 12 ? 'morning' : ((int) date('G') < 17 ? 'afternoon' : 'evening') ?>, <?= esc(session()->get('username')) ?></h1>
    <p class="text-muted"><?= esc(date('l, j F Y')) ?></p>
</div>

<?php if (! $linked): ?>
    <p>Your account isn't linked to an employee record yet. Contact HR.</p>
<?php else: ?>

<div class="attendance-box">
    <?php if (! $today || ! $today['check_in']): ?>
        <?php if ($today && $today['status'] === 'on_leave'): ?>
            <p>You're marked as on approved leave today.</p>
            <span class="badge badge-on_leave">On Leave</span>
        <?php else: ?>
            <p>You haven't checked in today.</p>
            <form action="<?= site_url('attendance/check-in') ?>" method="post">
                <?= csrf_field() ?>
                <button type="submit" class="btn-primary">Check In</button>
            </form>
        <?php endif; ?>
    <?php else: ?>
        <p>
            Checked in at <strong><?= esc(date('h:i A', strtotime($today['check_in']))) ?></strong>
            <?php if ($today['check_out']): ?>
                , checked out at <strong><?= esc(date('h:i A', strtotime($today['check_out']))) ?></strong>.
            <?php else: ?>
                .
            <?php endif; ?>
        </p>

        <div class="hours-row">
            <div class="hours-box">
                <span class="hours-number"><?= esc(format_hours($todayHours['login_hours'])) ?></span>
                <span class="hours-label">Login Time</span>
            </div>
            <div class="hours-box hours-box-accent">
                <span class="hours-number"><?= esc(format_hours($todayHours['productive_hours'])) ?></span>
                <span class="hours-label">Productive</span>
            </div>
            <div class="hours-box">
                <span class="hours-number"><?= esc(format_hours($todayHours['break_hours'])) ?></span>
                <span class="hours-label">On Break</span>
            </div>
        </div>

        <?php if ($openBreak): ?>
            <span class="badge badge-inactive">On Break since <?= esc(date('h:i A', strtotime($openBreak['break_start']))) ?></span>
        <?php elseif (! $today['check_out']): ?>
            <span class="badge badge-<?= esc($today['status']) ?>"><?= esc(format_status($today['status'])) ?></span>
        <?php else: ?>
            <span class="badge badge-<?= esc($today['status']) ?>"><?= esc(format_status($today['status'])) ?> &mdash; day complete</span>
        <?php endif; ?>
    <?php endif; ?>

    <p style="margin-top: 14px;">
        <a href="<?= site_url('attendance') ?>">View full attendance, calendar & history &rarr;</a>
    </p>
</div>

<h2>This Month at a Glance</h2>
<div class="stat-cards">
    <div class="stat-card"><span class="stat-number"><?= esc($summary['present']) ?></span><span class="stat-label">Present</span></div>
    <div class="stat-card"><span class="stat-number"><?= esc($summary['late']) ?></span><span class="stat-label">Late</span></div>
    <div class="stat-card"><span class="stat-number"><?= esc($summary['half_day']) ?></span><span class="stat-label">Half Day</span></div>
    <div class="stat-card"><span class="stat-number"><?= esc($summary['absent']) ?></span><span class="stat-label">Absent</span></div>
    <div class="stat-card"><span class="stat-number"><?= esc($summary['on_leave']) ?></span><span class="stat-label">On Leave</span></div>
</div>

<p>
    <a href="<?= site_url('leave') ?>" class="btn-secondary">My Leave</a>
    <a href="<?= site_url('payroll/my-payslips') ?>" class="btn-secondary">My Payslips</a>
    <a href="<?= site_url('profile') ?>" class="btn-secondary">My Profile</a>
</p>

<?php endif; ?>

<?= $this->endSection() ?>

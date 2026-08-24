<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<h1>Monthly Attendance Report</h1>
<p><?= esc($employee['first_name'] . ' ' . $employee['last_name']) ?> &mdash; <?= esc(date('F Y', mktime(0, 0, 0, $month, 1, $year))) ?></p>

<div class="stat-cards">
    <div class="stat-card"><span class="stat-number"><?= esc($summary['present']) ?></span><span class="stat-label">Present</span></div>
    <div class="stat-card"><span class="stat-number"><?= esc($summary['late']) ?></span><span class="stat-label">Late</span></div>
    <div class="stat-card"><span class="stat-number"><?= esc($summary['half_day']) ?></span><span class="stat-label">Half Day</span></div>
    <div class="stat-card"><span class="stat-number"><?= esc($summary['absent']) ?></span><span class="stat-label">Absent</span></div>
    <div class="stat-card"><span class="stat-number"><?= esc($summary['on_leave']) ?></span><span class="stat-label">On Leave</span></div>
</div>

<a href="<?= site_url('attendance/daily') ?>" class="btn-secondary">Back to Daily View</a>

<?= $this->endSection() ?>
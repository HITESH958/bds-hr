<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<div class="page-header">
    <h1>My Attendance</h1>
</div>

<form method="get" action="<?= site_url('my-attendance') ?>" class="filter-bar">
    <input type="month" name="month" value="<?= esc($month) ?>">
    <button type="submit" class="btn-secondary">View</button>
</form>

<table class="data-table">
    <thead>
        <tr><th>Date</th><th>Check In</th><th>Check Out</th><th>Worked</th><th>Status</th></tr>
    </thead>
    <tbody>
        <?php foreach ($records as $r): ?>
        <tr>
            <td><?= esc(date('d M Y', strtotime($r['attendance_date']))) ?></td>
            <td><?= $r['check_in'] ? esc(date('h:i A', strtotime($r['check_in']))) : '-' ?></td>
            <td><?= $r['check_out'] ? esc(date('h:i A', strtotime($r['check_out']))) : '-' ?></td>
            <td><?= $r['worked_minutes'] ? esc(floor($r['worked_minutes'] / 60) . 'h ' . ($r['worked_minutes'] % 60) . 'm') : '-' ?></td>
            <td><span class="badge badge-<?= in_array($r['status'], ['present'], true) ? 'active' : 'inactive' ?>"><?= esc($r['status']) ?></span></td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($records)): ?>
        <tr><td colspan="5">No attendance records for this month.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

<?= $this->endSection() ?>

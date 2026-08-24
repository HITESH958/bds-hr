<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<div class="page-header">
    <h1>Attendance Report</h1>
</div>

<form method="get" action="<?= site_url('attendance') ?>" class="filter-bar">
    <input type="date" name="date_from" value="<?= esc($dateFrom ?? '') ?>" placeholder="From">
    <input type="date" name="date_to" value="<?= esc($dateTo ?? '') ?>" placeholder="To">
    <select name="employee_id">
        <option value="">All Employees</option>
        <?php foreach ($employees as $emp): ?>
            <option value="<?= esc($emp['id']) ?>" <?= (string) ($employeeId ?? '') === (string) $emp['id'] ? 'selected' : '' ?>>
                <?= esc($emp['first_name'] . ' ' . $emp['last_name']) ?>
            </option>
        <?php endforeach; ?>
    </select>
    <button type="submit" class="btn-secondary">Filter</button>
</form>

<table class="data-table">
    <thead>
        <tr><th>Date</th><th>Employee</th><th>Code</th><th>Check In</th><th>Check Out</th><th>Worked</th><th>Status</th></tr>
    </thead>
    <tbody>
        <?php foreach ($records as $r): ?>
        <tr>
            <td><?= esc(date('d M Y', strtotime($r['attendance_date']))) ?></td>
            <td><?= esc($r['first_name'] . ' ' . $r['last_name']) ?></td>
            <td><?= esc($r['employee_code']) ?></td>
            <td><?= $r['check_in'] ? esc(date('h:i A', strtotime($r['check_in']))) : '-' ?></td>
            <td><?= $r['check_out'] ? esc(date('h:i A', strtotime($r['check_out']))) : '-' ?></td>
            <td><?= $r['worked_minutes'] ? esc(floor($r['worked_minutes'] / 60) . 'h ' . ($r['worked_minutes'] % 60) . 'm') : '-' ?></td>
            <td><span class="badge badge-<?= $r['status'] === 'present' ? 'active' : 'inactive' ?>"><?= esc($r['status']) ?></span></td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($records)): ?>
        <tr><td colspan="7">No attendance records found.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

<div class="pagination">
    <?= $pager->links() ?>
</div>

<?= $this->endSection() ?>

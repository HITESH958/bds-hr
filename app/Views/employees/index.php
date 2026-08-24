<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<div class="page-title-bar">
    <div class="page-title-icon">
        <svg viewBox="0 0 20 20" fill="none"><circle cx="7" cy="6" r="2.5" stroke="currentColor" stroke-width="1.5"/><path d="M2.5 16c0-2.5 2-4 4.5-4s4.5 1.5 4.5 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/><circle cx="14" cy="6.5" r="2" stroke="currentColor" stroke-width="1.5"/><path d="M12.5 12.2c1.8.3 3 1.6 3 3.8" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
    </div>
    <div style="flex: 1;">
        <h1>Employees</h1>
        <p class="text-muted">Manage your team's records, departments, and status.</p>
    </div>
    <a href="<?= site_url('employees/import') ?>" class="btn-secondary">Import CSV</a>
    <a href="<?= site_url('employees/export?q=' . urlencode($search ?? '') . '&department=' . urlencode((string) ($departmentId ?? ''))) ?>" class="btn-secondary">Export CSV</a>
    <a href="<?= site_url('employees/create') ?>" class="btn-primary">
        <svg class="btn-icon" viewBox="0 0 20 20" fill="none"><path d="M10 4v12M4 10h12" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
        Add Employee
    </a>
</div>

<form method="get" action="<?= site_url('employees') ?>" class="filter-bar">
    <div class="search-wrap">
        <svg class="search-icon" viewBox="0 0 20 20" fill="none"><circle cx="9" cy="9" r="6" stroke="currentColor" stroke-width="1.6"/><path d="M14 14l4 4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
        <input type="text" name="q" placeholder="Search name, code, email..." value="<?= esc($search ?? '') ?>">
    </div>
    <select name="department">
        <option value="">All Departments</option>
        <?php foreach ($departments as $dept): ?>
            <option value="<?= esc($dept['id']) ?>" <?= (string) ($departmentId ?? '') === (string) $dept['id'] ? 'selected' : '' ?>>
                <?= esc($dept['name']) ?>
            </option>
        <?php endforeach; ?>
    </select>
    <button type="submit" class="btn-secondary">Filter</button>
</form>

<?php $avatarColors = ['#142440', '#6a4c93', '#1e7e34', '#9c6b00', '#b02a2a', '#0f6674']; ?>

<table class="data-table">
    <thead>
        <tr>
            <th>Employee</th><th>Department</th><th>Designation</th>
            <th>Email</th><th>Phone</th><th>Status</th><th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($employees as $emp): ?>
        <tr>
            <td>
                <div class="name-cell">
                    <span class="avatar-circle" style="background: <?= esc($avatarColors[$emp['id'] % count($avatarColors)]) ?>;">
                        <?= esc(strtoupper(substr($emp['first_name'], 0, 1) . substr($emp['last_name'], 0, 1))) ?>
                    </span>
                    <div>
                        <div><?= esc($emp['first_name'] . ' ' . $emp['last_name']) ?></div>
                        <div class="text-muted" style="font-size: 0.78rem;"><?= esc($emp['employee_code']) ?></div>
                    </div>
                </div>
            </td>
            <td><?= esc($emp['department_name'] ?? '-') ?></td>
            <td><?= esc($emp['designation'] ?? '-') ?></td>
            <td><?= esc($emp['email']) ?></td>
            <td><?= esc($emp['phone'] ?? '-') ?></td>
            <td><span class="badge badge-<?= esc($emp['status']) ?>"><?= esc($emp['status']) ?></span></td>
            <td>
                <a href="<?= site_url('employees/' . $emp['id'] . '/edit') ?>">Edit</a>
                <?php if ($emp['status'] !== 'resigned'): ?>
                <form action="<?= site_url('employees/' . $emp['id'] . '/delete') ?>" method="post" class="inline-form"
                      onsubmit="return confirm('Mark this employee as resigned? Their attendance, leave, and payroll history will be kept.');">
                    <?= csrf_field() ?>
                    <button type="submit" class="link-danger">Mark Resigned</button>
                </form>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($employees)): ?>
        <tr><td colspan="7">
            <div class="empty-state">
                <svg viewBox="0 0 20 20" fill="none"><circle cx="9" cy="9" r="6" stroke="currentColor" stroke-width="1.6"/><path d="M14 14l4 4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
                <p>No employees found. Try adjusting your search or filter.</p>
            </div>
        </td></tr>
        <?php endif; ?>
    </tbody>
</table>

<div class="pagination">
    <?= $pager->links() ?>
</div>

<?= $this->endSection() ?>

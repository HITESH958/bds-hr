<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<div class="page-title-bar">
    <div class="page-title-icon">
        <svg viewBox="0 0 20 20" fill="none"><circle cx="10" cy="10" r="3.5" stroke="currentColor" stroke-width="1.5"/><path d="M10 2.5v2M10 15.5v2M17.5 10h-2M4.5 10h-2M15.3 4.7l-1.4 1.4M6.1 13.9l-1.4 1.4M15.3 15.3l-1.4-1.4M6.1 6.1L4.7 4.7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
    </div>
    <div>
        <h1>Company Holidays</h1>
        <p class="text-muted">Declared holidays affect attendance and payroll calculations.</p>
    </div>
</div>

<form action="<?= site_url('holidays') ?>" method="post" class="employee-form" style="margin-bottom: 24px;">
    <?= csrf_field() ?>
    <div class="form-row">
        <div>
            <label for="holiday_date">Date</label>
            <input type="date" id="holiday_date" name="holiday_date" required>
        </div>
        <div>
            <label for="name">Occasion</label>
            <input type="text" id="name" name="name" placeholder="e.g. Independence Day" required>
        </div>
    </div>
    <label style="display: flex; align-items: center; gap: 8px; margin-top: 16px; font-weight: 500;">
        <input type="checkbox" name="is_recurring" value="1" style="width: auto;">
        Repeats every year on this month/day (e.g. Independence Day, Christmas)
    </label>
    <button type="submit" class="btn-primary">Add Holiday</button>
</form>

<div class="page-header">
    <h2 style="margin-bottom: 0;">Holidays in <?= esc($year) ?></h2>
    <div>
        <a href="<?= site_url('holidays?year=' . ($year - 1)) ?>" class="btn-secondary">&laquo; <?= $year - 1 ?></a>
        <a href="<?= site_url('holidays?year=' . ($year + 1)) ?>" class="btn-secondary"><?= $year + 1 ?> &raquo;</a>
    </div>
</div>

<table class="data-table">
    <thead><tr><th>Date</th><th>Day</th><th>Occasion</th><th>Repeats</th><th>Action</th></tr></thead>
    <tbody>
        <?php foreach ($holidays as $h): ?>
        <tr>
            <td><?= esc(date('d M Y', strtotime($h['holiday_date']))) ?></td>
            <td><?= esc(date('l', strtotime($h['holiday_date']))) ?></td>
            <td>🎉 <?= esc($h['name']) ?></td>
            <td><?= ! empty($h['is_recurring']) ? '<span class="badge badge-active">Every year</span>' : '<span class="text-muted">One-time</span>' ?></td>
            <td>
                <form action="<?= site_url('holidays/' . $h['id'] . '/delete') ?>" method="post" class="inline-form"
                      onsubmit="return confirm('Remove this holiday?');">
                    <?= csrf_field() ?>
                    <button type="submit" class="link-danger">Remove</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($holidays)): ?>
        <tr><td colspan="5">
            <div class="empty-state">
                <svg viewBox="0 0 20 20" fill="none"><circle cx="10" cy="10" r="7" stroke="currentColor" stroke-width="1.5"/></svg>
                <p>No holidays declared for <?= esc($year) ?> yet.</p>
            </div>
        </td></tr>
        <?php endif; ?>
    </tbody>
</table>

<?= $this->endSection() ?>

<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<div class="page-title-bar">
    <div class="page-title-icon">
        <svg viewBox="0 0 20 20" fill="none"><rect x="3" y="4" width="14" height="13" rx="1.5" stroke="currentColor" stroke-width="1.5"/><path d="M3 8h14M7 2.5v3M13 2.5v3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
    </div>
    <div>
        <h1>My Leave</h1>
        <p class="text-muted">Apply for time off and track your requests.</p>
    </div>
</div>

<h2>Balances (<?= date('Y') ?>)</h2>
<table class="data-table">
    <thead>
        <tr><th>Leave Type</th><th>Allocated</th><th>Used</th><th>Remaining</th></tr>
    </thead>
    <tbody>
        <?php foreach ($balances as $bal): ?>
        <tr>
            <td><?= esc($bal['leave_type_name']) ?></td>
            <td><?= esc($bal['allocated_days']) ?></td>
            <td><?= esc($bal['used_days']) ?></td>
            <td><?= esc((float) $bal['allocated_days'] - (float) $bal['used_days']) ?></td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($balances)): ?>
        <tr><td colspan="4">No leave balance allocated yet. Contact HR.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

<h2>Apply for Leave</h2>
<form action="<?= site_url('leave/apply') ?>" method="post" class="employee-form">
    <?= csrf_field() ?>

    <label for="leave_type_id">Leave Type</label>
    <select id="leave_type_id" name="leave_type_id" required>
        <option value="">-- Select --</option>
        <?php foreach ($leaveTypes as $type): ?>
            <option value="<?= esc($type['id']) ?>"><?= esc($type['name']) ?></option>
        <?php endforeach; ?>
    </select>

    <div class="form-row">
        <div>
            <label for="start_date">Start Date</label>
            <input type="date" id="start_date" name="start_date" required>
        </div>
        <div>
            <label for="end_date">End Date</label>
            <input type="date" id="end_date" name="end_date" required>
        </div>
    </div>

    <label for="reason">Reason</label>
    <textarea id="reason" name="reason" rows="3"></textarea>

    <button type="submit" class="btn-primary">Submit Request</button>
</form>

<h2>My Requests</h2>
<table class="data-table">
    <thead>
        <tr><th>Type</th><th>From</th><th>To</th><th>Days</th><th>Status</th><th>Action</th></tr>
    </thead>
    <tbody>
        <?php foreach ($requests as $req): ?>
        <tr>
            <td><?= esc($req['leave_type_name']) ?></td>
            <td><?= esc($req['start_date']) ?></td>
            <td><?= esc($req['end_date']) ?></td>
            <td><?= esc($req['days']) ?></td>
            <td><span class="badge badge-<?= $req['status'] === 'approved' ? 'active' : ($req['status'] === 'rejected' ? 'resigned' : 'inactive') ?>"><?= esc(ucfirst($req['status'])) ?></span></td>
            <td>
                <?php if ($req['status'] === 'pending'): ?>
                <form action="<?= site_url('leave/' . $req['id'] . '/cancel') ?>" method="post" class="inline-form"
                      onsubmit="return confirm('Cancel this request?');">
                    <?= csrf_field() ?>
                    <button type="submit" class="link-danger">Cancel</button>
                </form>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($requests)): ?>
        <tr><td colspan="6">
            <div class="empty-state">
                <svg viewBox="0 0 20 20" fill="none"><rect x="3" y="4" width="14" height="13" rx="1.5" stroke="currentColor" stroke-width="1.5"/></svg>
                <p>No leave requests yet.</p>
            </div>
        </td></tr>
        <?php endif; ?>
    </tbody>
</table>

<?= $this->endSection() ?>

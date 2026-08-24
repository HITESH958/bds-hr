<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<div class="page-title-bar">
    <div class="page-title-icon">
        <svg viewBox="0 0 20 20" fill="none"><circle cx="10" cy="7" r="3" stroke="currentColor" stroke-width="1.5"/><path d="M4 17c0-3 2.7-5 6-5s6 2 6 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
    </div>
    <div>
        <h1>My Profile</h1>
        <p class="text-muted">Your basic information, managed by HR.</p>
    </div>
</div>

<div class="profile-card">
    <div class="profile-photo-wrap">
        <?php if (! empty($employee['profile_photo'])): ?>
            <img src="<?= base_url('uploads/profiles/' . $employee['profile_photo']) ?>" alt="Profile photo" class="profile-photo">
        <?php else: ?>
            <div class="profile-photo profile-photo-placeholder">
                <?= esc(strtoupper(substr($employee['first_name'], 0, 1) . substr($employee['last_name'], 0, 1))) ?>
            </div>
        <?php endif; ?>

        <form action="<?= site_url('profile/photo') ?>" method="post" enctype="multipart/form-data" class="photo-form">
            <?= csrf_field() ?>
            <input type="file" name="profile_photo" accept="image/jpeg,image/png" required>
            <button type="submit" class="btn-secondary">Upload Photo</button>
        </form>
    </div>

    <div class="profile-details">
        <h2 style="margin-top: 0;">Basic Information</h2>
        <p class="text-muted">Added by HR. Contact HR to request changes.</p>

        <table class="data-table">
            <tr><td>Employee Code</td><td><?= esc($employee['employee_code']) ?></td></tr>
            <tr><td>Full Name</td><td><?= esc($employee['first_name'] . ' ' . $employee['last_name']) ?></td></tr>
            <tr><td>Email</td><td><?= esc($employee['email']) ?></td></tr>
            <tr><td>Phone</td><td><?= esc($employee['phone'] ?? '-') ?></td></tr>
            <tr><td>Designation</td><td><?= esc($employee['designation'] ?? '-') ?></td></tr>
            <tr><td>Date of Joining</td><td><?= $employee['date_of_joining'] ? esc(date('d M Y', strtotime($employee['date_of_joining']))) : '-' ?></td></tr>
            <tr><td>Date of Birth</td><td><?= $employee['date_of_birth'] ? esc(date('d M Y', strtotime($employee['date_of_birth']))) : '-' ?></td></tr>
            <tr><td>Gender</td><td><?= esc(ucfirst($employee['gender'] ?? '-')) ?></td></tr>
            <tr><td>Address</td><td><?= esc($employee['address'] ?? '-') ?></td></tr>
            <tr><td>Status</td><td><span class="badge badge-<?= esc($employee['status']) ?>"><?= esc($employee['status']) ?></span></td></tr>
        </table>
    </div>
</div>

<?= $this->endSection() ?>

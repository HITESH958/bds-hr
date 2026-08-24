<?php
// Expects: $employee (array, may be empty for create), $departments (array)
$employee = $employee ?? [];
?>

<label for="employee_code">Employee Code</label>
<input type="text" id="employee_code" name="employee_code" required
       value="<?= esc(old('employee_code', $employee['employee_code'] ?? '')) ?>">

<div class="form-row">
    <div>
        <label for="first_name">First Name</label>
        <input type="text" id="first_name" name="first_name" required
               value="<?= esc(old('first_name', $employee['first_name'] ?? '')) ?>">
    </div>
    <div>
        <label for="last_name">Last Name</label>
        <input type="text" id="last_name" name="last_name" required
               value="<?= esc(old('last_name', $employee['last_name'] ?? '')) ?>">
    </div>
</div>

<div class="form-row">
    <div>
        <label for="email">Email</label>
        <input type="email" id="email" name="email" required
               value="<?= esc(old('email', $employee['email'] ?? '')) ?>">
    </div>
    <div>
        <label for="phone">Phone</label>
        <input type="text" id="phone" name="phone"
               value="<?= esc(old('phone', $employee['phone'] ?? '')) ?>">
    </div>
</div>

<div class="form-row">
    <div>
        <label for="department_id">Department</label>
        <select id="department_id" name="department_id">
            <option value="">-- Select --</option>
            <?php foreach ($departments as $dept): ?>
                <option value="<?= esc($dept['id']) ?>"
                    <?= (string) old('department_id', $employee['department_id'] ?? '') === (string) $dept['id'] ? 'selected' : '' ?>>
                    <?= esc($dept['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div>
        <label for="designation">Designation</label>
        <input type="text" id="designation" name="designation"
               value="<?= esc(old('designation', $employee['designation'] ?? '')) ?>">
    </div>
</div>

<div class="form-row">
    <div>
        <label for="date_of_joining">Date of Joining</label>
        <input type="date" id="date_of_joining" name="date_of_joining"
               value="<?= esc(old('date_of_joining', $employee['date_of_joining'] ?? '')) ?>">
    </div>
    <div>
        <label for="date_of_birth">Date of Birth</label>
        <input type="date" id="date_of_birth" name="date_of_birth"
               value="<?= esc(old('date_of_birth', $employee['date_of_birth'] ?? '')) ?>">
    </div>
</div>

<div class="form-row">
    <div>
        <label for="gender">Gender</label>
        <select id="gender" name="gender">
            <option value="">-- Select --</option>
            <?php foreach (['male', 'female', 'other'] as $g): ?>
                <option value="<?= $g ?>" <?= old('gender', $employee['gender'] ?? '') === $g ? 'selected' : '' ?>>
                    <?= ucfirst($g) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div>
        <label for="status">Status</label>
        <select id="status" name="status" required>
            <?php foreach (['active', 'inactive', 'resigned'] as $s): ?>
                <option value="<?= $s ?>" <?= old('status', $employee['status'] ?? 'active') === $s ? 'selected' : '' ?>>
                    <?= ucfirst($s) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
</div>

<label for="address">Address</label>
<textarea id="address" name="address" rows="3"><?= esc(old('address', $employee['address'] ?? '')) ?></textarea>

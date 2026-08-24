# Spine HR — Foundation Module (Auth + Employee Database)

Built for CodeIgniter 4. Drop these folders into a fresh CI4 project (or your
existing one) — no new files overwrite CI4 core.

## 1. Install CI4 (skip if you already have a fresh project)

```
composer create-project codeigniter4/appstarter spine-hr
cd spine-hr
```

## 2. Copy these files in

Copy `app/` and `public/assets/` from this package into your CI4 project root,
merging with the existing folders. `Routes.php` here should replace the
default `app/Config/Routes.php`.

## 3. Configure `.env`

```
database.default.hostname = localhost
database.default.database = spine_hr
database.default.username = root
database.default.password =
database.default.DBDriver = MySQLi

app.baseURL = 'http://localhost:8080/'
```

Create the database: `CREATE DATABASE spine_hr;`

## 4. Register the filters

Open `app/Config/Filters.php` and add these two lines to the `$aliases` array:

```php
'auth' => \App\Filters\AuthFilter::class,
'role' => \App\Filters\RoleFilter::class,
```

Routes.php already references `auth` and `role:admin,hr`.

## 5. Run migrations and seed data

```
php spark migrate
php spark db:seed InitialSeeder
```

Creates `departments`, `employees`, `users` tables, 6 sample departments,
and one admin login:

- **Username:** `admin`
- **Password:** `Admin@123`
- Change this immediately via the reset-password flow already built at
  `/forgot-password`.

## 6. Serve it

```
php spark serve
```

Visit `http://localhost:8080/login`.

## What's included

- **Auth**: login, logout, forgot/reset password (token-based, 1-hour
  expiry), session regeneration on login, role stored in session.
- **Roles**: `admin`, `hr`, `employee` — enforced via `RoleFilter`.
  Employee CRUD routes are locked to `admin`/`hr`.
- **Employee database**: full CRUD, search (name/code/email), department
  filter, pagination, department join.
- **Dashboard**: active employee count, department count, recent hires
  table (only shown to admin/hr).

## Attendance module (added)

- **Self-service**: any logged-in user with a linked `employee_id` gets a
  Check In / Check Out widget on the dashboard. One record per employee per
  day (enforced by a unique DB key on `employee_id + attendance_date`).
- **Status rules** (edit constants in `AttendanceModel.php` to match your
  actual office hours):
  - Check-in after `09:30:00` → marked `late`.
  - Worked time under 240 minutes (4 hrs) at check-out → marked `half_day`.
  - Otherwise → `present`.
- **My Attendance** (`/my-attendance`): employee's own monthly history.
- **Attendance Report** (`/attendance`, admin/HR only): all-employee report
  with date range and employee filters, paginated.

### Testing the self-service flow

The seeded `admin` account has no linked employee profile (by design —
it's a system account), so it won't show the check-in widget. To test:

1. Add an employee via `/employees/create`.
2. In phpMyAdmin, insert a row into `users` with `role = 'employee'` and
   `employee_id` set to that employee's ID (or extend `InitialSeeder.php`
   to do this automatically).
3. Log in as that user to see the check-in/out widget.

Building a proper "create login for employee" flow inside the Employees
module is a good candidate for a small follow-up — right now employee
profiles and user logins are created separately.

## What's NOT included yet (next modules)

- Leave, Payroll — as scoped, these come next.
- Email sending for password reset currently just logs the token to
  `writable/logs` — wire up PHPMailer/SMTP in `Auth::sendReset()` when ready.
- Employee self-service beyond the dashboard stats.
- CSRF protection is CI4 built-in — confirm `app.CSRFProtection` is enabled
  in `Config/App.php` (forms already use `csrf_field()`).

## Design decisions

- `users` and `employees` are separate tables (`users.employee_id` links
  them) so accounts that aren't employees (contractors, external admins)
  can still log in.
- Passwords use `password_hash()` / `PASSWORD_DEFAULT` (bcrypt).
- All queries go through query builder — no raw SQL.

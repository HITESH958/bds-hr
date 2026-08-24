<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Public routes
$routes->get('/', 'Auth::loginForm');
$routes->get('login', 'Auth::loginForm');
$routes->post('login', 'Auth::attemptLogin');
$routes->get('logout', 'Auth::logout');
$routes->get('forgot-password', 'Auth::forgotForm');
$routes->post('forgot-password', 'Auth::sendReset');
$routes->get('reset-password/(:segment)', 'Auth::resetForm/$1');
$routes->post('reset-password/(:segment)', 'Auth::resetPassword/$1');

// Authenticated routes (any logged-in role)
$routes->group('', ['filter' => 'auth'], static function ($routes) {
    $routes->get('dashboard', 'Dashboard::index');

    // Employee self-service attendance
    $routes->get('attendance', 'Attendance::myAttendance');
    $routes->post('attendance/check-in', 'Attendance::checkIn');
    $routes->post('attendance/check-out', 'Attendance::checkOut');
    $routes->post('attendance/start-break', 'Attendance::startBreak');
    $routes->post('attendance/end-break', 'Attendance::endBreak');

    // Employee self-service leave
    $routes->get('leave', 'Leave::index');
    $routes->post('leave/apply', 'Leave::apply');
    $routes->post('leave/(:num)/cancel', 'Leave::cancel/$1');

    // Employee self-service payroll
    $routes->get('payroll/my-payslips', 'Payroll::myPayslips');
    $routes->get('payroll/payslip/(:num)', 'Payroll::payslipView/$1');
    $routes->get('payroll/payslip/(:num)/pdf', 'Payroll::payslipPdf/$1');

    // Employee self-service profile
    $routes->get('profile', 'Profile::index');
    $routes->post('profile/photo', 'Profile::uploadPhoto');

    // Change password (any logged-in role)
    $routes->get('change-password', 'Auth::changePasswordForm');
    $routes->post('change-password', 'Auth::changePassword');
});

// Admin/HR-only routes (employee management)
$routes->group('employees', ['filter' => ['auth', 'role:admin,hr']], static function ($routes) {
    $routes->get('/', 'Employees::index');
    $routes->get('create', 'Employees::create');
    $routes->post('/', 'Employees::store');
    $routes->get('(:num)/edit', 'Employees::edit/$1');
    $routes->post('(:num)', 'Employees::update/$1');
    $routes->post('(:num)/delete', 'Employees::delete/$1');
    $routes->get('import', 'Employees::importForm');
    $routes->post('import', 'Employees::import');
    $routes->get('export', 'Employees::exportCsv');
});

// Admin/HR-only attendance routes
$routes->group('attendance', ['filter' => ['auth', 'role:admin,hr']], static function ($routes) {
    $routes->get('daily', 'Attendance::dailyView');
    $routes->post('manual-update', 'Attendance::manualUpdate');
    $routes->get('report/(:num)', 'Attendance::monthlyReport/$1');
    $routes->get('export', 'Attendance::exportDailyCsv');
});

// Admin/HR-only leave routes
$routes->group('leave', ['filter' => ['auth', 'role:admin,hr']], static function ($routes) {
    $routes->get('pending', 'Leave::pending');
    $routes->get('all', 'Leave::allRequests');
    $routes->post('(:num)/approve', 'Leave::approve/$1');
    $routes->post('(:num)/reject', 'Leave::reject/$1');
});

// Admin/HR-only payroll routes
$routes->group('payroll', ['filter' => ['auth', 'role:admin,hr']], static function ($routes) {
    $routes->get('salary', 'Payroll::salaryIndex');
    $routes->get('salary/(:num)/edit', 'Payroll::salaryEdit/$1');
    $routes->post('salary/(:num)', 'Payroll::salaryStore/$1');
    $routes->get('periods', 'Payroll::periods');
    $routes->post('generate', 'Payroll::generate');
    $routes->get('periods/(:num)', 'Payroll::payslips/$1');
    $routes->get('periods/(:num)/export', 'Payroll::exportPayslipsCsv/$1');
});

// Admin/HR-only user login management
$routes->group('users', ['filter' => ['auth', 'role:admin,hr']], static function ($routes) {
    $routes->get('/', 'Users::index');
    $routes->get('create', 'Users::create');
    $routes->post('/', 'Users::store');
    $routes->get('(:num)/reset-password', 'Users::resetPasswordForm/$1');
    $routes->post('(:num)/reset-password', 'Users::resetPassword/$1');
    $routes->post('(:num)/toggle-status', 'Users::toggleStatus/$1');
});

// Admin/HR-only holiday management
$routes->group('holidays', ['filter' => ['auth', 'role:admin,hr']], static function ($routes) {
    $routes->get('/', 'Holidays::index');
    $routes->post('/', 'Holidays::store');
    $routes->post('(:num)/delete', 'Holidays::delete/$1');
});
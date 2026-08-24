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

    // Self-service attendance (any logged-in employee with a linked profile)
    $routes->post('attendance/check-in', 'Attendance::checkIn');
    $routes->post('attendance/check-out', 'Attendance::checkOut');
    $routes->get('my-attendance', 'Attendance::myAttendance');
});

// Admin/HR-only routes (employee management)
$routes->group('employees', ['filter' => ['auth', 'role:admin,hr']], static function ($routes) {
    $routes->get('/', 'Employees::index');
    $routes->get('create', 'Employees::create');
    $routes->post('/', 'Employees::store');
    $routes->get('(:num)/edit', 'Employees::edit/$1');
    $routes->post('(:num)', 'Employees::update/$1');
    $routes->post('(:num)/delete', 'Employees::delete/$1');
});

// Admin/HR-only routes (attendance report)
$routes->group('attendance', ['filter' => ['auth', 'role:admin,hr']], static function ($routes) {
    $routes->get('/', 'Attendance::index');
});

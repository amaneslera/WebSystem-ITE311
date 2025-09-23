<?php

use App\Controllers\Home;
use CodeIgniter\Honeypot\Exceptions\HoneypotException;
use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('/about', 'Home::about');
$routes->get('/contact', 'Home::contact');

// Authentication routes
$routes->get('/register', 'Auth::register');
$routes->post('/register', 'Auth::register');
$routes->get('/login', 'Auth::login');
$routes->post('/login', 'Auth::login');
$routes->get('/logout', 'Auth::logout');

// Admin routes
$routes->group('admin', function($routes) {
    $routes->get('dashboard', 'AdminController::dashboard');
    $routes->get('users', 'AdminController::users');
    $routes->get('courses', 'AdminController::courses');
});

// Teacher routes
$routes->group('teacher', function($routes) {
    $routes->get('dashboard', 'TeacherController::dashboard');
    $routes->get('courses', 'TeacherController::courses');
    $routes->get('students', 'TeacherController::students');
});

// Student routes
$routes->group('student', function($routes) {
    $routes->get('dashboard', 'StudentController::dashboard');
    $routes->get('courses', 'StudentController::courses');
    $routes->get('assignments', 'StudentController::assignments');
    $routes->get('grades', 'StudentController::grades');
});

// Catch-all dashboard route (will redirect based on role)
$routes->get('/dashboard', 'Auth::dashboard');

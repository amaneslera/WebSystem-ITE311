<?php

use App\Controllers\Home;
use CodeIgniter\Honeypot\Exceptions\HoneypotException;
use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
//$routes->get('/', 'Home::index');//
$routes->get('/about', 'Home::about');
$routes->get('/contact', 'Home::contact');

// Authentication routes
$routes->get('/register', 'Auth::register');
$routes->post('/register', 'Auth::register');
$routes->get('/login', 'Auth::login');
$routes->post('/login', 'Auth::login');
$routes->get('/logout', 'Auth::logout');

// Central dashboard route
$routes->get('/dashboard', 'Auth::dashboard');
$routes->post('/course/enroll', 'Course::enroll');
$routes->get('/admin/course/(:num)/upload', 'Materials::upload/$1');
$routes->post('/admin/course/(:num)/upload', 'Materials::upload/$1');
$routes->get('/materials/delete/(:num)', 'Materials::delete/$1');
$routes->get('/materials/download/(:num)', 'Materials::download/$1');
$routes->get('/announcements', 'Announcement::index');

$routes->group('teacher', ['filter' => 'roleauth'], function($routes) {
    $routes->get('dashboard', 'Teacher::dashboard');
    // Add other teacher routes here
});

$routes->group('admin', ['filter' => 'roleauth'], function($routes) {
    $routes->get('dashboard', 'Admin::dashboard');
    // Add other admin routes here
});








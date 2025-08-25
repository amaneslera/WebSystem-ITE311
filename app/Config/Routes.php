<?php

use App\Controllers\Home;
use CodeIgniter\Honeypot\Exceptions\HoneypotException;
use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('/about', 'Home::about' );
$routes->get('/contact', 'Home::contact' );


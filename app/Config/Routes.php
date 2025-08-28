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

$routes->get('/register', 'Auth::register');
$routes->post('/register', 'Auth::register');
$routes->get('/login', 'Auth::login');
$routes->post('/login', 'Auth::login');
$routes->get('/logout', 'Auth::logout');
$routes->get('/dashboard', 'Auth::dashboard');

$routes->get('/test-db', 'Auth::testDb');
$routes->get('/test-login', 'Auth::testLogin');
$routes->get('/test-everything', 'Auth::testEverything');

// Debug routes
$routes->get('/clear-debug', function() {
    $debugFile = WRITEPATH . 'logs/debug.txt';
    if (file_exists($debugFile)) {
        unlink($debugFile);
        echo "Debug log cleared!<br>";
    }
    echo "<a href='" . base_url('register') . "'>Go to Register</a>";
});

$routes->get('/view-debug', function() {
    $debugFile = WRITEPATH . 'logs/debug.txt';
    echo "<h2>Register Debug Log:</h2>";
    if (file_exists($debugFile)) {
        echo "<pre>" . file_get_contents($debugFile) . "</pre>";
    } else {
        echo "<p>No debug log found.</p>";
    }
    echo "<br><a href='" . base_url('register') . "'>Go to Register</a><br>";
    echo "<a href='" . base_url('clear-debug') . "'>Clear Debug Log</a>";
});

$routes->get('/debug-session', function() {
    echo "<h2>Session Debug</h2>";
    echo "<pre>";
    print_r(session()->get());
    echo "</pre>";
    echo "<p><a href='/ITE311-ESLERA/login'>Back to Login</a></p>";
    echo "<p><a href='/ITE311-ESLERA/test-login'>Test Login</a></p>";
});

$routes->get('/simple-register', 'Auth::simpleRegister');
$routes->post('/simple-register', 'Auth::simpleRegister');
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

// Central dashboard route
$routes->get('/dashboard', 'Auth::dashboard');
$routes->post('/course/enroll', 'Course::enroll', ['filter' => 'ratelimit']);

// Lab 9: Search and Filtering - Multiple route aliases
$routes->get('/course/search', 'Course::search');
$routes->post('/course/search', 'Course::search');
$routes->get('/courses/search', 'Course::search');
$routes->post('/courses/search', 'Course::search');

// Course View (Single page for all course content)
$routes->get('/course/view/(:num)', 'CourseView::view/$1', ['filter' => 'roleauth']);
$routes->get('/admin/course/(:num)/upload', 'Materials::upload/$1');
$routes->post('/admin/course/(:num)/upload', 'Materials::upload/$1');
$routes->get('/teacher/course/(:num)/upload', 'Materials::upload/$1');
$routes->post('/teacher/course/(:num)/upload', 'Materials::upload/$1');
$routes->get('/materials/upload/(:num)', 'Materials::upload/$1');
$routes->post('/materials/upload/(:num)', 'Materials::upload/$1');
$routes->get('/materials/delete/(:num)', 'Materials::delete/$1');
$routes->get('/materials/download/(:num)', 'Materials::download/$1');
$routes->get('/announcements', 'Announcement::index');

// Notification Routes (Required only)
$routes->get('/notifications', 'Notifications::get');
$routes->post('/notifications/mark_read/(:num)', 'Notifications::mark_as_read/$1');

// Notification Routes (Traditional)
$routes->get('/notifications/all', 'Notifications::index');
$routes->get('/notifications/mark-read/(:num)', 'Notifications::markRead/$1');
$routes->get('/notifications/mark-all-read', 'Notifications::markAllRead');

// Enrollment Invitation Routes
// Student invitation routes
$routes->get('/enrollment/my-invitations', 'EnrollmentInvitations::myInvitations', ['filter' => 'roleauth']);
$routes->post('/enrollment/respond-invitation', 'EnrollmentInvitations::respondInvitation', ['filter' => 'roleauth']);
$routes->post('/enrollment/accept-invitation/(:num)', 'EnrollmentInvitations::acceptInvitation/$1', ['filter' => 'roleauth']);
$routes->post('/enrollment/decline-invitation/(:num)', 'EnrollmentInvitations::declineInvitation/$1', ['filter' => 'roleauth']);

// Teacher/Admin request routes
$routes->get('/enrollment/pending-requests', 'EnrollmentInvitations::pendingRequests', ['filter' => 'roleauth']);
$routes->post('/enrollment/accept-request/(:num)', 'EnrollmentInvitations::acceptRequest/$1', ['filter' => 'roleauth']);
$routes->post('/enrollment/decline-request/(:num)', 'EnrollmentInvitations::declineRequest/$1', ['filter' => 'roleauth']);

$routes->group('teacher', ['filter' => 'roleauth'], function($routes) {
    $routes->get('dashboard', 'Teacher::dashboard');
    
    // Teacher Course Management
    $routes->get('courses', 'TeacherCourses::index');
    $routes->get('courses/(:num)/students', 'TeacherCourses::viewStudents/$1');
    $routes->get('courses/available-students/(:num)', 'TeacherCourses::availableStudents/$1');
    $routes->post('courses/enroll-student', 'TeacherCourses::enrollStudent');
    $routes->post('courses/unenroll-student', 'TeacherCourses::unenrollStudent');
    $routes->post('courses/bulk-enroll', 'TeacherCourses::bulkEnroll');
    $routes->get('schedule', 'TeacherCourses::schedule');
});

// Assignment Routes
$routes->group('assignments', function($routes) {
    // Teacher routes
    $routes->get('index/(:num)', 'Assignments::index/$1', ['filter' => 'roleauth']);
    $routes->get('create/(:num)', 'Assignments::create/$1', ['filter' => 'roleauth']);
    $routes->post('store', 'Assignments::store', ['filter' => 'roleauth']);
    $routes->get('edit/(:num)', 'Assignments::edit/$1', ['filter' => 'roleauth']);
    $routes->post('update/(:num)', 'Assignments::update/$1', ['filter' => 'roleauth']);
    $routes->get('delete/(:num)', 'Assignments::delete/$1', ['filter' => 'roleauth']);
    $routes->get('submissions/(:num)', 'Assignments::submissions/$1', ['filter' => 'roleauth']);
    $routes->get('grade/(:num)', 'Assignments::grade/$1', ['filter' => 'roleauth']);
    $routes->post('grade/(:num)', 'Assignments::storeGrade/$1', ['filter' => 'roleauth']);
    
    // Student routes
    $routes->get('view/(:num)', 'Assignments::view/$1', ['filter' => 'roleauth']);
    $routes->post('submit/(:num)', 'Assignments::submit/$1', ['filter' => 'roleauth']);
    $routes->get('my-submissions', 'Assignments::mySubmissions', ['filter' => 'roleauth']);
    
    // Shared routes
    $routes->get('download/(:num)', 'Assignments::download/$1', ['filter' => 'roleauth']);
    $routes->get('download-attachment/(:num)', 'Assignments::downloadAttachment/$1', ['filter' => 'roleauth']);
});

$routes->group('admin', ['filter' => 'roleauth'], function($routes) {
    $routes->get('dashboard', 'Admin::dashboard');
    
    // Admin Course Management
    $routes->get('courses', 'AdminCourses::index');
    $routes->get('courses/create', 'AdminCourses::create');
    $routes->post('courses/create', 'AdminCourses::create');
    $routes->get('courses/edit/(:num)', 'AdminCourses::update/$1');
    $routes->post('courses/edit/(:num)', 'AdminCourses::update/$1');
    $routes->get('courses/available-students/(:num)', 'AdminCourses::availableStudents/$1');
    $routes->post('courses/enroll-student', 'AdminCourses::enrollStudent');
    $routes->post('courses/bulk-enroll', 'AdminCourses::bulkEnroll');
    $routes->post('courses/assign-teacher', 'AdminCourses::assignTeacher');
    $routes->post('courses/create-schedule', 'AdminCourses::createSchedule');
    
    // Completed Courses Management (for transferees)
    $routes->get('completed-courses', 'AdminCourses::completedCourses');
    $routes->post('completed-courses/add', 'AdminCourses::addCompletedCourse');
    $routes->get('completed-courses/delete/(:num)', 'AdminCourses::deleteCompletedCourse/$1');
});

// User Management Routes (Admin only)
$routes->get('/users', 'Users::index');
$routes->get('/users/inactive', 'Users::inactive');
$routes->get('/users/create', 'Users::create');
$routes->post('/users/store', 'Users::store');
$routes->get('/users/edit/(:num)', 'Users::edit/$1');
$routes->post('/users/update/(:num)', 'Users::update/$1');
$routes->get('/users/delete/(:num)', 'Users::delete/$1');
$routes->get('/users/deactivate/(:num)', 'Users::deactivate/$1');
$routes->get('/users/activate/(:num)', 'Users::activate/$1');








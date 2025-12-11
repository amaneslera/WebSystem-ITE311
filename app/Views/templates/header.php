<?php
// Initialize variables for notifications
$notificationCount = 0;
$notifications = [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="base-url" content="<?= base_url() ?>">
    <title><?= esc($title ?? 'LMS System') ?></title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    
    <style>
        /* Custom Notification Badge Styles */
        .notification-badge {
            position: relative;
        }
        .notification-badge .badge {
            position: absolute;
            top: -5px;
            right: -10px;
            font-size: 0.65rem;
            padding: 0.25em 0.45em;
            border-radius: 10px;
        }
        
        /* Notification Dropdown Styles */
        .notification-dropdown {
            min-width: 350px;
            max-width: 400px;
            max-height: 500px;
            overflow-y: auto;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }
        
        .notification-item {
            cursor: pointer;
            transition: all 0.2s ease;
        }
        
        .notification-item:hover {
            background-color: #f8f9fa;
        }
        
        .notification-time {
            font-size: 0.75rem;
            color: #6c757d;
        }
        
        .dropdown-header-custom {
            padding: 12px 16px;
            background-color: #f8f9fa;
            font-weight: 600;
            border-bottom: 2px solid #dee2e6;
        }
        
        /* Navbar Custom Styles */
        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
        }
        
        .nav-link {
            font-weight: 500;
            transition: all 0.2s ease;
        }
        
        .nav-link:hover {
            transform: translateY(-2px);
        }
        
        .empty-notifications {
            padding: 30px 20px;
            text-align: center;
            color: #6c757d;
        }
    </style>
</head>
<body class="bg-light">
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow">
        <div class="container-fluid">
            <!-- Brand/Logo -->
            <a class="navbar-brand" href="<?= base_url('/') ?>">
                <i class="bi bi-mortarboard-fill"></i> LMS System
            </a>
            
            <!-- Mobile Toggle Button -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <!-- Navigation Items -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto align-items-center">
                    <?php if (session()->get('isLoggedIn')): ?>
                        
                        <!-- Dashboard Link -->
                        <li class="nav-item">
                            <a class="nav-link <?= (current_url() == base_url('dashboard')) ? 'active' : '' ?>" 
                               href="<?= base_url('dashboard') ?>">
                                <i class="bi bi-speedometer2"></i> Dashboard
                            </a>
                        </li>

                        <?php if (session()->get('role') === 'admin'): ?>
                            <!-- Admin Navigation -->
                            <li class="nav-item">
                                <a class="nav-link" href="<?= base_url('users') ?>">
                                    <i class="bi bi-people"></i> User Management
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= base_url('admin/courses') ?>">
                                    <i class="bi bi-book"></i> Courses
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= base_url('admin/completed-courses') ?>">
                                    <i class="bi bi-check-circle"></i> Completed Courses
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= base_url('announcements') ?>">
                                    <i class="bi bi-megaphone"></i> Announcements
                                </a>
                            </li>
                        <?php elseif (session()->get('role') === 'teacher'): ?>
                            <!-- Teacher Navigation -->
                            <li class="nav-item">
                                <a class="nav-link" href="<?= base_url('teacher/courses') ?>">
                                    <i class="bi bi-book"></i> My Courses
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= base_url('teacher/schedule') ?>">
                                    <i class="bi bi-calendar"></i> Schedule
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= base_url('materials') ?>">
                                    <i class="bi bi-file-earmark-text"></i> Materials
                                </a>
                            </li>
                        <?php else: ?>
                            <!-- Student Navigation -->
                            <li class="nav-item">
                                <a class="nav-link" href="<?= base_url('dashboard') ?>">
                                    <i class="bi bi-journal-check"></i> My Courses
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= base_url('materials') ?>">
                                    <i class="bi bi-file-earmark-text"></i> Materials
                                </a>
                            </li>
                        <?php endif; ?>
                    <?php endif; ?>
                </ul>

                <ul class="navbar-nav ms-auto align-items-center">
                    <?php if (session()->get('isLoggedIn')): ?>

                        <!-- Notifications Dropdown -->
                        <li class="nav-item dropdown notification-badge mx-2">
                            <a class="nav-link position-relative" href="#" id="notificationDropdown" 
                               role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-bell-fill fs-5"></i>
                                <span class="badge bg-danger" id="notification-badge" style="display: none;">0</span>
                            </a>
                            
                            <ul class="dropdown-menu dropdown-menu-end notification-dropdown p-0" 
                                aria-labelledby="notificationDropdown">
                                
                                <!-- Dropdown Header -->
                                <li class="dropdown-header-custom">
                                    <i class="bi bi-bell"></i> Notifications
                                </li>
                                
                                <!-- Notification Items Container -->
                                <div id="notification-list">
                                    <li>
                                        <div class="empty-notifications">
                                            <i class="bi bi-bell-slash fs-1 text-muted"></i>
                                            <p class="mb-0 mt-2">Loading notifications...</p>
                                        </div>
                                    </li>
                                </div>
                            </ul>
                        </li>

                        <!-- User Profile Dropdown -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" 
                               id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-person-circle fs-5 me-1"></i>
                                <span><?= esc(session()->get('name')) ?></span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="userDropdown">
                                <li class="dropdown-header">
                                    <strong><?= esc(session()->get('name')) ?></strong>
                                    <br>
                                    <small class="text-muted"><?= ucfirst(session()->get('role')) ?></small>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item" href="<?= base_url('profile') ?>">
                                        <i class="bi bi-person"></i> My Profile
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="<?= base_url('settings') ?>">
                                        <i class="bi bi-gear"></i> Settings
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item text-danger" href="<?= base_url('logout') ?>">
                                        <i class="bi bi-box-arrow-right"></i> Logout
                                    </a>
                                </li>
                            </ul>
                        </li>
                        
                    <?php else: ?>
                        <!-- Guest Navigation -->
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('login') ?>">
                                <i class="bi bi-box-arrow-in-right"></i> Login
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link btn btn-light text-primary ms-2" href="<?= base_url('register') ?>">
                                <i class="bi bi-person-plus"></i> Register
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Page Content Starts Here -->
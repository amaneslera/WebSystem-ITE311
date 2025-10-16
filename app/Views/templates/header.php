<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'E-Learning System' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body>
    <?php 
    // Get user role from session
    $userRole = session()->get('role');
    $isLoggedIn = session()->get('isLoggedIn');
    
    // Set navbar color based on role
    $navbarClass = 'navbar-light bg-light';
    $navbarIcon = 'house';
    
    if ($isLoggedIn) {
        switch ($userRole) {
            case 'admin':
                $navbarClass = 'navbar-dark bg-dark';
                $navbarIcon = 'shield-lock';
                break;
            case 'teacher':
                $navbarClass = 'navbar-dark bg-primary';
                $navbarIcon = 'person-workspace';
                break;
            case 'student':
                $navbarClass = 'navbar-dark bg-success';
                $navbarIcon = 'mortarboard';
                break;
        }
    }
    ?>

    <nav class="navbar navbar-expand-lg <?= $navbarClass ?>">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?= base_url($isLoggedIn ? ($userRole . '/dashboard') : '/') ?>">
                <i class="bi bi-<?= $navbarIcon ?>"></i> 
                <?= $isLoggedIn ? ucfirst($userRole) . ' Dashboard' : 'E-Learning System' ?>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <?php if ($isLoggedIn): ?>
                        <!-- Common navigation item for all roles -->
                        <li class="nav-item">
                            <a class="nav-link <?= current_url() == base_url($userRole . '/dashboard') ? 'active' : '' ?>" 
                               href="<?= base_url($userRole . '/dashboard') ?>">
                                <i class="bi bi-speedometer2"></i> Dashboard
                            </a>
                        </li>
                        
                        <?php if ($userRole == 'admin'): ?>
                            <!-- Admin-specific navigation items -->
                            <li class="nav-item">
                                <a class="nav-link <?= strpos(current_url(), 'admin/users') !== false ? 'active' : '' ?>" 
                                   href="<?= base_url('/admin/users') ?>">
                                    <i class="bi bi-people"></i> Manage Users
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?= strpos(current_url(), 'admin/courses') !== false ? 'active' : '' ?>" 
                                   href="<?= base_url('/admin/courses') ?>">
                                    <i class="bi bi-book"></i> Manage Courses
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?= strpos(current_url(), 'admin/reports') !== false ? 'active' : '' ?>" 
                                   href="<?= base_url('/admin/reports') ?>">
                                    <i class="bi bi-bar-chart"></i> System Reports
                                </a>
                            </li>
                        <?php elseif ($userRole == 'teacher'): ?>
                            <!-- Teacher-specific navigation items -->
                            <li class="nav-item">
                                <a class="nav-link <?= strpos(current_url(), 'teacher/courses') !== false ? 'active' : '' ?>" 
                                   href="<?= base_url('/teacher/courses') ?>">
                                    <i class="bi bi-journal-text"></i> My Courses
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?= strpos(current_url(), 'teacher/assignments') !== false ? 'active' : '' ?>" 
                                   href="<?= base_url('/teacher/assignments') ?>">
                                    <i class="bi bi-file-earmark-text"></i> Assignments
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?= strpos(current_url(), 'teacher/students') !== false ? 'active' : '' ?>" 
                                   href="<?= base_url('/teacher/students') ?>">
                                    <i class="bi bi-people"></i> My Students
                                </a>
                            </li>
                        <?php elseif ($userRole == 'student'): ?>
                            <!-- Student-specific navigation items -->
                            <li class="nav-item">
                                <a class="nav-link <?= strpos(current_url(), 'student/courses') !== false ? 'active' : '' ?>" 
                                   href="<?= base_url('/student/courses') ?>">
                                    <i class="bi bi-journal-text"></i> My Courses
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?= strpos(current_url(), 'student/assignments') !== false ? 'active' : '' ?>" 
                                   href="<?= base_url('/student/assignments') ?>">
                                    <i class="bi bi-file-earmark-text"></i> Assignments
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?= strpos(current_url(), 'student/grades') !== false ? 'active' : '' ?>" 
                                   href="<?= base_url('/student/grades') ?>">
                                    <i class="bi bi-star"></i> Grades
                                </a>
                            </li>
                        <?php endif; ?>
                    <?php else: ?>
                        <!-- Navigation for non-logged in users -->
                        <li class="nav-item">
                            <a class="nav-link <?= current_url() == base_url('/') ? 'active' : '' ?>" 
                               href="<?= base_url('/') ?>">
                                <i class="bi bi-house"></i> Home
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= current_url() == base_url('/about') ? 'active' : '' ?>" 
                               href="<?= base_url('/about') ?>">
                                <i class="bi bi-info-circle"></i> About
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= current_url() == base_url('/contact') ? 'active' : '' ?>" 
                               href="<?= base_url('/contact') ?>">
                                <i class="bi bi-envelope"></i> Contact
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
                
                <ul class="navbar-nav">
                    <?php if ($isLoggedIn): ?>
                        <!-- User dropdown for logged in users -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle <?= $userRole == 'admin' || $userRole == 'teacher' || $userRole == 'student' ? 'text-white' : '' ?>" 
                               href="#" role="button" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle"></i> <?= session()->get('name') ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <span class="dropdown-item-text">
                                        Role: 
                                        <span class="badge bg-<?= $userRole == 'admin' ? 'danger' : ($userRole == 'teacher' ? 'primary' : 'success') ?>">
                                            <?= ucfirst($userRole) ?>
                                        </span>
                                    </span>
                                </li>
                                <li><span class="dropdown-item-text">Email: <?= session()->get('email') ?></span></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item" href="<?= base_url('/' . $userRole . '/profile') ?>">
                                        <i class="bi bi-person"></i> My Profile
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="<?= base_url('/logout') ?>">
                                        <i class="bi bi-box-arrow-right"></i> Logout
                                    </a>
                                </li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <!-- Login/Register buttons for non-logged in users -->
                        <li class="nav-item">
                            <a class="nav-link <?= current_url() == base_url('/login') ? 'active' : '' ?>" 
                               href="<?= base_url('/login') ?>">
                                <i class="bi bi-box-arrow-in-right"></i> Login
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= current_url() == base_url('/register') ? 'active' : '' ?>" 
                               href="<?= base_url('/register') ?>">
                                <i class="bi bi-person-plus"></i> Register
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
    
    <div class="container-fluid mt-4">
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger">
                <?= session()->getFlashdata('error') ?>
            </div>
        <?php endif; ?>
        
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success">
                <?= session()->getFlashdata('success') ?>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
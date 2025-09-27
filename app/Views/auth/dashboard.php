<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= ucfirst(session()->get('role')) ?> Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        /* Role-specific styles */
        .admin-bg { background-color: #212529; color: white; }
        .teacher-bg { background-color: #0d6efd; color: white; }
        .student-bg { background-color: #198754; color: white; }
        
        .stat-card {
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }
        .icon-large {
            font-size: 2.5rem;
        }
    </style>
</head>
<body>
    <?php
    // Set navbar color and icon based on role
    $role = session()->get('role');
    $navbarClass = '';
    $navbarIcon = '';
    
    switch($role) {
        case 'admin':
            $navbarClass = 'navbar-dark admin-bg';
            $navbarIcon = 'shield-lock';
            break;
        case 'teacher':
            $navbarClass = 'navbar-dark teacher-bg';
            $navbarIcon = 'person-workspace';
            break;
        case 'student':
        default:
            $navbarClass = 'navbar-dark student-bg';
            $navbarIcon = 'mortarboard';
            break;
    }
    ?>

    <nav class="navbar navbar-expand-lg <?= $navbarClass ?>">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?=base_url('/dashboard')?>
                <i class="bi bi-<?= $navbarIcon ?>"></i>
                <?= ucfirst($role) ?> Dashboard
            </a>
            
            <!-- Navigation links based on role -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" 
                    data-bs-target="#navbarContent">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <!-- Common navigation item -->
                    <li class="nav-item">
                        <a class="nav-link active" href="<?=base_url('/dashboard')?>">
                            <i class="bi bi-speedometer2"></i> Dashboard
                        </a>
                    </li>
                    
                    <?php if ($role === 'admin'): ?>
                        <!-- Admin-specific navigation items -->
                        <li class="nav-item">
                            <a class="nav-link" href="<?=base_url('/users')?>
                                <i class="bi bi-people"></i> Manage Users
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?=base_url('/courses')?>
                                <i class="bi bi-book"></i> Manage Courses
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?=base_url('/reports')?>
                                <i class="bi bi-bar-chart"></i> Reports
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
                
                <!-- User dropdown menu -->
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle text-white" href="#" role="button" 
                       data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-person-circle"></i> <?= session()->get('name') ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <span class="dropdown-item-text">
                                Role: 
                                <span class="badge bg-<?= $role == 'admin' ? 'danger' : ($role == 'teacher' ? 'primary' : 'success') ?>">
                                    <?= ucfirst($role) ?>
                                </span>
                            </span>
                        </li>
                        <li><span class="dropdown-item-text">Email: <?= session()->get('email') ?></span></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="<?= base_url('/profile') ?>"><i class="bi bi-person"></i> My Profile</a></li>
                        <li><a class="dropdown-item" href="<?= base_url('/logout') ?>"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <!-- Display flash messages -->
    <div class="container-fluid mt-4">
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>
                <?= session()->getFlashdata('success') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <?= session()->getFlashdata('error') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Welcome header -->
        <h2 class="mb-4">Welcome, <?= session()->get('name') ?>!</h2>
        
        <?php if ($role === 'admin'): ?>
            <!-- Admin Dashboard Content -->
            <div class="row">
                <!-- Statistics cards -->
                <div class="col-md-4 mb-4">
                    <div class="card stat-card text-white bg-primary">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="card-title">Total Teachers</h5>
                                    <h2 class="display-4"><?= $total_teachers ?? 0 ?></h2>
                                </div>
                                <i class="bi bi-person-workspace icon-large"></i>
                            </div>
                            <a href="<?= base_url('/users?role=teacher') ?>" class="btn btn-sm btn-light mt-3">Manage Teachers</a>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4 mb-4">
                    <div class="card stat-card text-white bg-success">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="card-title">Total Students</h5>
                                    <h2 class="display-4"><?= $total_students ?? 0 ?></h2>
                                </div>
                                <i class="bi bi-mortarboard icon-large"></i>
                            </div>
                            <a href="<?= base_url('/users?role=student') ?>" class="btn btn-sm btn-light mt-3">Manage Students</a>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4 mb-4">
                    <div class="card stat-card text-white bg-warning">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="card-title">Total Courses</h5>
                                    <h2 class="display-4"><?= $total_courses ?? 0 ?></h2>
                                </div>
                                <i class="bi bi-book icon-large"></i>
                            </div>
                            <a href="<?= base_url('/courses') ?>" class="btn btn-sm btn-light mt-3">Manage Courses</a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Recent Users Table -->
            <div class="card mt-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Recent Users</h5>
                    <a href="<?= base_url('/users') ?>" class="btn btn-sm btn-primary">View All Users</a>
                </div>
                <div class="card-body">
                    <?php if (!empty($users ?? [])): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Joined</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($users as $user): ?>
                                <tr>
                                    <td><?= $user['id'] ?></td>
                                    <td><?= $user['name'] ?></td>
                                    <td><?= $user['email'] ?></td>
                                    <td>
                                        <span class="badge bg-<?= $user['role'] == 'admin' ? 'danger' : ($user['role'] == 'teacher' ? 'primary' : 'success') ?>">
                                            <?= ucfirst($user['role']) ?>
                                        </span>
                                    </td>
                                    <td><?= date('M d, Y', strtotime($user['created_at'])) ?></td>
                                    <td>
                                        <a href="<?= base_url('/users/edit/'.$user['id']) ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                        <a href="<?= base_url('/users/delete/'.$user['id']) ?>" class="btn btn-sm btn-outline-danger" 
                                           onclick="return confirm('Are you sure you want to delete this user?')">Delete</a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                    <div class="alert alert-info">No users found.</div>
                    <?php endif; ?>
                </div>
            </div>
        <?php elseif ($role === 'teacher'): ?>
            <!-- Placeholder for teacher content -->
            <div class="alert alert-info">
                <h4>Teacher Dashboard</h4>
                <p>Teacher dashboard content will be implemented later.</p>
            </div>
        <?php else: ?>
            <!-- Placeholder for student content -->
            <div class="alert alert-info">
                <h4>Student Dashboard</h4>
                <p>Student dashboard content will be implemented later.</p>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Admin Dashboard' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body>
    <?= $this->include('templates/header') ?>

    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-md-12 mb-4">
                <h2>System Overview</h2>
                <div class="row mt-3">
                    <div class="col-md-4">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title">Total Teachers</h6>
                                        <h2 class="card-text"><?= $total_teachers ?? 0 ?></h2>
                                    </div>
                                    <i class="bi bi-person-workspace" style="font-size: 3rem;"></i>
                                </div>
                                <a href="<?= base_url('/admin/teachers') ?>" class="btn btn-outline-light btn-sm mt-3">Manage Teachers</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title">Total Students</h6>
                                        <h2 class="card-text"><?= $total_students ?? 0 ?></h2>
                                    </div>
                                    <i class="bi bi-mortarboard" style="font-size: 3rem;"></i>
                                </div>
                                <a href="<?= base_url('/admin/students') ?>" class="btn btn-outline-light btn-sm mt-3">Manage Students</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title">Total Courses</h6>
                                        <h2 class="card-text"><?= $total_courses ?? 0 ?></h2>
                                    </div>
                                    <i class="bi bi-journals" style="font-size: 3rem;"></i>
                                </div>
                                <a href="<?= base_url('/admin/courses') ?>" class="btn btn-outline-light btn-sm mt-3">Manage Courses</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Recent User Registrations</h5>
                        <a href="<?= base_url('/admin/users') ?>" class="btn btn-sm btn-primary">View All Users</a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Created At</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($users)): ?>
                                        <?php foreach (array_slice($users, 0, 5) as $user): ?>
                                        <tr>
                                            <td><?= $user['id'] ?></td>
                                            <td><?= $user['name'] ?></td>
                                            <td><?= $user['email'] ?></td>
                                            <td>
                                                <?php if ($user['role'] == 'admin'): ?>
                                                    <span class="badge bg-danger">Admin</span>
                                                <?php elseif ($user['role'] == 'teacher'): ?>
                                                    <span class="badge bg-primary">Teacher</span>
                                                <?php else: ?>
                                                    <span class="badge bg-success">Student</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= $user['created_at'] ?></td>
                                            <td>
                                                <a href="<?= base_url('/admin/users/edit/' . $user['id']) ?>" class="btn btn-sm btn-warning">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <a href="<?= base_url('/admin/users/delete/' . $user['id']) ?>" class="btn btn-sm btn-danger" 
                                                   onclick="return confirm('Are you sure you want to delete this user?')">
                                                    <i class="bi bi-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="6" class="text-center">No users found</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
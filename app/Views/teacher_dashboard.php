<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
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
        .course-card {
            transition: all 0.3s;
        }
        .course-card:hover {
            box-shadow: 0 8px 16px rgba(0,0,0,0.15);
        }
    </style>
</head>
<body>
    <?= $this->include('templates/header') ?>

    <div class="container-fluid mt-4">
        <div class="row mb-4">
            <div class="col">
                <h2 class="mb-0">Welcome, <?= session()->get('name') ?>!</h2>
                <p class="text-muted">Manage your courses and students</p>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="row mb-4">
            <div class="col-md-4 mb-3">
                <div class="card stat-card text-white bg-primary">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title mb-2">My Courses</h6>
                                <h2 class="display-4 mb-0"><?= count($courses ?? []) ?></h2>
                            </div>
                            <i class="bi bi-book icon-large"></i>
                        </div>
                        <a href="<?= base_url('teacher/courses') ?>" class="btn btn-sm btn-light mt-3 w-100">
                            View All Courses
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-3">
                <div class="card stat-card text-white bg-success">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title mb-2">Total Students</h6>
                                <h2 class="display-4 mb-0"><?= $total_students ?? 0 ?></h2>
                            </div>
                            <i class="bi bi-people icon-large"></i>
                        </div>
                        <small class="text-white-50">Across all your courses</small>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-3">
                <div class="card stat-card text-white bg-info">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title mb-2">Materials</h6>
                                <h2 class="display-4 mb-0"><?= $total_materials ?? 0 ?></h2>
                            </div>
                            <i class="bi bi-file-earmark-text icon-large"></i>
                        </div>
                        <a href="<?= base_url('materials') ?>" class="btn btn-sm btn-light mt-3 w-100">
                            Manage Materials
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- My Courses Section -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-book-half"></i> My Courses</h5>
                <a href="<?= base_url('teacher/courses') ?>" class="btn btn-sm btn-light">
                    View All
                </a>
            </div>
            <div class="card-body">
                <?php if (empty($courses ?? [])): ?>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> No courses assigned yet. Contact admin to assign courses to you.
                    </div>
                <?php else: ?>
                    <div class="row">
                        <?php foreach (array_slice($courses, 0, 3) as $course): ?>
                            <div class="col-md-4 mb-3">
                                <div class="card course-card h-100">
                                    <div class="card-body">
                                        <h6 class="card-title">
                                            <span class="badge bg-secondary"><?= esc($course['course_code']) ?></span>
                                        </h6>
                                        <h5><?= esc($course['title']) ?></h5>
                                        <p class="card-text text-muted small">
                                            <?= esc(substr($course['description'], 0, 100)) ?>...
                                        </p>
                                        <hr>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <small class="text-muted">
                                                <i class="bi bi-people"></i> 
                                                <?= $course['current_enrolled'] ?? 0 ?> students
                                            </small>
                                            <a href="<?= base_url('teacher/courses/' . $course['id'] . '/students') ?>" 
                                               class="btn btn-sm btn-primary">
                                                Manage
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <?php if (count($courses) > 3): ?>
                        <div class="text-center mt-3">
                            <a href="<?= base_url('teacher/courses') ?>" class="btn btn-outline-primary">
                                View All <?= count($courses) ?> Courses
                            </a>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card shadow-sm">
            <div class="card-header bg-secondary text-white">
                <h5 class="mb-0"><i class="bi bi-lightning"></i> Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-2">
                        <a href="<?= base_url('teacher/courses') ?>" class="btn btn-outline-primary w-100">
                            <i class="bi bi-book"></i> Manage Courses
                        </a>
                    </div>
                    <div class="col-md-4 mb-2">
                        <a href="<?= base_url('teacher/schedule') ?>" class="btn btn-outline-info w-100">
                            <i class="bi bi-calendar"></i> View Schedule
                        </a>
                    </div>
                    <div class="col-md-4 mb-2">
                        <a href="<?= base_url('materials') ?>" class="btn btn-outline-success w-100">
                            <i class="bi bi-file-earmark-text"></i> Upload Materials
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
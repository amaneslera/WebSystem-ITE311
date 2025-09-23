<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Student Dashboard' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body>
    <?= $this->include('templates/header') ?>

    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-md-4">
                <div class="card bg-success text-white mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title">Enrolled Courses</h6>
                                <h2 class="display-4"><?= $total_courses ?? 0 ?></h2>
                            </div>
                            <i class="bi bi-book" style="font-size: 3rem;"></i>
                        </div>
                        <a href="<?= base_url('/student/courses') ?>" class="btn btn-outline-light btn-sm mt-3">View All Courses</a>
                    </div>
                </div>
                
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Recent Grades</h5>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($recent_grades ?? [])): ?>
                            <div class="list-group">
                                <?php foreach ($recent_grades as $grade): ?>
                                <div class="list-group-item">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1"><?= $grade['assignment_title'] ?></h6>
                                        <span class="badge bg-<?= $grade['score'] >= 70 ? 'success' : ($grade['score'] >= 50 ? 'warning' : 'danger') ?>">
                                            <?= $grade['score'] ?>%
                                        </span>
                                    </div>
                                    <p class="mb-1">Course: <?= $grade['course_title'] ?></p>
                                    <small class="text-muted">Graded on: <?= $grade['graded_at'] ?></small>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-light">
                                <i class="bi bi-info-circle"></i> No recent grades to display
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">My Courses</h5>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($courses)): ?>
                            <div class="row row-cols-1 row-cols-md-2 g-4">
                                <?php foreach ($courses as $course): ?>
                                <div class="col">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <h5 class="card-title"><?= $course['title'] ?></h5>
                                            <p class="card-text"><?= $course['description'] ?? 'No description available' ?></p>
                                            
                                            <?php 
                                            // Find teacher for this course
                                            $teacher = null;
                                            if (!empty($teachers)) {
                                                foreach ($teachers as $t) {
                                                    if ($t['course_id'] == $course['id']) {
                                                        $teacher = $t;
                                                        break;
                                                    }
                                                }
                                            }
                                            ?>
                                            
                                            <?php if ($teacher): ?>
                                            <p class="card-text">
                                                <small class="text-muted">
                                                    Teacher: <?= $teacher['name'] ?>
                                                </small>
                                            </p>
                                            <?php endif; ?>
                                        </div>
                                        <div class="card-footer bg-transparent">
                                            <a href="<?= base_url('/student/course/' . $course['id']) ?>" class="btn btn-success btn-sm">
                                                <i class="bi bi-book"></i> Go to Course
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle"></i> You are not enrolled in any courses yet.
                                Please contact your administrator for enrollment information.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Upcoming Deadlines</h5>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($upcoming_deadlines ?? [])): ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Assignment</th>
                                            <th>Course</th>
                                            <th>Due Date</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($upcoming_deadlines as $deadline): ?>
                                        <tr>
                                            <td><?= $deadline['assignment_title'] ?></td>
                                            <td><?= $deadline['course_title'] ?></td>
                                            <td><?= $deadline['due_date'] ?></td>
                                            <td>
                                                <?php if ($deadline['status'] == 'submitted'): ?>
                                                    <span class="badge bg-success">Submitted</span>
                                                <?php elseif ($deadline['status'] == 'pending'): ?>
                                                    <span class="badge bg-warning">Pending</span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger">Not Submitted</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <a href="<?= base_url('/student/assignment/' . $deadline['id']) ?>" class="btn btn-sm btn-primary">
                                                    <?= $deadline['status'] == 'submitted' ? 'View' : 'Submit' ?>
                                                </a>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-light">
                                <i class="bi bi-check-circle"></i> No upcoming deadlines. You're all caught up!
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
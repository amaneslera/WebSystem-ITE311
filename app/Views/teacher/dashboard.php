<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Teacher Dashboard' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body>
    <?= $this->include('templates/header') ?>

    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">My Courses</h5>
                        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#newCourseModal">
                            <i class="bi bi-plus-circle"></i> Create New Course
                        </button>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($courses)): ?>
                            <div class="list-group">
                                <?php foreach ($courses as $course): ?>
                                <a href="<?= base_url('/teacher/course/' . $course['id']) ?>" class="list-group-item list-group-item-action">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h5 class="mb-1"><?= $course['title'] ?></h5>
                                        <small><?= isset($course['students_count']) ? $course['students_count'] . ' students' : 'No students' ?></small>
                                    </div>
                                    <p class="mb-1"><?= $course['description'] ?? 'No description available' ?></p>
                                    <div class="d-flex justify-content-end">
                                        <span class="badge bg-info me-1">Assignments: <?= isset($course['assignments_count']) ? $course['assignments_count'] : 0 ?></span>
                                        <span class="badge bg-warning">Pending: <?= isset($course['pending_submissions']) ? $course['pending_submissions'] : 0 ?></span>
                                    </div>
                                </a>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle"></i> You don't have any courses yet. 
                                Create your first course by clicking the "Create New Course" button.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="row">
                    <div class="col-md-12 mb-4">
                        <div class="card text-white bg-primary">
                            <div class="card-body">
                                <h5 class="card-title"><i class="bi bi-book"></i> My Courses</h5>
                                <h2 class="display-4"><?= $total_courses ?? 0 ?></h2>
                                <p class="card-text">Courses you're currently teaching</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 mb-4">
                        <div class="card text-white bg-success">
                            <div class="card-body">
                                <h5 class="card-title"><i class="bi bi-people"></i> My Students</h5>
                                <h2 class="display-4"><?= $total_students ?? 0 ?></h2>
                                <p class="card-text">Students enrolled in your courses</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Pending Notifications</h5>
                    </div>
                    <div class="card-body">
                        <div class="list-group">
                            <?php if (!empty($pending_submissions ?? [])): ?>
                                <?php foreach ($pending_submissions as $submission): ?>
                                <a href="<?= base_url('/teacher/submission/' . $submission['id']) ?>" class="list-group-item list-group-item-action">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1"><?= $submission['student_name'] ?></h6>
                                        <small class="text-muted"><?= $submission['submitted_at'] ?></small>
                                    </div>
                                    <p class="mb-1">Submitted: <?= $submission['assignment_title'] ?></p>
                                    <small>Course: <?= $submission['course_title'] ?></small>
                                </a>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="alert alert-light">
                                    <i class="bi bi-check-circle"></i> No pending submissions to review
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for creating a new course -->
    <div class="modal fade" id="newCourseModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Create New Course</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="<?= base_url('/teacher/courses/create') ?>" method="post">
                        <div class="mb-3">
                            <label for="courseTitle" class="form-label">Course Title</label>
                            <input type="text" class="form-control" id="courseTitle" name="title" required>
                        </div>
                        <div class="mb-3">
                            <label for="courseDescription" class="form-label">Course Description</label>
                            <textarea class="form-control" id="courseDescription" name="description" rows="3"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Create Course</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
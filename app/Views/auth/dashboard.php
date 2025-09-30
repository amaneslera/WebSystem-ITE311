<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= ucfirst(session()->get('role')) ?> Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
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
    <?= $this->include('templates/header') ?>

    <div class="container-fluid mt-4">
        <h2 class="mb-4">Welcome, <?= session()->get('name') ?>!</h2>
        
        <?php if (session()->get('role') === 'admin'): ?>
            <!-- Admin Dashboard Content -->
            <div class="row">
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
        <?php elseif (session()->get('role') === 'teacher'): ?>
            <!-- Teacher Dashboard Content -->
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
                            <?php if (!empty($courses ?? [])): ?>
                                <div class="list-group">
                                    <?php foreach ($courses as $course): ?>
                                    <a href="<?= base_url('/course/' . $course['id']) ?>" class="list-group-item list-group-item-action">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h5 class="mb-1"><?= $course['title'] ?></h5>
                                            <small><?= isset($course['students_count']) ? $course['students_count'] . ' students' : 'No students' ?></small>
                                        </div>
                                        <p class="mb-1"><?= $course['description'] ?? 'No description available' ?></p>
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
                    
                    <!-- Pending Assignments -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Pending Assignments to Grade</h5>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($pending_assignments ?? [])): ?>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Assignment</th>
                                                <th>Course</th>
                                                <th>Due Date</th>
                                                <th>Submissions</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($pending_assignments as $assignment): ?>
                                            <tr>
                                                <td><?= $assignment['title'] ?></td>
                                                <td><?= $assignment['course_title'] ?></td>
                                                <td><?= date('M d, Y', strtotime($assignment['due_date'])) ?></td>
                                                <td>
                                                    <span class="badge bg-primary rounded-pill"><?= $assignment['submission_count'] ?></span>
                                                </td>
                                                <td>
                                                    <a href="<?= base_url('/assignments/grade/' . $assignment['id']) ?>" class="btn btn-sm btn-outline-primary">
                                                        Grade
                                                    </a>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-light">
                                    <i class="bi bi-check-circle"></i> No pending assignments to grade.
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <div class="row">
                        <div class="col-md-12 mb-4">
                            <div class="card stat-card text-white bg-primary">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h5 class="card-title">My Courses</h5>
                                            <h2 class="display-4"><?= $total_courses ?? 0 ?></h2>
                                        </div>
                                        <i class="bi bi-journal-text icon-large"></i>
                                    </div>
                                    <p class="card-text">Courses you're currently teaching</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 mb-4">
                            <div class="card stat-card text-white bg-success">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h5 class="card-title">My Students</h5>
                                            <h2 class="display-4"><?= $total_students ?? 0 ?></h2>
                                        </div>
                                        <i class="bi bi-people icon-large"></i>
                                    </div>
                                    <p class="card-text">Students enrolled in your courses</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Notifications Card -->
                        <div class="col-md-12 mb-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Notifications</h5>
                                </div>
                                <div class="card-body">
                                    <?php if (!empty($notifications ?? [])): ?>
                                        <ul class="list-group">
                                            <?php foreach ($notifications as $notification): ?>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <div>
                                                    <p class="mb-1"><?= $notification['message'] ?></p>
                                                    <small class="text-muted"><?= $notification['time_ago'] ?></small>
                                                </div>
                                                <?php if ($notification['type'] == 'assignment'): ?>
                                                    <span class="badge bg-warning rounded-pill">!</span>
                                                <?php elseif ($notification['type'] == 'message'): ?>
                                                    <span class="badge bg-info rounded-pill">
                                                        <i class="bi bi-envelope"></i>
                                                    </span>
                                                <?php endif; ?>
                                            </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    <?php else: ?>
                                        <div class="alert alert-light">
                                            <i class="bi bi-bell"></i> No new notifications.
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Modal for teacher to create a new course -->
            <div class="modal fade" id="newCourseModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title">Create New Course</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form action="<?= base_url('/courses/create') ?>" method="post">
                                <div class="mb-3">
                                    <label for="courseTitle" class="form-label">Course Title</label>
                                    <input type="text" class="form-control" id="courseTitle" name="title" required>
                                </div>
                                <div class="mb-3">
                                    <label for="courseCode" class="form-label">Course Code</label>
                                    <input type="text" class="form-control" id="courseCode" name="code">
                                    <div class="form-text">Optional course code or identifier</div>
                                </div>
                                <div class="mb-3">
                                    <label for="courseDescription" class="form-label">Course Description</label>
                                    <textarea class="form-control" id="courseDescription" name="description" rows="3"></textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Course Availability</label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="isActive" name="is_active" checked>
                                        <label class="form-check-label" for="isActive">
                                            Make course available to students immediately
                                        </label>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary">Create Course</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        <?php elseif (session()->get('role') === 'student'): ?>
            <!-- Student Dashboard Content -->
            <div class="row">
                <div class="col-md-4">
                    <div class="card mb-4 stat-card text-white bg-success">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="card-title">Enrolled Courses</h5>
                                    <h2 class="display-4"><?= $total_courses ?? 0 ?></h2>
                                </div>
                                <i class="bi bi-journal-text icon-large"></i>
                            </div>
                            <p class="card-text">Courses you're currently taking</p>
                        </div>
                    </div>
                    
                    <!-- Recent Grades Card -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Recent Grades</h5>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($recent_grades ?? [])): ?>
                                <ul class="list-group">
                                    <?php foreach ($recent_grades as $grade): ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-0"><?= $grade['assignment_title'] ?></h6>
                                            <small class="text-muted"><?= $grade['course_title'] ?></small>
                                        </div>
                                        <span class="badge bg-<?= $grade['score'] >= 90 ? 'success' : ($grade['score'] >= 70 ? 'primary' : ($grade['score'] >= 60 ? 'warning' : 'danger')) ?> rounded-pill">
                                            <?= $grade['score'] ?>%
                                        </span>
                                    </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php else: ?>
                                <div class="alert alert-light">
                                    <i class="bi bi-info-circle"></i> No grades available yet.
                                </div>
                            <?php endif; ?>
                            <a href="<?= base_url('/grades') ?>" class="btn btn-outline-success btn-sm mt-3">View All Grades</a>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-8">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">My Courses</h5>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($enrolled_courses ?? [])): ?>
                                <div class="row row-cols-1 row-cols-md-2 g-4">
                                    <?php foreach ($enrolled_courses as $course): ?>
                                    <div class="col">
                                        <div class="card h-100">
                                            <div class="card-body">
                                                <h5 class="card-title"><?= $course['title'] ?></h5>
                                                <p class="card-text small text-muted">Instructor: <?= $course['teacher_name'] ?></p>
                                                <p class="card-text"><?= substr($course['description'] ?? 'No description available', 0, 100) ?>...</p>
                                            </div>
                                            <div class="card-footer bg-transparent">
                                                <a href="<?= base_url('/course/' . $course['id']) ?>" class="btn btn-sm btn-outline-success w-100">View Course</a>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle"></i> You are not enrolled in any courses yet.
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Upcoming Deadlines -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Upcoming Deadlines</h5>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($upcoming_assignments ?? [])): ?>
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
                                            <?php foreach ($upcoming_assignments as $assignment): ?>
                                            <tr>
                                                <td><?= $assignment['title'] ?></td>
                                                <td><?= $assignment['course_title'] ?></td>
                                                <td><?= date('M d, Y', strtotime($assignment['due_date'])) ?></td>
                                                <td>
                                                    <?php if ($assignment['submitted']): ?>
                                                        <span class="badge bg-success">Submitted</span>
                                                    <?php else: ?>
                                                        <?php if (strtotime($assignment['due_date']) < time()): ?>
                                                            <span class="badge bg-danger">Overdue</span>
                                                        <?php else: ?>
                                                            <span class="badge bg-warning">Pending</span>
                                                        <?php endif; ?>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if (!$assignment['submitted']): ?>
                                                        <a href="<?= base_url('/assignment/submit/' . $assignment['id']) ?>" class="btn btn-sm btn-outline-success">Submit</a>
                                                    <?php else: ?>
                                                        <a href="<?= base_url('/assignment/view/' . $assignment['id']) ?>" class="btn btn-sm btn-outline-primary">View</a>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-light">
                                    <i class="bi bi-check-circle"></i> No upcoming deadlines.
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

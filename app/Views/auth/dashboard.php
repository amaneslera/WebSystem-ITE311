<?= view('templates/header', ['title' => ucfirst(session()->get('role')) . ' Dashboard']) ?>

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

    <div class="container-fluid mt-4">
        <h2 class="mb-4">Welcome, <?= session()->get('name') ?>!</h2>
        
        <?php if (session()->get('role') === 'admin'): ?>
            <!-- Admin Dashboard Content -->
            
            <!-- Enrollment Requests Alert -->
            <?php
            $invitationModel = new \App\Models\EnrollmentInvitationModel();
            $allPendingRequests = $invitationModel->getAllPendingRequests();
            $adminRequestCount = count($allPendingRequests);
            ?>
            <?php if ($adminRequestCount > 0): ?>
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <h5 class="alert-heading"><i class="bi bi-person-raised-hand"></i> Enrollment Requests!</h5>
                    <p class="mb-2">There are <strong><?= $adminRequestCount ?></strong> pending enrollment request<?= $adminRequestCount > 1 ? 's' : '' ?> across all courses.</p>
                    <hr>
                    <a href="<?= base_url('enrollment/pending-requests') ?>" class="btn btn-info btn-sm">
                        <i class="bi bi-check-circle"></i> Review All Requests
                    </a>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
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
                    <!-- Search and Filter for Users -->
                    <div class="row mb-3">
                        <div class="col-md-8">
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-search"></i></span>
                                <input type="text" class="form-control" id="userSearch" placeholder="Search by name or email...">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <select class="form-select" id="roleFilter">
                                <option value="">All Roles</option>
                                <option value="admin">Admin</option>
                                <option value="teacher">Teacher</option>
                                <option value="student">Student</option>
                            </select>
                        </div>
                    </div>
                    
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
                            <tbody id="userTableBody">
                                <?php foreach ($users as $user): ?>
                                <tr data-user-name="<?= strtolower($user['name']) ?>" 
                                    data-user-email="<?= strtolower($user['email']) ?>"
                                    data-user-role="<?= $user['role'] ?>">
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
            
            <!-- All Courses Management for Admin -->
            <div class="card mt-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">All Courses</h5>
                    <div>
                        <button class="btn btn-sm btn-success me-2" data-bs-toggle="modal" data-bs-target="#createCourseModal">
                            <i class="bi bi-plus-circle"></i> Create Course
                        </button>
                        <button class="btn btn-sm btn-info me-2" data-bs-toggle="modal" data-bs-target="#enrollStudentModal">
                            <i class="bi bi-person-plus"></i> Enroll Student
                        </button>
                        <button class="btn btn-sm btn-primary me-2" data-bs-toggle="modal" data-bs-target="#adminBulkEnrollModal">
                            <i class="bi bi-people-fill"></i> Bulk Enroll
                        </button>
                        <a href="<?= base_url('/admin/courses') ?>" class="btn btn-sm btn-secondary">View All</a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Search and Filter for Courses -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-search"></i></span>
                                <input type="text" class="form-control" id="adminCourseSearch" placeholder="Search by course title, code, or instructor...">
                                <button class="btn btn-outline-secondary" type="button" id="clearAdminCourseSearch">
                                    <i class="bi bi-x-circle"></i> Clear
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <?php if (!empty($all_courses ?? [])): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Course Title</th>
                                        <th>Instructor</th>
                                        <th>Students</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="adminCourseTableBody">
                                    <?php 
                                    // Show first 5 courses by default, hide rest with CSS
                                    foreach ($all_courses as $index => $course): 
                                    ?>
                                    <tr data-course-title="<?= strtolower($course['title']) ?>"
                                        data-course-code="<?= strtolower($course['course_code'] ?? '') ?>"
                                        data-teacher-name="<?= strtolower($course['teacher_name'] ?? '') ?>"
                                        class="<?= $index >= 5 ? 'd-none' : '' ?>">
                                        <td>
                                            <strong><?= $course['title'] ?></strong><br>
                                            <small class="text-muted"><?= $course['course_code'] ?? 'N/A' ?></small>
                                        </td>
                                        <td><?= $course['teacher_name'] ?? 'Unassigned' ?></td>
                                        <td>
                                            <span class="badge bg-info"><?= $course['students_count'] ?? 0 ?> students</span>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-warning assign-teacher-btn" 
                                                    data-course-id="<?= $course['id'] ?>"
                                                    data-course-title="<?= $course['title'] ?>"
                                                    title="Assign Teacher">
                                                <i class="bi bi-person-check"></i>
                                            </button>
                                            <a href="<?= base_url('admin/course/' . $course['id'] . '/upload') ?>" 
                                               class="btn btn-sm btn-primary" 
                                               title="Upload Materials">
                                                <i class="bi bi-upload"></i>
                                            </a>
                                            <a href="<?= base_url('/admin/courses/edit/' . $course['id']) ?>" 
                                               class="btn btn-sm btn-outline-secondary"
                                               title="Edit Course">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i> No courses available in the system.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php elseif (session()->get('role') === 'teacher'): ?>
            <!-- Teacher Dashboard Content -->
            
            <!-- Enrollment Requests Alert -->
            <?php
            $invitationModel = new \App\Models\EnrollmentInvitationModel();
            $pendingRequests = $invitationModel->getPendingRequestsForTeacher(session()->get('user_id'));
            $requestCount = count($pendingRequests);
            ?>
            <?php if ($requestCount > 0): ?>
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <h5 class="alert-heading"><i class="bi bi-person-raised-hand"></i> Enrollment Requests!</h5>
                    <p class="mb-2">You have <strong><?= $requestCount ?></strong> pending enrollment request<?= $requestCount > 1 ? 's' : '' ?> for your courses.</p>
                    <hr>
                    <a href="<?= base_url('enrollment/pending-requests') ?>" class="btn btn-info btn-sm">
                        <i class="bi bi-check-circle"></i> Review Requests
                    </a>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <!-- Stats Cards Row -->
            <div class="row mb-4">
                <div class="col-md-4 mb-3">
                    <div class="card stat-card text-white bg-primary h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="card-title">My Courses</h6>
                                    <h2 class="display-4"><?= count($courses ?? []) ?></h2>
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
                    <div class="card stat-card text-white bg-success h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="card-title">Total Students</h6>
                                    <h2 class="display-4"><?= $total_students ?? 0 ?></h2>
                                </div>
                                <i class="bi bi-people icon-large"></i>
                            </div>
                            <small class="text-white-50">Across all courses</small>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 mb-3">
                    <div class="card stat-card text-white bg-info h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="card-title">Materials</h6>
                                    <h2 class="display-4"><?= $total_materials ?? 0 ?></h2>
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
            
            <div class="row">
                <div class="col-lg-8">
                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">My Courses</h5>
                            <a href="<?= base_url('/teacher/courses') ?>" class="btn btn-sm btn-primary">
                                <i class="bi bi-list-ul"></i> View All Courses
                            </a>
                        </div>
                        <div class="card-body">
                            <!-- Search for Teacher Courses -->
                            <div class="mb-3">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                                    <input type="text" class="form-control" id="teacherCourseSearch" placeholder="Search your courses...">
                                    <button class="btn btn-outline-secondary" type="button" id="clearTeacherCourseSearch">
                                        <i class="bi bi-x-circle"></i> Clear
                                    </button>
                                </div>
                            </div>
                            
                            <?php if (!empty($courses ?? [])): ?>
                                <div class="list-group" id="teacherCoursesList">
                                    <?php 
                                    // Show first 5 courses by default, hide rest with CSS
                                    foreach ($courses as $index => $course): 
                                    ?>
                                        <div class="list-group-item teacher-course-item <?= $index >= 5 ? 'd-none' : '' ?>" 
                                             data-course-title="<?= strtolower($course['title']) ?>"
                                             data-course-description="<?= strtolower($course['description'] ?? '') ?>">
                                            <div class="row align-items-center">
                                                <div class="col-md-8 mb-2 mb-md-0">
                                                    <h5 class="mb-1"><?= $course['title'] ?></h5>
                                                    <p class="mb-1 text-muted"><?= $course['description'] ?? 'No description available' ?></p>
                                                    <small class="text-muted d-block">
                                                        <i class="bi bi-people"></i> 
                                                        <?= isset($course['students_count']) ? $course['students_count'] . ' students enrolled' : 'No students' ?>
                                                    </small>
                                                    <?php if (!empty($course['room']) || !empty($course['schedule_days']) || !empty($course['schedule_time'])): ?>
                                                        <small class="text-muted d-block">
                                                            <?php if (!empty($course['room'])): ?>
                                                                <i class="bi bi-geo-alt"></i> <?= esc($course['room']) ?>
                                                            <?php endif; ?>
                                                            <?php if (!empty($course['schedule_days']) && !empty($course['schedule_time'])): ?>
                                                                • <i class="bi bi-clock"></i> <?= esc($course['schedule_days']) ?> <?= esc($course['schedule_time']) ?>
                                                            <?php endif; ?>
                                                        </small>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="col-md-4 text-md-end">
                                                    <div class="btn-group-vertical btn-group-sm w-100" role="group">
                                                        <button class="btn btn-success teacher-enroll-btn mb-1" 
                                                                data-course-id="<?= $course['id'] ?>"
                                                                data-course-title="<?= $course['title'] ?>">
                                                            <i class="bi bi-person-plus"></i> Enroll Student
                                                        </button>
                                                        <a href="<?= base_url('teacher/courses/' . $course['id'] . '/students') ?>" 
                                                           class="btn btn-info mb-1">
                                                            <i class="bi bi-people"></i> View Students
                                                        </a>
                                                        <a href="<?= base_url('teacher/course/' . $course['id'] . '/upload') ?>" 
                                                           class="btn btn-primary mb-1">
                                                            <i class="bi bi-upload"></i> Upload Materials
                                                        </a>
                                                        <a href="<?= base_url('assignments/index/' . $course['id']) ?>" 
                                                           class="btn btn-warning">
                                                            <i class="bi bi-file-earmark-text"></i> Assignments
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
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
                                                    <span class="badge bg-primary rounded-pill"><?= $assignment['pending_count'] ?></span>
                                                </td>
                                                <td>
                                                    <a href="<?= base_url('/assignments/submissions/' . $assignment['id']) ?>" class="btn btn-sm btn-outline-primary">
                                                        View Submissions
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
            <?php
            // Check for pending invitations
            try {
                $invitationModel = new \App\Models\EnrollmentInvitationModel();
                $userId = session()->get('user_id') ?? session()->get('id');
                $pendingInvitations = $invitationModel->getPendingInvitationsForStudent($userId);
                $invitationCount = count($pendingInvitations);
            } catch (\Exception $e) {
                $invitationCount = 0;
                log_message('error', 'Dashboard invitation check error: ' . $e->getMessage());
            }
            ?>
            
            <!-- Course Invitation Alert - Shows pending invitations with quick actions -->
            <?php if ($invitationCount > 0): ?>
                <div class="alert alert-warning alert-dismissible fade show mb-4" role="alert">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div>
                            <i class="bi bi-envelope-paper-fill me-2"></i>
                            <strong>You have been invited!</strong> 
                            You have <?= $invitationCount ?> pending course invitation<?= $invitationCount > 1 ? 's' : '' ?>.
                        </div>
                        <div>
                            <?php if ($invitationCount == 1 && isset($pendingInvitations[0])): ?>
                                <?php $invitation = $pendingInvitations[0]; ?>
                                <span class="me-2"><strong><?= esc($invitation['course_title']) ?></strong></span>
                                <button class="btn btn-sm btn-success me-1 accept-invitation-btn" 
                                        data-invitation-id="<?= $invitation['id'] ?>"
                                        data-course-title="<?= esc($invitation['course_title']) ?>">
                                    <i class="bi bi-check-circle"></i> Accept
                                </button>
                                <button class="btn btn-sm btn-danger me-1 decline-invitation-btn" 
                                        data-invitation-id="<?= $invitation['id'] ?>"
                                        data-course-title="<?= esc($invitation['course_title']) ?>">
                                    <i class="bi bi-x-circle"></i> Decline
                                </button>
                            <?php else: ?>
                                <a href="<?= base_url('enrollment/my-invitations') ?>" class="btn btn-sm btn-primary">
                                    <i class="bi bi-eye"></i> View All (<?= $invitationCount ?>)
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            
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
                    
                    <!-- Completed Courses (Transfer/Prior Credits) -->
                    <?php 
                    $completedCourseModel = new \App\Models\CompletedCourseModel();
                    $completedCourses = $completedCourseModel->getUserCompletedCourses(session()->get('user_id'));
                    if (!empty($completedCourses)): 
                    ?>
                    <div class="card mb-4">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0">
                                <i class="bi bi-check-circle-fill"></i> Completed Courses (Transfer/Prior Credits)
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Course Code</th>
                                            <th>Course Name</th>
                                            <th>Grade</th>
                                            <th>Institution</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($completedCourses as $completed): ?>
                                        <tr>
                                            <td><strong><?= esc($completed['course_code']) ?></strong></td>
                                            <td><?= esc($completed['course_name']) ?></td>
                                            <td>
                                                <?php if ($completed['grade']): ?>
                                                    <span class="badge bg-success"><?= esc($completed['grade']) ?></span>
                                                <?php else: ?>
                                                    <span class="text-muted">N/A</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= esc($completed['institution'] ?? 'This Institution') ?></td>
                                            <td><?= $completed['completed_date'] ? date('M Y', strtotime($completed['completed_date'])) : 'N/A' ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="alert alert-info mb-0 mt-2">
                                <i class="bi bi-info-circle"></i> These courses count towards prerequisite requirements.
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    
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
                                                    <?php if (isset($assignment['submission_status']) && $assignment['submission_status']): ?>
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
                                                    <?php if (!isset($assignment['submission_status']) || !$assignment['submission_status']): ?>
                                                        <a href="<?= base_url('assignments/view/' . $assignment['id']) ?>" class="btn btn-sm btn-outline-success">Submit</a>
                                                    <?php else: ?>
                                                        <a href="<?= base_url('assignments/view/' . $assignment['id']) ?>" class="btn btn-sm btn-outline-primary">View</a>
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
            
            <!-- Available Courses with Search - Showing First 5 -->
            <div class="card mt-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Available Courses</h5>
                    <div>
                        <span class="badge bg-primary rounded-pill me-2" id="coursesCount"><?= count($available_courses ?? []) ?> total</span>
                        <a href="<?= base_url('courses/search') ?>" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-grid-3x3"></i> View All & Advanced Search
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (!empty($available_courses ?? [])): ?>
                        <div class="table-responsive">
                            <table class="table table-hover" id="coursesTable">
                                <thead>
                                    <tr>
                                        <th>Course</th>
                                        <th>Instructor</th>
                                        <th>Description</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="coursesTableBody">
                                    <?php 
                                    // Show first 5 courses by default
                                    $displayedCourses = array_slice($available_courses, 0, 5);
                                    foreach ($displayedCourses as $course): 
                                    ?>
                                    <tr data-course-title="<?= esc(strtolower($course['title'])) ?>"
                                        data-course-code="<?= esc(strtolower($course['course_code'] ?? $course['code'] ?? '')) ?>"
                                        data-course-description="<?= esc(strtolower($course['description'] ?? '')) ?>"
                                        data-teacher-name="<?= esc(strtolower($course['teacher_name'])) ?>">
                                        <td>
                                            <strong><?= esc($course['title']) ?></strong>
                                            <?php if (!empty($course['course_code'] ?? $course['code'])): ?>
                                                <br><small class="text-muted"><?= esc($course['course_code'] ?? $course['code']) ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= esc($course['teacher_name']) ?></td>
                                        <td><?= esc(substr($course['description'] ?? 'No description available', 0, 80)) ?>...</td>
                                        <td>
                                            <button class="btn btn-sm btn-success enroll-btn" 
                                                    data-course-id="<?= $course['id'] ?>" 
                                                    data-course-title="<?= esc($course['title']) ?>">
                                                <i class="bi bi-plus-circle"></i> Enroll
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                    
                                    <!-- Hidden rows for remaining courses (for search functionality) -->
                                    <?php 
                                    $remainingCourses = array_slice($available_courses, 5);
                                    foreach ($remainingCourses as $course): 
                                    ?>
                                    <tr class="d-none" data-course-title="<?= esc(strtolower($course['title'])) ?>"
                                        data-course-code="<?= esc(strtolower($course['course_code'] ?? $course['code'] ?? '')) ?>"
                                        data-course-description="<?= esc(strtolower($course['description'] ?? '')) ?>"
                                        data-teacher-name="<?= esc(strtolower($course['teacher_name'])) ?>">
                                        <td>
                                            <strong><?= esc($course['title']) ?></strong>
                                            <?php if (!empty($course['course_code'] ?? $course['code'])): ?>
                                                <br><small class="text-muted"><?= esc($course['course_code'] ?? $course['code']) ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= esc($course['teacher_name']) ?></td>
                                        <td><?= esc(substr($course['description'] ?? 'No description available', 0, 80)) ?>...</td>
                                        <td>
                                            <button class="btn btn-sm btn-success enroll-btn" 
                                                    data-course-id="<?= $course['id'] ?>" 
                                                    data-course-title="<?= esc($course['title']) ?>">
                                                <i class="bi bi-plus-circle"></i> Enroll
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="text-center mt-3">
                            <a href="<?= base_url('courses/search') ?>" class="btn btn-primary">
                                <i class="bi bi-search"></i> Advanced Search - Browse All <?= count($available_courses) ?> Courses
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-light">
                            <i class="bi bi-info-circle"></i> No courses available for enrollment at this time.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Downloadable Materials Section for Students -->
            <div class="card mt-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Course Materials</h5>
                    <span class="badge bg-primary rounded-pill"><?= count($materials ?? []) ?> files</span>
                </div>
                <div class="card-body">
                    <?php if (!empty($materials)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Course</th>
                                        <th>File Name</th>
                                        <th>Uploaded</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($materials as $material): ?>
                                    <tr>
                                        <td>
                                            <span class="badge bg-info"><?= esc($material['course_code'] ?? 'N/A') ?></span>
                                            <br>
                                            <small class="text-muted"><?= esc($material['course_title']) ?></small>
                                        </td>
                                        <td>
                                            <i class="bi bi-file-earmark-<?= pathinfo($material['file_name'], PATHINFO_EXTENSION) == 'pdf' ? 'pdf' : 'text' ?>"></i>
                                            <?= esc($material['file_name']) ?>
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                <?= date('M d, Y', strtotime($material['created_at'])) ?>
                                            </small>
                                        </td>
                                        <td>
                                            <a href="<?= base_url('materials/download/' . $material['id']) ?>" 
                                               class="btn btn-success btn-sm">
                                                <i class="bi bi-download"></i> Download
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i> No materials available for your courses yet.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- jQuery MUST load first -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- Bootstrap JS Bundle (includes Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Notification JavaScript -->
    <script src="<?= base_url('assets/js/notifications.js') ?>"></script>
    
    <script>
$(document).ready(function() {
    // Remove all existing alerts when page loads (except structural ones)
    $('.alert:not(.alert-info):not(.alert-warning)').remove();
    
    // ========================================
    // Dashboard Quick Search Function
    // ========================================
    
    console.log('🔍 Dashboard search initialized');
    console.log('Course rows available:', $('#coursesTableBody tr').length);
    
    // Dashboard quick search function - searches through all courses
    function dashboardQuickSearch(searchTerm) {
        const $rows = $('#coursesTableBody tr');
        let visibleCount = 0;
        
        console.log('Dashboard search for:', searchTerm);
        
        if (!searchTerm || searchTerm.trim() === '') {
            // Reset: show only first 5 courses
            $rows.each(function(index) {
                if (index < 5) {
                    $(this).removeClass('d-none').show();
                } else {
                    $(this).addClass('d-none').hide();
                }
            });
            visibleCount = Math.min(5, $rows.length);
            $('#searchResults').empty();
            $('#coursesCount').text('<?= count($available_courses ?? []) ?> total');
        } else {
            // Search through ALL courses (including hidden ones) - same logic as advanced search
            const term = searchTerm.toLowerCase().trim();
            
            $rows.each(function() {
                const $row = $(this);
                // Search through the entire row text content (same as advanced search page)
                const rowText = $row.text().toLowerCase();
                
                // Check if search term matches
                if (rowText.indexOf(term) > -1) {
                    $row.removeClass('d-none').show();
                    visibleCount++;
                } else {
                    $row.addClass('d-none').hide();
                }
            });
            
            console.log('Found courses:', visibleCount);
            
            // Show search results message
            if (visibleCount === 0) {
                $('#searchResults').html(
                    '<div class="alert alert-warning">' +
                    '<i class="bi bi-exclamation-triangle"></i> No courses found. <a href="<?= base_url('courses/search') ?>">Try advanced search</a>' +
                    '</div>'
                );
            } else {
                $('#searchResults').html(
                    '<div class="alert alert-success">' +
                    '<i class="bi bi-check-circle"></i> Found ' + visibleCount + ' course(s)' +
                    '</div>'
                );
            }
            
            $('#coursesCount').text(visibleCount + ' found');
        }
        
        // Re-bind enroll buttons for newly visible courses
        bindEnrollButtons();
    }
    
    // Server-side search function (accurate, comprehensive)
    function serverSideSearch(searchTerm) {
        if (!searchTerm) {
            location.reload(); // Reload to show all courses
            return;
        }
        
        // Show loading state
        $('#searchResults').html(
            '<div class="alert alert-info">' +
            '<span class="spinner-border spinner-border-sm me-2"></span> Searching server database...' +
            '</div>'
        );
        
        // Make AJAX request to server
        $.ajax({
            url: '<?= base_url('course/search') ?>',
            type: 'GET',
            data: { search_term: searchTerm },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Clear table
                    $('#coursesTableBody').empty();
                    
                    if (response.courses.length > 0) {
                        // Add each course to table
                        response.courses.forEach(function(course) {
                            const row = `
                                <tr data-course-title="${(course.title || '').toLowerCase()}"
                                    data-course-code="${(course.course_code || course.code || '').toLowerCase()}"
                                    data-course-description="${(course.description || '').toLowerCase()}"
                                    data-teacher-name="${(course.teacher_name || '').toLowerCase()}">
                                    <td>
                                        <strong>${course.title}</strong>
                                        ${course.course_code || course.code ? '<br><small class="text-muted">' + (course.course_code || course.code) + '</small>' : ''}
                                    </td>
                                    <td>${course.teacher_name || 'No instructor'}</td>
                                    <td>${(course.description || 'No description').substring(0, 80)}...</td>
                                    <td>
                                        <button class="btn btn-sm btn-success enroll-btn" 
                                                data-course-id="${course.id}" 
                                                data-course-title="${course.title}">
                                            <i class="bi bi-plus-circle"></i> Enroll
                                        </button>
                                    </td>
                                </tr>
                            `;
                            $('#coursesTableBody').append(row);
                        });
                        
                        // Update count
                        $('#coursesCount').text(response.count + ' courses');
                        
                        // Show success message
                        $('#searchResults').html(
                            '<div class="alert alert-success">' +
                            '<i class="bi bi-check-circle"></i> Found ' + response.count + ' course(s) from server' +
                            '</div>'
                        );
                    } else {
                        $('#searchResults').html(
                            '<div class="alert alert-warning">' +
                            '<i class="bi bi-exclamation-triangle"></i> No courses found in database matching "' + searchTerm + '"' +
                            '</div>'
                        );
                        $('#coursesCount').text('0 courses');
                    }
                    
                    // Re-bind enroll buttons
                    bindEnrollButtons();
                } else {
                    $('#searchResults').html(
                        '<div class="alert alert-danger">' +
                        '<i class="bi bi-x-circle"></i> Search failed: ' + (response.message || 'Unknown error') +
                        '</div>'
                    );
                }
            },
            error: function(xhr, status, error) {
                $('#searchResults').html(
                    '<div class="alert alert-danger">' +
                    '<i class="bi bi-x-circle"></i> Error performing search. Please try again.' +
                    '</div>'
                );
            }
        });
    }
    
    // Function to bind enroll button events
    function bindEnrollButtons() {
        $('.enroll-btn').off('click').on('click', function(e) {
            enrollButtonHandler.call(this, e);
        });
    }
    
    // Initial binding of enroll buttons
    bindEnrollButtons();
    
    // ========================================
    // Admin User Search and Filter
    // ========================================
    $('#userSearch, #roleFilter').on('input change', function() {
        const searchTerm = $('#userSearch').val().toLowerCase();
        const roleFilter = $('#roleFilter').val();
        
        $('#userTableBody tr').each(function() {
            const $row = $(this);
            const name = $row.attr('data-user-name') || '';
            const email = $row.attr('data-user-email') || '';
            const role = $row.attr('data-user-role') || '';
            
            const matchesSearch = name.includes(searchTerm) || email.includes(searchTerm);
            const matchesRole = !roleFilter || role === roleFilter;
            
            if (matchesSearch && matchesRole) {
                $row.show();
            } else {
                $row.hide();
            }
        });
    });
    
    // ========================================
    // Admin Course Search
    // ========================================
    $('#adminCourseSearch').on('input', function() {
        const searchTerm = $(this).val().toLowerCase();
        
        $('#adminCourseTableBody tr').each(function() {
            const $row = $(this);
            const title = $row.attr('data-course-title') || '';
            const code = $row.attr('data-course-code') || '';
            const teacher = $row.attr('data-teacher-name') || '';
            
            if (title.includes(searchTerm) || code.includes(searchTerm) || teacher.includes(searchTerm)) {
                $row.removeClass('d-none');
            } else {
                $row.addClass('d-none');
            }
        });
    });
    
    $('#clearAdminCourseSearch').on('click', function() {
        $('#adminCourseSearch').val('');
        // Reset to initial state: show first 5, hide rest
        $('#adminCourseTableBody tr').each(function(index) {
            if (index < 5) {
                $(this).removeClass('d-none');
            } else {
                $(this).addClass('d-none');
            }
        });
    });
    
    // ========================================
    // Teacher Course Search
    // ========================================
    $('#teacherCourseSearch').on('input', function() {
        const searchTerm = $(this).val().toLowerCase();
        
        $('.teacher-course-item').each(function() {
            const $item = $(this);
            const title = $item.attr('data-course-title') || '';
            const description = $item.attr('data-course-description') || '';
            
            if (title.includes(searchTerm) || description.includes(searchTerm)) {
                $item.removeClass('d-none');
            } else {
                $item.addClass('d-none');
            }
        });
    });
    
    $('#clearTeacherCourseSearch').on('click', function() {
        $('#teacherCourseSearch').val('');
        // Reset to initial state: show first 5, hide rest
        $('.teacher-course-item').each(function(index) {
            if (index < 5) {
                $(this).removeClass('d-none');
            } else {
                $(this).addClass('d-none');
            }
        });
    });
    
    // ========================================
    // Invitation Accept/Decline Handlers
    // ========================================
    $('.accept-invitation-btn').on('click', function() {
        const invitationId = $(this).data('invitation-id');
        const courseTitle = $(this).data('course-title');
        const $btn = $(this);
        
        if (!confirm(`Accept invitation to "${courseTitle}"?`)) {
            return;
        }
        
        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');
        
        $.post('<?= base_url('enrollment/respond-invitation') ?>', {
            invitation_id: invitationId,
            action: 'accept',
            <?= csrf_token() ?>: '<?= csrf_hash() ?>'
        }).done(function(data) {
            if (data.success) {
                $('.alert-warning').fadeOut(300, function() {
                    $(this).remove();
                });
                
                // Show success message
                $('body').prepend(`
                    <div class="alert alert-success alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3" style="z-index: 9999;" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        ${data.message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                `);
                
                setTimeout(() => location.reload(), 2000);
            } else {
                alert('Error: ' + data.message);
                $btn.prop('disabled', false).html('<i class="bi bi-check-circle"></i> Accept');
            }
        }).fail(function() {
            alert('An error occurred. Please try again.');
            $btn.prop('disabled', false).html('<i class="bi bi-check-circle"></i> Accept');
        });
    });
    
    $('.decline-invitation-btn').on('click', function() {
        const invitationId = $(this).data('invitation-id');
        const courseTitle = $(this).data('course-title');
        const $btn = $(this);
        
        if (!confirm(`Decline invitation to "${courseTitle}"?`)) {
            return;
        }
        
        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');
        
        $.post('<?= base_url('enrollment/respond-invitation') ?>', {
            invitation_id: invitationId,
            action: 'decline',
            <?= csrf_token() ?>: '<?= csrf_hash() ?>'
        }).done(function(data) {
            if (data.success) {
                $('.alert-warning').fadeOut(300, function() {
                    $(this).remove();
                });
                
                // Show success message
                $('body').prepend(`
                    <div class="alert alert-info alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3" style="z-index: 9999;" role="alert">
                        <i class="bi bi-info-circle-fill me-2"></i>
                        ${data.message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                `);
                
                setTimeout(() => location.reload(), 2000);
            } else {
                alert('Error: ' + data.message);
                $btn.prop('disabled', false).html('<i class="bi bi-x-circle"></i> Decline');
            }
        }).fail(function() {
            alert('An error occurred. Please try again.');
            $btn.prop('disabled', false).html('<i class="bi bi-x-circle"></i> Decline');
        });
    });
    
    // ========================================
    // End Lab 9 Search Code
    // ========================================
    
    // Store the enroll button handler for reuse
    function enrollButtonHandler(e) {
        e.preventDefault();
        
        // Get course data from data attributes
        const courseId = $(this).data('course-id');
        const courseTitle = $(this).data('course-title');
        const $button = $(this);
        
        // Disable button and show loading state
        $button.prop('disabled', true)
               .html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Enrolling...');
        
        // Send AJAX request with CSRF token
        $.post('<?= base_url('course/enroll') ?>', { 
            course_id: courseId,
            <?= csrf_token() ?>: '<?= csrf_hash() ?>'
        })
            .done(function(data) {
                if (data.success) {
                    // Remove ALL existing alerts before adding a new one
                    $('.alert').remove();
                    
                    // Show success message with Bootstrap alert
                    const $alert = $('<div class="alert alert-success alert-dismissible fade show" role="alert">' +
                        '<i class="bi bi-check-circle-fill me-2"></i>' +
                        'Successfully enrolled in <strong>' + courseTitle + '</strong>!' +
                        '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
                        '</div>');
                    
                    $('.container-fluid').prepend($alert);
                    
                    // Remove the course from available courses list
                    $button.closest('tr').fadeOut(500, function() {
                        $(this).remove();
                        
                        // Update available courses count
                        const countBadge = $('.card-header .badge');
                        const newCount = parseInt(countBadge.text()) - 1;
                        countBadge.text(newCount + ' courses');
                    });
                    
                    // Update enrolled courses count
                    const $enrolledCount = $('.display-4').first();
                    if ($enrolledCount.length) {
                        $enrolledCount.text(parseInt($enrolledCount.text() || 0) + 1);
                    }
                    
                    // Add the new course to the enrolled courses list without page reload
                    if (data.course) {
                        const $newCourse = $(
                            '<div class="col">' +
                                '<div class="card h-100">' +
                                    '<div class="card-body">' +
                                        '<h5 class="card-title">' + courseTitle + '</h5>' +
                                        '<p class="card-text small text-muted">Instructor: ' + data.course.teacher_name + '</p>' +
                                        '<p class="card-text">' + data.course.description.substring(0, 100) + '...</p>' +
                                    '</div>' +
                                    '<div class="card-footer bg-transparent">' +
                                        '<a href="<?= base_url('/course/') ?>' + courseId + '" class="btn btn-sm btn-outline-success w-100">View Course</a>' +
                                    '</div>' +
                                '</div>' +
                            '</div>'
                        );
                        
                        // Check if "no courses" message exists and remove it if found
                        const $noCoursesMsg = $('.card-body .alert-info:contains("not enrolled in any courses")');
                        if ($noCoursesMsg.length) {
                            $noCoursesMsg.remove();
                            $('.card-body .row').show();
                        }
                        
                        // Add the new course to the list with animation
                        $('.card-body .row.row-cols-1').append($newCourse);
                        $newCourse.hide().fadeIn(1000);
                    }
                } else {
                    // Show error message
                    alert('Error: ' + data.message);
                    // Reset button
                    $button.prop('disabled', false)
                           .html('<i class="bi bi-plus-circle"></i> Enroll');
                }
            })
            .fail(function(xhr, status, error) {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
                $button.prop('disabled', false)
                       .html('<i class="bi bi-plus-circle"></i> Enroll');
            });
    };

    // Admin: Assign Teacher Button
    $('.assign-teacher-btn').click(function() {
        const courseId = $(this).data('course-id');
        const courseTitle = $(this).data('course-title');
        $('#assignTeacherModal').data('course-id', courseId);
        $('#assignCourseTitle').text(courseTitle);
        $('#assignTeacherModal').modal('show');
    });

    // Admin: Assign Teacher Form Submit
    $('#assignTeacherForm').submit(function(e) {
        e.preventDefault();
        const courseId = $('#assignTeacherModal').data('course-id');
        const teacherId = $('#teacherSelect').val();

        $.post('<?= base_url('admin/courses/assign-teacher') ?>', {
            course_id: courseId,
            teacher_id: teacherId,
            <?= csrf_token() ?>: '<?= csrf_hash() ?>'
        }).done(function(data) {
            if (data.success) {
                alert(data.message);
                $('#assignTeacherModal').modal('hide');
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        }).fail(function() {
            alert('An error occurred. Please try again.');
        });
    });

    // Admin: Enroll Student Form Submit
    $('#enrollStudentForm').submit(function(e) {
        e.preventDefault();
        
        const enrollmentMethod = $('input[name="enrollmentMethod"]:checked').val();
        
        $.post('<?= base_url('admin/courses/enroll-student') ?>', {
            student_id: $('#adminStudentSelect').val(),
            course_id: $('#adminCourseSelect').val(),
            enrollment_method: enrollmentMethod,
            <?= csrf_token() ?>: '<?= csrf_hash() ?>'
        }).done(function(data) {
            if (data.success) {
                alert(data.message);
                $('#enrollStudentModal').modal('hide');
                $('#enrollStudentForm')[0].reset();
            } else {
                alert('Error: ' + data.message);
            }
        }).fail(function(xhr) {
            let errorMessage = 'An error occurred. Please try again.';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            }
            alert(errorMessage);
        });
    });

    // Admin: Bulk Enroll Students Form Submit
    $('#adminBulkEnrollForm').submit(function(e) {
        e.preventDefault();
        
        const selectedStudents = $('#adminBulkStudentSelect').val();
        const courseId = $('#adminBulkCourseSelect').val();
        
        if (!selectedStudents || selectedStudents.length === 0) {
            alert('Please select at least one student');
            return;
        }
        
        if (!courseId) {
            alert('Please select a course');
            return;
        }
        
        if (!confirm(`Are you sure you want to enroll ${selectedStudents.length} student(s)?`)) {
            return;
        }
        
        const enrollmentMethod = $('input[name="bulkEnrollmentMethod"]:checked').val();
        const $submitBtn = $('#adminBulkEnrollForm button[type="submit"]');
        $submitBtn.prop('disabled', true).html('<i class="bi bi-hourglass-split"></i> Processing...');
        
        $.post('<?= base_url('admin/courses/bulk-enroll') ?>', {
            student_ids: selectedStudents,
            course_id: courseId,
            enrollment_method: enrollmentMethod,
            <?= csrf_token() ?>: '<?= csrf_hash() ?>'
        }).done(function(data) {
            if (data.success) {
                alert(`Successfully enrolled ${data.enrolled_count} student(s)!\n${data.message}`);
                $('#adminBulkEnrollModal').modal('hide');
                $('#adminBulkEnrollForm')[0].reset();
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        }).fail(function(xhr) {
            const errorMsg = xhr.responseJSON?.message || 'An error occurred. Please try again.';
            alert('Error: ' + errorMsg);
        }).always(function() {
            $submitBtn.prop('disabled', false).html('<i class="bi bi-people-fill"></i> Enroll Selected Students');
        });
    });

    // Teacher: Enroll Student Button
    $('.teacher-enroll-btn').click(function() {
        const courseId = $(this).data('course-id');
        const courseTitle = $(this).data('course-title');
        $('#teacherEnrollModal').data('course-id', courseId);
        $('#teacherEnrollCourseTitle').text(courseTitle);
        $('#teacherEnrollModal').modal('show');
    });

    // Teacher: Enroll Student Form Submit
    $('#teacherEnrollForm').submit(function(e) {
        e.preventDefault();
        const courseId = $('#teacherEnrollModal').data('course-id');
        const studentId = $('#teacherStudentSelect').val();

        $.post('<?= base_url('teacher/courses/enroll-student') ?>', {
            student_id: studentId,
            course_id: courseId,
            <?= csrf_token() ?>: '<?= csrf_hash() ?>'
        }).done(function(data) {
            if (data.success) {
                let message = data.message;
                if (data.prerequisite_warning) {
                    message += '\\n\\nWarning: Student has not completed prerequisites!';
                }
                alert(message);
                $('#teacherEnrollModal').modal('hide');
                $('#teacherEnrollForm')[0].reset();
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        }).fail(function(xhr) {
            let errorMessage = 'An error occurred. Please try again.';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            }
            alert(errorMessage);
        });
    });
    
}); // End of $(document).ready()

</script>

<!-- Admin: Create Course Modal -->
<div class="modal fade" id="createCourseModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create New Course</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('admin/courses/create') ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Course Code</label>
                            <input type="text" name="course_code" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Course Title</label>
                            <input type="text" name="title" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Units</label>
                            <input type="number" name="units" class="form-control" value="3" min="1" max="6">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Max Students</label>
                            <input type="number" name="max_students" class="form-control" placeholder="Leave empty for unlimited">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Semester</label>
                            <select name="semester" class="form-select">
                                <option value="1st Semester">1st Semester</option>
                                <option value="2nd Semester">2nd Semester</option>
                                <option value="Summer">Summer</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Assign Teacher</label>
                        <select name="teacher_id" class="form-select" required>
                            <option value="">Select Teacher</option>
                            <?php if (!empty($all_courses)): ?>
                                <?php 
                                $userModel = new \App\Models\UserModel();
                                $teachers = $userModel->where('role', 'teacher')->where('status', 'active')->findAll();
                                foreach ($teachers as $teacher): 
                                ?>
                                    <option value="<?= $teacher['id'] ?>"><?= $teacher['name'] ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Course</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Admin: Assign Teacher Modal -->
<div class="modal fade" id="assignTeacherModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Assign Teacher</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="assignTeacherForm">
                <div class="modal-body">
                    <p>Course: <strong id="assignCourseTitle"></strong></p>
                    <div class="mb-3">
                        <label class="form-label">Select Teacher</label>
                        <select id="teacherSelect" class="form-select" required>
                            <option value="">Choose teacher...</option>
                            <?php 
                            if (session()->get('role') === 'admin') {
                                $userModel = new \App\Models\UserModel();
                                $teachers = $userModel->where('role', 'teacher')->where('status', 'active')->findAll();
                                foreach ($teachers as $teacher): 
                            ?>
                                <option value="<?= $teacher['id'] ?>"><?= $teacher['name'] ?></option>
                            <?php 
                                endforeach;
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Assign Teacher</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Admin: Enroll Student Modal -->
<div class="modal fade" id="enrollStudentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Manually Enroll Student</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="enrollStudentForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Select Student</label>
                        <select id="adminStudentSelect" class="form-select" required>
                            <option value="">Choose student...</option>
                            <?php 
                            if (session()->get('role') === 'admin') {
                                $userModel = new \App\Models\UserModel();
                                $students = $userModel->where('role', 'student')->where('status', 'active')->findAll();
                                foreach ($students as $student): 
                            ?>
                                <option value="<?= $student['id'] ?>"><?= $student['name'] ?> (<?= $student['email'] ?>)</option>
                            <?php 
                                endforeach;
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Select Course</label>
                        <select id="adminCourseSelect" class="form-select" required>
                            <option value="">Choose course...</option>
                            <?php 
                            if (session()->get('role') === 'admin' && !empty($all_courses)) {
                                foreach ($all_courses as $course): 
                            ?>
                                <option value="<?= $course['id'] ?>"><?= $course['title'] ?> (<?= $course['course_code'] ?>)</option>
                            <?php 
                                endforeach;
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Enrollment Method</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="enrollmentMethod" id="directEnroll" value="direct" checked>
                            <label class="form-check-label" for="directEnroll">
                                <i class="bi bi-lightning-charge-fill text-success"></i> <strong>Direct Enrollment</strong>
                                <small class="d-block text-muted">Student will be enrolled immediately</small>
                            </label>
                        </div>
                        <div class="form-check mt-2">
                            <input class="form-check-input" type="radio" name="enrollmentMethod" id="sendInvitation" value="invitation">
                            <label class="form-check-label" for="sendInvitation">
                                <i class="bi bi-envelope-fill text-primary"></i> <strong>Send Invitation</strong>
                                <small class="d-block text-muted">Student must accept invitation to enroll</small>
                            </label>
                        </div>
                    </div>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> Admin can override all restrictions (capacity, prerequisites, schedule conflicts)
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Enroll Student</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Admin: Bulk Enroll Students Modal -->
<div class="modal fade" id="adminBulkEnrollModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="bi bi-people-fill"></i> Bulk Enroll Students to Course</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="adminBulkEnrollForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Select Course <span class="text-danger">*</span></label>
                        <select id="adminBulkCourseSelect" class="form-select" required>
                            <option value="">Choose course...</option>
                            <?php 
                            if (session()->get('role') === 'admin' && !empty($all_courses)) {
                                foreach ($all_courses as $course): 
                            ?>
                                <option value="<?= $course['id'] ?>"><?= $course['title'] ?> (<?= $course['course_code'] ?>)</option>
                            <?php 
                                endforeach;
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Select Students (Hold Ctrl/Cmd to select multiple) <span class="text-danger">*</span></label>
                        <select class="form-select" id="adminBulkStudentSelect" multiple size="10" required>
                            <?php 
                            if (session()->get('role') === 'admin') {
                                $userModel = new \App\Models\UserModel();
                                $students = $userModel->where('role', 'student')->where('status', 'active')->findAll();
                                foreach ($students as $student): 
                            ?>
                                <option value="<?= $student['id'] ?>"><?= $student['name'] ?> - <?= $student['email'] ?> <?= $student['student_id'] ? '(' . $student['student_id'] . ')' : '' ?></option>
                            <?php 
                                endforeach;
                            }
                            ?>
                        </select>
                        <small class="form-text text-muted">
                            You can select multiple students at once. Click first student, hold Ctrl/Cmd, click others.
                        </small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Enrollment Method</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="bulkEnrollmentMethod" id="bulkDirectEnroll" value="direct" checked>
                            <label class="form-check-label" for="bulkDirectEnroll">
                                <i class="bi bi-lightning-charge-fill text-success"></i> <strong>Direct Enrollment</strong>
                                <small class="d-block text-muted">All students will be enrolled immediately</small>
                            </label>
                        </div>
                        <div class="form-check mt-2">
                            <input class="form-check-input" type="radio" name="bulkEnrollmentMethod" id="bulkSendInvitation" value="invitation">
                            <label class="form-check-label" for="bulkSendInvitation">
                                <i class="bi bi-envelope-fill text-primary"></i> <strong>Send Invitations</strong>
                                <small class="d-block text-muted">Students must accept invitations to enroll</small>
                            </label>
                        </div>
                    </div>
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle"></i> 
                        <strong>Bulk Enrollment</strong><br>
                        This will process all selected students at once. Admin can override capacity limits.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-people-fill"></i> Enroll Selected Students
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Teacher: Enroll Student Modal -->
<div class="modal fade" id="teacherEnrollModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Enroll Student</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="teacherEnrollForm">
                <div class="modal-body">
                    <p>Course: <strong id="teacherEnrollCourseTitle"></strong></p>
                    <div class="mb-3">
                        <label class="form-label">Select Student</label>
                        <select id="teacherStudentSelect" class="form-select" required>
                            <option value="">Choose student...</option>
                            <?php 
                            if (session()->get('role') === 'teacher') {
                                $userModel = new \App\Models\UserModel();
                                $students = $userModel->where('role', 'student')->where('status', 'active')->findAll();
                                foreach ($students as $student): 
                            ?>
                                <option value="<?= $student['id'] ?>"><?= $student['name'] ?> (<?= $student['student_id'] ?? $student['email'] ?>)</option>
                            <?php 
                                endforeach;
                            }
                            ?>
                        </select>
                    </div>
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle"></i> System will check prerequisites and show warnings if not met.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Enroll Student</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Real-Time Notification Updates Script -->
<script>
</script>

<!-- Add rotation animation for loading indicator -->
<style>
@keyframes rotating {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

.rotating {
    animation: rotating 1s linear infinite;
    display: inline-block;
}

#notificationsList .list-group-item.bg-light {
    border-left: 3px solid #0d6efd;
    font-weight: 500;
}
</style>

</body>
</html>

<?= view('templates/header', ['title' => 'My Courses - Teacher']) ?>

<style>
    .course-card {
        transition: all 0.3s;
        border-left: 4px solid #0d6efd;
    }
    .course-card:hover {
        box-shadow: 0 8px 16px rgba(0,0,0,0.15);
        transform: translateY(-3px);
    }
</style>

    <div class="container mt-4">
        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col">
                <h2 class="mb-0">
                    <i class="bi bi-book-half"></i> My Courses
                </h2>
                <p class="text-muted">Manage your assigned courses and enrolled students</p>
            </div>
            <div class="col text-end">
                <a href="<?= base_url('dashboard') ?>" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Back to Dashboard
                </a>
            </div>
        </div>

        <!-- Search Bar -->
        <div class="row mb-4">
            <div class="col-md-8 mx-auto">
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" class="form-control" id="teacherAllCoursesSearch" 
                           placeholder="Search by course title, code, description, department...">
                    <button class="btn btn-outline-secondary" type="button" id="clearTeacherAllCoursesSearch">
                        <i class="bi bi-x-lg"></i> Clear
                    </button>
                </div>
            </div>
        </div>

        <!-- Summary Stats -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card text-white bg-primary">
                    <div class="card-body">
                        <h6 class="card-title">Total Courses</h6>
                        <h2 class="display-4"><?= count($courses) ?></h2>
                        <small>Courses assigned to you</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-success">
                    <div class="card-body">
                        <h6 class="card-title">Total Students</h6>
                        <h2 class="display-4">
                            <?php 
                            $totalStudents = 0;
                            foreach ($courses as $course) {
                                $totalStudents += $course['enrolled_count'] ?? 0;
                            }
                            echo $totalStudents;
                            ?>
                        </h2>
                        <small>Across all courses</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-info">
                    <div class="card-body">
                        <h6 class="card-title">Active Courses</h6>
                        <h2 class="display-4"><?= count($courses) ?></h2>
                        <small>Currently teaching</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Courses List -->
        <?php if (empty($courses)): ?>
            <div class="card shadow-sm">
                <div class="card-body text-center py-5">
                    <i class="bi bi-inbox text-muted" style="font-size: 4rem;"></i>
                    <h4 class="mt-3">No Courses Assigned</h4>
                    <p class="text-muted">You don't have any courses assigned yet. Please contact the administrator to assign courses to you.</p>
                    <a href="<?= base_url('dashboard') ?>" class="btn btn-primary mt-3">
                        <i class="bi bi-house"></i> Go to Dashboard
                    </a>
                </div>
            </div>
        <?php else: ?>
            <div class="row" id="teacherAllCoursesContainer">
                <?php foreach ($courses as $course): ?>
                    <div class="col-md-6 mb-4 teacher-all-course-item" 
                         data-course-title="<?= strtolower(esc($course['title'])) ?>"
                         data-course-code="<?= strtolower(esc($course['course_code'])) ?>"
                         data-course-description="<?= strtolower(esc($course['description'] ?? '')) ?>"
                         data-course-department="<?= strtolower(esc($course['department'] ?? '')) ?>"
                         data-course-semester="<?= strtolower(esc($course['semester'] ?? '')) ?>">
                        <div class="card course-card h-100 shadow-sm">
                            <div class="card-header bg-primary text-white">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="badge bg-light text-primary">
                                        <?= esc($course['course_code']) ?>
                                    </span>
                                    <?php if ($course['status'] === 'active'): ?>
                                        <span class="badge bg-success">Active</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Inactive</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="card-body">
                                <h5 class="card-title"><?= esc($course['title']) ?></h5>
                                <p class="card-text text-muted">
                                    <?= esc(substr($course['description'], 0, 120)) ?>
                                    <?= strlen($course['description']) > 120 ? '...' : '' ?>
                                </p>
                                
                                <div class="row text-center mb-3">
                                    <div class="col-6">
                                        <div class="border rounded p-2">
                                            <i class="bi bi-people text-primary"></i>
                                            <div class="mt-1">
                                                <strong><?= $course['enrolled_count'] ?? 0 ?></strong>
                                                <br>
                                                <small class="text-muted">Students</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="border rounded p-2">
                                            <i class="bi bi-book text-info"></i>
                                            <div class="mt-1">
                                                <strong><?= $course['units'] ?? 'N/A' ?></strong>
                                                <br>
                                                <small class="text-muted">Units</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-2">
                                    <small class="text-muted">
                                        <i class="bi bi-calendar"></i> <?= esc($course['semester']) ?>
                                    </small>
                                    <br>
                                    <small class="text-muted">
                                        <i class="bi bi-building"></i> <?= esc($course['department']) ?>
                                    </small>
                                    <?php if (!empty($course['room'])): ?>
                                        <br>
                                        <small class="text-muted">
                                            <i class="bi bi-geo-alt"></i> <?= esc($course['room']) ?>
                                        </small>
                                    <?php endif; ?>
                                    <?php if (!empty($course['schedule_days']) && !empty($course['schedule_time'])): ?>
                                        <br>
                                        <small class="text-muted">
                                            <i class="bi bi-clock"></i> <?= esc($course['schedule_days']) ?> <?= esc($course['schedule_time']) ?>
                                        </small>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="card-footer bg-light">
                                <div class="d-grid gap-2">
                                    <a href="<?= base_url('teacher/courses/' . $course['id'] . '/students') ?>" 
                                       class="btn btn-info btn-sm">
                                        <i class="bi bi-people"></i> View Students (<?= $course['enrolled_count'] ?? 0 ?>)
                                    </a>
                                    
                                    <div class="btn-group" role="group">
                                        <button class="btn btn-success btn-sm teacher-enroll-btn" 
                                                data-course-id="<?= $course['id'] ?>"
                                                data-course-title="<?= esc($course['title']) ?>">
                                            <i class="bi bi-person-plus"></i> Enroll
                                        </button>
                                        <a href="<?= base_url('teacher/course/' . $course['id'] . '/upload') ?>" 
                                           class="btn btn-secondary btn-sm">
                                            <i class="bi bi-upload"></i> Materials
                                        </a>
                                        <a href="<?= base_url('assignments/index/' . $course['id']) ?>" 
                                           class="btn btn-warning btn-sm">
                                            <i class="bi bi-file-earmark-text"></i> Assignments
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Enroll Student Modal -->
    <div class="modal fade" id="teacherEnrollModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">
                        <i class="bi bi-person-plus"></i> Enroll Student to 
                        <span id="teacherEnrollCourseTitle"></span>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="teacherEnrollForm">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="teacherStudentSelect" class="form-label">Select Student</label>
                            <select class="form-select" id="teacherStudentSelect" required>
                                <option value="">Loading students...</option>
                            </select>
                        </div>
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i> You can enroll students to courses you teach. 
                            Prerequisites will be checked but can be bypassed with a warning.
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        const csrfToken = '<?= csrf_token() ?>';
        const csrfHash = '<?= csrf_hash() ?>';
        let currentCourseId = null;

        // Teacher: Enroll Student Button
        $('.teacher-enroll-btn').click(function() {
            currentCourseId = $(this).data('course-id');
            const courseTitle = $(this).data('course-title');
            $('#teacherEnrollCourseTitle').text(courseTitle);
            
            // Load available students
            loadAvailableStudents(currentCourseId);
            
            $('#teacherEnrollModal').modal('show');
        });

        function loadAvailableStudents(courseId) {
            $('#teacherStudentSelect').html('<option value="">Loading...</option>');
            
            $.get('<?= base_url('teacher/courses/available-students') ?>/' + courseId)
                .done(function(data) {
                    let options = '<option value="">Choose a student...</option>';
                    
                    if (data.students && data.students.length > 0) {
                        data.students.forEach(function(student) {
                            options += `<option value="${student.id}">${student.name} (${student.email})</option>`;
                        });
                    } else {
                        options = '<option value="">No available students</option>';
                    }
                    
                    $('#teacherStudentSelect').html(options);
                })
                .fail(function() {
                    $('#teacherStudentSelect').html('<option value="">Error loading students</option>');
                });
        }

        // Teacher: Enroll Student Form Submit
        $('#teacherEnrollForm').submit(function(e) {
            e.preventDefault();
            const studentId = $('#teacherStudentSelect').val();

            if (!studentId) {
                alert('Please select a student');
                return;
            }

            const data = {
                student_id: studentId,
                course_id: currentCourseId
            };
            data[csrfToken] = csrfHash;

            $.post('<?= base_url('teacher/courses/enroll-student') ?>', data)
                .done(function(response) {
                    if (response.success) {
                        let message = response.message;
                        if (response.prerequisite_warning) {
                            message += '\n\nWarning: Student has not completed prerequisites!';
                        }
                        alert(message);
                        $('#teacherEnrollModal').modal('hide');
                        location.reload();
                    } else {
                        alert('Error: ' + response.message);
                    }
                })
                .fail(function(xhr) {
                    let errorMessage = 'An error occurred. Please try again.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    alert(errorMessage);
                });
        });

        // ========================================
        // Teacher All Courses Page Search
        // ========================================
        $('#teacherAllCoursesSearch').on('input', function() {
            const searchTerm = $(this).val().toLowerCase();
            
            $('.teacher-all-course-item').each(function() {
                const $item = $(this);
                const title = $item.attr('data-course-title') || '';
                const code = $item.attr('data-course-code') || '';
                const description = $item.attr('data-course-description') || '';
                const department = $item.attr('data-course-department') || '';
                const semester = $item.attr('data-course-semester') || '';
                
                if (title.includes(searchTerm) || 
                    code.includes(searchTerm) || 
                    description.includes(searchTerm) || 
                    department.includes(searchTerm) || 
                    semester.includes(searchTerm)) {
                    $item.show();
                } else {
                    $item.hide();
                }
            });
        });
        
        $('#clearTeacherAllCoursesSearch').on('click', function() {
            $('#teacherAllCoursesSearch').val('');
            $('.teacher-all-course-item').show();
        });
    </script>
</body>
</html>

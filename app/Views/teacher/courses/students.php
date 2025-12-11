<?= view('templates/header', ['title' => 'Course Students - ' . esc($course['title'])]) ?>

<style>
    .student-card {
        transition: all 0.3s;
    }
    .student-card:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        transform: translateY(-2px);
    }
</style>

    <div class="container mt-4">
        <!-- Course Header -->
        <div class="row mb-4">
            <div class="col">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('teacher/courses') ?>">My Courses</a></li>
                        <li class="breadcrumb-item active"><?= esc($course['title']) ?></li>
                    </ol>
                </nav>
                <h2 class="mb-0">
                    <i class="bi bi-book"></i> <?= esc($course['title']) ?>
                </h2>
                <p class="text-muted"><?= esc($course['course_code']) ?> - <?= esc($course['description']) ?></p>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="row mb-3">
            <div class="col">
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#enrollStudentModal">
                    <i class="bi bi-person-plus"></i> Enroll Student
                </button>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#bulkEnrollModal">
                    <i class="bi bi-people-fill"></i> Bulk Enroll Students
                </button>
                <a href="<?= base_url('teacher/courses') ?>" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Back to Courses
                </a>
            </div>
        </div>

        <!-- Students Stats -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card text-white bg-primary">
                    <div class="card-body">
                        <h6 class="card-title">Total Students</h6>
                        <h2 class="display-4"><?= count($students) ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-info">
                    <div class="card-body">
                        <h6 class="card-title">Max Capacity</h6>
                        <h2 class="display-4"><?= $course['max_students'] ?? 'Unlimited' ?></h2>
                    </div>
                </div>
            </div>
        </div>

        <!-- Students List -->
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-people"></i> Enrolled Students</h5>
            </div>
            <div class="card-body">
                <?php if (empty($students)): ?>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> No students enrolled yet. Use the "Enroll Student" button to add students.
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Student ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Year Level</th>
                                    <th>Enrolled Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($students as $index => $student): ?>
                                    <tr>
                                        <td><?= $index + 1 ?></td>
                                        <td><?= esc($student['student_id'] ?? 'N/A') ?></td>
                                        <td>
                                            <i class="bi bi-person-circle"></i> 
                                            <?= esc($student['name']) ?>
                                        </td>
                                        <td><?= esc($student['email']) ?></td>
                                        <td>
                                            <?php if ($student['year_level']): ?>
                                                <span class="badge bg-info"><?= esc($student['year_level']) ?> Year</span>
                                            <?php else: ?>
                                                <span class="text-muted">N/A</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= date('M d, Y', strtotime($student['enrollment_date'])) ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-danger unenroll-btn" 
                                                    data-student-id="<?= $student['user_id'] ?>"
                                                    data-student-name="<?= esc($student['name']) ?>">
                                                <i class="bi bi-trash"></i> Unenroll
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Enroll Student Modal -->
    <div class="modal fade" id="enrollStudentModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title"><i class="bi bi-person-plus"></i> Enroll Student</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="enrollForm">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="studentSelect" class="form-label">Select Student</label>
                            <select class="form-select" id="studentSelect" required>
                                <option value="">Choose a student...</option>
                                <!-- Will be populated via AJAX -->
                            </select>
                        </div>
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i> Only students not currently enrolled will appear in the list.
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

    <!-- Bulk Enroll Modal -->
    <div class="modal fade" id="bulkEnrollModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="bi bi-people-fill"></i> Bulk Enroll Students</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="bulkEnrollForm">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Select Students (Hold Ctrl/Cmd to select multiple)</label>
                            <select class="form-select" id="bulkStudentSelect" multiple size="10" required>
                                <!-- Will be populated via AJAX -->
                            </select>
                            <small class="form-text text-muted">
                                You can select multiple students at once
                            </small>
                        </div>
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle"></i> 
                            <strong>Bulk Enrollment</strong><br>
                            This will enroll all selected students at once. Only students not currently enrolled will appear.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Enroll Selected Students</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        const courseId = <?= $course['id'] ?>;
        const csrfToken = '<?= csrf_token() ?>';
        const csrfHash = '<?= csrf_hash() ?>';

        // Load available students when modal opens
        $('#enrollStudentModal, #bulkEnrollModal').on('show.bs.modal', function() {
            loadAvailableStudents();
        });

        function loadAvailableStudents() {
            $.get('<?= base_url('teacher/courses/available-students') ?>/' + courseId)
                .done(function(data) {
                    let options = '<option value="">Choose a student...</option>';
                    let bulkOptions = '';
                    
                    if (data.students && data.students.length > 0) {
                        data.students.forEach(function(student) {
                            options += `<option value="${student.id}">${student.name} (${student.email})</option>`;
                            bulkOptions += `<option value="${student.id}">${student.name} - ${student.email}</option>`;
                        });
                    } else {
                        options = '<option value="">No available students</option>';
                        bulkOptions = '<option value="">No available students</option>';
                    }
                    
                    $('#studentSelect').html(options);
                    $('#bulkStudentSelect').html(bulkOptions);
                })
                .fail(function() {
                    alert('Failed to load students');
                });
        }

        // Single Student Enrollment
        $('#enrollForm').submit(function(e) {
            e.preventDefault();
            const studentId = $('#studentSelect').val();
            
            if (!studentId) {
                alert('Please select a student');
                return;
            }

            const data = {
                student_id: studentId,
                course_id: courseId
            };
            data[csrfToken] = csrfHash;

            $.post('<?= base_url('teacher/courses/enroll-student') ?>', data)
                .done(function(response) {
                    if (response.success) {
                        alert(response.message);
                        location.reload();
                    } else {
                        alert('Error: ' + response.message);
                    }
                })
                .fail(function() {
                    alert('An error occurred. Please try again.');
                });
        });

        // Bulk Student Enrollment
        $('#bulkEnrollForm').submit(function(e) {
            e.preventDefault();
            const selectedStudents = $('#bulkStudentSelect').val();
            
            if (!selectedStudents || selectedStudents.length === 0) {
                alert('Please select at least one student');
                return;
            }

            if (!confirm(`Are you sure you want to enroll ${selectedStudents.length} student(s)?`)) {
                return;
            }

            const data = {
                student_ids: selectedStudents,
                course_id: courseId
            };
            data[csrfToken] = csrfHash;

            $.post('<?= base_url('teacher/courses/bulk-enroll') ?>', data)
                .done(function(response) {
                    if (response.success) {
                        alert(`Successfully enrolled ${response.enrolled_count} student(s)!\n${response.message}`);
                        location.reload();
                    } else {
                        alert('Error: ' + response.message);
                    }
                })
                .fail(function() {
                    alert('An error occurred. Please try again.');
                });
        });

        // Unenroll Student
        $('.unenroll-btn').click(function() {
            const studentId = $(this).data('student-id');
            const studentName = $(this).data('student-name');
            
            if (!confirm(`Are you sure you want to unenroll ${studentName} from this course?`)) {
                return;
            }

            const data = {
                student_id: studentId,
                course_id: courseId
            };
            data[csrfToken] = csrfHash;

            $.post('<?= base_url('teacher/courses/unenroll-student') ?>', data)
                .done(function(response) {
                    if (response.success) {
                        alert(response.message);
                        location.reload();
                    } else {
                        alert('Error: ' + response.message);
                    }
                })
                .fail(function() {
                    alert('An error occurred. Please try again.');
                });
        });
    </script>
</body>
</html>

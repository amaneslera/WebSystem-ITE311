<?= view('templates/header', ['title' => 'Completed Courses']) ?>

<div class="container my-4">
    <main>
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h1 class="h2"><i class="bi bi-check-circle-fill text-success"></i> Completed Courses Management</h1>
            <div class="btn-toolbar mb-2 mb-md-0">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCompletedCourseModal">
                    <i class="bi bi-plus-circle"></i> Add Completed Course
                </button>
            </div>
        </div>

        <!-- Flash Messages -->
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= session()->getFlashdata('success') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= session()->getFlashdata('error') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Info Card -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="alert alert-info">
                    <h5><i class="bi bi-info-circle"></i> About Completed Courses</h5>
                    <p class="mb-0">
                        This page allows you to track courses that students have completed at other institutions or through transfer credits. 
                        Completed courses will satisfy prerequisite requirements for enrollment in advanced courses.
                    </p>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card text-white bg-primary">
                    <div class="card-body">
                        <h6 class="card-title">Total Students</h6>
                        <h3><?= count($students) ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-success">
                    <div class="card-body">
                        <h6 class="card-title">Completed Course Records</h6>
                        <h3><?= count($completedCourses) ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-info">
                    <div class="card-body">
                        <h6 class="card-title">Students with Transfer Credits</h6>
                        <h3>
                            <?php 
                            $studentsWithCredits = [];
                            foreach ($completedCourses as $cc) {
                                $studentsWithCredits[$cc['user_id']] = true;
                            }
                            echo count($studentsWithCredits);
                            ?>
                        </h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Options -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <label for="filterStudent" class="form-label">Filter by Student</label>
                        <select class="form-select" id="filterStudent" onchange="filterTable()">
                            <option value="">All Students</option>
                            <?php foreach ($students as $student): ?>
                                <option value="<?= $student['id'] ?>"><?= esc($student['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="filterCourse" class="form-label">Filter by Course</label>
                        <input type="text" class="form-control" id="filterCourse" placeholder="Search course name or code..." onkeyup="filterTable()">
                    </div>
                </div>
            </div>
        </div>

        <!-- Completed Courses Table -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">All Completed Courses</h5>
            </div>
            <div class="card-body">
                <?php if (!empty($completedCourses)): ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="completedCoursesTable">
                        <thead>
                            <tr>
                                <th>Student Name</th>
                                <th>Course Code</th>
                                <th>Course Name</th>
                                <th>Completed Date</th>
                                <th>Grade</th>
                                <th>Institution</th>
                                <th>Notes</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($completedCourses as $completed): ?>
                            <tr data-student-id="<?= $completed['user_id'] ?>" 
                                data-course-name="<?= strtolower(esc($completed['course_name'])) ?>" 
                                data-course-code="<?= strtolower(esc($completed['course_code'])) ?>">
                                <td>
                                    <strong><?= esc($completed['student_name']) ?></strong>
                                </td>
                                <td><span class="badge bg-secondary"><?= esc($completed['course_code']) ?></span></td>
                                <td><?= esc($completed['course_name']) ?></td>
                                <td><?= $completed['completed_date'] ? date('M d, Y', strtotime($completed['completed_date'])) : '<span class="text-muted">N/A</span>' ?></td>
                                <td>
                                    <?php if ($completed['grade']): ?>
                                        <span class="badge bg-success"><?= esc($completed['grade']) ?></span>
                                    <?php else: ?>
                                        <span class="text-muted">N/A</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= esc($completed['institution'] ?? 'This Institution') ?></td>
                                <td>
                                    <?php if ($completed['notes']): ?>
                                        <span class="text-truncate d-inline-block" style="max-width: 150px;" title="<?= esc($completed['notes']) ?>">
                                            <?= esc($completed['notes']) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="<?= base_url('/admin/completed-courses/delete/'.$completed['id']) ?>" 
                                       class="btn btn-sm btn-outline-danger"
                                       onclick="return confirm('Are you sure you want to remove this completed course record?')">
                                        <i class="bi bi-trash"></i> Remove
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="alert alert-info mb-0">
                    <i class="bi bi-info-circle"></i> No completed courses on record yet. Use the "Add Completed Course" button to add transfer credits or prior learning.
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Students Without Completed Courses -->
        <?php
        $studentsWithoutCredits = [];
        $studentIdsWithCredits = array_column($completedCourses, 'user_id');
        foreach ($students as $student) {
            if (!in_array($student['id'], $studentIdsWithCredits)) {
                $studentsWithoutCredits[] = $student;
            }
        }
        ?>
        <?php if (!empty($studentsWithoutCredits)): ?>
        <div class="card mt-4">
            <div class="card-header bg-light">
                <h5 class="mb-0">Students Without Transfer Credits (<?= count($studentsWithoutCredits) ?>)</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <?php foreach ($studentsWithoutCredits as $student): ?>
                    <div class="col-md-4 mb-2">
                        <div class="d-flex justify-content-between align-items-center p-2 border rounded">
                            <div>
                                <strong><?= esc($student['name']) ?></strong><br>
                                <small class="text-muted"><?= esc($student['email']) ?></small>
                            </div>
                            <button class="btn btn-sm btn-outline-primary" 
                                    onclick="openAddModalForStudent(<?= $student['id'] ?>, '<?= esc($student['name']) ?>')">
                                <i class="bi bi-plus"></i> Add
                            </button>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </main>
</div>

<!-- Add Completed Course Modal -->
<div class="modal fade" id="addCompletedCourseModal" tabindex="-1" aria-labelledby="addCompletedCourseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addCompletedCourseModalLabel">Add Completed Course</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addCompletedCourseForm">
                    <?= csrf_field() ?>
                    
                    <div class="mb-3">
                        <label for="user_id" class="form-label">Student <span class="text-danger">*</span></label>
                        <select class="form-select" id="user_id" name="user_id" required>
                            <option value="">Select Student</option>
                            <?php foreach ($students as $student): ?>
                                <option value="<?= $student['id'] ?>">
                                    <?= esc($student['name']) ?> - <?= esc($student['email']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="course_id" class="form-label">Course <span class="text-danger">*</span></label>
                        <select class="form-select" id="course_id" name="course_id" required>
                            <option value="">Select Course</option>
                            <?php foreach ($availableCourses as $course): ?>
                                <option value="<?= $course['id'] ?>">
                                    <?= esc($course['course_code']) ?> - <?= esc($course['title']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="completed_date" class="form-label">Completed Date</label>
                                <input type="date" class="form-control" id="completed_date" name="completed_date">
                                <div class="form-text">When the course was completed</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="grade" class="form-label">Grade</label>
                                <input type="text" class="form-control" id="grade" name="grade" placeholder="e.g., 1.0, A, 95">
                                <div class="form-text">Grade received in the course</div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="institution" class="form-label">Institution</label>
                        <input type="text" class="form-control" id="institution" name="institution" placeholder="e.g., Previous University Name">
                        <div class="form-text">Leave blank if completed at this institution</div>
                    </div>

                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Any additional information (optional)"></textarea>
                    </div>

                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle"></i> <strong>Important:</strong> This course will be marked as completed and will satisfy prerequisite requirements for enrollment in dependent courses.
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="addCompletedCourse()">
                    <i class="bi bi-check-circle"></i> Mark as Completed
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<script>
function addCompletedCourse() {
    const form = document.getElementById('addCompletedCourseForm');
    const formData = new FormData(form);

    fetch('<?= base_url('/admin/completed-courses/add') ?>', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert(data.message || 'Failed to add completed course.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
    });
}

function openAddModalForStudent(studentId, studentName) {
    document.getElementById('user_id').value = studentId;
    const modal = new bootstrap.Modal(document.getElementById('addCompletedCourseModal'));
    modal.show();
}

function filterTable() {
    const studentFilter = document.getElementById('filterStudent').value.toLowerCase();
    const courseFilter = document.getElementById('filterCourse').value.toLowerCase();
    const table = document.getElementById('completedCoursesTable');
    const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');

    for (let i = 0; i < rows.length; i++) {
        const row = rows[i];
        const studentId = row.getAttribute('data-student-id');
        const courseName = row.getAttribute('data-course-name');
        const courseCode = row.getAttribute('data-course-code');

        let showRow = true;

        // Filter by student
        if (studentFilter && studentId !== studentFilter) {
            showRow = false;
        }

        // Filter by course
        if (courseFilter && !courseName.includes(courseFilter) && !courseCode.includes(courseFilter)) {
            showRow = false;
        }

        row.style.display = showRow ? '' : 'none';
    }
}
</script>

</body>
</html>

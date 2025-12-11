<?= view('templates/header') ?>

<div class="container my-4">
    <main>
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h1 class="h2">Completed Courses - <?= esc($user['name']) ?></h1>
            <div class="btn-toolbar mb-2 mb-md-0">
                <a href="<?= base_url('/users') ?>" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Back to Users
                </a>
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

        <!-- Student Info Card -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Student ID:</strong> <?= esc($user['student_id'] ?? 'N/A') ?></p>
                                <p><strong>Email:</strong> <?= esc($user['email']) ?></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Program:</strong> 
                                    <?php 
                                    if (!empty($user['program_id'])) {
                                        $db = \Config\Database::connect();
                                        $program = $db->table('course_programs')->where('id', $user['program_id'])->get()->getRowArray();
                                        echo $program ? esc($program['name']) : 'N/A';
                                    } else {
                                        echo 'N/A';
                                    }
                                    ?>
                                </p>
                                <p><strong>Year Level:</strong> <?= $user['year_level'] ? $user['year_level'] . ' Year' : 'N/A' ?></p>
                            </div>
                        </div>
                        <div class="alert alert-info mb-0 mt-2">
                            <i class="bi bi-info-circle"></i> <strong>Note:</strong> Use this page to mark courses as completed for transfer students or students with prior credit. These courses will count towards prerequisite requirements.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add Completed Course Button -->
        <div class="mb-3">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCompletedCourseModal">
                <i class="bi bi-plus-circle"></i> Add Completed Course
            </button>
        </div>

        <!-- Completed Courses Table -->
        <div class="card">
            <div class="card-body">
                <?php if (!empty($completedCourses)): ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
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
                            <tr>
                                <td><?= esc($completed['course_code']) ?></td>
                                <td><?= esc($completed['course_name']) ?></td>
                                <td><?= $completed['completed_date'] ? date('M d, Y', strtotime($completed['completed_date'])) : 'N/A' ?></td>
                                <td>
                                    <?php if ($completed['grade']): ?>
                                        <span class="badge bg-success"><?= esc($completed['grade']) ?></span>
                                    <?php else: ?>
                                        <span class="text-muted">N/A</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= esc($completed['institution'] ?? 'This Institution') ?></td>
                                <td><?= esc($completed['notes'] ?? '-') ?></td>
                                <td>
                                    <a href="<?= base_url('/users/completed-courses/delete/'.$completed['id']) ?>" 
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
                    <i class="bi bi-info-circle"></i> No completed courses on record. Use the "Add Completed Course" button to add transfer credits or prior learning.
                </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
</div>

<!-- Add Completed Course Modal -->
<div class="modal fade" id="addCompletedCourseModal" tabindex="-1" aria-labelledby="addCompletedCourseModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addCompletedCourseModalLabel">Add Completed Course</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addCompletedCourseForm">
                    <?= csrf_field() ?>
                    <input type="hidden" name="user_id" value="<?= $user['id'] ?>">

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

                    <div class="mb-3">
                        <label for="completed_date" class="form-label">Completed Date</label>
                        <input type="date" class="form-control" id="completed_date" name="completed_date">
                    </div>

                    <div class="mb-3">
                        <label for="grade" class="form-label">Grade</label>
                        <input type="text" class="form-control" id="grade" name="grade" placeholder="e.g., 1.0, A, 95">
                        <div class="form-text">Optional: Enter the grade received</div>
                    </div>

                    <div class="mb-3">
                        <label for="institution" class="form-label">Institution</label>
                        <input type="text" class="form-control" id="institution" name="institution" placeholder="e.g., Previous University">
                        <div class="form-text">Leave blank if completed at this institution</div>
                    </div>

                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea class="form-control" id="notes" name="notes" rows="2" placeholder="Any additional information"></textarea>
                    </div>

                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle"></i> This course will be marked as completed and will satisfy prerequisite requirements for enrollment.
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

    fetch('<?= base_url('/users/completed-courses/add') ?>', {
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
</script>

</body>
</html>

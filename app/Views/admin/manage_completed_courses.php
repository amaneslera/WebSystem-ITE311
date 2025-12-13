<?= view('templates/header'); ?>

<div class="container-fluid mt-4">
    <div class="row mb-4">
        <div class="col-12">
            <a href="<?= base_url('/admin/completed-courses') ?>" class="btn btn-secondary mb-3">
                <i class="bi bi-arrow-left"></i> Back to Students List
            </a>
            <h2><i class="bi bi-clipboard-check"></i> Manage Completed Courses</h2>
            <h4 class="text-muted"><?= esc($student['name']) ?> - <?= esc($student['email']) ?></h4>
        </div>
    </div>

    <div class="alert alert-success d-none" id="successAlert"></div>
    <div class="alert alert-danger d-none" id="errorAlert"></div>

    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Add New Completed Course</h5>
        </div>
        <div class="card-body">
            <form id="addCourseForm">
                <input type="hidden" name="user_id" value="<?= $student['id'] ?>">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="course_id" class="form-label">Course *</label>
                        <select name="course_id" id="course_id" class="form-select" required>
                            <option value="">Select a course</option>
                            <?php foreach ($availableCourses as $course): ?>
                                <option value="<?= $course['id'] ?>">
                                    <?= esc($course['course_code']) ?> - <?= esc($course['title']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="completed_date" class="form-label">Completion Date *</label>
                        <input type="date" name="completed_date" id="completed_date" class="form-control" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="grade" class="form-label">Grade</label>
                        <input type="text" name="grade" id="grade" class="form-control" placeholder="e.g., 1.0, A, 95">
                    </div>
                    <div class="col-md-8 mb-3">
                        <label for="institution" class="form-label">Institution</label>
                        <input type="text" name="institution" id="institution" class="form-control" 
                               placeholder="Previous school/university">
                    </div>
                </div>
                <div class="mb-3">
                    <label for="notes" class="form-label">Notes</label>
                    <textarea name="notes" id="notes" class="form-control" rows="2" 
                              placeholder="Additional information..."></textarea>
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Add Completed Course
                </button>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Existing Completed Courses</h5>
        </div>
        <div class="card-body">
            <?php if (empty($completedCourses)): ?>
                <p class="text-muted">No completed courses found for this student.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Course Code</th>
                                <th>Course Title</th>
                                <th>Grade</th>
                                <th>Completed Date</th>
                                <th>Institution</th>
                                <th>Notes</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="completedCoursesTable">
                            <?php foreach ($completedCourses as $cc): ?>
                                <tr id="row-<?= $cc['id'] ?>">
                                    <td><?= esc($cc['course_code']) ?></td>
                                    <td><?= esc($cc['course_name']) ?></td>
                                    <td><span class="badge bg-success"><?= esc($cc['grade'] ?? 'N/A') ?></span></td>
                                    <td><?= esc($cc['completed_date']) ?></td>
                                    <td><?= esc($cc['institution'] ?? 'N/A') ?></td>
                                    <td><?= esc($cc['notes'] ?? '-') ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-warning edit-btn" data-id="<?= $cc['id'] ?>"
                                                data-course="<?= esc($cc['course_code']) ?> - <?= esc($cc['course_name']) ?>"
                                                data-grade="<?= esc($cc['grade']) ?>"
                                                data-date="<?= esc($cc['completed_date']) ?>"
                                                data-institution="<?= esc($cc['institution']) ?>"
                                                data-notes="<?= esc($cc['notes']) ?>">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger delete-btn" data-id="<?= $cc['id'] ?>"
                                                data-course="<?= esc($cc['course_code']) ?>">
                                            <i class="bi bi-trash"></i>
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

<!-- jQuery (must be loaded before other scripts) -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<!-- Bootstrap 5 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Completed Course</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editCourseForm">
                <div class="modal-body">
                    <input type="hidden" id="edit_id">
                    <div class="mb-3">
                        <label class="form-label">Course</label>
                        <input type="text" id="edit_course" class="form-control" disabled>
                    </div>
                    <div class="mb-3">
                        <label for="edit_completed_date" class="form-label">Completion Date *</label>
                        <input type="date" id="edit_completed_date" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_grade" class="form-label">Grade</label>
                        <input type="text" id="edit_grade" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="edit_institution" class="form-label">Institution</label>
                        <input type="text" id="edit_institution" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="edit_notes" class="form-label">Notes</label>
                        <textarea id="edit_notes" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Add completed course
    $('#addCourseForm').on('submit', function(e) {
        e.preventDefault();
        
        $.ajax({
            url: '<?= base_url('/admin/completed-courses/add') ?>',
            method: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    showAlert('success', response.message);
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showAlert('error', response.message);
                }
            },
            error: function() {
                showAlert('error', 'Failed to add completed course');
            }
        });
    });

    // Edit button click
    $('.edit-btn').on('click', function() {
        const id = $(this).data('id');
        const course = $(this).data('course');
        const grade = $(this).data('grade');
        const date = $(this).data('date');
        const institution = $(this).data('institution');
        const notes = $(this).data('notes');

        $('#edit_id').val(id);
        $('#edit_course').val(course);
        $('#edit_grade').val(grade);
        $('#edit_completed_date').val(date);
        $('#edit_institution').val(institution);
        $('#edit_notes').val(notes);

        $('#editModal').modal('show');
    });

    // Update completed course
    $('#editCourseForm').on('submit', function(e) {
        e.preventDefault();
        
        const id = $('#edit_id').val();
        const formData = {
            completed_date: $('#edit_completed_date').val(),
            grade: $('#edit_grade').val(),
            institution: $('#edit_institution').val(),
            notes: $('#edit_notes').val()
        };

        $.ajax({
            url: '<?= base_url('/admin/completed-courses/update/') ?>' + id,
            method: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                $('#editModal').modal('hide');
                if (response.success) {
                    showAlert('success', response.message);
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showAlert('error', response.message);
                }
            },
            error: function() {
                showAlert('error', 'Failed to update completed course');
            }
        });
    });

    // Delete button click
    $('.delete-btn').on('click', function() {
        const id = $(this).data('id');
        const course = $(this).data('course');

        if (confirm('Are you sure you want to delete ' + course + '?')) {
            $.ajax({
                url: '<?= base_url('/admin/completed-courses/delete/') ?>' + id,
                method: 'POST',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        showAlert('success', response.message);
                        $('#row-' + id).fadeOut(500, function() {
                            $(this).remove();
                        });
                    } else {
                        showAlert('error', response.message);
                    }
                },
                error: function() {
                    showAlert('error', 'Failed to delete completed course');
                }
            });
        }
    });

    function showAlert(type, message) {
        const alertId = type === 'success' ? '#successAlert' : '#errorAlert';
        $(alertId).text(message).removeClass('d-none');
        setTimeout(() => $(alertId).addClass('d-none'), 5000);
    }
});
</script>

</body>
</html>

<?= view('templates/header', ['title' => $title]) ?>

<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('teacher/courses') ?>">My Courses</a></li>
                    <li class="breadcrumb-item active"><?= esc($course['title']) ?> - Assignments</li>
                </ol>
            </nav>
            <h2><i class="bi bi-file-earmark-text"></i> Manage Assignments</h2>
            <p class="text-muted">Course: <strong><?= esc($course['title']) ?></strong> (<?= esc($course['course_code']) ?>)</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="<?= base_url('assignments/create/' . $course['id']) ?>" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Create New Assignment
            </a>
        </div>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (empty($assignments)): ?>
        <div class="alert alert-info">
            <i class="bi bi-info-circle"></i> No assignments created yet. Click "Create New Assignment" to get started.
        </div>
    <?php else: ?>
        <div class="row">
            <?php foreach ($assignments as $assignment): ?>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-header bg-<?= $assignment['status'] == 'active' ? 'success' : 'secondary' ?> text-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">
                                    <i class="bi bi-<?= 
                                        $assignment['type'] == 'exam' ? 'clipboard-check' : 
                                        ($assignment['type'] == 'quiz' ? 'question-circle' : 
                                        ($assignment['type'] == 'project' ? 'folder' : 'file-text')) 
                                    ?>"></i>
                                    <?= esc($assignment['title']) ?>
                                </h5>
                                <span class="badge bg-light text-dark"><?= ucfirst($assignment['type']) ?></span>
                            </div>
                        </div>
                        <div class="card-body">
                            <?php if ($assignment['description']): ?>
                                <p class="card-text"><?= nl2br(esc(substr($assignment['description'], 0, 100))) ?><?= strlen($assignment['description']) > 100 ? '...' : '' ?></p>
                            <?php endif; ?>
                            
                            <?php if (!empty($assignment['attachment_file'])): ?>
                                <div class="mb-2">
                                    <span class="badge bg-primary">
                                        <i class="bi bi-paperclip"></i> <?= esc($assignment['attachment_file']) ?>
                                    </span>
                                </div>
                            <?php endif; ?>
                            
                            <div class="mb-2">
                                <small class="text-muted">
                                    <i class="bi bi-calendar-event"></i> Due: 
                                    <?= $assignment['due_date'] ? date('M d, Y h:i A', strtotime($assignment['due_date'])) : 'No due date' ?>
                                </small>
                            </div>
                            
                            <div class="mb-2">
                                <small class="text-muted">
                                    <i class="bi bi-trophy"></i> Max Points: <?= $assignment['max_points'] ?>
                                </small>
                            </div>
                            
                            <div class="mb-2">
                                <small class="text-muted">
                                    <i class="bi bi-file-earmark"></i> Allowed: <?= esc($assignment['allowed_file_types']) ?>
                                </small>
                            </div>
                            
                            <?php if ($assignment['allow_late_submission']): ?>
                                <div class="mb-2">
                                    <span class="badge bg-info"><i class="bi bi-clock-history"></i> Late submission allowed</span>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="card-footer bg-light">
                            <div class="d-grid gap-2">
                                <?php if (!empty($assignment['attachment_file'])): ?>
                                    <a href="<?= base_url('assignments/download-attachment/' . $assignment['id']) ?>" 
                                       class="btn btn-success btn-sm">
                                        <i class="bi bi-download"></i> Download Assignment File
                                    </a>
                                <?php endif; ?>
                                <a href="<?= base_url('assignments/submissions/' . $assignment['id']) ?>" 
                                   class="btn btn-primary btn-sm">
                                    <i class="bi bi-files"></i> View Submissions
                                </a>
                                <div class="btn-group" role="group">
                                    <a href="<?= base_url('assignments/edit/' . $assignment['id']) ?>" 
                                       class="btn btn-warning btn-sm">
                                        <i class="bi bi-pencil"></i> Edit
                                    </a>
                                    <button class="btn btn-danger btn-sm delete-assignment" 
                                            data-id="<?= $assignment['id'] ?>"
                                            data-title="<?= esc($assignment['title']) ?>">
                                        <i class="bi bi-trash"></i> Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<script>
$(document).ready(function() {
    $('.delete-assignment').click(function() {
        const id = $(this).data('id');
        const title = $(this).data('title');
        
        if (confirm('Are you sure you want to delete "' + title + '"? All submissions will also be deleted.')) {
            $.get('<?= base_url('assignments/delete/') ?>' + id)
                .done(function(response) {
                    if (response.success) {
                        location.reload();
                    } else {
                        alert(response.message || 'Failed to delete assignment');
                    }
                })
                .fail(function() {
                    alert('Error deleting assignment');
                });
        }
    });
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

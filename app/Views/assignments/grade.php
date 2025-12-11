<?= view('templates/header', ['title' => $title]) ?>

<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('teacher/courses') ?>">My Courses</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('assignments/index/' . $assignment['course_id']) ?>">Assignments</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('assignments/submissions/' . $assignment['id']) ?>">Submissions</a></li>
                    <li class="breadcrumb-item active">Grade Submission</li>
                </ol>
            </nav>
            <h2><i class="bi bi-pencil-square"></i> Grade Submission</h2>
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

    <div class="row">
        <div class="col-md-8">
            <!-- Assignment Details -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-file-earmark-text"></i> Assignment Details</h5>
                </div>
                <div class="card-body">
                    <h4><?= esc($assignment['title']) ?></h4>
                    <p class="text-muted"><?= esc($assignment['course_title']) ?> (<?= esc($assignment['course_code']) ?>)</p>
                    
                    <?php if ($assignment['description']): ?>
                        <hr>
                        <h6>Instructions:</h6>
                        <p><?= nl2br(esc($assignment['description'])) ?></p>
                    <?php endif; ?>

                    <hr>
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Type:</strong> <?= ucfirst($assignment['type']) ?></p>
                            <p><strong>Maximum Points:</strong> <?= $assignment['max_points'] ?></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Due Date:</strong> 
                                <?= $assignment['due_date'] ? date('F d, Y h:i A', strtotime($assignment['due_date'])) : 'No deadline' ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Student Submission -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="bi bi-file-earmark-check"></i> Student Submission</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p><strong>Student:</strong> <?= esc($submission['student_name']) ?></p>
                            <p><strong>Email:</strong> <?= esc($submission['student_email']) ?></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Submitted:</strong> <?= date('F d, Y h:i A', strtotime($submission['submitted_at'])) ?></p>
                            <p><strong>Status:</strong> 
                                <span class="badge bg-<?= 
                                    $submission['status'] == 'graded' ? 'success' : 
                                    ($submission['status'] == 'late' ? 'danger' : 
                                    ($submission['status'] == 'resubmitted' ? 'info' : 'warning')) 
                                ?>">
                                    <?= ucfirst($submission['status']) ?>
                                </span>
                            </p>
                        </div>
                    </div>

                    <div class="alert alert-light">
                        <i class="bi bi-paperclip"></i> <strong>Submitted File:</strong> <?= esc($submission['file_name']) ?>
                        <a href="<?= base_url('assignments/download/' . $submission['id']) ?>" 
                           class="btn btn-sm btn-primary ms-3">
                            <i class="bi bi-download"></i> Download File
                        </a>
                    </div>

                    <?php if ($submission['status'] == 'graded' && $submission['graded_at']): ?>
                        <div class="alert alert-warning">
                            <i class="bi bi-info-circle"></i> 
                            <strong>Note:</strong> This submission was previously graded on 
                            <?= date('F d, Y h:i A', strtotime($submission['graded_at'])) ?>. 
                            Submitting this form will update the grade.
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Grading Form -->
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="bi bi-award"></i> Grade Assignment</h5>
                </div>
                <div class="card-body">
                    <form action="<?= base_url('assignments/grade/' . $submission['id']) ?>" method="post">
                        <?= csrf_field() ?>

                        <div class="mb-4">
                            <label for="grade" class="form-label">
                                Grade <span class="text-danger">*</span>
                                <span class="text-muted">(Max: <?= $assignment['max_points'] ?> points)</span>
                            </label>
                            <input type="number" 
                                   class="form-control form-control-lg" 
                                   id="grade" 
                                   name="grade" 
                                   min="0" 
                                   max="<?= $assignment['max_points'] ?>" 
                                   step="0.01"
                                   value="<?= old('grade', $submission['grade'] ?? '') ?>" 
                                   required>
                            <div class="form-text">
                                Enter a value between 0 and <?= $assignment['max_points'] ?>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="feedback" class="form-label">Feedback</label>
                            <textarea class="form-control" 
                                      id="feedback" 
                                      name="feedback" 
                                      rows="6"
                                      placeholder="Provide feedback to the student (optional)"><?= old('feedback', $submission['feedback'] ?? '') ?></textarea>
                            <div class="form-text">
                                Share comments, suggestions, or explanations about the grade
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="bi bi-check-circle"></i> Submit Grade
                            </button>
                            <a href="<?= base_url('assignments/submissions/' . $assignment['id']) ?>" 
                               class="btn btn-secondary btn-lg">
                                <i class="bi bi-x-circle"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-md-4">
            <!-- Quick Stats -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-secondary text-white">
                    <h6 class="mb-0"><i class="bi bi-bar-chart"></i> Grading Guide</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h6>Percentage Calculator</h6>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <tr>
                                    <td>100%</td>
                                    <td class="text-end"><strong><?= $assignment['max_points'] ?></strong></td>
                                </tr>
                                <tr>
                                    <td>90%</td>
                                    <td class="text-end"><?= round($assignment['max_points'] * 0.9, 1) ?></td>
                                </tr>
                                <tr>
                                    <td>80%</td>
                                    <td class="text-end"><?= round($assignment['max_points'] * 0.8, 1) ?></td>
                                </tr>
                                <tr>
                                    <td>70%</td>
                                    <td class="text-end"><?= round($assignment['max_points'] * 0.7, 1) ?></td>
                                </tr>
                                <tr>
                                    <td>60%</td>
                                    <td class="text-end"><?= round($assignment['max_points'] * 0.6, 1) ?></td>
                                </tr>
                                <tr>
                                    <td>50%</td>
                                    <td class="text-end"><?= round($assignment['max_points'] * 0.5, 1) ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <?php if ($submission['status'] == 'late'): ?>
                        <div class="alert alert-warning p-2">
                            <small><i class="bi bi-exclamation-triangle"></i> <strong>Late Submission</strong></small>
                        </div>
                    <?php endif; ?>

                    <?php if ($submission['status'] == 'resubmitted'): ?>
                        <div class="alert alert-info p-2">
                            <small><i class="bi bi-arrow-repeat"></i> <strong>Resubmitted</strong></small>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Previous Grade (if exists) -->
            <?php if ($submission['status'] == 'graded'): ?>
                <div class="card shadow-sm">
                    <div class="card-header bg-warning text-dark">
                        <h6 class="mb-0"><i class="bi bi-clock-history"></i> Current Grade</h6>
                    </div>
                    <div class="card-body">
                        <h3 class="text-center mb-3">
                            <?= $submission['grade'] ?> / <?= $assignment['max_points'] ?>
                        </h3>
                        <p class="text-center text-muted">
                            (<?= round(($submission['grade'] / $assignment['max_points']) * 100, 1) ?>%)
                        </p>
                        <?php if ($submission['feedback']): ?>
                            <hr>
                            <h6>Previous Feedback:</h6>
                            <p class="small"><?= nl2br(esc($submission['feedback'])) ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
// Real-time percentage calculator
document.getElementById('grade').addEventListener('input', function() {
    const grade = parseFloat(this.value);
    const maxPoints = <?= $assignment['max_points'] ?>;
    
    if (!isNaN(grade) && grade >= 0) {
        const percentage = (grade / maxPoints * 100).toFixed(1);
        const formText = this.nextElementSibling;
        
        if (grade > maxPoints) {
            formText.textContent = `Grade cannot exceed ${maxPoints} points`;
            formText.className = 'form-text text-danger';
        } else {
            formText.textContent = `${percentage}% of total points`;
            formText.className = 'form-text text-success';
        }
    }
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?= view('templates/header', ['title' => $title]) ?>

<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('teacher/courses') ?>">My Courses</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('assignments/index/' . $course['id']) ?>"><?= esc($course['title']) ?> - Assignments</a></li>
                    <li class="breadcrumb-item active">Create Assignment</li>
                </ol>
            </nav>
            <h2><i class="bi bi-plus-circle"></i> Create New Assignment</h2>
            <p class="text-muted">Course: <strong><?= esc($course['title']) ?></strong></p>
        </div>
    </div>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($errors) && !empty($errors)): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <ul class="mb-0">
                <?php foreach ($errors as $error): ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach; ?>
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="<?= base_url('assignments/store') ?>" method="post" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <input type="hidden" name="course_id" value="<?= $course['id'] ?>">

                <div class="row">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label for="title" class="form-label">Assignment Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="title" name="title" 
                                   value="<?= old('title') ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" 
                                      rows="5"><?= old('description') ?></textarea>
                            <small class="text-muted">Instructions for students</small>
                        </div>

                        <div class="mb-3">
                            <label for="attachment" class="form-label">
                                <i class="bi bi-paperclip"></i> Attach Assignment File (Optional)
                            </label>
                            <input type="file" class="form-control" id="attachment" name="attachment" 
                                   accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx">
                            <small class="text-muted">
                                Upload PDF, Word, PowerPoint, or Excel files containing assignment details. Max 10MB.
                            </small>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="type" class="form-label">Type <span class="text-danger">*</span></label>
                                <select class="form-select" id="type" name="type" required>
                                    <option value="assignment" <?= old('type') == 'assignment' ? 'selected' : '' ?>>Assignment</option>
                                    <option value="quiz" <?= old('type') == 'quiz' ? 'selected' : '' ?>>Quiz</option>
                                    <option value="exam" <?= old('type') == 'exam' ? 'selected' : '' ?>>Exam</option>
                                    <option value="project" <?= old('type') == 'project' ? 'selected' : '' ?>>Project</option>
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="max_points" class="form-label">Max Points <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="max_points" name="max_points" 
                                       value="<?= old('max_points', 100) ?>" min="1" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="due_date" class="form-label">Due Date & Time</label>
                            <input type="datetime-local" class="form-control" id="due_date" name="due_date" 
                                   value="<?= old('due_date') ?>">
                            <small class="text-muted">Leave blank for no deadline</small>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card bg-light">
                            <div class="card-header">
                                <h6 class="mb-0"><i class="bi bi-gear"></i> Submission Settings</h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="allowed_file_types" class="form-label">Allowed File Types <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="allowed_file_types" name="allowed_file_types" 
                                           value="<?= old('allowed_file_types', 'pdf,doc,docx') ?>" required>
                                    <small class="text-muted">Comma-separated (e.g., pdf,doc,docx)</small>
                                </div>

                                <div class="mb-3">
                                    <label for="max_file_size" class="form-label">Max File Size (KB) <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="max_file_size" name="max_file_size" 
                                           value="<?= old('max_file_size', 10240) ?>" min="100" required>
                                    <small class="text-muted">10240 KB = 10 MB</small>
                                </div>

                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" id="allow_late_submission" 
                                           name="allow_late_submission" value="1" <?= old('allow_late_submission') ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="allow_late_submission">
                                        Allow Late Submission
                                    </label>
                                </div>

                                <div class="mb-3">
                                    <label for="max_attempts" class="form-label">Max Submission Attempts <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="max_attempts" name="max_attempts" 
                                           value="<?= old('max_attempts', 1) ?>" min="1" max="10" required>
                                    <small class="text-muted">Number of times a student can submit (1-10)</small>
                                </div>

                                <div class="mb-3">
                                    <label for="extended_deadline" class="form-label">Extended Deadline</label>
                                    <input type="datetime-local" class="form-control" id="extended_deadline" name="extended_deadline" 
                                           value="<?= old('extended_deadline') ?>">
                                    <small class="text-muted">Allow submissions past due date until this time (will be marked as late)</small>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-info mt-3">
                            <small>
                                <i class="bi bi-info-circle"></i> Students will be able to upload files 
                                according to these settings. Extended deadline submissions will be marked as late.
                            </small>
                        </div>
                    </div>
                </div>

                <hr>

                <div class="d-flex justify-content-between">
                    <a href="<?= base_url('assignments/index/' . $course['id']) ?>" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle"></i> Create Assignment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

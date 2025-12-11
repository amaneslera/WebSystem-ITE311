<?= view('templates/header', ['title' => $title]) ?>

<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('course/view/' . $assignment['course_id']) ?>"><?= esc($assignment['course_title']) ?></a></li>
                    <li class="breadcrumb-item active"><?= esc($assignment['title']) ?></li>
                </ol>
            </nav>
            <h2><i class="bi bi-file-earmark-text"></i> <?= esc($assignment['title']) ?></h2>
            <span class="badge bg-<?= $assignment['type'] == 'exam' ? 'danger' : ($assignment['type'] == 'quiz' ? 'info' : 'secondary') ?>">
                <?= ucfirst($assignment['type']) ?>
            </span>
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
                    <h5 class="mb-0"><i class="bi bi-info-circle"></i> Assignment Details</h5>
                </div>
                <div class="card-body">
                    <?php if ($assignment['description']): ?>
                        <h6>Instructions:</h6>
                        <p><?= nl2br(esc($assignment['description'])) ?></p>
                    <?php endif; ?>

                    <?php if (!empty($assignment['attachment_file'])): ?>
                        <div class="alert alert-info">
                            <i class="bi bi-paperclip"></i> <strong>Attached File:</strong>
                            <a href="<?= base_url('assignments/download-attachment/' . $assignment['id']) ?>" class="btn btn-sm btn-primary ms-2">
                                <i class="bi bi-download"></i> Download <?= esc($assignment['attachment_file']) ?>
                            </a>
                        </div>
                    <?php endif; ?>

                    <div class="row mt-4">
                        <div class="col-md-6">
                            <p><strong>Due Date:</strong> 
                                <?= $assignment['due_date'] ? date('F d, Y h:i A', strtotime($assignment['due_date'])) : 'No deadline' ?>
                            </p>
                            <p><strong>Maximum Points:</strong> <?= $assignment['max_points'] ?></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Allowed File Types:</strong> <?= esc($assignment['allowed_file_types']) ?></p>
                            <p><strong>Max File Size:</strong> <?= $assignment['max_file_size'] ?> KB</p>
                        </div>
                    </div>

                    <div class="alert alert-secondary">
                        <p class="mb-1"><strong>Submission Settings:</strong></p>
                        <ul class="mb-0">
                            <li>Maximum Attempts: <?= $assignment['max_attempts'] ?></li>
                            <?php if ($assignment['allow_late_submission']): ?>
                                <li><i class="bi bi-clock-history"></i> Late submissions allowed</li>
                            <?php endif; ?>
                            <?php if ($assignment['extended_deadline']): ?>
                                <li><i class="bi bi-calendar-plus"></i> Extended deadline: <?= date('F d, Y h:i A', strtotime($assignment['extended_deadline'])) ?></li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Submission Section -->
            <?php if ($submission): ?>
                <!-- Already Submitted -->
                <div class="card shadow-sm">
                    <div class="card-header bg-<?= $submission['status'] == 'graded' ? 'success' : 'warning' ?> text-white">
                        <h5 class="mb-0"><i class="bi bi-check-circle"></i> Your Submission</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Submitted:</strong> <?= date('F d, Y h:i A', strtotime($submission['submitted_at'])) ?></p>
                        <p><strong>File:</strong> <?= esc($submission['file_name']) ?></p>
                        <p><strong>Status:</strong> 
                            <span class="badge bg-<?= 
                                $submission['status'] == 'graded' ? 'success' : 
                                ($submission['status'] == 'late' ? 'warning' : 
                                ($submission['status'] == 'resubmitted' ? 'info' : 'secondary')) 
                            ?>">
                                <?= ucfirst($submission['status']) ?>
                            </span>
                        </p>

                        <?php if ($submission['status'] == 'graded'): ?>
                            <hr>
                            <h5>Grade Results</h5>
                            <div class="alert alert-success">
                                <h3 class="mb-0">
                                    <i class="bi bi-trophy"></i> 
                                    <?= $submission['grade'] ?> / <?= $assignment['max_points'] ?> points
                                    (<?= round(($submission['grade'] / $assignment['max_points']) * 100, 1) ?>%)
                                </h3>
                            </div>

                            <?php if ($submission['feedback']): ?>
                                <h6>Instructor Feedback:</h6>
                                <div class="alert alert-light">
                                    <?= nl2br(esc($submission['feedback'])) ?>
                                </div>
                            <?php endif; ?>

                            <p class="text-muted"><small>Graded on: <?= date('F d, Y h:i A', strtotime($submission['graded_at'])) ?></small></p>
                        <?php else: ?>
                            <div class="alert alert-info">
                                <i class="bi bi-hourglass-split"></i> Your submission is pending review by the instructor.
                            </div>
                        <?php endif; ?>

                        <a href="<?= base_url('assignments/download/' . $submission['id']) ?>" class="btn btn-primary">
                            <i class="bi bi-download"></i> Download My Submission
                        </a>

                        <?php if ($can_submit['can_submit'] && $assignment['status'] == 'active'): ?>
                            <hr>
                            <h6>Resubmit Assignment</h6>
                            <form action="<?= base_url('assignments/submit/' . $assignment['id']) ?>" method="post" enctype="multipart/form-data">
                                <?= csrf_field() ?>
                                <div class="mb-3">
                                    <input type="file" name="file" class="form-control" required>
                                    <small class="text-muted">This will replace your previous submission.</small>
                                </div>
                                <button type="submit" class="btn btn-warning">
                                    <i class="bi bi-arrow-repeat"></i> Resubmit
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            <?php else: ?>
                <!-- Submit Assignment Form -->
                <?php if ($can_submit['can_submit']): ?>
                    <div class="card shadow-sm">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0"><i class="bi bi-upload"></i> Submit Your Work</h5>
                        </div>
                        <div class="card-body">
                            <?php if (isset($can_submit['attempts_remaining'])): ?>
                                <div class="alert alert-<?= $can_submit['attempts_remaining'] <= 1 ? 'warning' : 'info' ?>">
                                    <i class="bi bi-info-circle"></i> 
                                    <strong>Attempts Remaining:</strong> <?= $can_submit['attempts_remaining'] ?> of <?= $assignment['max_attempts'] ?>
                                </div>
                            <?php endif; ?>

                            <?php if (isset($can_submit['is_late']) && $can_submit['is_late']): ?>
                                <div class="alert alert-warning">
                                    <i class="bi bi-exclamation-triangle"></i> 
                                    <strong>Notice:</strong> This submission will be marked as late.
                                </div>
                            <?php endif; ?>

                            <form action="<?= base_url('assignments/submit/' . $assignment['id']) ?>" method="post" enctype="multipart/form-data">
                                <?= csrf_field() ?>
                                
                                <div class="mb-3">
                                    <label for="file" class="form-label">Select File to Upload <span class="text-danger">*</span></label>
                                    <input type="file" class="form-control" id="file" name="file" required>
                                    <small class="text-muted">
                                        Allowed types: <?= esc($assignment['allowed_file_types']) ?><br>
                                        Maximum size: <?= $assignment['max_file_size'] ?> KB (<?= round($assignment['max_file_size'] / 1024, 1) ?> MB)
                                    </small>
                                </div>

                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle"></i> 
                                    Make sure your file is properly named and contains all required content before submitting.
                                </div>

                                <button type="submit" class="btn btn-success btn-lg">
                                    <i class="bi bi-check-circle"></i> Submit Assignment
                                </button>
                            </form>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- Cannot Submit -->
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <div class="alert alert-danger">
                                <i class="bi bi-x-circle"></i> 
                                <strong>Submission Closed:</strong> <?= $can_submit['reason'] ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>

        <!-- Sidebar -->
        <div class="col-md-4">
            <!-- Course Info -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-secondary text-white">
                    <h6 class="mb-0"><i class="bi bi-book"></i> Course</h6>
                </div>
                <div class="card-body">
                    <h5><?= esc($assignment['course_title']) ?></h5>
                    <p class="text-muted"><?= esc($assignment['course_code']) ?></p>
                    <p><strong>Instructor:</strong><br><?= esc($assignment['teacher_name']) ?></p>
                </div>
            </div>

            <!-- Assignment Status -->
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0"><i class="bi bi-clipboard-check"></i> Status</h6>
                </div>
                <div class="card-body">
                    <?php if ($submission): ?>
                        <div class="text-center">
                            <i class="bi bi-check-circle text-success" style="font-size: 3rem;"></i>
                            <h5 class="mt-2">Submitted</h5>
                            <?php if ($submission['status'] == 'graded'): ?>
                                <p class="text-success mb-0">Graded</p>
                            <?php else: ?>
                                <p class="text-warning mb-0">Awaiting Grade</p>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center">
                            <i class="bi bi-clock-history text-warning" style="font-size: 3rem;"></i>
                            <h5 class="mt-2">Not Submitted</h5>
                            <?php if ($assignment['due_date']): ?>
                                <?php
                                $now = time();
                                $due = strtotime($assignment['due_date']);
                                $diff = $due - $now;
                                ?>
                                <?php if ($diff > 0): ?>
                                    <p class="text-muted mb-0">
                                        <?php
                                        $days = floor($diff / 86400);
                                        $hours = floor(($diff % 86400) / 3600);
                                        if ($days > 0) {
                                            echo $days . ' day' . ($days > 1 ? 's' : '') . ' remaining';
                                        } elseif ($hours > 0) {
                                            echo $hours . ' hour' . ($hours > 1 ? 's' : '') . ' remaining';
                                        } else {
                                            echo 'Due soon!';
                                        }
                                        ?>
                                    </p>
                                <?php else: ?>
                                    <p class="text-danger mb-0">Overdue</p>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

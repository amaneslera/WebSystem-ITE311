<?= view('templates/header', ['title' => $title]) ?>

<div class="container-fluid py-4">
    <!-- Course Header -->
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-2">
                            <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
                            <li class="breadcrumb-item active"><?= esc($course['title']) ?></li>
                        </ol>
                    </nav>
                    <h2 class="mb-2"><i class="bi bi-book"></i> <?= esc($course['title']) ?></h2>
                    <p class="text-muted mb-2"><?= esc($course['course_code']) ?> • <?= esc($course['description']) ?></p>
                    
                    <?php if (!empty($course['room']) || !empty($course['schedule_days']) || !empty($course['schedule_time'])): ?>
                        <div class="alert alert-info py-2 mb-3">
                            <div class="d-flex flex-wrap gap-3">
                                <?php if (!empty($course['room'])): ?>
                                    <span><i class="bi bi-geo-alt-fill"></i> <strong>Room:</strong> <?= esc($course['room']) ?></span>
                                <?php endif; ?>
                                <?php if (!empty($course['schedule_days'])): ?>
                                    <span><i class="bi bi-calendar-week"></i> <strong>Days:</strong> <?= esc($course['schedule_days']) ?></span>
                                <?php endif; ?>
                                <?php if (!empty($course['schedule_time'])): ?>
                                    <span><i class="bi bi-clock"></i> <strong>Time:</strong> <?= esc($course['schedule_time']) ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <div class="d-flex gap-3">
                        <small class="text-muted"><i class="bi bi-person"></i> Instructor: <strong><?= esc($course['teacher_name']) ?></strong></small>
                        <small class="text-muted"><i class="bi bi-people"></i> Students: <strong><?= $student_count ?></strong></small>
                        <?php if ($role == 'student' && isset($enrollment) && !empty($enrollment['enrollment_date'])): ?>
                            <small class="text-muted"><i class="bi bi-calendar-check"></i> Enrolled: <?= date('M d, Y', strtotime($enrollment['enrollment_date'])) ?></small>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-md-4 text-end">
                    <?php if ($role == 'teacher' || $role == 'admin'): ?>
                        <div class="btn-group-vertical w-100">
                            <a href="<?= base_url('materials/upload/' . $course['id']) ?>" class="btn btn-success mb-2">
                                <i class="bi bi-cloud-upload"></i> Upload Material
                            </a>
                            <a href="<?= base_url('assignments/create/' . $course['id']) ?>" class="btn btn-warning mb-2">
                                <i class="bi bi-plus-circle"></i> Create Assignment
                            </a>
                            <a href="<?= base_url('teacher/courses/' . $course['id'] . '/students') ?>" class="btn btn-info">
                                <i class="bi bi-people"></i> Manage Students
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <!-- Left Column: Course Content -->
        <div class="col-lg-8">
            <!-- Course Materials Section -->
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-folder"></i> Course Materials</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($materials)): ?>
                        <p class="text-muted text-center py-4">No materials uploaded yet</p>
                    <?php else: ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($materials as $material): ?>
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <i class="bi bi-file-earmark-pdf text-danger"></i>
                                        <strong><?= esc($material['file_name']) ?></strong>
                                        <br>
                                        <small class="text-muted">
                                            Uploaded: <?= date('M d, Y', strtotime($material['created_at'])) ?>
                                        </small>
                                    </div>
                                    <div>
                                        <a href="<?= base_url('materials/download/' . $material['id']) ?>" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-download"></i> Download
                                        </a>
                                        <?php if ($role == 'teacher' || $role == 'admin'): ?>
                                            <a href="<?= base_url('materials/delete/' . $material['id']) ?>" class="btn btn-sm btn-outline-danger"
                                               onclick="return confirm('Delete this material?')">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Assignments Section -->
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="bi bi-file-earmark-text"></i> Assignments</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($assignments)): ?>
                        <p class="text-muted text-center py-4">No assignments posted yet</p>
                    <?php else: ?>
                        <div class="accordion" id="assignmentsAccordion">
                            <?php foreach ($assignments as $index => $assignment): ?>
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="heading<?= $assignment['id'] ?>">
                                        <button class="accordion-button <?= $index > 0 ? 'collapsed' : '' ?>" type="button" 
                                                data-bs-toggle="collapse" data-bs-target="#collapse<?= $assignment['id'] ?>">
                                            <div class="w-100 d-flex justify-content-between align-items-center pe-3">
                                                <span>
                                                    <i class="bi bi-<?= $assignment['type'] == 'exam' ? 'clipboard-check' : 'file-text' ?>"></i>
                                                    <strong><?= esc($assignment['title']) ?></strong>
                                                    <span class="badge bg-<?= $assignment['type'] == 'exam' ? 'danger' : ($assignment['type'] == 'quiz' ? 'info' : 'secondary') ?> ms-2">
                                                        <?= ucfirst($assignment['type']) ?>
                                                    </span>
                                                </span>
                                                <span class="text-muted small">
                                                    <i class="bi bi-calendar"></i> Due: <?= $assignment['due_date'] ? date('M d, Y', strtotime($assignment['due_date'])) : 'No deadline' ?>
                                                </span>
                                            </div>
                                        </button>
                                    </h2>
                                    <div id="collapse<?= $assignment['id'] ?>" class="accordion-collapse collapse <?= $index == 0 ? 'show' : '' ?>" 
                                         data-bs-parent="#assignmentsAccordion">
                                        <div class="accordion-body">
                                            <p><?= nl2br(esc($assignment['description'])) ?></p>
                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <small class="text-muted"><i class="bi bi-trophy"></i> Points: <strong><?= $assignment['max_points'] ?></strong></small>
                                                </div>
                                                <div class="col-md-6">
                                                    <small class="text-muted"><i class="bi bi-file-earmark"></i> Allowed: <?= esc($assignment['allowed_file_types']) ?></small>
                                                </div>
                                            </div>

                                            <?php if ($role == 'student'): ?>
                                                <?php 
                                                $submission = isset($my_submissions[$assignment['id']]) ? $my_submissions[$assignment['id']] : null;
                                                ?>
                                                <?php if ($submission): ?>
                                                    <div class="alert alert-<?= $submission['status'] == 'graded' ? 'success' : 'info' ?>">
                                                        <strong><i class="bi bi-check-circle"></i> Submitted:</strong> <?= date('M d, Y h:i A', strtotime($submission['submitted_at'])) ?>
                                                        <?php if ($submission['status'] == 'graded'): ?>
                                                            <br><strong>Grade:</strong> <?= $submission['grade'] ?>/<?= $assignment['max_points'] ?>
                                                            <?php if ($submission['feedback']): ?>
                                                                <br><strong>Feedback:</strong> <?= nl2br(esc($submission['feedback'])) ?>
                                                            <?php endif; ?>
                                                        <?php else: ?>
                                                            <br><small>Status: <?= ucfirst($submission['status']) ?></small>
                                                        <?php endif; ?>
                                                    </div>
                                                    <a href="<?= base_url('assignments/download/' . $submission['id']) ?>" class="btn btn-sm btn-outline-primary">
                                                        <i class="bi bi-download"></i> Download My Submission
                                                    </a>
                                                <?php else: ?>
                                                    <form action="<?= base_url('assignments/submit/' . $assignment['id']) ?>" method="post" enctype="multipart/form-data">
                                                        <?= csrf_field() ?>
                                                        <div class="mb-3">
                                                            <label class="form-label">Upload Your Work</label>
                                                            <input type="file" name="file" class="form-control" required>
                                                            <small class="text-muted">Max size: <?= $assignment['max_file_size'] ?> KB</small>
                                                        </div>
                                                        <button type="submit" class="btn btn-primary">
                                                            <i class="bi bi-upload"></i> Submit Assignment
                                                        </button>
                                                    </form>
                                                <?php endif; ?>
                                            <?php elseif ($role == 'teacher' || $role == 'admin'): ?>
                                                <div class="btn-group">
                                                    <a href="<?= base_url('assignments/submissions/' . $assignment['id']) ?>" class="btn btn-sm btn-primary">
                                                        <i class="bi bi-files"></i> View Submissions
                                                    </a>
                                                    <a href="<?= base_url('assignments/edit/' . $assignment['id']) ?>" class="btn btn-sm btn-warning">
                                                        <i class="bi bi-pencil"></i> Edit
                                                    </a>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Right Column: Sidebar -->
        <div class="col-lg-4">
            <!-- Student Performance (for students) -->
            <?php if ($role == 'student'): ?>
                <div class="card mb-4 shadow-sm">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="bi bi-graph-up"></i> My Performance</h5>
                    </div>
                    <div class="card-body">
                        <?php
                        $submitted_count = count($my_submissions ?? []);
                        $total_assignments = count($assignments);
                        $graded_count = 0;
                        $total_score = 0;
                        $total_possible = 0;
                        
                        if (isset($my_submissions)) {
                            foreach ($my_submissions as $sub) {
                                if ($sub['status'] == 'graded' && $sub['grade'] !== null) {
                                    $graded_count++;
                                    $total_score += $sub['grade'];
                                    // Find assignment max points
                                    foreach ($assignments as $asg) {
                                        if ($asg['id'] == $sub['assignment_id']) {
                                            $total_possible += $asg['max_points'];
                                            break;
                                        }
                                    }
                                }
                            }
                        }
                        
                        $average = $total_possible > 0 ? round(($total_score / $total_possible) * 100, 1) : 0;
                        ?>
                        
                        <div class="mb-3">
                            <h6>Assignments Submitted</h6>
                            <div class="progress" style="height: 25px;">
                                <div class="progress-bar bg-info" style="width: <?= $total_assignments > 0 ? ($submitted_count/$total_assignments)*100 : 0 ?>%">
                                    <?= $submitted_count ?> / <?= $total_assignments ?>
                                </div>
                            </div>
                        </div>
                        
                        <?php if ($graded_count > 0): ?>
                            <div class="mb-3">
                                <h6>Overall Grade Average</h6>
                                <div class="progress" style="height: 25px;">
                                    <div class="progress-bar bg-<?= $average >= 75 ? 'success' : ($average >= 60 ? 'warning' : 'danger') ?>" 
                                         style="width: <?= $average ?>%">
                                        <?= $average ?>%
                                    </div>
                                </div>
                                <small class="text-muted"><?= $total_score ?> / <?= $total_possible ?> points</small>
                            </div>
                        <?php endif; ?>
                        
                        <div class="text-center">
                            <div class="row">
                                <div class="col-6">
                                    <h3 class="text-primary"><?= $submitted_count ?></h3>
                                    <small class="text-muted">Submitted</small>
                                </div>
                                <div class="col-6">
                                    <h3 class="text-success"><?= $graded_count ?></h3>
                                    <small class="text-muted">Graded</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Teacher Quick Actions -->
            <?php if ($role == 'teacher' || $role == 'admin'): ?>
                <div class="card mb-4 shadow-sm">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="bi bi-lightning"></i> Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="<?= base_url('materials/upload/' . $course['id']) ?>" class="btn btn-outline-success">
                                <i class="bi bi-cloud-upload"></i> Upload Material
                            </a>
                            <a href="<?= base_url('assignments/create/' . $course['id']) ?>" class="btn btn-outline-warning">
                                <i class="bi bi-plus-circle"></i> Create Assignment
                            </a>
                            <a href="<?= base_url('teacher/courses/' . $course['id'] . '/students') ?>" class="btn btn-outline-primary">
                                <i class="bi bi-people"></i> Manage Students
                            </a>
                        </div>
                        
                        <?php if (isset($pending_count) && $pending_count > 0): ?>
                            <div class="alert alert-warning mt-3 mb-0">
                                <i class="bi bi-exclamation-triangle"></i> 
                                <strong><?= $pending_count ?></strong> submission(s) pending review
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Course Info -->
            <div class="card shadow-sm">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"><i class="bi bi-info-circle"></i> Course Information</h5>
                </div>
                <div class="card-body">
                    <p><strong>Instructor:</strong><br><?= esc($course['teacher_name']) ?></p>
                    <p><strong>Email:</strong><br><a href="mailto:<?= esc($course['teacher_email']) ?>"><?= esc($course['teacher_email']) ?></a></p>
                    <p><strong>Course Code:</strong><br><?= esc($course['course_code']) ?></p>
                    <p><strong>Enrolled Students:</strong><br><?= $student_count ?> students</p>
                    <p><strong>Materials:</strong><br><?= count($materials) ?> files</p>
                    <p><strong>Assignments:</strong><br><?= count($assignments) ?> items</p>
                </div>
            </div>
        </div>
    </div>
</div>

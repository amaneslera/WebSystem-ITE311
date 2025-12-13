<?= view('templates/header', ['title' => 'Academic Transcript']) ?>

<style>
    .transcript-section {
        margin-bottom: 30px;
    }
    .course-item {
        border-left: 4px solid #0d6efd;
        transition: all 0.2s;
    }
    .course-item:hover {
        transform: translateX(5px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    .completed-course {
        border-left-color: #198754;
        background-color: #f0f9f4;
    }
    .enrolled-course {
        border-left-color: #0d6efd;
        background-color: #f0f5ff;
    }
    .remaining-course {
        border-left-color: #ffc107;
        background-color: #fffbf0;
    }
    .stat-badge {
        font-size: 1.5rem;
        padding: 10px 20px;
    }
    .grade-badge {
        font-size: 1.1rem;
        min-width: 60px;
    }
    .print-btn {
        cursor: pointer;
    }
    @media print {
        .no-print {
            display: none;
        }
        .course-item {
            break-inside: avoid;
        }
    }
</style>

<div class="container-fluid mt-4">
    <!-- Page Header -->
    <div class="row mb-4 no-print">
        <div class="col">
            <h2 class="mb-0">
                <i class="bi bi-file-earmark-text"></i> Academic Transcript
            </h2>
            <p class="text-muted">Your complete academic journey and progress</p>
        </div>
        <div class="col text-end">
            <button class="btn btn-outline-primary me-2 print-btn" onclick="window.print()">
                <i class="bi bi-printer"></i> Print Transcript
            </button>
            <a href="<?= base_url('dashboard') ?>" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Back to Dashboard
            </a>
        </div>
    </div>

    <!-- Student Information Card -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="bi bi-person-badge"></i> Student Information</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Name:</strong> <?= esc($student['name']) ?></p>
                    <p><strong>Student ID:</strong> <?= esc($student['student_id'] ?? 'Not assigned') ?></p>
                    <p><strong>Email:</strong> <?= esc($student['email']) ?></p>
                </div>
                <div class="col-md-6">
                    <p><strong>Year Level:</strong> 
                        <?php if ($student['year_level']): ?>
                            <span class="badge bg-info"><?= esc($student['year_level']) ?></span>
                        <?php else: ?>
                            <span class="text-muted">Not specified</span>
                        <?php endif; ?>
                    </p>
                    <p><strong>Program:</strong> <?= esc($student['program_name'] ?? 'Not specified') ?></p>
                    <p><strong>Status:</strong> <span class="badge bg-success">Active</span></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Summary -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-white bg-success shadow-sm">
                <div class="card-body text-center">
                    <i class="bi bi-check-circle" style="font-size: 2rem;"></i>
                    <h2 class="stat-badge"><?= $totalCompleted ?></h2>
                    <p class="mb-0">Completed Courses</p>
                    <small><?= $totalCreditsCompleted ?> units</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-primary shadow-sm">
                <div class="card-body text-center">
                    <i class="bi bi-book" style="font-size: 2rem;"></i>
                    <h2 class="stat-badge"><?= $totalEnrolled ?></h2>
                    <p class="mb-0">Currently Enrolled</p>
                    <small><?= $totalCreditsEnrolled ?> units</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-warning shadow-sm">
                <div class="card-body text-center">
                    <i class="bi bi-hourglass-split" style="font-size: 2rem;"></i>
                    <h2 class="stat-badge"><?= $totalRemaining ?></h2>
                    <p class="mb-0">Available Courses</p>
                    <small>Not yet taken</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-info shadow-sm">
                <div class="card-body text-center">
                    <i class="bi bi-calculator" style="font-size: 2rem;"></i>
                    <h2 class="stat-badge"><?= $totalCreditsCompleted + $totalCreditsEnrolled ?></h2>
                    <p class="mb-0">Total Credits</p>
                    <small>Completed + Enrolled</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Completed Courses (Transfer Credits) -->
    <div class="transcript-section">
        <div class="card shadow-sm">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">
                    <i class="bi bi-check-circle-fill"></i> Completed Courses
                    <span class="badge bg-light text-success float-end"><?= $totalCompleted ?> courses</span>
                </h5>
            </div>
            <div class="card-body">
                <?php if (empty($completedCourses)): ?>
                    <div class="alert alert-info mb-0">
                        <i class="bi bi-info-circle"></i> No completed courses recorded. Transfer credits will appear here.
                    </div>
                <?php else: ?>
                    <div class="row">
                        <?php foreach ($completedCourses as $course): ?>
                            <div class="col-md-6 mb-3">
                                <div class="card course-item completed-course h-100">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h6 class="mb-0"><?= esc($course['course_name']) ?></h6>
                                            <?php if ($course['grade']): ?>
                                                <span class="badge grade-badge bg-success"><?= esc($course['grade']) ?></span>
                                            <?php endif; ?>
                                        </div>
                                        <p class="text-muted mb-1"><small><strong>Code:</strong> <?= esc($course['course_code']) ?></small></p>
                                        <p class="text-muted mb-1"><small><strong>Completed:</strong> <?= date('M d, Y', strtotime($course['completed_date'])) ?></small></p>
                                        <?php if ($course['institution']): ?>
                                            <p class="text-muted mb-1"><small><strong>Institution:</strong> <?= esc($course['institution']) ?></small></p>
                                        <?php endif; ?>
                                        <?php if ($course['notes']): ?>
                                            <p class="text-muted mb-0"><small><i class="bi bi-sticky"></i> <?= esc($course['notes']) ?></small></p>
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

    <!-- Currently Enrolled Courses -->
    <div class="transcript-section">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="bi bi-book-half"></i> Currently Enrolled Courses
                    <span class="badge bg-light text-primary float-end"><?= $totalEnrolled ?> courses</span>
                </h5>
            </div>
            <div class="card-body">
                <?php if (empty($enrolledCourses)): ?>
                    <div class="alert alert-info mb-0">
                        <i class="bi bi-info-circle"></i> You are not currently enrolled in any courses. Visit the <a href="<?= base_url('courses/search') ?>">course search</a> to enroll.
                    </div>
                <?php else: ?>
                    <div class="row">
                        <?php foreach ($enrolledCourses as $course): ?>
                            <div class="col-md-6 mb-3">
                                <div class="card course-item enrolled-course h-100">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h6 class="mb-0"><?= esc($course['course_title']) ?></h6>
                                            <span class="badge bg-primary">In Progress</span>
                                        </div>
                                        <p class="text-muted mb-1"><small><strong>Instructor:</strong> <?= esc($course['teacher_name'] ?? 'Not assigned') ?></small></p>
                                        <p class="text-muted mb-1"><small><strong>Enrolled:</strong> <?= date('M d, Y', strtotime($course['enrollment_date'])) ?></small></p>
                                        <?php if ($course['description']): ?>
                                            <p class="text-muted mb-0"><small><?= esc(substr($course['description'], 0, 100)) ?><?= strlen($course['description']) > 100 ? '...' : '' ?></small></p>
                                        <?php endif; ?>
                                        <div class="mt-2">
                                            <a href="<?= base_url('course/view/' . $course['course_id']) ?>" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-eye"></i> View Course
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Available Courses -->
    <div class="transcript-section">
        <div class="card shadow-sm">
            <div class="card-header bg-warning">
                <h5 class="mb-0">
                    <i class="bi bi-list-check"></i> Available Courses
                    <span class="badge bg-light text-dark float-end"><?= $totalRemaining ?> courses</span>
                </h5>
            </div>
            <div class="card-body">
                <?php if (empty($remainingCourses)): ?>
                    <div class="alert alert-success mb-0">
                        <i class="bi bi-trophy"></i> Congratulations! You have completed or are enrolled in all available courses.
                    </div>
                <?php else: ?>
                    <p class="text-muted mb-3">These courses are available for enrollment:</p>
                    <div class="row">
                        <?php 
                        $displayCount = 0;
                        foreach ($remainingCourses as $course): 
                            if ($displayCount >= 6) break; // Show only first 6
                            $displayCount++;
                        ?>
                            <div class="col-md-6 mb-3">
                                <div class="card course-item remaining-course h-100">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h6 class="mb-0"><?= esc($course['title']) ?></h6>
                                            <span class="badge bg-warning text-dark"><?= esc($course['units'] ?? 3) ?> units</span>
                                        </div>
                                        <p class="text-muted mb-1"><small><strong>Code:</strong> <?= esc($course['course_code']) ?></small></p>
                                        <?php if ($course['year_level']): ?>
                                            <p class="text-muted mb-1"><small><strong>Year Level:</strong> <?= esc($course['year_level']) ?></small></p>
                                        <?php endif; ?>
                                        <?php if ($course['description']): ?>
                                            <p class="text-muted mb-0"><small><?= esc(substr($course['description'], 0, 80)) ?><?= strlen($course['description']) > 80 ? '...' : '' ?></small></p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <?php if (count($remainingCourses) > 6): ?>
                        <div class="text-center mt-3">
                            <a href="<?= base_url('courses/search') ?>" class="btn btn-warning">
                                <i class="bi bi-search"></i> View All Available Courses (<?= count($remainingCourses) ?>)
                            </a>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Academic Progress Summary -->
    <div class="card shadow-sm border-info">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0"><i class="bi bi-graph-up"></i> Academic Progress Summary</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h6 class="text-info">Course Progress</h6>
                    <div class="progress mb-3" style="height: 30px;">
                        <?php 
                        $totalCourses = $totalCompleted + $totalEnrolled + $totalRemaining;
                        $progressPercent = $totalCourses > 0 ? (($totalCompleted + $totalEnrolled) / $totalCourses * 100) : 0;
                        ?>
                        <div class="progress-bar bg-success" style="width: <?= ($totalCompleted / $totalCourses * 100) ?>%">
                            Completed: <?= $totalCompleted ?>
                        </div>
                        <div class="progress-bar bg-primary" style="width: <?= ($totalEnrolled / $totalCourses * 100) ?>%">
                            Enrolled: <?= $totalEnrolled ?>
                        </div>
                    </div>
                    <p class="text-muted"><small><?= number_format($progressPercent, 1) ?>% of all courses completed or in progress</small></p>
                </div>
                <div class="col-md-6">
                    <h6 class="text-info">Key Metrics</h6>
                    <ul class="list-unstyled">
                        <li><i class="bi bi-check-circle text-success"></i> <strong><?= $totalCreditsCompleted ?></strong> credits completed</li>
                        <li><i class="bi bi-book text-primary"></i> <strong><?= $totalCreditsEnrolled ?></strong> credits in progress</li>
                        <li><i class="bi bi-calculator text-info"></i> <strong><?= $totalCreditsCompleted + $totalCreditsEnrolled ?></strong> total credits earned/pending</li>
                        <?php if ($totalCompleted > 0): ?>
                            <li><i class="bi bi-mortarboard text-warning"></i> <strong><?= $totalCompleted ?></strong> transfer credits recognized</li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="text-center mt-4 mb-4 text-muted no-print">
        <small>
            <i class="bi bi-info-circle"></i> This transcript is generated on <?= date('F d, Y') ?> at <?= date('h:i A') ?>
        </small>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

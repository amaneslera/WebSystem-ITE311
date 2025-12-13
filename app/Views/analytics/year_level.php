<?= view('templates/header', ['title' => 'Year Level Analytics']) ?>

<style>
    .stat-card {
        border-left: 4px solid #0d6efd;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 16px rgba(0,0,0,0.1);
    }
    .chart-container {
        position: relative;
        height: 300px;
    }
    .progress-bar-animated {
        animation: progress-bar-stripes 1s linear infinite;
    }
</style>

<div class="container-fluid mt-4">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col">
            <h2 class="mb-0">
                <i class="bi bi-bar-chart-line"></i> Year Level Analytics
            </h2>
            <p class="text-muted">Comprehensive analysis of student and course distribution by year level</p>
        </div>
        <div class="col text-end">
            <a href="<?= base_url('dashboard') ?>" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Back to Dashboard
            </a>
        </div>
    </div>

    <!-- Summary Statistics -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card stat-card shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Total Students</h6>
                            <h2 class="mb-0"><?= number_format($totalStudents) ?></h2>
                        </div>
                        <i class="bi bi-people text-primary" style="font-size: 3rem;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stat-card shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Total Courses</h6>
                            <h2 class="mb-0"><?= number_format($totalCourses) ?></h2>
                        </div>
                        <i class="bi bi-book text-success" style="font-size: 3rem;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stat-card shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Total Enrollments</h6>
                            <h2 class="mb-0"><?= number_format($totalEnrollments) ?></h2>
                        </div>
                        <i class="bi bi-clipboard-check text-info" style="font-size: 3rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Student Distribution -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-people"></i> Student Distribution by Year Level</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($studentYearLevels)): ?>
                        <p class="text-muted text-center py-4">No student data available</p>
                    <?php else: ?>
                        <?php 
                        $colors = ['#0d6efd', '#198754', '#ffc107', '#dc3545', '#6c757d'];
                        $colorIndex = 0;
                        foreach ($studentYearLevels as $level): 
                            $percentage = $totalStudents > 0 ? ($level['count'] / $totalStudents * 100) : 0;
                            $color = $colors[$colorIndex % count($colors)];
                            $colorIndex++;
                        ?>
                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-1">
                                    <span><strong><?= esc($level['year_level']) ?></strong></span>
                                    <span class="text-muted"><?= $level['count'] ?> students (<?= number_format($percentage, 1) ?>%)</span>
                                </div>
                                <div class="progress" style="height: 25px;">
                                    <div class="progress-bar" role="progressbar" 
                                         style="width: <?= $percentage ?>%; background-color: <?= $color ?>;"
                                         aria-valuenow="<?= $percentage ?>" aria-valuemin="0" aria-valuemax="100">
                                        <?= number_format($percentage, 1) ?>%
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="bi bi-book"></i> Course Distribution by Year Level</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($courseYearLevels)): ?>
                        <p class="text-muted text-center py-4">No course data available</p>
                    <?php else: ?>
                        <?php 
                        $colors = ['#198754', '#0d6efd', '#ffc107', '#dc3545', '#6c757d'];
                        $colorIndex = 0;
                        foreach ($courseYearLevels as $level): 
                            $percentage = $totalCourses > 0 ? ($level['count'] / $totalCourses * 100) : 0;
                            $color = $colors[$colorIndex % count($colors)];
                            $colorIndex++;
                        ?>
                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-1">
                                    <span><strong><?= esc($level['year_level']) ?></strong></span>
                                    <span class="text-muted"><?= $level['count'] ?> courses (<?= number_format($percentage, 1) ?>%)</span>
                                </div>
                                <div class="progress" style="height: 25px;">
                                    <div class="progress-bar" role="progressbar" 
                                         style="width: <?= $percentage ?>%; background-color: <?= $color ?>;"
                                         aria-valuenow="<?= $percentage ?>" aria-valuemin="0" aria-valuemax="100">
                                        <?= number_format($percentage, 1) ?>%
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Enrollment Statistics -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="bi bi-clipboard-data"></i> Enrollment Statistics by Year Level</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped">
                            <thead class="table-light">
                                <tr>
                                    <th>Year Level</th>
                                    <th class="text-center">Enrolled Students</th>
                                    <th class="text-center">Total Enrollments</th>
                                    <th class="text-center">Avg Courses/Student</th>
                                    <th class="text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($enrollmentStats)): ?>
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">No enrollment data available</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($enrollmentStats as $stat): ?>
                                        <tr>
                                            <td><strong><?= esc($stat['year_level']) ?></strong></td>
                                            <td class="text-center">
                                                <span class="badge bg-primary"><?= number_format($stat['enrolled_students']) ?></span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-success"><?= number_format($stat['total_enrollments']) ?></span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-info"><?= number_format($stat['avg_courses_per_student'], 2) ?></span>
                                            </td>
                                            <td class="text-center">
                                                <?php if ($stat['avg_courses_per_student'] >= 5): ?>
                                                    <span class="badge bg-success">High Engagement</span>
                                                <?php elseif ($stat['avg_courses_per_student'] >= 3): ?>
                                                    <span class="badge bg-info">Moderate</span>
                                                <?php elseif ($stat['avg_courses_per_student'] > 0): ?>
                                                    <span class="badge bg-warning">Low</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary">No Enrollments</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Course Enrollment Averages -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-warning">
                    <h5 class="mb-0"><i class="bi bi-graph-up"></i> Average Students per Course by Year Level</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Year Level</th>
                                    <th class="text-center">Total Courses</th>
                                    <th class="text-center">Total Enrollments</th>
                                    <th class="text-center">Avg Students/Course</th>
                                    <th class="text-center">Capacity Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($avgEnrollmentsByYear)): ?>
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">No data available</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($avgEnrollmentsByYear as $avg): ?>
                                        <tr>
                                            <td><strong><?= esc($avg['year_level']) ?></strong></td>
                                            <td class="text-center"><?= number_format($avg['course_count']) ?></td>
                                            <td class="text-center"><?= number_format($avg['total_enrollments']) ?></td>
                                            <td class="text-center">
                                                <strong class="text-primary"><?= number_format($avg['avg_students_per_course'], 1) ?></strong>
                                            </td>
                                            <td class="text-center">
                                                <?php if ($avg['avg_students_per_course'] >= 30): ?>
                                                    <span class="badge bg-danger">High Capacity</span>
                                                <?php elseif ($avg['avg_students_per_course'] >= 20): ?>
                                                    <span class="badge bg-warning">Moderate</span>
                                                <?php elseif ($avg['avg_students_per_course'] > 0): ?>
                                                    <span class="badge bg-success">Good Balance</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary">No Enrollments</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Insights -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-primary">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-lightbulb"></i> Key Insights</h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> Total of <strong><?= number_format($totalStudents) ?> active students</strong> across all year levels</li>
                        <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> <strong><?= number_format($totalCourses) ?> active courses</strong> available for enrollment</li>
                        <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> Average of <strong><?= $totalStudents > 0 ? number_format($totalEnrollments / $totalStudents, 2) : 0 ?> enrollments per student</strong></li>
                        <?php if (!empty($studentYearLevels)): ?>
                            <?php 
                            $maxYear = array_reduce($studentYearLevels, function($carry, $item) {
                                return ($carry === null || $item['count'] > $carry['count']) ? $item : $carry;
                            });
                            ?>
                            <li class="mb-2"><i class="bi bi-star-fill text-warning me-2"></i> Most populated year level: <strong><?= esc($maxYear['year_level']) ?></strong> with <?= $maxYear['count'] ?> students</li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

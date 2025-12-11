<?= view('templates/header', ['title' => $title]) ?>

<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('teacher/courses') ?>">My Courses</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('assignments/index/' . $assignment['course_id']) ?>">Assignments</a></li>
                    <li class="breadcrumb-item active"><?= esc($assignment['title']) ?> - Submissions</li>
                </ol>
            </nav>
            <h2><i class="bi bi-file-earmark-check"></i> Submissions: <?= esc($assignment['title']) ?></h2>
            <p class="text-muted">
                Course: <?= esc($assignment['course_title']) ?> (<?= esc($assignment['course_code']) ?>) | 
                Due: <?= $assignment['due_date'] ? date('F d, Y h:i A', strtotime($assignment['due_date'])) : 'No deadline' ?>
            </p>
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

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm bg-primary text-white">
                <div class="card-body text-center">
                    <h3 class="mb-0"><?= $stats['total_enrolled'] ?></h3>
                    <p class="mb-0">Total Students</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm bg-success text-white">
                <div class="card-body text-center">
                    <h3 class="mb-0"><?= $stats['submitted'] ?></h3>
                    <p class="mb-0">Submitted</p>
                    <small><?= $stats['total_enrolled'] > 0 ? round(($stats['submitted'] / $stats['total_enrolled']) * 100, 1) : 0 ?>%</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm bg-warning text-white">
                <div class="card-body text-center">
                    <h3 class="mb-0"><?= $stats['pending'] ?></h3>
                    <p class="mb-0">Pending Review</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm bg-info text-white">
                <div class="card-body text-center">
                    <h3 class="mb-0"><?= $stats['graded'] ?></h3>
                    <p class="mb-0">Graded</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Buttons -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-outline-primary active" onclick="filterSubmissions('all')">
                    All (<?= count($submissions) ?>)
                </button>
                <button type="button" class="btn btn-outline-warning" onclick="filterSubmissions('pending')">
                    Pending (<?= $stats['pending'] ?>)
                </button>
                <button type="button" class="btn btn-outline-success" onclick="filterSubmissions('graded')">
                    Graded (<?= $stats['graded'] ?>)
                </button>
                <button type="button" class="btn btn-outline-danger" onclick="filterSubmissions('late')">
                    Late (<?= $stats['late'] ?>)
                </button>
            </div>
        </div>
    </div>

    <!-- Submissions Table -->
    <div class="card shadow-sm">
        <div class="card-header bg-dark text-white">
            <h5 class="mb-0"><i class="bi bi-list-check"></i> Student Submissions</h5>
        </div>
        <div class="card-body">
            <?php if (empty($submissions)): ?>
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> No submissions yet.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Student</th>
                                <th>Submitted</th>
                                <th>File</th>
                                <th>Status</th>
                                <th>Grade</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="submissionsTable">
                            <?php foreach ($submissions as $submission): ?>
                            <tr data-status="<?= $submission['status'] ?>">
                                <td>
                                    <strong><?= esc($submission['student_name']) ?></strong><br>
                                    <small class="text-muted"><?= esc($submission['student_email']) ?></small>
                                </td>
                                <td>
                                    <?= date('M d, Y', strtotime($submission['submitted_at'])) ?><br>
                                    <small class="text-muted"><?= date('h:i A', strtotime($submission['submitted_at'])) ?></small>
                                </td>
                                <td>
                                    <small><?= esc($submission['file_name']) ?></small>
                                </td>
                                <td>
                                    <span class="badge bg-<?= 
                                        $submission['status'] == 'graded' ? 'success' : 
                                        ($submission['status'] == 'late' ? 'danger' : 
                                        ($submission['status'] == 'resubmitted' ? 'info' : 'warning')) 
                                    ?>">
                                        <?= ucfirst($submission['status']) ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($submission['status'] == 'graded'): ?>
                                        <strong><?= $submission['grade'] ?> / <?= $assignment['max_points'] ?></strong><br>
                                        <small class="text-muted"><?= round(($submission['grade'] / $assignment['max_points']) * 100, 1) ?>%</small>
                                    <?php else: ?>
                                        <span class="text-muted">Not graded</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="<?= base_url('assignments/download/' . $submission['id']) ?>" 
                                           class="btn btn-outline-primary" 
                                           title="Download">
                                            <i class="bi bi-download"></i>
                                        </a>
                                        <?php if ($submission['status'] != 'graded'): ?>
                                            <a href="<?= base_url('assignments/grade/' . $submission['id']) ?>" 
                                               class="btn btn-outline-success" 
                                               title="Grade">
                                                <i class="bi bi-pencil-square"></i> Grade
                                            </a>
                                        <?php else: ?>
                                            <a href="<?= base_url('assignments/grade/' . $submission['id']) ?>" 
                                               class="btn btn-outline-info" 
                                               title="Edit Grade">
                                                <i class="bi bi-pencil"></i> Edit
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Back Button -->
    <div class="mt-3">
        <a href="<?= base_url('assignments/index/' . $assignment['course_id']) ?>" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Back to Assignments
        </a>
    </div>
</div>

<script>
function filterSubmissions(status) {
    const rows = document.querySelectorAll('#submissionsTable tr');
    const buttons = document.querySelectorAll('.btn-group button');
    
    // Update active button
    buttons.forEach(btn => btn.classList.remove('active'));
    event.target.classList.add('active');
    
    // Filter rows
    rows.forEach(row => {
        const rowStatus = row.getAttribute('data-status');
        if (status === 'all' || rowStatus === status) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

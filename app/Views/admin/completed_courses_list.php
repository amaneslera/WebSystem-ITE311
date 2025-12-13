<?= view('templates/header'); ?>

<div class="container-fluid mt-4">
    <div class="row mb-4">
        <div class="col-12">
            <h2><i class="bi bi-clipboard-check"></i> Manage Student Completed Courses</h2>
            <p class="text-muted">Add or edit transfer credits and completed courses for students</p>
        </div>
    </div>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Students List</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Student ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Year Level</th>
                            <th>Program</th>
                            <th>Completed Courses</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($students as $student): ?>
                            <tr>
                                <td><?= esc($student['id']) ?></td>
                                <td><?= esc($student['name']) ?></td>
                                <td><?= esc($student['email']) ?></td>
                                <td><?= esc($student['year_level'] ?? 'N/A') ?></td>
                                <td><?= esc($student['program'] ?? 'N/A') ?></td>
                                <td>
                                    <span class="badge bg-primary"><?= $student['completed_count'] ?> courses</span>
                                </td>
                                <td>
                                    <a href="<?= base_url('/admin/completed-courses/' . $student['id']) ?>" class="btn btn-sm btn-primary">
                                        <i class="bi bi-pencil-square"></i> Manage
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

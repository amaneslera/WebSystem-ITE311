<?= view('templates/header', ['title' => 'Inactive Users']) ?>

<!-- 
    Inactive/Archived Users View
    This view displays all INACTIVE users (where status = 'inactive')
    These are users who have been deactivated/deleted but not permanently removed
-->

<div class="container my-4">
        <main>
            <!-- Navigation Tabs -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <ul class="nav nav-tabs">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('/users') ?>">
                            <i class="bi bi-people-fill"></i> Active Users
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="<?= base_url('/users/inactive') ?>">
                            <i class="bi bi-archive"></i> Inactive Users
                        </a>
                    </li>
                </ul>
            </div>

            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Inactive Users</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="<?= base_url('/users') ?>" class="btn btn-outline-primary">
                        <i class="bi bi-arrow-left"></i> Back to Active Users
                    </a>
                </div>
            </div>

            <!-- Flash Messages -->
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= session()->getFlashdata('success') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= session()->getFlashdata('error') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <!-- Inactive Users Table -->
            <div class="card">
                <div class="card-body">
                    <?php if (!empty($users)): ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                    <th>Last Updated</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($users as $user): ?>
                                <tr class="table-secondary">
                                    <td><?= $user['id'] ?></td>
                                    <td><?= esc($user['name']) ?></td>
                                    <td><?= esc($user['email']) ?></td>
                                    <td>
                                        <span class="badge bg-<?= $user['role'] == 'admin' ? 'danger' : ($user['role'] == 'teacher' ? 'primary' : 'success') ?>">
                                            <?= ucfirst($user['role']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">
                                            <i class="bi bi-archive"></i> Inactive
                                        </span>
                                    </td>
                                    <td><?= date('M d, Y', strtotime($user['created_at'])) ?></td>
                                    <td><?= date('M d, Y', strtotime($user['updated_at'])) ?></td>
                                    <td>
                                        <?php if ($user['role'] !== 'admin'): ?>
                                            <a href="<?= base_url('/users/activate/'.$user['id']) ?>" 
                                               class="btn btn-sm btn-success" 
                                               title="Activate User"
                                               onclick="return confirm('Activate this user? They will be able to login again.')">
                                                <i class="bi bi-check-circle"></i> Activate
                                            </a>
                                        <?php else: ?>
                                            <button class="btn btn-sm btn-outline-secondary" 
                                                    disabled 
                                                    title="Contact system administrator">
                                                <i class="bi bi-shield-lock"></i> Protected
                                            </button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> No inactive users found. All users are currently active.
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Information Box -->
            <div class="alert alert-info mt-3">
                <h5 class="alert-heading"><i class="bi bi-info-circle"></i> About Inactive Users</h5>
                <p class="mb-0">
                    <strong>Inactive users:</strong> These are users who have been deactivated or deleted. 
                    Their accounts are preserved in the database but they cannot login. 
                    You can restore their access by clicking the "Activate" button.
                </p>
            </div>

            <!-- Statistics -->
            <div class="mt-3">
                <small class="text-muted">
                    <i class="bi bi-graph-up"></i> 
                    <strong>Total Inactive Users:</strong> <?= count($users) ?>
                </small>
            </div>
        </main>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

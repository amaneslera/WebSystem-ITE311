<?= view('templates/header') ?>

<!-- 
    User Management View - Active Users
    This view displays only ACTIVE users (where status = 'active')
    Inactive users can be viewed on the Inactive Users page
-->

<div class="container my-4">
        <main>
            <!-- Navigation Tabs -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <ul class="nav nav-tabs">
                    <li class="nav-item">
                        <a class="nav-link active" href="<?= base_url('/users') ?>">
                            <i class="bi bi-people-fill"></i> Active Users
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('/users/inactive') ?>">
                            <i class="bi bi-archive"></i> Inactive Users
                        </a>
                    </li>
                </ul>
            </div>

            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">User Management</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="<?= base_url('/users/create') ?>" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Add New User
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

            <!-- Users Table -->
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
                                    <th>Updated</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($users as $user): ?>
                                <tr>
                                    <td><?= $user['id'] ?></td>
                                    <td><?= esc($user['name']) ?></td>
                                    <td><?= esc($user['email']) ?></td>
                                    <td>
                                        <span class="badge bg-<?= $user['role'] == 'admin' ? 'danger' : ($user['role'] == 'teacher' ? 'primary' : 'success') ?>">
                                            <?= ucfirst($user['role']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if (isset($user['status'])): ?>
                                            <span class="badge bg-<?= $user['status'] == 'active' ? 'success' : 'secondary' ?>">
                                                <?= ucfirst($user['status']) ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-success">Active</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= date('M d, Y', strtotime($user['created_at'])) ?></td>
                                    <td><?= date('M d, Y', strtotime($user['updated_at'])) ?></td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <?php if ($user['id'] != session()->get('user_id')): ?>
                                                <a href="<?= base_url('/users/edit/'.$user['id']) ?>" 
                                                   class="btn btn-outline-primary" 
                                                   title="Edit">
                                                    <i class="bi bi-pencil"></i> Edit
                                                </a>
                                            <?php else: ?>
                                                <button class="btn btn-outline-secondary" 
                                                        disabled 
                                                        title="You cannot edit your own account here">
                                                    <i class="bi bi-pencil"></i> Edit (Self)
                                                </button>
                                            <?php endif; ?>
                                            
                                            <?php if ($user['role'] !== 'admin'): ?>
                                                <?php 
                                                $userStatus = isset($user['status']) ? $user['status'] : 'active';
                                                ?>
                                                
                                                <?php if ($userStatus == 'active'): ?>
                                                    <a href="<?= base_url('/users/deactivate/'.$user['id']) ?>" 
                                                       class="btn btn-outline-warning" 
                                                       title="Deactivate"
                                                       onclick="return confirm('Are you sure you want to deactivate this user?')">
                                                        <i class="bi bi-pause-circle"></i> Deactivate
                                                    </a>
                                                <?php else: ?>
                                                    <a href="<?= base_url('/users/activate/'.$user['id']) ?>" 
                                                       class="btn btn-outline-success" 
                                                       title="Activate"
                                                       onclick="return confirm('Are you sure you want to activate this user?')">
                                                        <i class="bi bi-play-circle"></i> Activate
                                                    </a>
                                                <?php endif; ?>
                                                
                                                <a href="<?= base_url('/users/delete/'.$user['id']) ?>" 
                                                   class="btn btn-outline-danger" 
                                                   title="Delete"
                                                   onclick="return confirm('Are you sure you want to delete this user?')">
                                                    <i class="bi bi-trash"></i> Delete
                                                </a>
                                            <?php else: ?>
                                            <button class="btn btn-outline-secondary" 
                                                    disabled 
                                                    title="Admin users cannot be deactivated or deleted">
                                                <i class="bi bi-shield-lock"></i> Protected
                                            </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> No users found in the system.
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Legend -->
            <div class="mt-3">
                <small class="text-muted">
                    <i class="bi bi-info-circle"></i> 
                    <strong>Note:</strong> Admin users cannot be deactivated or deleted for security reasons, but can be edited.
                    <br>
                    <span class="badge bg-success">Active</span> Users can login and access the system.
                    <span class="badge bg-secondary">Inactive</span> Users cannot login but data is preserved.
                </small>
            </div>
        </main>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

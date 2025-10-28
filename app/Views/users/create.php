<?= view('templates/header') ?>

<div class="container my-4">
        <main>
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Add New User</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="<?= base_url('/users') ?>" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Back to Users
                    </a>
                </div>
            </div>

            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-body">
                            <?php if ($validation): ?>
                                <div class="alert alert-danger">
                                    <?= $validation->listErrors() ?>
                                </div>
                            <?php endif; ?>

                            <form action="<?= base_url('/users/store') ?>" method="post">
                                <?= csrf_field() ?>

                                <div class="mb-3">
                                    <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control <?= $validation && $validation->hasError('name') ? 'is-invalid' : '' ?>" 
                                           id="name" 
                                           name="name" 
                                           value="<?= old('name') ?>"
                                           placeholder="Enter full name (letters and spaces only)"
                                           required>
                                    <div class="form-text">Only letters and spaces are allowed (e.g., John Doe)</div>
                                    <?php if ($validation && $validation->hasError('name')): ?>
                                        <div class="invalid-feedback">
                                            <?= $validation->getError('name') ?>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <div class="mb-3">
                                    <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                    <input type="email" 
                                           class="form-control <?= $validation && $validation->hasError('email') ? 'is-invalid' : '' ?>" 
                                           id="email" 
                                           name="email" 
                                           value="<?= old('email') ?>"
                                           placeholder="Enter email address"
                                           required>
                                    <?php if ($validation && $validation->hasError('email')): ?>
                                        <div class="invalid-feedback">
                                            <?= $validation->getError('email') ?>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <div class="mb-3">
                                    <label for="role" class="form-label">Role <span class="text-danger">*</span></label>
                                    <select class="form-select <?= $validation && $validation->hasError('role') ? 'is-invalid' : '' ?>" 
                                            id="role" 
                                            name="role" 
                                            required>
                                        <option value="">Select Role</option>
                                        <option value="student" <?= old('role') == 'student' ? 'selected' : '' ?>>Student</option>
                                        <option value="teacher" <?= old('role') == 'teacher' ? 'selected' : '' ?>>Teacher</option>
                                        <option value="admin" <?= old('role') == 'admin' ? 'selected' : '' ?>>Admin</option>
                                    </select>
                                    <?php if ($validation && $validation->hasError('role')): ?>
                                        <div class="invalid-feedback">
                                            <?= $validation->getError('role') ?>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <div class="mb-3">
                                    <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                                    <input type="password" 
                                           class="form-control <?= $validation && $validation->hasError('password') ? 'is-invalid' : '' ?>" 
                                           id="password" 
                                           name="password"
                                           placeholder="Enter password (min. 6 characters)"
                                           required>
                                    <?php if ($validation && $validation->hasError('password')): ?>
                                        <div class="invalid-feedback">
                                            <?= $validation->getError('password') ?>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <div class="mb-3">
                                    <label for="password_confirm" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                                    <input type="password" 
                                           class="form-control <?= $validation && $validation->hasError('password_confirm') ? 'is-invalid' : '' ?>" 
                                           id="password_confirm" 
                                           name="password_confirm"
                                           placeholder="Re-enter password"
                                           required>
                                    <?php if ($validation && $validation->hasError('password_confirm')): ?>
                                        <div class="invalid-feedback">
                                            <?= $validation->getError('password_confirm') ?>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                    <a href="<?= base_url('/users') ?>" class="btn btn-secondary">Cancel</a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-save"></i> Create User
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Help Panel -->
                <div class="col-md-4">
                    <div class="card bg-light">
                        <div class="card-body">
                            <h5 class="card-title"><i class="bi bi-info-circle"></i> Guidelines</h5>
                            <ul class="small">
                                <li><strong>Name:</strong> Must contain only letters and spaces (no numbers or special characters)</li>
                                <li><strong>Email:</strong> Must be a valid email address and unique in the system</li>
                                <li><strong>Password:</strong> Minimum 6 characters required</li>
                                <li><strong>Role:</strong> Determines user permissions in the system</li>
                            </ul>
                            <hr>
                            <h6>Role Permissions:</h6>
                            <ul class="small">
                                <li><span class="badge bg-danger">Admin</span> - Full system access</li>
                                <li><span class="badge bg-primary">Teacher</span> - Manage courses and students</li>
                                <li><span class="badge bg-success">Student</span> - View courses and materials</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </main>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

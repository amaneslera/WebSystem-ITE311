<?= view('templates/header', ['title' => 'Edit User']) ?>

<div class="container my-4">
        <main>
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Edit User</h1>
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

                            <!-- Show warning if editing admin -->
                            <?php if ($user['role'] === 'admin'): ?>
                                <div class="alert alert-warning">
                                    <i class="bi bi-exclamation-triangle"></i> 
                                    <strong>Warning:</strong> You are editing an admin user. Admin users cannot be deleted but can be edited.
                                </div>
                            <?php endif; ?>

                            <form action="<?= base_url('/users/update/'.$user['id']) ?>" method="post">
                                <?= csrf_field() ?>

                                <div class="mb-3">
                                    <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control <?= $validation && $validation->hasError('name') ? 'is-invalid' : '' ?>" 
                                           id="name" 
                                           name="name" 
                                           value="<?= old('name', $user['name']) ?>"
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
                                           value="<?= old('email', $user['email']) ?>"
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
                                            onchange="toggleStudentFields()"
                                            required>
                                        <option value="">Select Role</option>
                                        <option value="student" <?= old('role', $user['role']) == 'student' ? 'selected' : '' ?>>Student</option>
                                        <option value="teacher" <?= old('role', $user['role']) == 'teacher' ? 'selected' : '' ?>>Teacher</option>
                                        <option value="admin" <?= old('role', $user['role']) == 'admin' ? 'selected' : '' ?>>Admin</option>
                                    </select>
                                    <div class="form-text">
                                        <?php if ($user['role'] === 'admin'): ?>
                                            <i class="bi bi-shield-lock text-warning"></i> Admin role can be changed, but admin users cannot be deleted.
                                        <?php endif; ?>
                                    </div>
                                    <?php if ($validation && $validation->hasError('role')): ?>
                                        <div class="invalid-feedback">
                                            <?= $validation->getError('role') ?>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <!-- Student-specific fields -->
                                <div id="studentFields" style="display: <?= old('role', $user['role']) == 'student' ? 'block' : 'none' ?>;">
                                    <div class="mb-3">
                                        <label for="student_id" class="form-label">Student ID</label>
                                        <input type="text" 
                                               class="form-control" 
                                               id="student_id" 
                                               name="student_id" 
                                               value="<?= old('student_id', $user['student_id'] ?? '') ?>"
                                               placeholder="Enter student ID (e.g., 2024-00001)">
                                        <div class="form-text">Optional unique identifier for the student</div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="program_id" class="form-label">Program</label>
                                        <select class="form-select" id="program_id" name="program_id">
                                            <option value="">Select Program</option>
                                            <?php 
                                            $db = \Config\Database::connect();
                                            $programs = $db->table('course_programs')->get()->getResultArray();
                                            foreach ($programs as $program): 
                                            ?>
                                                <option value="<?= $program['id'] ?>" <?= old('program_id', $user['program_id'] ?? '') == $program['id'] ? 'selected' : '' ?>>
                                                    <?= $program['name'] ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <div class="form-text">Student's academic program</div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="year_level" class="form-label">Year Level</label>
                                        <select class="form-select" id="year_level" name="year_level">
                                            <option value="">Select Year Level</option>
                                            <option value="1" <?= old('year_level', $user['year_level'] ?? '') == '1' ? 'selected' : '' ?>>1st Year</option>
                                            <option value="2" <?= old('year_level', $user['year_level'] ?? '') == '2' ? 'selected' : '' ?>>2nd Year</option>
                                            <option value="3" <?= old('year_level', $user['year_level'] ?? '') == '3' ? 'selected' : '' ?>>3rd Year</option>
                                            <option value="4" <?= old('year_level', $user['year_level'] ?? '') == '4' ? 'selected' : '' ?>>4th Year</option>
                                        </select>
                                        <div class="form-text">Current year level in the program</div>
                                    </div>
                                </div>

                                <hr class="my-4">
                                <h5 class="mb-3">Change Password (Optional)</h5>
                                <p class="text-muted small">Leave blank to keep the current password</p>

                                <div class="mb-3">
                                    <label for="password" class="form-label">New Password</label>
                                    <input type="password" 
                                           class="form-control <?= $validation && $validation->hasError('password') ? 'is-invalid' : '' ?>" 
                                           id="password" 
                                           name="password"
                                           placeholder="Enter new password (min. 6 characters)">
                                    <?php if ($validation && $validation->hasError('password')): ?>
                                        <div class="invalid-feedback">
                                            <?= $validation->getError('password') ?>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <div class="mb-3">
                                    <label for="password_confirm" class="form-label">Confirm New Password</label>
                                    <input type="password" 
                                           class="form-control <?= $validation && $validation->hasError('password_confirm') ? 'is-invalid' : '' ?>" 
                                           id="password_confirm" 
                                           name="password_confirm"
                                           placeholder="Re-enter new password">
                                    <?php if ($validation && $validation->hasError('password_confirm')): ?>
                                        <div class="invalid-feedback">
                                            <?= $validation->getError('password_confirm') ?>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                    <a href="<?= base_url('/users') ?>" class="btn btn-secondary">Cancel</a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-save"></i> Update User
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
                            <h5 class="card-title"><i class="bi bi-info-circle"></i> User Information</h5>
                            <ul class="list-unstyled small">
                                <li class="mb-2"><strong>User ID:</strong> <?= $user['id'] ?></li>
                                <li class="mb-2"><strong>Created:</strong> <?= date('M d, Y', strtotime($user['created_at'])) ?></li>
                                <li class="mb-2"><strong>Last Updated:</strong> <?= date('M d, Y', strtotime($user['updated_at'])) ?></li>
                                <li class="mb-2">
                                    <strong>Current Role:</strong> 
                                    <span class="badge bg-<?= $user['role'] == 'admin' ? 'danger' : ($user['role'] == 'teacher' ? 'primary' : 'success') ?>">
                                        <?= ucfirst($user['role']) ?>
                                    </span>
                                </li>
                            </ul>
                            <hr>
                            <h6>Edit Guidelines:</h6>
                            <ul class="small">
                                <li><strong>Name:</strong> Only letters and spaces allowed</li>
                                <li><strong>Email:</strong> Must be unique in the system</li>
                                <li><strong>Password:</strong> Leave blank to keep current password</li>
                                <li><strong>Role:</strong> Can be changed even for admin users</li>
                            </ul>
                            <hr>
                            <div class="alert alert-info small mb-0">
                                <i class="bi bi-shield-check"></i> Admin accounts are protected from deletion but can be edited.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
function toggleStudentFields() {
    const role = document.getElementById('role').value;
    const studentFields = document.getElementById('studentFields');
    
    if (role === 'student') {
        studentFields.style.display = 'block';
    } else {
        studentFields.style.display = 'none';
    }
}
</script>
</body>
</html>

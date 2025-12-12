<?= view('templates/header', ['title' => 'My Course Invitations']) ?>

<style>
    .invitation-card {
        border-radius: 15px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        transition: transform 0.3s;
        margin-bottom: 20px;
    }
    .invitation-card:hover {
        transform: translateY(-5px);
    }
</style>

<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-envelope-paper"></i> My Course Invitations</h2>
        <a href="<?= base_url('dashboard') ?>" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Back to Dashboard
        </a>
    </div>

    <!-- Flash Messages -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle"></i> <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle"></i> <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Invitations List -->
    <?php if (empty($invitations)): ?>
        <div class="card invitation-card">
            <div class="card-body text-center py-5">
                <i class="bi bi-inbox" style="font-size: 4rem; color: #ccc;"></i>
                <h4 class="mt-3">No Pending Invitations</h4>
                <p class="text-muted">You don't have any pending course invitations at the moment.</p>
                <a href="<?= base_url('dashboard') ?>" class="btn btn-primary mt-3">Go to Dashboard</a>
            </div>
        </div>
    <?php else: ?>
            <?php foreach ($invitations as $invitation): ?>
                <div class="card invitation-card">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-7">
                                <h4 class="card-title mb-3">
                                    <i class="bi bi-book-fill text-primary"></i> 
                                    <?= esc($invitation['course_title']) ?>
                                </h4>
                                <h6 class="text-muted mb-3">
                                    <i class="bi bi-code-square"></i> Course Code: 
                                    <span class="badge bg-primary"><?= esc($invitation['course_code']) ?></span>
                                </h6>
                                
                                <p class="mb-2">
                                    <i class="bi bi-person-circle"></i> 
                                    <strong>Invited by:</strong> 
                                    <?= esc($invitation['invited_by_name']) ?> 
                                    <span class="badge bg-secondary ms-1"><?= ucfirst(esc($invitation['invited_by_role'])) ?></span>
                                </p>
                                
                                <p class="text-muted mb-0">
                                    <i class="bi bi-calendar3"></i> 
                                    <small><?= date('F j, Y g:i A', strtotime($invitation['created_at'])) ?></small>
                                </p>
                            </div>
                            
                            <div class="col-md-5 text-center">
                                <button class="btn btn-success btn-lg w-100 mb-2" onclick="acceptInvitation(<?= $invitation['id'] ?>)">
                                    <i class="bi bi-check-circle-fill"></i> Accept
                                </button>
                                <button class="btn btn-outline-danger w-100" onclick="declineInvitation(<?= $invitation['id'] ?>)">
                                    <i class="bi bi-x-circle"></i> Decline
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
        function acceptInvitation(invitationId) {
            if (!confirm('Do you want to accept this course invitation and enroll in the course?')) {
                return;
            }
            
            const btn = event.target;
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Accepting...';
            
            $.ajax({
                url: '<?= base_url('enrollment/accept-invitation/') ?>' + invitationId,
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                data: {
                    response_message: '',
                    <?= csrf_token() ?>: '<?= csrf_hash() ?>'
                },
                success: function(response) {
                    if (response.success) {
                        alert('Success! You are now enrolled in the course.');
                        window.location.href = '<?= base_url('dashboard') ?>';
                    } else {
                        alert('Error: ' + (response.message || 'Failed to accept invitation'));
                        location.reload();
                    }
                },
                error: function(xhr) {
                    const response = xhr.responseJSON || {};
                    alert('Error: ' + (response.message || 'Failed to accept invitation'));
                    location.reload();
                }
            });
        }

        function declineInvitation(invitationId) {
            if (!confirm('Are you sure you want to decline this course invitation?')) {
                return;
            }
            
            const btn = event.target;
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Declining...';
            
            $.ajax({
                url: '<?= base_url('enrollment/decline-invitation/') ?>' + invitationId,
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                data: {
                    response_message: '',
                    <?= csrf_token() ?>: '<?= csrf_hash() ?>'
                },
                success: function(response) {
                    if (response.success) {
                        alert('Invitation declined.');
                        window.location.reload();
                    } else {
                        alert('Error: ' + (response.message || 'Failed to decline invitation'));
                        location.reload();
                    }
                },
                error: function(xhr) {
                    const response = xhr.responseJSON || {};
                    alert('Error: ' + (response.message || 'Failed to decline invitation'));
                    location.reload();
                }
            });
        }
    </script>
</body>
</html>

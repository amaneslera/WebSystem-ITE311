<?= view('templates/header', ['title' => 'Pending Enrollment Requests']) ?>

<style>
    .request-card {
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        transition: transform 0.2s, box-shadow 0.2s;
        margin-bottom: 20px;
    }
    .request-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    .badge-request {
        font-size: 0.85rem;
        padding: 5px 10px;
    }
</style>

<div class="container-fluid mt-4">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col">
            <h2 class="mb-0">
                <i class="bi bi-person-raised-hand"></i> Pending Enrollment Requests
            </h2>
            <p class="text-muted">Review and respond to student enrollment requests</p>
        </div>
        <div class="col text-end">
            <a href="<?= base_url('dashboard') ?>" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Back to Dashboard
            </a>
        </div>
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
                <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= session()->getFlashdata('error') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Requests List -->
        <?php if (empty($requests)): ?>
            <div class="card request-card shadow-sm">
                <div class="card-body text-center py-5">
                    <i class="bi bi-inbox text-muted" style="font-size: 4rem;"></i>
                    <h4 class="mt-3">No Pending Requests</h4>
                    <p class="text-muted">There are no pending enrollment requests at the moment.</p>
                    <a href="<?= base_url('dashboard') ?>" class="btn btn-primary mt-3">
                        <i class="bi bi-house"></i> Go to Dashboard
                    </a>
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($requests as $request): ?>
                <div class="card request-card shadow-sm">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <h5 class="card-title">
                                    <i class="bi bi-book"></i> <?= esc($request['course_title']) ?>
                                    <span class="badge bg-primary badge-request ms-2">
                                        <?= esc($request['course_code']) ?>
                                    </span>
                                </h5>
                                
                                <div class="mb-2">
                                    <p class="text-muted mb-1">
                                        <i class="bi bi-person"></i> <strong>Student:</strong> 
                                        <?= esc($request['student_name']) ?>
                                    </p>
                                    <p class="text-muted mb-1">
                                        <i class="bi bi-envelope"></i> <strong>Email:</strong> 
                                        <?= esc($request['student_email']) ?>
                                    </p>
                                    <p class="text-muted mb-1">
                                        <i class="bi bi-card-text"></i> <strong>Student ID:</strong> 
                                        <?= esc($request['student_id']) ?>
                                    </p>
                                </div>
                                
                                <?php if (!empty($request['message'])): ?>
                                    <div class="alert alert-info mt-3">
                                        <strong><i class="bi bi-chat-left-quote"></i> Student's Message:</strong>
                                        <p class="mb-0 mt-2"><?= esc($request['message']) ?></p>
                                    </div>
                                <?php endif; ?>
                                
                                <p class="text-muted mb-0">
                                    <i class="bi bi-clock"></i> <small>Requested: <?= date('F j, Y g:i A', strtotime($request['created_at'])) ?></small>
                                </p>
                            </div>
                            
                            <div class="col-md-4 text-end d-flex flex-column justify-content-center">
                                <button class="btn btn-success mb-2" onclick="respondToRequest(<?= $request['id'] ?>, 'accept')">
                                    <i class="bi bi-check-circle"></i> Approve
                                </button>
                                <button class="btn btn-danger" onclick="respondToRequest(<?= $request['id'] ?>, 'decline')">
                                    <i class="bi bi-x-circle"></i> Decline
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Response Modal -->
    <div class="modal fade" id="responseModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="responseModalTitle"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="responseMessage" class="form-label">Message to Student (Optional):</label>
                        <textarea class="form-control" id="responseMessage" rows="3" placeholder="Add a message to the student..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn" id="confirmResponseBtn"></button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
        let currentRequestId = null;
        let currentAction = null;

        function respondToRequest(requestId, action) {
            currentRequestId = requestId;
            currentAction = action;

            const modal = new bootstrap.Modal(document.getElementById('responseModal'));
            const modalTitle = document.getElementById('responseModalTitle');
            const confirmBtn = document.getElementById('confirmResponseBtn');
            
            if (action === 'accept') {
                modalTitle.textContent = 'Approve Enrollment Request';
                confirmBtn.textContent = 'Approve Request';
                confirmBtn.className = 'btn btn-success';
            } else {
                modalTitle.textContent = 'Decline Enrollment Request';
                confirmBtn.textContent = 'Decline Request';
                confirmBtn.className = 'btn btn-danger';
            }
            
            document.getElementById('responseMessage').value = '';
            modal.show();
        }

        document.getElementById('confirmResponseBtn').addEventListener('click', function() {
            const responseMessage = document.getElementById('responseMessage').value;
            const url = `/enrollment/${currentAction}-request/${currentRequestId}`;
            
            this.disabled = true;
            this.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';
            
            $.ajax({
                url: url,
                method: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({
                    response_message: responseMessage
                }),
                success: function(response) {
                    if (response.success) {
                        window.location.reload();
                    } else {
                        alert('Error: ' + (response.message || 'Unknown error occurred'));
                        location.reload();
                    }
                },
                error: function(xhr) {
                    const response = xhr.responseJSON || {};
                    alert('Error: ' + (response.message || 'Failed to process request'));
                    location.reload();
                }
            });
        });
</script>
</body>
</html>

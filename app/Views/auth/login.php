<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .login-container {
            max-width: 400px;
            margin: 50px auto;
        }
        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .login-card {
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        #loginSuccessMessage {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1050;
            display: none;
            color: white !important; /* Make text white */
            font-weight: 500;
            font-size: 1.1rem;
            padding: 15px;
        }
        .success-icon {
            margin-right: 10px;
        }
    </style>
</head>
<body>
    <!-- Success Message Alert - Changed from alert-success to custom styling -->
    <div id="loginSuccessMessage" class="text-center">
        <i class="bi bi-check-circle-fill success-icon"></i>
        <span id="successText"></span>
    </div>

    <div class="container login-container">
        <div class="login-header">
            <h1 class="display-6">E-Learning System</h1>
            <p class="lead">Login to your account</p>
        </div>
        
        <div class="card login-card">
            <div class="card-body">
                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger">
                        <?= session()->getFlashdata('error') ?>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($validation)): ?>
                    <div class="alert alert-danger">
                        <?= $validation->listErrors() ?>
                    </div>
                <?php endif; ?>
                
                <form id="loginForm" method="post">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email address</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Login</button>
                    </div>
                </form>
            </div>
            <div class="card-footer text-center">
                <p class="mb-0">Don't have an account? <a href="<?= base_url('/register') ?>">Register</a></p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#loginForm').on('submit', function(e) {
                e.preventDefault();
                
                $.ajax({
                    url: '<?= base_url('/login') ?>',
                    type: 'POST',
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            // Set success message content
                            $('#successText').text(response.message);
                            
                            // Set background color and text color based on user role
                            if (response.user.role === 'admin') {
                                // Dark background for admin with white text
                                $('#loginSuccessMessage').css({
                                    'background-color': '#212529',
                                    'color': 'white'
                                });
                            } else if (response.user.role === 'teacher') {
                                // Blue background for teacher with white text
                                $('#loginSuccessMessage').css({
                                    'background-color': '#0d6efd',
                                    'color': 'white'
                                });
                            } else {
                                // Green background for student with white text
                                $('#loginSuccessMessage').css({
                                    'background-color': '#198754',
                                    'color': 'white'
                                });
                            }
                            
                            // Show success message with animation
                            $('#loginSuccessMessage').fadeIn();
                            
                            // Change button text and disable it
                            $('button[type="submit"]').html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Redirecting...').prop('disabled', true);
                            
                            // Wait 2 seconds then redirect
                            setTimeout(function() {
                                window.location.href = response.redirect.startsWith('/') ? 
                                    '<?= base_url() ?>' + response.redirect.substring(1) : 
                                    '<?= base_url() ?>' + response.redirect;
                            }, 2000);
                        } else {
                            // Display error message
                            let errorHtml = '<div class="alert alert-danger">' + response.message + '</div>';
                            
                            if (response.errors) {
                                errorHtml = '<div class="alert alert-danger"><ul>';
                                for (let field in response.errors) {
                                    errorHtml += '<li>' + response.errors[field] + '</li>';
                                }
                                errorHtml += '</ul></div>';
                            }
                            
                            $('.alert').remove();
                            $('#loginForm').before(errorHtml);
                        }
                    },
                    error: function() {
                        $('.alert').remove();
                        $('#loginForm').before('<div class="alert alert-danger">An error occurred. Please try again.</div>');
                    }
                });
            });
        });
    </script>
</body>
</html>

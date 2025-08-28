
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Connection Test</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .status-success { color: #28a745; }
        .status-error { color: #dc3545; }
        .status-warning { color: #ffc107; }
    </style>
</head>
<body>
<div class="container mt-5">
    <div class="row">
        <div class="col-md-12">
            <h1 class="mb-4">Database Connection Test</h1>
            
            <!-- Connection Status -->
            <div class="card mb-4">
                <div class="card-header">
                    <h3>Database Connection Status</h3>
                </div>
                <div class="card-body">
                    <?php if($connection_status === 'SUCCESS'): ?>
                        <h4 class="status-success">✅ <?= $connection_status ?></h4>
                        <p class="status-success"><?= $connection_message ?></p>
                    <?php else: ?>
                        <h4 class="status-error">❌ <?= $connection_status ?></h4>
                        <p class="status-error"><?= $connection_message ?></p>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Users Table Status -->
            <?php if($connection_status === 'SUCCESS'): ?>
                <div class="card mb-4">
                    <div class="card-header">
                        <h3>Users Table Status</h3>
                    </div>
                    <div class="card-body">
                        <?php if(isset($table_exists) && $table_exists): ?>
                            <h4 class="status-success">✅ Users Table EXISTS</h4>
                            <p>Total users in database: <strong><?= $user_count ?></strong></p>
                            
                            <!-- Users List -->
                            <?php if(!empty($users)): ?>
                                <h5 class="mt-4">Users in Database:</h5>
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>ID</th>
                                                <th>Name</th>
                                                <th>Email</th>
                                                <th>Role</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($users as $user): ?>
                                                <tr>
                                                    <td><?= esc($user['id']) ?></td>
                                                    <td><?= esc($user['name']) ?></td>
                                                    <td><?= esc($user['email']) ?></td>
                                                    <td><?= esc($user['role']) ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <p class="status-warning">⚠️ No users found in database.</p>
                            <?php endif; ?>
                            
                            <!-- Table Structure -->
                            <?php if(!empty($table_structure)): ?>
                                <h5 class="mt-4">Users Table Structure:</h5>
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>Field</th>
                                                <th>Type</th>
                                                <th>Max Length</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($table_structure as $field): ?>
                                                <tr>
                                                    <td><?= esc($field->name) ?></td>
                                                    <td><?= esc($field->type) ?></td>
                                                    <td><?= esc($field->max_length) ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                            
                        <?php else: ?>
                            <h4 class="status-error">❌ Users Table NOT FOUND</h4>
                            <p class="status-error">The 'users' table does not exist in the database.</p>
                            
                            <!-- Available Tables -->
                            <?php if(!empty($available_tables)): ?>
                                <h5 class="mt-3">Available Tables:</h5>
                                <ul class="list-group">
                                    <?php foreach($available_tables as $table): ?>
                                        <li class="list-group-item"><?= esc($table) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php else: ?>
                                <p class="status-warning">⚠️ No tables found in database.</p>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
            
            <!-- Navigation Links -->
            <div class="card">
                <div class="card-body">
                    <h5>Navigation</h5>
                    <a href="<?= base_url('register') ?>" class="btn btn-primary me-2">Go to Register</a>
                    <a href="<?= base_url('login') ?>" class="btn btn-success me-2">Go to Login</a>
                    <a href="<?= base_url() ?>" class="btn btn-secondary">Go to Home</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
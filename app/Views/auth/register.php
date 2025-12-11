<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Register</h2>

    <?php if(session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>

    <?php if(isset($validation)): ?>
        <div class="alert alert-danger"><?= $validation->listErrors() ?></div>
    <?php endif; ?>

    <form action="<?= base_url('/register') ?>" method="post">
        <?= csrf_field() ?>
        <div class="mb-3">
            <label for="name" class="form-label">Name</label>
            <input type="text" name="name" id="name" class="form-control" autocomplete="name" value="<?= set_value('name') ?>">
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" name="email" id="email" class="form-control" autocomplete="email" value="<?= set_value('email') ?>">
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" name="password" id="password" class="form-control" autocomplete="new-password">
        </div>
        <div class="mb-3">
            <label for="password_confirm" class="form-label">Confirm Password</label>
            <input type="password" name="password_confirm" id="password_confirm" class="form-control" autocomplete="new-password">
        </div>

        <!-- Student Program and Year Level -->
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="program_id" class="form-label">Program (Optional)</label>
                <select class="form-select" id="program_id" name="program_id">
                    <option value="">Select Program</option>
                    <?php 
                    $db = \Config\Database::connect();
                    $programs = $db->table('course_programs')->where('status', 'active')->get()->getResultArray();
                    foreach ($programs as $program): ?>
                        <option value="<?= $program['id'] ?>" <?= set_value('program_id') == $program['id'] ? 'selected' : '' ?>>
                            <?= esc($program['program_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <div class="form-text">Select your degree program (e.g., BSIT, BSCS)</div>
            </div>

            <div class="col-md-6 mb-3">
                <label for="year_level" class="form-label">Year Level (Optional)</label>
                <select class="form-select" id="year_level" name="year_level">
                    <option value="">Select Year</option>
                    <option value="1" <?= set_value('year_level') == '1' ? 'selected' : '' ?>>1st Year</option>
                    <option value="2" <?= set_value('year_level') == '2' ? 'selected' : '' ?>>2nd Year</option>
                    <option value="3" <?= set_value('year_level') == '3' ? 'selected' : '' ?>>3rd Year</option>
                    <option value="4" <?= set_value('year_level') == '4' ? 'selected' : '' ?>>4th Year</option>
                </select>
                <div class="form-text">Select your current year level</div>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Register</button>
        <a href="<?= base_url('/login') ?>" class="btn btn-link">Login</a>
    </form>
</div>
</body>
</html>

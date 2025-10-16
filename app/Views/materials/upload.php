
<!DOCTYPE html>
<html>
<head>
    <title>Upload Course Material</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h3 class="mb-4">Upload Material for Course #<?= esc($course_id) ?></h3>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>
    <form action="" method="post" enctype="multipart/form-data" class="card p-4 shadow-sm">
        <div class="mb-3">
            <label for="material_file" class="form-label">Select File</label>
            <input type="file" class="form-control" id="material_file" name="material_file" required>
            <div class="form-text">Allowed types: pdf, doc, docx, ppt, pptx, zip, rar, jpg, jpeg, png. Max size: 10MB.</div>
        </div>
        <button type="submit" class="btn btn-primary">Upload</button>
        <a href="<?= base_url('/courses/manage/' . $course_id) ?>" class="btn btn-secondary ms-2">Back</a>
    </form>
</div>
</body>
</html>
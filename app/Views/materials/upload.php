<?= view('templates/header', ['title' => 'Upload Course Material']) ?>

<div class="container mt-5">
    <h3 class="mb-4">Upload Material for Course #<?= esc($course_id) ?></h3>
    
    <?php if (session()->has('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    
    <?php if (session()->has('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    
    <form action="<?= base_url('materials/upload/' . $course_id) ?>" method="post" enctype="multipart/form-data" class="card p-4 shadow-sm">
        <div class="mb-3">
            <label for="material_file" class="form-label">Select File</label>
            <input type="file" class="form-control" id="material_file" name="material_file" required>
            <div class="form-text">Allowed types: pdf, doc, docx, ppt, pptx, zip, rar, jpg, jpeg, png. Max size: 10MB.</div>
        </div>
        <button type="submit" class="btn btn-primary">Upload</button>
        <a href="<?= base_url('/dashboard') ?>" class="btn btn-secondary ms-2">Back</a>
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
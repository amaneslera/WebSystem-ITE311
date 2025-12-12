<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results - <?= esc($search_term) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>
    <div class="container py-5">
        <div class="row mb-4">
            <div class="col">
                <h2><i class="bi bi-search"></i> Search Results</h2>
                <p class="text-muted">
                    <?php if (!empty($search_term)): ?>
                        Showing results for: <strong>"<?= esc($search_term) ?>"</strong>
                    <?php else: ?>
                        Showing all available courses
                    <?php endif; ?>
                </p>
            </div>
            <div class="col-auto">
                <a href="<?= base_url('auth/dashboard') ?>" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Back to Dashboard
                </a>
            </div>
        </div>

        <?php if (!empty($courses)): ?>
            <div class="alert alert-success">
                <i class="bi bi-check-circle"></i> Found <strong><?= count($courses) ?></strong> course(s)
            </div>

            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                <?php foreach ($courses as $course): ?>
                    <div class="col">
                        <div class="card h-100">
                            <div class="card-body">
                                <h5 class="card-title"><?= esc($course['title']) ?></h5>
                                <?php if (!empty($course['course_code'] ?? $course['code'])): ?>
                                    <p class="card-text">
                                        <span class="badge bg-primary"><?= esc($course['course_code'] ?? $course['code']) ?></span>
                                    </p>
                                <?php endif; ?>
                                <p class="card-text text-muted">
                                    <i class="bi bi-person"></i> <?= esc($course['teacher_name']) ?>
                                </p>
                                <p class="card-text"><?= esc(substr($course['description'] ?? 'No description', 0, 100)) ?>...</p>
                            </div>
                            <div class="card-footer bg-transparent">
                                <?php if (session()->get('role') === 'student'): ?>
                                    <button class="btn btn-success btn-sm w-100 enroll-btn" 
                                            data-course-id="<?= $course['id'] ?>"
                                            data-course-title="<?= esc($course['title']) ?>">
                                        <i class="bi bi-plus-circle"></i> Enroll
                                    </button>
                                <?php else: ?>
                                    <a href="<?= base_url('course/view/' . $course['id']) ?>" class="btn btn-info btn-sm w-100">
                                        <i class="bi bi-eye"></i> View Course
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-warning">
                <i class="bi bi-exclamation-triangle"></i> 
                No courses found<?= !empty($search_term) ? ' matching "' . esc($search_term) . '"' : '' ?>.
            </div>
        <?php endif; ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <?php if (session()->get('role') === 'student'): ?>
    <script>
    $(document).ready(function() {
        $('.enroll-btn').click(function(e) {
            e.preventDefault();
            
            const courseId = $(this).data('course-id');
            const courseTitle = $(this).data('course-title');
            const $button = $(this);
            
            $button.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Enrolling...');
            
            $.post('<?= base_url('course/enroll') ?>', { 
                course_id: courseId,
                <?= csrf_token() ?>: '<?= csrf_hash() ?>'
            })
            .done(function(data) {
                if (data.success) {
                    alert('Successfully enrolled in ' + courseTitle + '!');
                    $button.closest('.card').fadeOut();
                } else {
                    alert('Error: ' + data.message);
                    $button.prop('disabled', false).html('<i class="bi bi-plus-circle"></i> Enroll');
                }
            })
            .fail(function() {
                alert('An error occurred. Please try again.');
                $button.prop('disabled', false).html('<i class="bi bi-plus-circle"></i> Enroll');
            });
        });
    });
    </script>
    <?php endif; ?>
</body>
</html>

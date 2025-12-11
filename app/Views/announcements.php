<?= view('templates/header', ['title' => 'Announcements']) ?>
<!DOCTYPE html>
<html>
<head>
    <title>Announcements</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Announcements</h2>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger">
            <?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>
    <?php if (!empty($announcements)): ?>
        <ul class="list-group">
            <?php foreach ($announcements as $announcement): ?>
                <li class="list-group-item">
                    <h5><?= esc($announcement['title']) ?></h5>
                    <p><?= esc($announcement['content']) ?></p>
                    <small class="text-muted">Posted: <?= date('M d, Y H:i', strtotime($announcement['created_at'])) ?></small>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <div class="alert alert-info">No announcements found.</div>
    <?php endif; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</div>
</body>
</html>
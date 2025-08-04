<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My CodeIgniter 4 App</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>

    
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">ITE311</a>
            
        </div>
    </nav>

       <div class="alert alert-success" role="alert">
            Bootstrap is ADDED! You can now use Bootstrap components!!!!!!
       </div>

    
    <div class="container mt-4">
        <?= $this->renderSection('content') ?>
    </div>

</body>
</html>

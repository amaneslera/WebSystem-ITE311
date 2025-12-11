<?= view('templates/header', ['title' => 'Manage Courses']) ?>

<div class="container my-4">
    <main>
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h1 class="h2"><i class="bi bi-book"></i> Manage Courses</h1>
        </div>

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

        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-list-ul"></i> All Courses</h5>
            </div>
            <div class="card-body">
                <?php if (empty($courses)): ?>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> No courses found. Create a new course from the dashboard.
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Course Code</th>
                                    <th>Title</th>
                                    <th>Teacher</th>
                                    <th>Department</th>
                                    <th>Semester</th>
                                    <th>Units</th>
                                    <th>Enrollment</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($courses as $course): ?>
                                    <tr>
                                        <td><strong><?= esc($course['course_code']) ?></strong></td>
                                        <td><?= esc($course['title']) ?></td>
                                        <td>
                                            <?php if (!empty($course['teacher_name'])): ?>
                                                <i class="bi bi-person"></i> <?= esc($course['teacher_name']) ?>
                                            <?php else: ?>
                                                <span class="text-muted">No teacher assigned</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= esc($course['department']) ?></td>
                                        <td><?= esc($course['semester']) ?></td>
                                        <td><?= esc($course['units']) ?></td>
                                        <td>
                                            <span class="badge bg-info">
                                                <?= esc($course['current_enrolled']) ?> / 
                                                <?= esc($course['max_students'] ?? 'Unlimited') ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($course['status'] === 'active'): ?>
                                                <span class="badge bg-success">Active</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Inactive</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="d-flex gap-1">
                                                <button type="button" class="btn btn-outline-primary btn-sm btn-view-details" 
                                                        data-course='<?= json_encode($course) ?>'
                                                        data-bs-toggle="modal" data-bs-target="#courseDetailsModal"
                                                        title="View Details">
                                                    <i class="bi bi-eye"></i>
                                                </button>
                                                <a href="<?= base_url('assignments/index/' . $course['id']) ?>" 
                                                   class="btn btn-outline-success btn-sm" 
                                                   title="Manage Assignments">
                                                    <i class="bi bi-file-earmark-text"></i>
                                                </a>
                                                <button type="button" class="btn btn-outline-warning btn-sm btn-assign-teacher" 
                                                        data-course-id="<?= $course['id'] ?>"
                                                        data-course-title="<?= esc($course['title']) ?>"
                                                        title="Assign Teacher">
                                                    <i class="bi bi-person-plus"></i>
                                                </button>
                                                <a href="<?= base_url('admin/courses/edit/' . $course['id']) ?>" 
                                                   class="btn btn-outline-info btn-sm"
                                                   title="Edit Course">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <button type="button" class="btn btn-outline-danger btn-sm btn-toggle-status" 
                                                        data-course-id="<?= $course['id'] ?>"
                                                        data-current-status="<?= $course['status'] ?>"
                                                        title="Toggle Status">
                                                    <i class="bi bi-power"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Course Details Modal -->
    <div class="modal fade" id="courseDetailsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="bi bi-info-circle"></i> Course Details</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="courseDetailsContent">
                    <!-- Content will be populated by JavaScript -->
                </div>
            </div>
        </div>
    </div>
    </main>
</div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        $(document).ready(function() {
            // View course details
            $('.btn-view-details').on('click', function() {
                const course = $(this).data('course');
                const content = `
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Course Code:</strong> ${course.course_code}</p>
                            <p><strong>Title:</strong> ${course.title}</p>
                            <p><strong>Department:</strong> ${course.department}</p>
                            <p><strong>Semester:</strong> ${course.semester}</p>
                            <p><strong>Units:</strong> ${course.units}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Teacher:</strong> ${course.teacher_name || 'Not assigned'}</p>
                            <p><strong>Max Students:</strong> ${course.max_students || 'Unlimited'}</p>
                            <p><strong>Current Enrolled:</strong> ${course.current_enrolled}</p>
                            <p><strong>Lecture Hours:</strong> ${course.lecture_hours || 'N/A'}</p>
                            <p><strong>Lab Hours:</strong> ${course.lab_hours || 'N/A'}</p>
                        </div>
                    </div>
                    <hr>
                    <p><strong>Description:</strong></p>
                    <p>${course.description}</p>
                `;
                $('#courseDetailsContent').html(content);
            });

            // Toggle course status
            $('.btn-toggle-status').on('click', function() {
                const courseId = $(this).data('course-id');
                const currentStatus = $(this).data('current-status');
                const newStatus = currentStatus === 'active' ? 'inactive' : 'active';
                
                if (confirm(`Are you sure you want to ${newStatus === 'active' ? 'activate' : 'deactivate'} this course?`)) {
                    // TODO: Implement AJAX call to toggle status
                    alert('Status toggle functionality - to be implemented');
                }
            });

            // Assign teacher (redirect to dashboard with modal)
            $('.btn-assign-teacher').on('click', function() {
                alert('Please use the Assign Teacher feature from the dashboard');
                window.location.href = '<?= base_url('/admin/dashboard') ?>';
            });
        });
    </script>

</body>
</html>

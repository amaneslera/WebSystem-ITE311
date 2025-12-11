<?= view('templates/header', ['title' => 'Edit Course']) ?>

<div class="container my-4">
    <main>
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h1 class="h2"><i class="bi bi-pencil-square"></i> Edit Course</h1>
            <a href="<?= base_url('admin/courses') ?>" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to Courses
            </a>
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

        <?php if (session()->getFlashdata('errors')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle"></i> <strong>Validation Errors:</strong>
                <ul class="mb-0 mt-2">
                    <?php foreach (session()->getFlashdata('errors') as $error): ?>
                        <li><?= esc($error) ?></li>
                    <?php endforeach; ?>
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-file-earmark-text"></i> Course Information</h5>
            </div>
            <div class="card-body">
                <form action="<?= base_url('admin/courses/edit/' . $course['id']) ?>" method="post" onsubmit="console.log('Form submitting to:', this.action); return true;">
                    <?= csrf_field() ?>
                    <input type="hidden" name="debug_test" value="form_submitted">

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="course_code" class="form-label">Course Code <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="course_code" name="course_code" 
                                   value="<?= old('course_code', $course['course_code']) ?>" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="title" class="form-label">Course Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="title" name="title" 
                                   value="<?= old('title', $course['title']) ?>" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="4"><?= old('description', $course['description'] ?? '') ?></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="teacher_id" class="form-label">Assign Teacher</label>
                            <select class="form-select" id="teacher_id" name="teacher_id">
                                <option value="">-- No Teacher Assigned --</option>
                                <?php foreach ($teachers as $teacher): ?>
                                    <option value="<?= $teacher['id'] ?>" 
                                            <?= old('teacher_id', $course['teacher_id'] ?? '') == $teacher['id'] ? 'selected' : '' ?>>
                                        <?= esc($teacher['name']) ?> (<?= esc($teacher['email']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="department" class="form-label">Department</label>
                            <input type="text" class="form-control" id="department" name="department" 
                                   value="<?= old('department', $course['department'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="units" class="form-label">Units</label>
                            <input type="number" class="form-control" id="units" name="units" 
                                   value="<?= old('units', $course['units'] ?? 3) ?>" min="1" max="10">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="semester" class="form-label">Semester</label>
                            <select class="form-select" id="semester" name="semester">
                                <option value="">-- Select Semester --</option>
                                <option value="1st" <?= old('semester', $course['semester'] ?? '') == '1st' ? 'selected' : '' ?>>1st Semester</option>
                                <option value="2nd" <?= old('semester', $course['semester'] ?? '') == '2nd' ? 'selected' : '' ?>>2nd Semester</option>
                                <option value="Summer" <?= old('semester', $course['semester'] ?? '') == 'Summer' ? 'selected' : '' ?>>Summer</option>
                            </select>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="year_level" class="form-label">Year Level</label>
                            <select class="form-select" id="year_level" name="year_level">
                                <option value="">-- Select Year --</option>
                                <option value="1" <?= old('year_level', $course['year_level'] ?? '') == '1' ? 'selected' : '' ?>>1st Year</option>
                                <option value="2" <?= old('year_level', $course['year_level'] ?? '') == '2' ? 'selected' : '' ?>>2nd Year</option>
                                <option value="3" <?= old('year_level', $course['year_level'] ?? '') == '3' ? 'selected' : '' ?>>3rd Year</option>
                                <option value="4" <?= old('year_level', $course['year_level'] ?? '') == '4' ? 'selected' : '' ?>>4th Year</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="program_id" class="form-label">Program</label>
                            <select class="form-select" id="program_id" name="program_id">
                                <option value="">-- Select Program --</option>
                                <?php if (isset($programs)): ?>
                                    <?php foreach ($programs as $program): ?>
                                        <option value="<?= $program['id'] ?>" 
                                                <?= old('program_id', $course['program_id'] ?? '') == $program['id'] ? 'selected' : '' ?>>
                                            <?= esc($program['name'] ?? $program['program_name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="max_students" class="form-label">Max Students</label>
                            <input type="number" class="form-control" id="max_students" name="max_students" 
                                   value="<?= old('max_students', $course['max_students'] ?? '') ?>" 
                                   min="1" placeholder="Leave empty for no limit">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="room" class="form-label">Room</label>
                            <input type="text" class="form-control" id="room" name="room" 
                                   value="<?= old('room', $course['room'] ?? '') ?>" 
                                   placeholder="e.g., Room 201, Lab A">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="lecture_hours" class="form-label">Lecture Hours</label>
                            <input type="number" class="form-control" id="lecture_hours" name="lecture_hours" 
                                   value="<?= old('lecture_hours', $course['lecture_hours'] ?? 0) ?>" 
                                   min="0" step="0.5" placeholder="e.g., 3">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="lab_hours" class="form-label">Lab Hours</label>
                            <input type="number" class="form-control" id="lab_hours" name="lab_hours" 
                                   value="<?= old('lab_hours', $course['lab_hours'] ?? 0) ?>" 
                                   min="0" step="0.5" placeholder="e.g., 3">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="schedule_days" class="form-label">Schedule Days</label>
                            <input type="text" class="form-control" id="schedule_days" name="schedule_days" 
                                   value="<?= old('schedule_days', $course['schedule_days'] ?? '') ?>" 
                                   placeholder="e.g., MWF, TTH, MW">
                            <small class="text-muted">Days when the course meets</small>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="schedule_time" class="form-label">Schedule Time</label>
                            <input type="text" class="form-control" id="schedule_time" name="schedule_time" 
                                   value="<?= old('schedule_time', $course['schedule_time'] ?? '') ?>" 
                                   placeholder="e.g., 8:00-9:30 AM, 1:00-4:00 PM">
                            <small class="text-muted">Time when the course meets</small>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="prerequisite_course_ids" class="form-label">Prerequisite Course</label>
                            <select class="form-select" id="prerequisite_course_ids" name="prerequisite_course_ids">
                                <option value="">-- No Prerequisite --</option>
                                <?php foreach ($courses as $prereq_course): ?>
                                    <?php if ($prereq_course['id'] != $course['id']): ?>
                                        <option value="<?= $prereq_course['id'] ?>" 
                                                <?= old('prerequisite_course_ids', $course['prerequisite_course_ids'] ?? '') == $prereq_course['id'] ? 'selected' : '' ?>>
                                            <?= esc($prereq_course['course_code']) ?> - <?= esc($prereq_course['title']) ?>
                                        </option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="active" <?= old('status', $course['status'] ?? 'active') == 'active' ? 'selected' : '' ?>>Active</option>
                                <option value="inactive" <?= old('status', $course['status'] ?? 'active') == 'inactive' ? 'selected' : '' ?>>Inactive</option>
                            </select>
                        </div>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between">
                        <a href="<?= base_url('admin/courses') ?>" class="btn btn-secondary">
                            <i class="bi bi-x-circle"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Update Course
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

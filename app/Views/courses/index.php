<?= view('templates/header', ['title' => 'Search Courses']) ?>

<div class="container py-4">
    <div class="row mb-4">
        <div class="col">
            <h2><i class="bi bi-search"></i> Course Search</h2>
            <p class="text-muted">Search and filter available courses</p>
        </div>
        <div class="col-auto">
            <a href="<?= base_url('dashboard') ?>" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to Dashboard
            </a>
        </div>
    </div>

    <!-- Search Form - Lab 9: Step 4 -->
    <div class="card mb-4">
        <div class="card-body">
            <form id="searchForm" method="GET" action="<?= base_url('courses/search') ?>">
                <div class="row g-3 align-items-end">
                    <div class="col-md-8">
                        <label for="search_term" class="form-label">
                            <i class="bi bi-search"></i> Search Courses
                        </label>
                        <div class="input-group input-group-lg">
                            <span class="input-group-text">
                                <i class="bi bi-book"></i>
                            </span>
                            <input type="text" 
                                   class="form-control" 
                                   id="search_term" 
                                   name="search_term" 
                                   placeholder="Enter course title, code, or description..."
                                   value="<?= esc($search_term ?? '') ?>"
                                   autocomplete="off">
                            <button class="btn btn-primary" type="submit">
                                <i class="bi bi-search"></i> Search
                            </button>
                        </div>
                        <div class="form-text">
                            <i class="bi bi-info-circle"></i> Search by course title, course code, or description keywords
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="d-grid gap-2">
                            <button type="button" class="btn btn-outline-secondary" id="clearSearch">
                                <i class="bi bi-x-circle"></i> Clear Search
                            </button>
                            <button type="button" class="btn btn-outline-info" id="ajaxSearch">
                                <i class="bi bi-lightning"></i> AJAX Search
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Search Results Counter -->
    <div id="searchStatus" class="alert alert-info d-none">
        <i class="bi bi-hourglass-split"></i> Searching...
    </div>

    <!-- Results Section -->
    <div id="resultsSection">
        <?php if (isset($courses)): ?>
            <?php if (!empty($courses)): ?>
                <div class="alert alert-success mb-4">
                    <i class="bi bi-check-circle"></i> Found <strong><?= count($courses) ?></strong> course(s)
                    <?php if (!empty($search_term)): ?>
                        for "<strong><?= esc($search_term) ?></strong>"
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle"></i> 
                    No courses found
                    <?php if (!empty($search_term)): ?>
                        matching "<strong><?= esc($search_term) ?></strong>"
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="alert alert-info">
                <i class="bi bi-info-circle"></i> Enter a search term to find courses
            </div>
        <?php endif; ?>
    </div>

    <!-- Step 6: Courses Container with proper structure for search functionality -->
    <div id="coursesContainer" class="row g-4 justify-content-start">
        <?php if (isset($courses) && !empty($courses)): ?>
            <?php foreach ($courses as $course): ?>
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="card course-card h-100 shadow-sm">
                        <div class="card-header bg-primary text-white">
                            <h5 class="card-title mb-0"><?= esc($course['title']) ?></h5>
                        </div>
                        <div class="card-body d-flex flex-column">
                            <?php if (!empty($course['course_code'])): ?>
                                <p class="mb-2">
                                    <span class="badge bg-secondary"><?= esc($course['course_code']) ?></span>
                                </p>
                            <?php endif; ?>
                            <p class="card-text text-muted mb-2">
                                <i class="bi bi-person-fill"></i> <strong>Instructor:</strong> <?= esc($course['teacher_name'] ?? 'No instructor') ?>
                            </p>
                            <p class="card-text flex-grow-1"><?= esc(substr($course['description'] ?? 'No description available', 0, 150)) ?><?= strlen($course['description'] ?? '') > 150 ? '...' : '' ?></p>
                        </div>
                        <div class="card-footer bg-light d-flex gap-2">
                            <a href="<?= base_url('course/view/' . $course['id']) ?>" class="btn btn-primary btn-sm flex-fill">
                                <i class="bi bi-eye"></i> View Course
                            </a>
                            <?php if (session()->get('role') === 'student'): ?>
                                <button class="btn btn-success btn-sm flex-fill enroll-btn" 
                                        data-course-id="<?= $course['id'] ?>" 
                                        data-course-title="<?= esc($course['title']) ?>">
                                    <i class="bi bi-plus-circle"></i> Enroll
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Lab 9: jQuery and AJAX Implementation -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
$(document).ready(function() {
    // Step 5: Client-Side Filtering with jQuery - Real-time instant filtering
    $('#search_term').on('keyup', function() {
        var value = $(this).val().toLowerCase();
        
        // Filter course cards in real-time - toggle the parent column div
        $('.course-card').each(function() {
            var $card = $(this);
            var $col = $card.closest('.col-12, .col, [class*="col-"]');
            if ($card.text().toLowerCase().indexOf(value) > -1) {
                $col.show();
            } else {
                $col.hide();
            }
        });
        
        // Update results counter
        var visibleCount = $('.course-card:visible').length;
        if (value !== '') {
            if (visibleCount > 0) {
                $('#resultsSection .alert').remove();
                $('#resultsSection').prepend(`
                    <div class="alert alert-success mb-4">
                        <i class="bi bi-check-circle"></i> Found <strong>${visibleCount}</strong> course(s) matching "<strong>${value}</strong>"
                    </div>
                `);
            } else {
                $('#resultsSection .alert').remove();
                $('#resultsSection').prepend(`
                    <div class="alert alert-warning mb-4">
                        <i class="bi bi-exclamation-triangle"></i> No courses found matching "<strong>${value}</strong>"
                    </div>
                `);
            }
        } else {
            $('#resultsSection .alert').remove();
        }
    });

    // Clear Search Button
    $('#clearSearch').on('click', function() {
        $('#search_term').val('');
        $('.course-card').show(); // Show all courses
        $('#resultsSection .alert').remove();
        $('#resultsSection').html(`
            <div class="alert alert-info">
                <i class="bi bi-info-circle"></i> Enter a search term to find courses
            </div>
        `);
        $('#search_term').focus();
    });

    // Step 5: Server-Side Search with AJAX - Form submission
    $('#searchForm').on('submit', function(e) {
        e.preventDefault();
        var searchTerm = $('#search_term').val().trim();
        
        if (searchTerm === '') {
            alert('Please enter a search term');
            return;
        }

        // Show loading status
        $('#searchStatus').removeClass('d-none').html('<i class="bi bi-hourglass-split"></i> Searching database...');
        
        // Server-Side AJAX Request with GET method
        $.get('<?= base_url('courses/search') ?>', { search_term: searchTerm }, function(data) {
            $('#searchStatus').addClass('d-none');
            
            // Clear current results
            $('#coursesContainer').empty();
            $('#resultsSection .alert').remove();
            
            if (data.length > 0) {
                // Success message
                $('#resultsSection').prepend(`
                    <div class="alert alert-success mb-4">
                        <i class="bi bi-check-circle"></i> Found <strong>${data.length}</strong> course(s) matching "<strong>${searchTerm}</strong>"
                    </div>
                `);
                
                // Build and display course cards - Step 6 structure
                $.each(data, function(index, course) {
                    var courseCode = course.course_code || '';
                    var description = course.description || 'No description available';
                    var teacherName = course.teacher_name || 'No instructor';
                    
                    var courseHtml = `
                        <div class="col-md-4 mb-4">
                            <div class="card course-card">
                                <div class="card-body">
                                    <h5 class="card-title">${escapeHtml(course.title)}</h5>
                                    ${courseCode ? '<p class="mb-2"><span class="badge bg-secondary">' + escapeHtml(courseCode) + '</span></p>' : ''}
                                    <p class="card-text text-muted mb-2">
                                        <i class="bi bi-person-fill"></i> ${escapeHtml(teacherName)}
                                    </p>
                                    <p class="card-text">${escapeHtml(description)}</p>
                                    <a href="<?= base_url('course/view/') ?>${course.id}" class="btn btn-primary">
                                        <i class="bi bi-eye"></i> View Course
                                    </a>
                                    <?php if (session()->get('role') === 'student'): ?>
                                        <button class="btn btn-success btn-sm enroll-btn" 
                                                data-course-id="${course.id}" 
                                                data-course-title="${escapeHtml(course.title)}">
                                            <i class="bi bi-plus-circle"></i> Enroll
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    `;
                    
                    $('#coursesContainer').append(courseHtml);
                });
                
                // Rebind enrollment buttons after adding new content
                bindEnrollButtons();
            } else {
                $('#resultsSection').prepend(`
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle"></i> No courses found matching "<strong>${searchTerm}</strong>"
                    </div>
                `);
            }
        }).fail(function(xhr, status, error) {
            $('#searchStatus').addClass('d-none');
            $('#resultsSection').html(`
                <div class="alert alert-danger">
                    <i class="bi bi-x-circle"></i> Error: ${error}. Please try again.
                </div>
            `);
        });
    });

    // AJAX Search Button - Alternative method using POST
    $('#ajaxSearch').on('click', function() {
        var searchTerm = $('#search_term').val().trim();
        
        if (searchTerm === '') {
            alert('Please enter a search term');
            return;
        }

        // Show loading status
        $('#searchStatus').removeClass('d-none').html('<i class="bi bi-hourglass-split"></i> Searching database with POST...');
        
        // Server-Side AJAX Request with POST method
        $.ajax({
            url: '<?= base_url('courses/search') ?>',
            type: 'POST',
            data: {
                search_term: searchTerm,
                <?= csrf_token() ?>: '<?= csrf_hash() ?>'
            },
            dataType: 'json',
            success: function(response) {
                $('#searchStatus').addClass('d-none');
                
                if (response.success && response.courses) {
                    displayResults(response.courses, searchTerm);
                } else {
                    $('#resultsSection').html(`
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle"></i> No courses found
                        </div>
                    `);
                }
            },
            error: function(xhr, status, error) {
                $('#searchStatus').addClass('d-none');
                $('#resultsSection').html(`
                    <div class="alert alert-danger">
                        <i class="bi bi-x-circle"></i> Error: ${error}
                    </div>
                `);
            }
        });
    });

    // Display Results Function
    function displayResults(courses, searchTerm) {
        var html = `
            <div class="alert alert-success mb-4">
                <i class="bi bi-check-circle"></i> Found <strong>${courses.length}</strong> course(s) for "<strong>${searchTerm}</strong>"
            </div>
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4" id="coursesGrid">
        `;

        courses.forEach(function(course) {
            var courseCode = course.course_code || course.code || '';
            var description = course.description || 'No description available';
            var truncatedDesc = description.length > 120 ? description.substring(0, 120) + '...' : description;
            
            html += `
                <div class="col course-card">
                    <div class="card h-100 shadow-sm">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">${escapeHtml(course.title)}</h5>
                        </div>
                        <div class="card-body">
            `;
            
            if (courseCode) {
                html += `
                    <p class="mb-2">
                        <span class="badge bg-secondary">${escapeHtml(courseCode)}</span>
                    </p>
                `;
            }
            
            html += `
                            <p class="text-muted mb-2">
                                <i class="bi bi-person-fill"></i> 
                                <strong>Instructor:</strong> ${escapeHtml(course.teacher_name)}
                            </p>
                            <p class="card-text">${escapeHtml(truncatedDesc)}</p>
                        </div>
                        <div class="card-footer bg-light">
            `;
            
            <?php if (session()->get('role') === 'student'): ?>
            html += `
                            <button class="btn btn-success btn-sm w-100 enroll-btn" 
                                    data-course-id="${course.id}" 
                                    data-course-title="${escapeHtml(course.title)}">
                                <i class="bi bi-plus-circle"></i> Enroll in this Course
                            </button>
            `;
            <?php else: ?>
            html += `
                            <a href="<?= base_url('course/view/') ?>${course.id}" 
                               class="btn btn-primary btn-sm w-100">
                                <i class="bi bi-eye"></i> View Course
                            </a>
            `;
            <?php endif; ?>
            
            html += `
                        </div>
                    </div>
                </div>
            `;
        });

        html += '</div>';
        $('#resultsSection').html(html);
        
        // Rebind enrollment buttons
        bindEnrollButtons();
    }

    // Helper function to escape HTML
    function escapeHtml(text) {
        var map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, function(m) { return map[m]; });
    }

    // Enrollment functionality for students
    function bindEnrollButtons() {
        $('.enroll-btn').off('click').on('click', function() {
            var courseId = $(this).data('course-id');
            var courseTitle = $(this).data('course-title');
            var button = $(this);

            if (confirm('Do you want to enroll in "' + courseTitle + '"?')) {
                button.prop('disabled', true).html('<i class="bi bi-hourglass-split"></i> Enrolling...');

                $.ajax({
                    url: '<?= base_url('course/enroll') ?>',
                    type: 'POST',
                    data: {
                        course_id: courseId,
                        <?= csrf_token() ?>: '<?= csrf_hash() ?>'
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            button.removeClass('btn-success').addClass('btn-secondary')
                                  .html('<i class="bi bi-check-circle"></i> Enrolled')
                                  .prop('disabled', true);
                            alert('Success: ' + response.message);
                        } else {
                            button.prop('disabled', false)
                                  .html('<i class="bi bi-plus-circle"></i> Enroll in this Course');
                            alert('Error: ' + response.message);
                        }
                    },
                    error: function() {
                        button.prop('disabled', false)
                              .html('<i class="bi bi-plus-circle"></i> Enroll in this Course');
                        alert('An error occurred. Please try again.');
                    }
                });
            }
        });
    }

    // Bind enrollment buttons on page load
    bindEnrollButtons();

    // Real-time search (optional enhancement)
    $('#search_term').on('keyup', function(e) {
        if (e.key === 'Enter') {
            $('#ajaxSearch').click();
        }
    });
});
</script>

</body>
</html>

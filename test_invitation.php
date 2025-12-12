<?php
// Simple test to check if invitations work
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR);

// Bootstrap CodeIgniter
$pathsPath = FCPATH . '../app/Config/Paths.php';
require realpath($pathsPath) ?: $pathsPath;

$paths = new Config\Paths();
$bootstrap = rtrim($paths->systemDirectory, '\\/ ') . DIRECTORY_SEPARATOR . 'bootstrap.php';
$app = require realpath($bootstrap) ?: $bootstrap;

$app->initialize();

// Get database connection
$db = \Config\Database::connect();

echo "=== TESTING INVITATION SYSTEM ===\n\n";

// 1. Check students
$query = $db->query('SELECT id, name, email FROM users WHERE role = "student" ORDER BY id LIMIT 3');
$students = $query->getResultArray();

echo "STUDENTS:\n";
foreach ($students as $s) {
    echo "  ID: {$s['id']} - {$s['name']} ({$s['email']})\n";
}

// 2. Check courses
$query = $db->query('SELECT id, title, course_code FROM courses ORDER BY id LIMIT 3');
$courses = $query->getResultArray();

echo "\nCOURSES:\n";
foreach ($courses as $c) {
    echo "  ID: {$c['id']} - {$c['title']} ({$c['course_code']})\n";
}

// 3. Create test invitation if none exist
$query = $db->query('SELECT COUNT(*) as count FROM enrollment_invitations WHERE status = "pending"');
$result = $query->getRowArray();

echo "\nPENDING INVITATIONS: {$result['count']}\n";

if ($result['count'] == 0 && !empty($students) && !empty($courses)) {
    echo "\nCreating test invitation...\n";
    
    $student = $students[0];
    $course = $courses[0];
    $adminId = 1; // Assuming admin ID is 1
    
    $invitationModel = new \App\Models\EnrollmentInvitationModel();
    $result = $invitationModel->createInvitation(
        $student['id'], 
        $course['id'], 
        $adminId, 
        "Test invitation"
    );
    
    if ($result['success']) {
        echo "✓ Invitation created successfully!\n";
        echo "  Student: {$student['name']}\n";
        echo "  Course: {$course['title']}\n";
    } else {
        echo "✗ Failed: {$result['message']}\n";
    }
}

// 4. Show all pending invitations
$query = $db->query('
    SELECT 
        ei.id,
        ei.user_id,
        ei.course_id,
        ei.status,
        ei.created_at,
        u.name as student_name,
        u.email as student_email,
        c.title as course_title,
        c.course_code
    FROM enrollment_invitations ei
    JOIN users u ON u.id = ei.user_id
    JOIN courses c ON c.id = ei.course_id
    WHERE ei.status = "pending"
    ORDER BY ei.created_at DESC
');
$invitations = $query->getResultArray();

echo "\n=== ALL PENDING INVITATIONS ===\n";
foreach ($invitations as $inv) {
    echo "\nInvitation #{$inv['id']}:\n";
    echo "  Student: {$inv['student_name']} (ID: {$inv['user_id']}, {$inv['student_email']})\n";
    echo "  Course: {$inv['course_title']} ({$inv['course_code']})\n";
    echo "  Created: {$inv['created_at']}\n";
}

if (empty($invitations)) {
    echo "  No pending invitations found.\n";
}

echo "\n=== DONE ===\n";

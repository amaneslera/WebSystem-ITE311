<?php
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR);
require __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/app/Config/Paths.php';

$paths = new Config\Paths();
require __DIR__ . '/system/bootstrap.php';

$db = \Config\Database::connect();

// Check enrollment_invitations table
$query = $db->query('SELECT * FROM enrollment_invitations WHERE status = "pending"');
$invitations = $query->getResultArray();

echo "=== PENDING INVITATIONS ===\n";
echo "Total: " . count($invitations) . "\n\n";

foreach ($invitations as $inv) {
    echo "Invitation ID: " . $inv['id'] . "\n";
    echo "  Student ID: " . $inv['user_id'] . "\n";
    echo "  Course ID: " . $inv['course_id'] . "\n";
    echo "  Type: " . $inv['type'] . "\n";
    echo "  Invited by: " . $inv['invited_by'] . "\n";
    echo "  Status: " . $inv['status'] . "\n";
    echo "  Created: " . $inv['created_at'] . "\n";
    echo "---\n";
}

// Check users
$query = $db->query('SELECT id, name, email, role FROM users WHERE role = "student"');
$students = $query->getResultArray();

echo "\n=== STUDENTS ===\n";
foreach ($students as $student) {
    echo "ID: " . $student['id'] . " - " . $student['name'] . " (" . $student['email'] . ")\n";
}

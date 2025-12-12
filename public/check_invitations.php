<?php
// Quick database check - place this in public folder and access via browser
// URL: http://localhost/check_invitations.php

// Database connection
$host = 'localhost';
$db   = 'lms_eslera';  // Change if different
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h1>Enrollment Invitations Check</h1>";
    
    // Check if table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'enrollment_invitations'");
    if ($stmt->rowCount() == 0) {
        echo "<p style='color:red;'>❌ Table 'enrollment_invitations' does NOT exist!</p>";
        echo "<p>Run migration: <code>php spark migrate</code></p>";
        exit;
    }
    
    echo "<p style='color:green;'>✅ Table 'enrollment_invitations' exists</p>";
    
    // Get all invitations
    $stmt = $pdo->query("
        SELECT ei.*, 
               u.name as student_name, 
               u.email as student_email,
               c.course_code, 
               c.course_name,
               inv.name as inviter_name
        FROM enrollment_invitations ei
        JOIN users u ON ei.user_id = u.id
        JOIN courses c ON ei.course_id = c.id
        LEFT JOIN users inv ON ei.invited_by = inv.id
        ORDER BY ei.created_at DESC
    ");
    
    $invitations = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h2>Total Invitations: " . count($invitations) . "</h2>";
    
    if (count($invitations) > 0) {
        echo "<table border='1' cellpadding='10' style='border-collapse: collapse;'>";
        echo "<tr>
                <th>ID</th>
                <th>Student</th>
                <th>Email</th>
                <th>Course</th>
                <th>Type</th>
                <th>Status</th>
                <th>Invited By</th>
                <th>Created</th>
              </tr>";
        
        foreach ($invitations as $inv) {
            $statusColor = $inv['status'] == 'pending' ? 'orange' : ($inv['status'] == 'accepted' ? 'green' : 'red');
            echo "<tr>";
            echo "<td>{$inv['id']}</td>";
            echo "<td>{$inv['student_name']}</td>";
            echo "<td>{$inv['student_email']}</td>";
            echo "<td>{$inv['course_code']} - {$inv['course_name']}</td>";
            echo "<td>{$inv['type']}</td>";
            echo "<td style='color:{$statusColor};'><strong>{$inv['status']}</strong></td>";
            echo "<td>{$inv['inviter_name']}</td>";
            echo "<td>{$inv['created_at']}</td>";
            echo "</tr>";
        }
        
        echo "</table>";
        
        // Show pending invitations per user
        echo "<h2>Pending Invitations by Student</h2>";
        $stmt = $pdo->query("
            SELECT u.id, u.name, u.email, COUNT(*) as pending_count
            FROM enrollment_invitations ei
            JOIN users u ON ei.user_id = u.id
            WHERE ei.status = 'pending'
            GROUP BY u.id
        ");
        
        $pending = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (count($pending) > 0) {
            echo "<ul>";
            foreach ($pending as $p) {
                echo "<li><strong>{$p['name']}</strong> (ID: {$p['id']}, Email: {$p['email']}) - {$p['pending_count']} pending invitation(s)</li>";
            }
            echo "</ul>";
        } else {
            echo "<p>No pending invitations</p>";
        }
        
    } else {
        echo "<p style='color:red;'>No invitations found in database!</p>";
        echo "<p>Try enrolling a student from admin/teacher panel.</p>";
    }
    
} catch (PDOException $e) {
    echo "<p style='color:red;'>Database Error: " . $e->getMessage() . "</p>";
}
?>

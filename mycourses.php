<?php
require_once 'config/db.php';

// Get user information (assuming user is logged in)
$userId = 1; // This should come from your authentication system
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();

// Get user's courses
$stmt = $pdo->prepare("
    SELECT c.*, uc.progress, uc.completed_lessons, uc.registration_date, uc.status
    FROM courses c
    JOIN user_courses uc ON c.id = uc.course_id
    WHERE uc.user_id = ?
");
$stmt->execute([$userId]);
$courses = $stmt->fetchAll();

// Get statistics
$stmt = $pdo->prepare("
    SELECT 
        COUNT(CASE WHEN status = 'completed' THEN 1 END) as completed_courses,
        COUNT(CASE WHEN status = 'inprogress' THEN 1 END) as ongoing_courses
    FROM user_courses
    WHERE user_id = ?
");
$stmt->execute([$userId]);
$stats = $stmt->fetch();
?>
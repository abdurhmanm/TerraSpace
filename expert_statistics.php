<?php
session_start();
if (!isset($_SESSION["expert_id"])) {
    header("Location: login.html");
    exit();
}

$conn = new mysqli("localhost", "root", "", "tira_space");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$expert_id = $_SESSION["expert_id"];

// Get completion rate
$sql = "SELECT AVG(completion_rate) as avg_completion
        FROM course_statistics 
        WHERE expert_id = ? 
        AND created_at >= DATE_SUB(NOW(), INTERVAL 1 MONTH)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $expert_id);
$stmt->execute();
$completion_rate = $stmt->get_result()->fetch_assoc()['avg_completion'] ?? 0;

// Get average rating
$sql = "SELECT AVG(r.rating) as avg_rating 
        FROM course_ratings r
        JOIN courses c ON r.course_id = c.id
        WHERE c.expert_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $expert_id);
$stmt->execute();
$avg_rating = $stmt->get_result()->fetch_assoc()['avg_rating'] ?? 0;

// Get active students count
$sql = "SELECT COUNT(DISTINCT sa.student_id) as active_students
        FROM student_activity sa
        JOIN courses c ON sa.course_id = c.id
        WHERE c.expert_id = ?
        AND sa.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $expert_id);
$stmt->execute();
$active_students = $stmt->get_result()->fetch_assoc()['active_students'] ?? 0;

// Get weekly activity data
$sql = "SELECT DATE_FORMAT(created_at, '%W') as day, 
        COUNT(*) as activity_count
        FROM student_activity sa
        JOIN courses c ON sa.course_id = c.id
        WHERE c.expert_id = ?
        AND sa.created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
        GROUP BY DATE_FORMAT(created_at, '%W')
        ORDER BY created_at";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $expert_id);
$stmt->execute();
$weekly_activity = $stmt->get_result();

$activity_days = [];
$activity_data = [];
while ($row = $weekly_activity->fetch_assoc()) {
    $activity_days[] = $row['day'];
    $activity_data[] = $row['activity_count'];
}

// Get course ratings
$sql = "SELECT c.course_name, AVG(r.rating) as avg_rating
        FROM course_ratings r
        JOIN courses c ON r.course_id = c.id
        WHERE c.expert_id = ?
        GROUP BY c.id
        ORDER BY avg_rating DESC
        LIMIT 5";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $expert_id);
$stmt->execute();
$course_ratings = $stmt->get_result();

$course_names = [];
$rating_data = [];
while ($row = $course_ratings->fetch_assoc()) {
    $course_names[] = $row['course_name'];
    $rating_data[] = $row['avg_rating'];
}

$conn->close();
?>
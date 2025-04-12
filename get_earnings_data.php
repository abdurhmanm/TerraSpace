<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION["expert_id"])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$conn = new mysqli("localhost", "root", "", "tira_space");
$expert_id = $_SESSION["expert_id"];
$period = $_GET['period'] ?? 'all';

// Build date condition
$date_condition = match($period) {
    'month' => 'AND created_at >= DATE_SUB(NOW(), INTERVAL 1 MONTH)',
    'quarter' => 'AND created_at >= DATE_SUB(NOW(), INTERVAL 3 MONTH)',
    'year' => 'AND created_at >= DATE_SUB(NOW(), INTERVAL 1 YEAR)',
    default => ''
};

// Get filtered data
$sql = "SELECT 
            SUM(amount) as total,
            COUNT(*) as sales,
            COUNT(DISTINCT student_id) as students
        FROM transactions 
        WHERE expert_id = ? $date_condition";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $expert_id);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();

echo json_encode($data);

$conn->close();
<?php
session_start();
header('Content-Type: application/json');

$conn = new mysqli("localhost", "root", "", "tira_space");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$expert_id = $_SESSION["expert_id"] ?? null;
if (!$expert_id) {
    echo json_encode(['error' => 'Not authorized']);
    exit();
}

$sql = "SELECT * FROM courses WHERE expert_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $expert_id);
$stmt->execute();
$result = $stmt->get_result();

$courses = [];
while($row = $result->fetch_assoc()) {
    $courses[] = $row;
}

echo json_encode($courses);

$stmt->close();
$conn->close();
?>

<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION["expert_id"])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
$course_id = $data['id'] ?? null;

if (!$course_id) {
    echo json_encode(['success' => false, 'message' => 'Course ID is required']);
    exit();
}

$conn = new mysqli("localhost", "root", "", "tira_space");
$expert_id = $_SESSION["expert_id"];

// Verify course ownership before deletion
$sql = "DELETE FROM courses WHERE id = ? AND expert_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $course_id, $expert_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Course deleted successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to delete course']);
}

$conn->close();
?>
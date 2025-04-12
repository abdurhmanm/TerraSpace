<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION["expert_id"])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$conn = new mysqli("localhost", "root", "", "tira_space");
$expert_id = $_SESSION["expert_id"];

$sql = "SELECT DISTINCT s.id, s.name 
        FROM students s
        JOIN course_enrollments ce ON s.id = ce.student_id
        JOIN courses c ON ce.course_id = c.id
        WHERE c.expert_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $expert_id);
$stmt->execute();
$result = $stmt->get_result();

$students = [];
while ($row = $result->fetch_assoc()) {
    $students[] = $row;
}

echo json_encode($students);
$conn->close();
?>
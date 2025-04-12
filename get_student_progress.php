<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION["expert_id"])) {
    echo json_encode(['success' => false, 'message' => 'غير مصرح بالوصول']);
    exit();
}

$student_id = $_GET['id'] ?? 0;

$conn = new mysqli("localhost", "root", "", "tira_space");
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'خطأ في الاتصال']);
    exit();
}

$sql = "SELECT s.*, c.course_name 
        FROM students s 
        JOIN courses c ON s.course_id = c.id 
        WHERE s.id = ? AND c.expert_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $student_id, $_SESSION["expert_id"]);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();

if (!$student) {
    echo json_encode(['success' => false, 'message' => 'لم يتم العثور على الطالب']);
    exit();
}

echo json_encode([
    'success' => true,
    'data' => [
        'theory' => 80, // You can calculate these values based on your needs
        'practical' => 65,
        'tests' => 90,
        'overall' => $student['progress']
    ]
]);
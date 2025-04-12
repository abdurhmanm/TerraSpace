<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION["expert_id"])) {
    echo json_encode(['success' => false, 'message' => 'غير مصرح بالوصول']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
$skill_name = $data['skill_name'] ?? '';

if (empty($skill_name)) {
    echo json_encode(['success' => false, 'message' => 'يرجى إدخال اسم المهارة']);
    exit();
}

$conn = new mysqli("localhost", "root", "", "tira_space");
$expert_id = $_SESSION["expert_id"];

$sql = "INSERT INTO expert_skills (expert_id, skill_name) VALUES (?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $expert_id, $skill_name);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'فشل إضافة المهارة']);
}

$conn->close();
?>
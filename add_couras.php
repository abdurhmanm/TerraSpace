<?php
header("Content-Type: application/json");
require_once "tira_space.php"; // تأكد من اتصال قاعدة البيانات

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data["title"]) || !isset($data["doctor"])) {
    echo json_encode(["success" => false, "message" => "البيانات غير مكتملة"]);
    exit;
}

$title = $conn->real_escape_string($data["title"]);
$doctor = $conn->real_escape_string($data["doctor"]);
$category = $conn->real_escape_string($data["category"]);
$duration = $conn->real_escape_string($data["duration"]);
$start_date = $conn->real_escape_string($data["start_date"]);
$rating = $conn->real_escape_string($data["rating"]);
$image = $conn->real_escape_string($data["image"]);
$description = $conn->real_escape_string($data["description"]);

$sql = "INSERT INTO coourses (title, doctor, category, duration, start_date, rating, image, description) 
        VALUES ('$title', '$doctor', '$category', '$duration', '$start_date', '$rating', '$image', '$description')";

if ($conn->query($sql) === TRUE) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "message" => "خطأ في الإضافة: " . $conn->error]);
}

$conn->close();
?>

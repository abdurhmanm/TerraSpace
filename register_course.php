<?php
session_start();

// التحقق من تسجيل الدخول
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'يجب تسجيل الدخول أولاً.']);
    exit();
}

// الاتصال بقاعدة البيانات
$conn = new mysqli("localhost", "root", "", "tira_space");

// التحقق من الاتصال
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'فشل الاتصال بقاعدة البيانات: ' . $conn->connect_error]);
    exit();
}

// استقبال البيانات
$data = json_decode(file_get_contents('php://input'), true);
$course_id = $data['course_id'];
$user_id = $_SESSION['user_id'];

// التحقق من أن الدورة موجودة
$check_course_stmt = $conn->prepare("SELECT id FROM courses WHERE id = ?");
if (!$check_course_stmt) {
    echo json_encode(['success' => false, 'message' => 'خطأ في الاستعلام: ' . $conn->error]);
    exit();
}
$check_course_stmt->bind_param("i", $course_id);
$check_course_stmt->execute();
$check_course_stmt->store_result();

if ($check_course_stmt->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'الدورة غير موجودة.']);
    exit();
}

$check_course_stmt->close();

// إدخال التسجيل في قاعدة البيانات
$insert_stmt = $conn->prepare("INSERT INTO course_registrations (user_id, course_id) VALUES (?, ?)");
if (!$insert_stmt) {
    echo json_encode(['success' => false, 'message' => 'خطأ في الاستعلام: ' . $conn->error]);
    exit();
}
$insert_stmt->bind_param("ii", $user_id, $course_id);

if ($insert_stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'خطأ أثناء التسجيل: ' . $insert_stmt->error]);
}

$insert_stmt->close();
$conn->close();
?>
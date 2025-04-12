<?php
header("Content-Type: application/json");
$servername = "localhost"; 
$username = "root"; 
$password = ""; 
$dbname = "tira_space"; // اسم قاعدة البيانات

// الاتصال بقاعدة البيانات
$conn = new mysqli($servername, $username, $password, $dbname);

// التحقق من الاتصال
if ($conn->connect_error) {
    die(json_encode(["error" => "فشل الاتصال بقاعدة البيانات: " . $conn->connect_error]));
}

// استقبال البيانات من الطلب
$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    echo json_encode(["error" => "❌ لم يتم استقبال بيانات"]);
    exit;
}

// استخراج البيانات من JSON
$title = $data['title'];
$field = $data['field'];
$date = $data['date'];
$type = $data['type'];
$duration = $data['duration'];
$doctor = $data['doctor'];
$rating = $data['rating'];
$image = $data['image'];

// تنفيذ استعلام الإدراج
$sql = "INSERT INTO coourses (title, field, date, type, duration, doctor, rating, image) 
        VALUES ('$title', '$field', '$date', '$type', '$duration', '$doctor', '$rating', '$image')";

if ($conn->query($sql) === TRUE) {
    echo json_encode(["success" => "✅ تم إضافة الدورة بنجاح"]);
} else {
    echo json_encode(["error" => "❌ خطأ في الإدراج: " . $conn->error]);
}

// إغلاق الاتصال
$conn->close();
?>

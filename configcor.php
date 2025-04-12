<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "tera_space";

// إنشاء الاتصال بقاعدة البيانات
$conn = new mysqli($servername, $username, $password, $dbname);

// التحقق من الاتصال
if ($conn->connect_error) {
    die("فشل الاتصال بقاعدة البيانات: " . $conn->connect_error);
}

// جلب الدورات من قاعدة البيانات
$sql = "SELECT * FROM courses";
$result = $conn->query($sql);
?>

<?php
$servername = "localhost"; // تأكد من أن السيرفر صحيح
$username = "root"; // اسم المستخدم الافتراضي
$password = ""; // كلمة المرور الافتراضية (اتركها فارغة إذا لم تقم بتغييرها)
$dbname = "tira_space"; // تأكد أن اسم قاعدة البيانات صحيح

$conn = new mysqli("localhost", "root", "", "tira_space");

// التحقق من الاتصال
if ($conn->connect_error) {
    die("❌ فشل الاتصال بقاعدة البيانات: " . $conn->connect_error);
}
?>

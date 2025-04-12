<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: login.html");
    exit();
}

// تخزين اسم المستخدم في متغير
$userName = $_SESSION["user_name"] ?? "زائر";
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>الصفحة الرئيسية</title>
</head>
<body>
    <h1>مرحباً <?php echo $userName; ?>!</h1>
    <p>أنت الآن في الصفحة الرئيسية</p>
    <a href="logout.php">تسجيل الخروج</a>
</body>
</html>

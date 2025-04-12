<?php
session_start();

// التحقق من تسجيل الدخول
if (!isset($_SESSION['user_id'])) {
    header("Location: index.html"); // توجيه المستخدم إلى صفحة تسجيل الدخول
    exit();
}

// الاتصال بقاعدة البيانات
$host = 'localhost';
$dbname = 'education_platform';
$username = 'root';
$password = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // استرجاع بيانات المستخدم
    $user_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT * FROM Users WHERE user_id = :user_id");
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // استرجاع الدورات
    $stmt = $conn->query("SELECT * FROM Courses");
    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
$conn = null;
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة التحكم</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; }
        .container { margin: 20px; }
        .course { border: 1px solid #ccc; padding: 10px; margin: 10px; display: inline-block; }
    </style>
</head>
<body>
    <div class="container">
        <h1>مرحبًا, <?php echo $user['username']; ?></h1>
        <h2>الدورات المتاحة:</h2>
        <?php foreach ($courses as $course): ?>
            <div class="course">
                <h3><?php echo $course['title']; ?></h3>
                <p><?php echo $course['description']; ?></p>
            </div>
        <?php endforeach; ?>
        <br>
        <a href="logout.php">تسجيل الخروج</a>
    </div>
</body>
</html>
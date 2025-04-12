<?php
session_start();

// الاتصال بقاعدة البيانات
$host = 'localhost';
$dbname = 'education_platform';
$username = 'root';
$password = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // جمع البيانات من النموذج
    $email = $_POST['email'];
    $password = $_POST['password'];

    // البحث عن المستخدم
    $stmt = $conn->prepare("SELECT * FROM Users WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password_hash'])) {
        // تخزين بيانات المستخدم في الجلسة
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        header("Location: dashboard.php"); // توجيه المستخدم إلى لوحة التحكم
    } else {
        echo "البريد الإلكتروني أو كلمة المرور غير صحيحة.";
    }
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
$conn = null;
?>
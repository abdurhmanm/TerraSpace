<?php
session_start(); // بدء الجلسة

// الاتصال بقاعدة البيانات
$conn = new mysqli("localhost", "root", "", "tira_space");

// تحقق من الاتصال
if ($conn->connect_error) {
    die("❌ فشل الاتصال بقاعدة البيانات: " . $conn->connect_error);
}

// استقبال بيانات تسجيل الدخول
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    if (!empty($email) && !empty($password)) {
        // البحث عن المستخدم في قاعدة البيانات
        $sql = "SELECT id, first_name, last_name, password FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($id, $first_name, $last_name, $hashed_password);
            $stmt->fetch();

            // تحقق من كلمة المرور
            if (password_verify($password, $hashed_password)) {
                $_SESSION["user_id"] = $id;
                $_SESSION["user_name"] = $first_name . " " . $last_name;
                $_SESSION["email"] = $email; // إضافة البريد الإلكتروني للجلسة

                // إعادة التوجيه إلى الصفحة الرئيسية
                header("Location: home.php");
                exit();
            } else {
                echo json_encode(['success' => false, 'message' => '❌ كلمة المرور غير صحيحة!']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => '❌ البريد الإلكتروني غير مسجل!']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => '❌ يجب ملء جميع الحقول!']);
    }
}
?>

<a href="home.php">
    <button class="btn">الرئيسية</button>
</a>

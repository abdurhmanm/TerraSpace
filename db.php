<?php
    // db.php - ملف الاتصال بقاعدة البيانات
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "tira_space";

    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("فشل الاتصال بقاعدة البيانات: " . $conn->connect_error);
    }
    ?>

    <!-- index.php - الصفحة الرئيسية -->
    <?php include 'db.php'; ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>تيرا سبيس - الرئيسية</title>
    </head>
    <body>
        <h1>مرحبًا بك في تيرا سبيس</h1>
        <a href="register.php">تسجيل جديد</a> | <a href="login.php">تسجيل الدخول</a>
    </body>
    </html>

    <!-- register.php - تسجيل حساب جديد -->
    <?php
    include 'db.php';
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $first_name = $_POST['first_name'];
        $last_name = $_POST['last_name'];
        $email = $_POST['email'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    
        $sql = "INSERT INTO users (first_name, last_name, email, password) VALUES ('$first_name', '$last_name', '$email', '$password')";
    
        if ($conn->query($sql) === TRUE) {
            header("Location: login.php");
            exit();
        } else {
            echo "خطأ في التسجيل: " . $conn->error;
        }
    }
    ?>
    <form method="post">
        <input type="text" name="first_name" placeholder="الاسم الأول" required>
        <input type="text" name="last_name" placeholder="الاسم الأخير" required>
        <input type="email" name="email" placeholder="البريد الإلكتروني" required>
        <input type="password" name="password" placeholder="كلمة المرور" required>
        <button type="submit">تسجيل</button>
    </form>

    <!-- login.php - تسجيل الدخول -->
    <?php
    include 'db.php';
    session_start();
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $email = $_POST['email'];
        $password = $_POST['password'];
        
        $result = $conn->query("SELECT * FROM users WHERE email='$email'");
        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                header("Location: dashboard.php");
                exit();
            } else {
                echo "❌ كلمة المرور غير صحيحة!";
            }
        } else {
            echo "❌ البريد الإلكتروني غير موجود!";
        }
    }
    ?>
    <form method="post">
        <input type="email" name="email" placeholder="البريد الإلكتروني" required>
        <input type="password" name="password" placeholder="كلمة المرور" required>
        <button type="submit">تسجيل الدخول</button>
    </form>

    <!-- dashboard.php - لوحة التحكم -->
    <?php
    session_start();
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }
    echo "<h1>مرحبًا بك في لوحة التحكم</h1>";
    ?>

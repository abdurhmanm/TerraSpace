<?php
// إعداد الاتصال بقاعدة البيانات
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "tira_space";

$conn = new mysqli($servername, $username, $password, $dbname);

// التحقق من الاتصال
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// التحقق من إرسال النموذج
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
    $last_name = mysqli_real_escape_string($conn, $_POST['last_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // تشفير كلمة المرور
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);

    // التحقق من تطابق كلمات المرور
    if ($_POST['password'] !== $_POST['confirm_password']) {
        echo "كلمات المرور غير متطابقة";
        exit();
    }

    // إدراج البيانات في قاعدة البيانات
    $sql = "INSERT INTO userss (first_name, last_name, email, password, phone) 
            VALUES ('$first_name', '$last_name', '$email', '$password', '$phone')";

    if ($conn->query($sql) === TRUE) {
        // إعادة التوجيه إلى صفحة الترحيب
        header("Location: welcome.html");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>
<?php
include 'register_connect.php'; // التأكد من تضمين ملف الاتصال بقاعدة البيانات

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $specialty = $_POST['specialty'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // تشفير كلمة المرور
    $phone = $_POST['phone'];

    // فحص ما إذا كانت البيانات قد استُلمت بنجاح
    if (empty($first_name) || empty($last_name) || empty($specialty) || empty($email) || empty($password) || empty($phone)) {
        die("❌ يرجى ملء جميع الحقول!");
    }

    $sql = "INSERT INTO Experts (first_name, last_name, specialty, email, password_hash, phone) 
            VALUES (?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssss", $first_name, $last_name, $specialty, $email, $password, $phone);

    if ($stmt->execute()) {
        echo "✅ تم التسجيل بنجاح!";
    } else {
        echo "❌ خطأ في التسجيل: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    echo "❌ الطلب غير صالح!";
}
?>

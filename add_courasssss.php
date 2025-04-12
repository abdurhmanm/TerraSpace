<?php
include 'db.php'; // ربط قاعدة البيانات

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $category = $_POST['category'];
    $start_date = $_POST['start_date'];

    $sql = "INSERT INTO courses (title, description, category, start_date) 
            VALUES ('$title', '$description', '$category', '$start_date')";

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('✅ تمت إضافة الدورة بنجاح!'); window.location.href='index.php';</script>";
    } else {
        echo "❌ خطأ في الإدخال: " . $conn->error;
    }

    $conn->close();
}
?>

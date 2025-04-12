<?php
include 'db.php'; // الاتصال بقاعدة البيانات

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $category = $_POST['category'];
    $start_date = $_POST['start_date'];

    $sql = "INSERT INTO courses (title, description, category, start_date) 
            VALUES ('$title', '$description', '$category', '$start_date')";

    if ($conn->query($sql) === TRUE) {
        echo "✅ تم إضافة الدورة بنجاح!";
    } else {
        echo "❌ خطأ: " . $conn->error;
    }
}

$conn->close();
?>

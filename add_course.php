<?php
session_start();

// التحقق من تسجيل دخول الخبير
if (!isset($_SESSION["expert_id"])) {
    header("Location: login.html");
    exit();
}

$success_message = $error_message = "";

// التحقق من إرسال النموذج
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // معالجة إضافة الدورة
    $conn = new mysqli("localhost", "root", "", "tira_space");
    
    if ($conn->connect_error) {
        die("فشل الاتصال: " . $conn->connect_error);
    }

    $course_name = $_POST['course_name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $expert_id = $_SESSION["expert_id"];
    $category = $_POST['category'];
    
    $target_dir = "uploads/courses/";
    $cover_image = "";
    
    if (isset($_FILES["cover_image"]) && $_FILES["cover_image"]["error"] == 0) {
        $file_extension = pathinfo($_FILES["cover_image"]["name"], PATHINFO_EXTENSION);
        $file_name = uniqid() . "." . $file_extension;
        $target_file = $target_dir . $file_name;
        
        if (move_uploaded_file($_FILES["cover_image"]["tmp_name"], $target_file)) {
            $cover_image = $file_name;
        }
    }

    $sql = "INSERT INTO courses (course_name, description, price, expert_id, category, cover_image, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, NOW())";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssdsss", $course_name, $description, $price, $expert_id, $category, $cover_image);
    
    if ($stmt->execute()) {
        $success_message = "تم إضافة الدورة بنجاح!";
    } else {
        $error_message = "حدث خطأ أثناء إضافة الدورة";
    }
    
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>إضافة دورة جديدة</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* نسخ نفس التنسيقات الأساسية من homeexpert.php */
        :root {
            --primary-color: #2c6eaf;
            --secondary-color: #1d4e80;
            --accent-color: #46b3e6;
            --dark-color: #1a2c42;
            --light-color: #f0f5fc;
            --success-color: #2ecc71;
            --border-radius: 8px;
            --box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .course-form {
            max-width: 800px;
            margin: 40px auto;
            padding: 30px;
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: var(--dark-color);
        }

        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: var(--border-radius);
            font-size: 16px;
        }

        .form-group textarea {
            height: 150px;
            resize: vertical;
        }

        .submit-btn {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: var(--border-radius);
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
        }

        .submit-btn:hover {
            background-color: var(--secondary-color);
        }

        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: var(--border-radius);
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
    <div class="course-form">
        <h1>إضافة دورة جديدة</h1>
        
        <?php if ($success_message): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>
        
        <?php if ($error_message): ?>
            <div class="alert alert-error"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label>اسم الدورة</label>
                <input type="text" name="course_name" required>
            </div>

            <div class="form-group">
                <label>وصف الدورة</label>
                <textarea name="description" required></textarea>
            </div>

            <div class="form-group">
                <label>السعر</label>
                <input type="number" name="price" required min="0">
            </div>

            <div class="form-group">
                <label>التصنيف</label>
                <select name="category" required>
                    <option value="">اختر التصنيف</option>
                    <option value="programming">برمجة</option>
                    <option value="design">تصميم</option>
                    <option value="business">أعمال</option>
                    <option value="marketing">تسويق</option>
                    <option value="other">أخرى</option>
                </select>
            </div>

            <div class="form-group">
                <label>صورة الغلاف</label>
                <input type="file" name="cover_image" accept="image/*">
            </div>

            <button type="submit" class="submit-btn">إضافة الدورة</button>
        </form>
    </div>
</body>
</html>
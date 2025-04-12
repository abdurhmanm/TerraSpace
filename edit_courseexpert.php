<?php
session_start();
if (!isset($_SESSION["expert_id"])) {
    header("Location: login.html");
    exit();
}

$conn = new mysqli("localhost", "root", "", "tira_space");
$course_id = $_GET['id'] ?? null;
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    
    $sql = "UPDATE courses SET course_name = ?, description = ?, price = ? WHERE id = ? AND expert_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssdii", $title, $description, $price, $course_id, $_SESSION["expert_id"]);
    
    if ($stmt->execute()) {
        $message = '<div class="bg-green-500 text-white p-4 rounded mb-4">تم تحديث الدورة بنجاح</div>';
    } else {
        $message = '<div class="bg-red-500 text-white p-4 rounded mb-4">فشل تحديث الدورة</div>';
    }
}

$sql = "SELECT * FROM courses WHERE id = ? AND expert_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $course_id, $_SESSION["expert_id"]);
$stmt->execute();
$result = $stmt->get_result();
$course = $result->fetch_assoc();

if (!$course) {
    header("Location: my_coursesexpert.html");
    exit();
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تعديل الدورة | تيرا سبيس</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-[#1a1a1a] to-[#4a4a4a] text-white min-h-screen p-6">
    <div class="max-w-3xl mx-auto">
        <?php echo $message; ?>
        
        <form method="POST" class="bg-[#2a2a2a] p-6 rounded-lg shadow-xl">
            <h1 class="text-2xl font-bold text-[#46b3e6] mb-6">تعديل الدورة</h1>
            
            <div class="mb-4">
                <label class="block text-[#46b3e6] mb-2">عنوان الدورة</label>
                <input type="text" name="title" value="<?php echo htmlspecialchars($course['course_name']); ?>"
                       class="w-full bg-[#3a3a3a] text-white p-2 rounded">
            </div>
            
            <div class="mb-4">
                <label class="block text-[#46b3e6] mb-2">الوصف</label>
                <textarea name="description" class="w-full bg-[#3a3a3a] text-white p-2 rounded h-32"><?php 
                    echo htmlspecialchars($course['description']); 
                ?></textarea>
            </div>
            
            <div class="mb-6">
                <label class="block text-[#46b3e6] mb-2">السعر</label>
                <input type="number" name="price" value="<?php echo htmlspecialchars($course['price']); ?>"
                       class="w-full bg-[#3a3a3a] text-white p-2 rounded">
            </div>
            
            <div class="flex gap-4">
                <button type="submit" 
                        class="bg-[#46b3e6] text-white px-6 py-2 rounded hover:bg-[#2c6eaf] transition-colors">
                    حفظ التغييرات
                </button>
                <a href="my_coursesexpert.html" 
                   class="bg-[#3a3a3a] text-white px-6 py-2 rounded hover:bg-[#2a2a2a] transition-colors">
                    إلغاء
                </a>
            </div>
        </form>
    </div>
</body>
</html>
<?php
session_start();
if (!isset($_SESSION["expert_id"])) {
    header("Location: login.html");
    exit();
}

$conn = new mysqli("localhost", "root", "", "tira_space");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$expert_id = $_SESSION["expert_id"];

// Get expert data
$sql = "SELECT * FROM users WHERE id = ? AND role = 'expert'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $expert_id);
$stmt->execute();
$expert = $stmt->get_result()->fetch_assoc();

// Get expert skills
$sql = "SELECT skill_name FROM expert_skills WHERE expert_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $expert_id);
$stmt->execute();
$skills = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Get expert statistics
$sql = "SELECT 
        (SELECT COUNT(*) FROM courses WHERE expert_id = ?) as total_courses,
        (SELECT COUNT(DISTINCT ce.student_id) FROM course_enrollments ce 
         JOIN courses c ON ce.course_id = c.id WHERE c.expert_id = ?) as total_students,
        (SELECT AVG(rating) FROM expert_ratings WHERE expert_id = ?) as avg_rating";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iii", $expert_id, $expert_id, $expert_id);
$stmt->execute();
$stats = $stmt->get_result()->fetch_assoc();

$conn->close();
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>الملف الشخصي | تيرا سبيس</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-[#1a1a1a] to-[#4a4a4a] text-white min-h-screen p-6">
    <!-- زر العودة -->
    <a href="homeexpert.html" class="inline-flex items-center text-[#46b3e6] hover:text-[#2c6eaf] mb-6 transition-colors">
        <i class="fas fa-arrow-right ml-2"></i>
        العودة للرئيسية
    </a>

    <div class="max-w-7xl mx-auto">
        <!-- معلومات الملف الشخصي -->
        <div class="bg-[#2a2a2a] rounded-lg shadow-xl p-8 mb-8">
            <div class="flex flex-col md:flex-row gap-8">
                <!-- الصورة والمعلومات الأساسية -->
                <div class="md:w-1/3 text-center">
                    <div class="w-48 h-48 bg-[#46b3e6] rounded-full mx-auto mb-4 flex items-center justify-center text-4xl font-bold">
                        <?php echo strtoupper(substr($expert['name'], 0, 1)); ?>
                    </div>
                    <h1 class="text-2xl font-bold text-[#46b3e6] mb-2"><?php echo htmlspecialchars($expert['name']); ?></h1>
                    <p class="text-gray-400 mb-4"><?php echo htmlspecialchars($expert['email']); ?></p>
                    <button onclick="openEditProfile()" 
                            class="bg-[#46b3e6] text-white px-6 py-2 rounded-lg hover:bg-[#2c6eaf] transition-all">
                        <i class="fas fa-edit ml-2"></i>
                        تعديل الملف الشخصي
                    </button>
                </div>

                <!-- الإحصائيات -->
                <div class="md:w-2/3">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                        <div class="bg-[#3a3a3a] p-6 rounded-lg text-center">
                            <div class="text-3xl font-bold text-[#46b3e6] mb-2">
                                <?php echo number_format($stats['total_courses']); ?>
                            </div>
                            <div class="text-gray-400">الدورات التدريبية</div>
                        </div>
                        <div class="bg-[#3a3a3a] p-6 rounded-lg text-center">
                            <div class="text-3xl font-bold text-[#46b3e6] mb-2">
                                <?php echo number_format($stats['total_students']); ?>
                            </div>
                            <div class="text-gray-400">الطلاب المسجلين</div>
                        </div>
                        <div class="bg-[#3a3a3a] p-6 rounded-lg text-center">
                            <div class="text-3xl font-bold text-[#46b3e6] mb-2">
                                <?php echo number_format($stats['avg_rating'], 1); ?>
                            </div>
                            <div class="text-gray-400">متوسط التقييم</div>
                        </div>
                    </div>

                    <!-- المهارات -->
                    <div class="mb-8">
                        <h2 class="text-xl font-bold text-[#46b3e6] mb-4">المهارات</h2>
                        <div class="flex flex-wrap gap-2">
                            <?php foreach ($skills as $skill): ?>
                            <span class="bg-[#3a3a3a] text-[#46b3e6] px-4 py-2 rounded-full">
                                <?php echo htmlspecialchars($skill['skill_name']); ?>
                            </span>
                            <?php endforeach; ?>
                            <button onclick="addSkill()" 
                                    class="bg-[#3a3a3a] text-[#46b3e6] px-4 py-2 rounded-full hover:bg-[#46b3e6] hover:text-white transition-all">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>

                    <!-- نبذة شخصية -->
                    <div>
                        <h2 class="text-xl font-bold text-[#46b3e6] mb-4">نبذة شخصية</h2>
                        <p class="text-gray-400">
                            <?php echo htmlspecialchars($expert['bio'] ?? 'لا توجد نبذة شخصية بعد.'); ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openEditProfile() {
            // سيتم تنفيذ تعديل الملف الشخصي
            window.location.href = 'edit_profile.php';
        }

        function addSkill() {
            const skill = prompt('أدخل المهارة الجديدة:');
            if (skill) {
                fetch('add_skill.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ skill_name: skill })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert(data.message || 'حدث خطأ أثناء إضافة المهارة');
                    }
                })
                .catch(error => alert('حدث خطأ أثناء إضافة المهارة'));
            }
        }
    </script>
</body>
</html>
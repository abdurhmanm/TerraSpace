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

// Get courses for filter dropdown
$courses_sql = "SELECT DISTINCT c.id, c.course_name 
                FROM courses c 
                WHERE c.expert_id = ?";
$stmt = $conn->prepare($courses_sql);
$stmt->bind_param("i", $_SESSION["expert_id"]);
$stmt->execute();
$courses_result = $stmt->get_result();

// Get students
$students_sql = "SELECT s.*, c.course_name 
                 FROM students s 
                 JOIN courses c ON s.course_id = c.id 
                 WHERE c.expert_id = ?";
$stmt = $conn->prepare($students_sql);
$stmt->bind_param("i", $_SESSION["expert_id"]);
$stmt->execute();
$students_result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>طلابي | تيرا سبيس</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-[#1a1a1a] to-[#4a4a4a] text-white min-h-screen p-6">
    <!-- ... existing header code ... -->

    <!-- شريط البحث والفلترة -->
    <div class="bg-[#2a2a2a] p-4 rounded-lg shadow-xl mb-6">
        <div class="flex flex-wrap gap-4">
            <input type="text" id="searchInput"
                   placeholder="ابحث عن طالب..." 
                   class="flex-1 bg-[#3a3a3a] text-white px-4 py-2 rounded-lg border border-[#46b3e6] focus:outline-none focus:ring-2 focus:ring-[#2c6eaf]">
            
            <select id="courseFilter" class="bg-[#3a3a3a] text-white px-4 py-2 rounded-lg border border-[#46b3e6] focus:outline-none focus:ring-2 focus:ring-[#2c6eaf]">
                <option value="">كل الدورات</option>
                <?php while($course = $courses_result->fetch_assoc()): ?>
                    <option value="<?php echo htmlspecialchars($course['id']); ?>">
                        <?php echo htmlspecialchars($course['course_name']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
    </div>

    <!-- جدول الطلاب -->
    <div class="bg-[#2a2a2a] rounded-lg shadow-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-[#1a1a1a]">
                    <tr>
                        <th class="py-4 px-6 text-right text-[#46b3e6]">اسم الطالب</th>
                        <th class="py-4 px-6 text-right text-[#46b3e6]">الدورة</th>
                        <th class="py-4 px-6 text-right text-[#46b3e6]">تاريخ التسجيل</th>
                        <th class="py-4 px-6 text-right text-[#46b3e6]">التقدم</th>
                        <th class="py-4 px-6 text-right text-[#46b3e6]">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#3a3a3a]">
                    <?php while($student = $students_result->fetch_assoc()): ?>
                    <tr class="hover:bg-[#3a3a3a] transition-colors">
                        <td class="py-4 px-6">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-[#46b3e6] rounded-full flex items-center justify-center text-white font-bold mr-3">
                                    <?php echo substr($student['name'], 0, 1); ?>
                                </div>
                                <div>
                                    <div class="font-medium"><?php echo htmlspecialchars($student['name']); ?></div>
                                    <div class="text-sm text-gray-400"><?php echo htmlspecialchars($student['email']); ?></div>
                                </div>
                            </div>
                        </td>
                        <td class="py-4 px-6"><?php echo htmlspecialchars($student['course_name']); ?></td>
                        <td class="py-4 px-6"><?php echo date('Y/m/d', strtotime($student['registration_date'])); ?></td>
                        <td class="py-4 px-6">
                            <div class="w-full bg-[#1a1a1a] rounded-full h-2.5">
                                <div class="bg-[#46b3e6] h-2.5 rounded-full" style="width: <?php echo $student['progress']; ?>%"></div>
                            </div>
                            <span class="text-sm text-gray-400"><?php echo $student['progress']; ?>%</span>
                        </td>
                        <td class="py-4 px-6">
                            <div class="flex gap-2">
                                <button onclick="showMessageModal('<?php echo htmlspecialchars($student['name']); ?>', '<?php echo htmlspecialchars($student['email']); ?>')" 
                                        class="bg-[#3a3a3a] text-[#46b3e6] p-2 rounded hover:bg-[#2c6eaf] hover:text-white transition-all">
                                    <i class="fas fa-envelope"></i>
                                </button>
                                <button onclick="showProgressDetails('<?php echo htmlspecialchars($student['name']); ?>', <?php echo $student['id']; ?>)" 
                                        class="bg-[#3a3a3a] text-[#46b3e6] p-2 rounded hover:bg-[#2c6eaf] hover:text-white transition-all">
                                    <i class="fas fa-chart-line"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- ... existing pagination and JavaScript code ... -->
</body>
</html>
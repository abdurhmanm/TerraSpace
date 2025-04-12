<?php
include 'configcor.php';

// جلب الدورات من قاعدة البيانات
$sql = "SELECT * FROM courses";
$result = $conn->query($sql);
?>

<?php
$sql = "SELECT * FROM courses";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<div class='course'>";
        echo "<h3>" . $row['course_name'] . "</h3>";
        echo "<p>المدرب: " . $row['instructor_name'] . "</p>";
        echo "<button>التسجيل الآن</button>";
        echo "</div>";
    }
} else {
    echo "لا توجد دورات متاحة حاليًا";
}
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>قائمة الدورات</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-10">

    <h1 class="text-3xl font-bold text-center mb-6">📚 قائمة الدورات المتاحة</h1>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()) : ?>
                <div class="bg-white p-4 rounded-lg shadow-lg hover:shadow-2xl transition">
                    <img src="images/<?= htmlspecialchars($row['image']) ?>" class="w-full h-40 object-cover rounded" alt="<?= htmlspecialchars($row['title']) ?>">
                    <h2 class="text-xl font-semibold mt-2"><?= htmlspecialchars($row['title']) ?></h2>
                    <p class="text-gray-600 mt-1"><?= htmlspecialchars($row['description']) ?></p>
                    <p class="text-lg font-bold text-blue-600 mt-2"><?= number_format($row['price'], 2) ?> ريال</p>
                    <button class="mt-3 bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-700">🔗 التسجيل الآن</button>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="text-center text-gray-500">❌ لا توجد دورات متاحة حاليًا.</p>
        <?php endif; ?>
    </div>

</body>
</html>

<?php $conn->close(); ?>

<?php
include 'db.php';

$sql = "SELECT * FROM coourses";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>الدورات</title>
    <style>
        body { font-family: Arial, sans-serif; direction: rtl; text-align: right; }
        .course { border: 1px solid #ddd; padding: 10px; margin: 10px; border-radius: 5px; background: #f9f9f9; }
    </style>
</head>
<body>

<h2>قائمة الدورات</h2>

<?php
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo "<div class='course'>";
        echo "<h3>" . $row["title"] . "</h3>";
        echo "<p>التصنيف: " . $row["category"] . "</p>";
        echo "<p>المحاضر: " . $row["instructor"] . "</p>";
        echo "<p>التقييم: " . $row["rating"] . " ⭐</p>";
        echo "<p>التاريخ: " . $row["date"] . "</p>";
        echo "</div>";
    }
} else {
    echo "لا توجد دورات متاحة.";
}
$conn->close();
?>

</body>
</html>

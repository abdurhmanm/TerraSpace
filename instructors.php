<?php
include 'db.php';

$sql = "SELECT * FROM instructors";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>المدربون</title>
    <style>
        body { font-family: Arial, sans-serif; direction: rtl; text-align: right; }
        .instructor { border: 1px solid #ddd; padding: 10px; margin: 10px; border-radius: 5px; background: #f9f9f9; }
    </style>
</head>
<body>

<h2>قائمة المدربين</h2>

<?php
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo "<div class='instructor'>";
        echo "<h3>" . $row["name"] . "</h3>";
        echo "<p>نبذة: " . $row["bio"] . "</p>";
        echo "<p>التقييم: " . $row["rating"] . " ⭐</p>";
        echo "</div>";
    }
} else {
    echo "لا يوجد مدربون متاحون.";
}
$conn->close();
?>

</body>
</html>

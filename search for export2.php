<?php
include 'db.php';
$sql = "SELECT * FROM coourses";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "<div class='grid md:grid-cols-3 gap-4'>";
    while ($row = $result->fetch_assoc()) {
        echo "<div class='bg-gray-800 p-4 rounded-lg hover:scale-105 transition-transform'>";
        echo "<h2 class='text-lg font-semibold'>" . $row['title'] . "</h2>";
        echo "<p class='text-sm text-gray-400'>📅 تاريخ البدء: " . $row['start_date'] . "</p>";
        echo "<p class='text-sm text-gray-400'>📖 " . $row['description'] . "</p>";
        echo "</div>";
    }
    echo "</div>";
} else {
    echo "<p class='text-white'>❌ لا توجد دورات متاحة.</p>";
}
$conn->close();
?>

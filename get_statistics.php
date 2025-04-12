<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION["expert_id"])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$conn = new mysqli("localhost", "root", "", "tira_space");
$expert_id = $_SESSION["expert_id"];
$period = $_GET['period'] ?? 'week';

$date_condition = match($period) {
    'week' => 'INTERVAL 7 DAY',
    'month' => 'INTERVAL 1 MONTH',
    'year' => 'INTERVAL 1 YEAR',
    default => 'INTERVAL 7 DAY'
};

// Get statistics for the selected period
$sql = "SELECT 
            AVG(cs.completion_rate) as completion_rate,
            COUNT(DISTINCT sa.student_id) as active_students,
            AVG(cs.engagement_rate) as engagement_rate
        FROM course_statistics cs
        LEFT JOIN student_activity sa ON cs.course_id = sa.course_id
        WHERE cs.expert_id = ? 
        AND cs.created_at >= DATE_SUB(NOW(), $date_condition)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $expert_id);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();

echo json_encode([
    'success' => true,
    'data' => $result,
    'period' => $period
]);

$conn->close();
?>

<script>
// ...existing chart initialization code...

function updateStatistics(data) {
    // Update completion rate
    document.querySelector('[data-stat="completion"]').textContent = 
        `${data.completion_rate.toFixed(1)}%`;
    
    // Update active students
    document.querySelector('[data-stat="students"]').textContent = 
        data.active_students;
    
    // Update engagement rate
    document.querySelector('[data-stat="engagement"]').textContent = 
        `${data.engagement_rate.toFixed(1)}%`;
    
    // Update charts
    updateCharts(data);
}

function filterStats(period) {
    fetch(`get_statistics.php?period=${period}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateStatistics(data.data);
                // Show success message
                showAlert('تم تحديث البيانات بنجاح', 'success');
            } else {
                throw new Error(data.error);
            }
        })
        .catch(error => {
            showAlert('حدث خطأ أثناء تحديث البيانات', 'error');
            console.error('Error:', error);
        });
}

function showAlert(message, type) {
    const alert = document.createElement('div');
    alert.className = `fixed top-4 right-4 p-4 rounded-lg ${
        type === 'success' ? 'bg-green-500' : 'bg-red-500'
    } text-white z-50`;
    alert.textContent = message;
    document.body.appendChild(alert);
    setTimeout(() => alert.remove(), 3000);
}

// Initialize with weekly data
document.addEventListener('DOMContentLoaded', () => {
    initCharts();
    filterStats('week');
});
</script>
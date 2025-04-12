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

// Get total earnings
$sql = "SELECT SUM(amount) as total_earnings FROM transactions WHERE expert_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $expert_id);
$stmt->execute();
$total_earnings = $stmt->get_result()->fetch_assoc()['total_earnings'] ?? 0;

// Get total sales count
$sql = "SELECT COUNT(*) as total_sales FROM transactions WHERE expert_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $expert_id);
$stmt->execute();
$total_sales = $stmt->get_result()->fetch_assoc()['total_sales'] ?? 0;

// Get new students count (last 30 days)
$sql = "SELECT COUNT(DISTINCT student_id) as new_students 
        FROM transactions 
        WHERE expert_id = ? AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $expert_id);
$stmt->execute();
$new_students = $stmt->get_result()->fetch_assoc()['new_students'] ?? 0;

// Get average rating
$sql = "SELECT AVG(rating) as avg_rating FROM course_ratings WHERE expert_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $expert_id);
$stmt->execute();
$avg_rating = $stmt->get_result()->fetch_assoc()['avg_rating'] ?? 0;

// Get monthly earnings for chart
$sql = "SELECT DATE_FORMAT(created_at, '%Y-%m') as month, 
        SUM(amount) as monthly_total 
        FROM transactions 
        WHERE expert_id = ? 
        GROUP BY month 
        ORDER BY month DESC 
        LIMIT 6";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $expert_id);
$stmt->execute();
$monthly_result = $stmt->get_result();

$months = [];
$earnings = [];
while ($row = $monthly_result->fetch_assoc()) {
    array_unshift($months, date('F', strtotime($row['month'])));
    array_unshift($earnings, $row['monthly_total']);
}

// Get recent transactions
$sql = "SELECT t.*, c.course_name, s.name as student_name 
        FROM transactions t
        JOIN courses c ON t.course_id = c.id
        JOIN students s ON t.student_id = s.id
        WHERE t.expert_id = ?
        ORDER BY t.created_at DESC
        LIMIT 3";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $expert_id);
$stmt->execute();
$recent_transactions = $stmt->get_result();

// Calculate growth percentages
$sql = "SELECT 
            (SELECT SUM(amount) FROM transactions 
             WHERE expert_id = ? 
             AND created_at >= DATE_FORMAT(NOW() - INTERVAL 1 MONTH, '%Y-%m-01')) as current_month,
            (SELECT SUM(amount) FROM transactions 
             WHERE expert_id = ? 
             AND created_at >= DATE_FORMAT(NOW() - INTERVAL 2 MONTH, '%Y-%m-01')
             AND created_at < DATE_FORMAT(NOW() - INTERVAL 1 MONTH, '%Y-%m-01')) as last_month";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $expert_id, $expert_id);
$stmt->execute();
$growth_data = $stmt->get_result()->fetch_assoc();

$growth_percentage = 0;
if ($growth_data['last_month'] > 0) {
    $growth_percentage = (($growth_data['current_month'] - $growth_data['last_month']) / $growth_data['last_month']) * 100;
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<!-- ... existing head content ... -->

<body class="bg-gradient-to-br from-[#1a1a1a] to-[#4a4a4a] text-white min-h-screen p-6">
    <!-- ... existing navigation ... -->

    <div class="max-w-7xl mx-auto">
        <!-- Filter Controls -->
        <div class="bg-[#2a2a2a] p-4 rounded-lg shadow-xl mb-6">
            <div class="flex flex-wrap gap-4">
                <select id="periodFilter" class="bg-[#3a3a3a] text-white px-4 py-2 rounded-lg border border-[#46b3e6]">
                    <option value="all">كل الفترات</option>
                    <option value="month">هذا الشهر</option>
                    <option value="quarter">هذا الربع</option>
                    <option value="year">هذا العام</option>
                </select>
                <button onclick="refreshData()" 
                        class="bg-[#46b3e6] text-white px-4 py-2 rounded-lg hover:bg-[#2c6eaf] transition-all">
                    <i class="fas fa-sync-alt ml-2"></i>تحديث
                </button>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- إجمالي الأرباح -->
            <div class="bg-[#2a2a2a] p-6 rounded-lg shadow-xl">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-[#46b3e6]">إجمالي الأرباح</h3>
                    <i class="fas fa-dollar-sign text-2xl text-[#46b3e6]"></i>
                </div>
                <div class="text-3xl font-bold"><?php echo number_format($total_earnings, 2); ?> ر.س</div>
                <div class="text-<?php echo $growth_percentage >= 0 ? 'green' : 'red'; ?>-500 text-sm mt-2">
                    <i class="fas fa-arrow-<?php echo $growth_percentage >= 0 ? 'up' : 'down'; ?>"></i>
                    <?php echo abs(round($growth_percentage)); ?>% من الشهر الماضي
                </div>
            </div>
            <!-- ... other cards ... -->
        </div>

        <!-- Recent Transactions -->
        <div class="bg-[#2a2a2a] p-6 rounded-lg shadow-xl">
            <h3 class="text-xl font-bold text-[#46b3e6] mb-4">آخر المعاملات</h3>
            <div class="space-y-4">
                <?php while($transaction = $recent_transactions->fetch_assoc()): ?>
                <div class="flex items-center justify-between p-3 bg-[#3a3a3a] rounded-lg">
                    <div>
                        <div class="font-medium"><?php echo htmlspecialchars($transaction['course_name']); ?></div>
                        <div class="text-sm text-gray-400"><?php echo htmlspecialchars($transaction['student_name']); ?></div>
                    </div>
                    <div class="text-green-500">+<?php echo number_format($transaction['amount'], 2); ?> ر.س</div>
                </div>
                <?php endwhile; ?>
            </div>
        </div>

        <!-- Add JavaScript for interactivity -->
        <script>
            function refreshData() {
                const period = document.getElementById('periodFilter').value;
                fetch(`get_earnings_data.php?period=${period}`)
                    .then(response => response.json())
                    .then(data => {
                        updateStatistics(data);
                        updateChart(data.chartData);
                        showAlert('تم تحديث البيانات بنجاح', 'success');
                    })
                    .catch(error => {
                        showAlert('حدث خطأ أثناء تحديث البيانات', 'error');
                    });
            }

            function updateStatistics(data) {
                // Update statistics cards
                document.querySelector('#totalEarnings').textContent = 
                    `${data.total.toLocaleString()} ر.س`;
                document.querySelector('#salesCount').textContent = 
                    data.sales.toLocaleString();
                // ... update other statistics
            }

            function showAlert(message, type) {
                const alert = document.createElement('div');
                alert.className = `fixed top-4 right-4 p-4 rounded-lg ${
                    type === 'success' ? 'bg-green-500' : 'bg-red-500'
                } text-white`;
                alert.textContent = message;
                document.body.appendChild(alert);
                setTimeout(() => alert.remove(), 3000);
            }

            // Initialize chart with PHP data
            const months = <?php echo json_encode($months); ?>;
            const earnings = <?php echo json_encode($earnings); ?>;
            const ctx = document.getElementById('earningsChart').getContext('2d');
            const chart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: months,
                    datasets: [{
                        label: 'الأرباح الشهرية',
                        data: earnings,
                        borderColor: '#46b3e6',
                        backgroundColor: 'rgba(70, 179, 230, 0.1)',
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    // ... existing chart options ...
                }
            });

            // Add event listener for period filter
            document.getElementById('periodFilter').addEventListener('change', refreshData);
        </script>
    </div>
</body>
</html>
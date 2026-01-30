<?php
require_once '../config/db.php';
require_once '../includes/header.php';

// Get data for charts using SQL
$monthly_data_sql = "SELECT 
    MONTHNAME(visit_date) as month,
    COUNT(*) as visits
FROM visits
WHERE visit_date >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
GROUP BY MONTH(visit_date)
ORDER BY MONTH(visit_date)";

$monthly_data = $conn->query($monthly_data_sql);

$months = [];
$visit_counts = [];

while ($row = $monthly_data->fetch_assoc()) {
    $months[] = $row['month'];
    $visit_counts[] = $row['visits'];
}

// Age distribution
$age_dist_sql = "SELECT 
    CASE 
        WHEN TIMESTAMPDIFF(YEAR, dob, CURDATE()) < 20 THEN '0-19'
        WHEN TIMESTAMPDIFF(YEAR, dob, CURDATE()) < 40 THEN '20-39'
        WHEN TIMESTAMPDIFF(YEAR, dob, CURDATE()) < 60 THEN '40-59'
        WHEN TIMESTAMPDIFF(YEAR, dob, CURDATE()) < 80 THEN '60-79'
        ELSE '80+'
    END as age_group,
    COUNT(*) as count
FROM patients
GROUP BY age_group
ORDER BY age_group";

$age_dist = $conn->query($age_dist_sql);

$age_groups = [];
$age_counts = [];

while ($row = $age_dist->fetch_assoc()) {
    $age_groups[] = $row['age_group'];
    $age_counts[] = $row['count'];
}
?>

<header>
    <h1>Analytics Dashboard</h1>
</header>

<div class="card">
    <div class="card-header">
        <h2>Monthly Visits Trend</h2>
    </div>
    <div class="chart-container">
        <canvas id="monthlyChart"></canvas>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h2>Patient Age Distribution</h2>
    </div>
    <div class="chart-container">
        <canvas id="ageChart"></canvas>
    </div>
</div>

<script>
// Monthly Visits Chart
const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
new Chart(monthlyCtx, {
    type: 'bar',
    data: {
        labels: <?php echo json_encode($months); ?>,
        datasets: [{
            label: 'Number of Visits',
            data: <?php echo json_encode($visit_counts); ?>,
            backgroundColor: 'rgba(102, 126, 234, 0.5)',
            borderColor: 'rgba(102, 126, 234, 1)',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

// Age Distribution Chart
const ageCtx = document.getElementById('ageChart').getContext('2d');
new Chart(ageCtx, {
    type: 'pie',
    data: {
        labels: <?php echo json_encode($age_groups); ?>,
        datasets: [{
            data: <?php echo json_encode($age_counts); ?>,
            backgroundColor: [
                'rgba(255, 99, 132, 0.5)',
                'rgba(54, 162, 235, 0.5)',
                'rgba(255, 206, 86, 0.5)',
                'rgba(75, 192, 192, 0.5)',
                'rgba(153, 102, 255, 0.5)'
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false
    }
});
</script>

<?php require_once '../includes/footer.php'; ?>
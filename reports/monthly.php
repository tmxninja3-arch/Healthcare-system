<?php
require_once '../config/db.php';
require_once '../includes/header.php';

// Monthly visit count (last 6 months)
$monthly_visits_sql = "SELECT 
    DATE_FORMAT(visit_date, '%Y-%m') as month,
    MONTHNAME(visit_date) as month_name,
    YEAR(visit_date) as year,
    COUNT(*) as visit_count,
    SUM(consultation_fee + lab_fee) as total_revenue
FROM visits
WHERE visit_date >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
GROUP BY DATE_FORMAT(visit_date, '%Y-%m')
ORDER BY visit_date DESC";

$monthly_visits = $conn->query($monthly_visits_sql);

// Patients joined per month
$monthly_patients_sql = "SELECT 
    DATE_FORMAT(join_date, '%Y-%m') as month,
    MONTHNAME(join_date) as month_name,
    YEAR(join_date) as year,
    COUNT(*) as patient_count
FROM patients
WHERE join_date >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
GROUP BY DATE_FORMAT(join_date, '%Y-%m')
ORDER BY join_date DESC";

$monthly_patients = $conn->query($monthly_patients_sql);
?>

<header>
    <h1>Monthly Report</h1>
</header>

<div class="card">
    <div class="card-header">
        <h2>Monthly Visits (Last 6 Months)</h2>
    </div>
    <table>
        <thead>
            <tr>
                <th>Month</th>
                <th>Year</th>
                <th>Visit Count</th>
                <th>Total Revenue</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $monthly_visits->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['month_name']; ?></td>
                <td><?php echo $row['year']; ?></td>
                <td><span class="badge badge-info"><?php echo $row['visit_count']; ?></span></td>
                <td>$<?php echo number_format($row['total_revenue'], 2); ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<div class="card">
    <div class="card-header">
        <h2>New Patients Per Month (Last 6 Months)</h2>
    </div>
    <table>
        <thead>
            <tr>
                <th>Month</th>
                <th>Year</th>
                <th>New Patients</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $monthly_patients->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['month_name']; ?></td>
                <td><?php echo $row['year']; ?></td>
                <td><span class="badge badge-success"><?php echo $row['patient_count']; ?></span></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php require_once '../includes/footer.php'; ?>
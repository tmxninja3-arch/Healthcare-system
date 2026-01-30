<?php
require_once '../config/db.php';
require_once '../includes/header.php';

// Full summary report with all SQL calculations
$summary_sql = "SELECT 
    p.patient_id,
    p.name,
    TIMESTAMPDIFF(YEAR, p.dob, CURDATE()) as age,
    (SELECT COUNT(*) FROM visits WHERE patient_id = p.patient_id) as total_visits,
    (SELECT DATE_FORMAT(MAX(visit_date), '%M %d, %Y') 
     FROM visits WHERE patient_id = p.patient_id) as last_visit,
    (SELECT DATEDIFF(CURDATE(), MAX(visit_date)) 
     FROM visits WHERE patient_id = p.patient_id) as days_since_visit,
    (SELECT DATE_FORMAT(follow_up_due, '%M %d, %Y') 
     FROM visits WHERE patient_id = p.patient_id 
     ORDER BY visit_date DESC LIMIT 1) as next_followup
FROM patients p
ORDER BY p.name ASC";

$result = $conn->query($summary_sql);
?>

<header>
    <h1>Full Patient Summary Report</h1>
</header>

<div class="card">
    <div class="card-header">
        <h2>Complete Patient Overview</h2>
    </div>
    <table>
        <thead>
            <tr>
                <th>Patient Name</th>
                <th>Age</th>
                <th>Total Visits</th>
                <th>Last Visit</th>
                <th>Days Since</th>
                <th>Next Follow-up</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td>
                    <a href="../patients/view.php?id=<?php echo $row['patient_id']; ?>">
                        <?php echo htmlspecialchars($row['name']); ?>
                    </a>
                </td>
                <td><?php echo $row['age']; ?> years</td>
                <td>
                    <span class="badge badge-info">
                        <?php echo $row['total_visits']; ?>
                    </span>
                </td>
                <td><?php echo $row['last_visit'] ?: 'Never'; ?></td>
                <td>
                    <?php if ($row['days_since_visit']): ?>
                        <?php if ($row['days_since_visit'] > 180): ?>
                            <span class="badge badge-danger"><?php echo $row['days_since_visit']; ?> days</span>
                        <?php else: ?>
                            <?php echo $row['days_since_visit']; ?> days
                        <?php endif; ?>
                    <?php else: ?>
                        -
                    <?php endif; ?>
                </td>
                <td><?php echo $row['next_followup'] ?: 'None'; ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<div class="actions">
    <button onclick="window.print()" class="btn btn-primary">Print Report</button>
</div>

<?php require_once '../includes/footer.php'; ?>
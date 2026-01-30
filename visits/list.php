<?php
require_once '../config/db.php';
require_once '../includes/header.php';

// SQL with all date calculations
$sql = "SELECT 
    v.visit_id,
    p.name as patient_name,
    p.patient_id,
    DATE_FORMAT(v.visit_date, '%M %d, %Y') as visit_date,
    DATEDIFF(CURDATE(), v.visit_date) as days_since,       --Calculates number of days passed since the visit
    v.consultation_fee + v.lab_fee as total_fee,
    DATE_FORMAT(v.follow_up_due, '%M %d, %Y') as followup,
    CASE 
        WHEN v.follow_up_due < CURDATE() THEN 'Overdue'
        WHEN v.follow_up_due = CURDATE() THEN 'Today'
        WHEN v.follow_up_due BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY) THEN 'Upcoming'
        ELSE 'Scheduled'
    END as status
FROM visits v
JOIN patients p ON v.patient_id = p.patient_id
ORDER BY v.visit_date DESC";

$result = $conn->query($sql);
?>

<header>
    <h1>All Visits</h1>
</header>

<div class="card">
    <div class="card-header">
        <h2>Visit List</h2>
    </div>
    <table>
        <thead>
            <tr>
                <th>Visit ID</th>
                <th>Patient Name</th>
                <th>Visit Date</th>
                <th>Days Since</th>
                <th>Total Fee</th>
                <th>Follow-up Due</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['visit_id']; ?></td>
                <td>
                    <a href="../patients/view.php?id=<?php echo $row['patient_id']; ?>">
                        <?php echo htmlspecialchars($row['patient_name']); ?>
                    </a>
                </td>
                <td><?php echo $row['visit_date']; ?></td>
                <td><?php echo $row['days_since']; ?> days</td>
                <td>$<?php echo number_format($row['total_fee'], 2); ?></td>
                <td><?php echo $row['followup']; ?></td>
                <td>
                    <span class="badge badge-<?php 
                        echo $row['status'] == 'Overdue' ? 'danger' : 
                            ($row['status'] == 'Today' ? 'warning' : 
                            ($row['status'] == 'Upcoming' ? 'info' : 'success')); 
                    ?>">
                        <?php echo $row['status']; ?>
                    </span>
                </td>
                <td>
                    <a href="patient_visits.php?id=<?php echo $row['patient_id']; ?>" class="btn btn-sm btn-info">History</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php require_once '../includes/footer.php'; ?>
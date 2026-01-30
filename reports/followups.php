<?php
require_once '../config/db.php';
require_once '../includes/header.php';

// Upcoming follow-ups (next 7 days)
$upcoming_sql = "SELECT 
    p.name,
    p.phone,
    DATE_FORMAT(v.follow_up_due, '%M %d, %Y') as followup_date,
    DATEDIFF(v.follow_up_due, CURDATE()) as days_until
FROM visits v
JOIN patients p ON v.patient_id = p.patient_id
WHERE v.follow_up_due BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)
ORDER BY v.follow_up_due ASC";

$upcoming = $conn->query($upcoming_sql);

// Overdue follow-ups
$overdue_sql = "SELECT 
    p.name,
    p.phone,
    DATE_FORMAT(v.follow_up_due, '%M %d, %Y') as followup_date,
    DATEDIFF(CURDATE(), v.follow_up_due) as days_overdue                        
FROM visits v
JOIN patients p ON v.patient_id = p.patient_id
WHERE v.follow_up_due < CURDATE()
AND NOT EXISTS (
    SELECT 1 FROM visits v2 
    WHERE v2.patient_id = v.patient_id 
    AND v2.visit_date > v.follow_up_due
)
ORDER BY v.follow_up_due DESC";

$overdue = $conn->query($overdue_sql);
?>

<header>
    <h1>Follow-up Report</h1>
</header>

<div class="card">
    <div class="card-header">
        <h2>Upcoming Follow-ups (Next 7 Days)</h2>
    </div>
    <table>
        <thead>
            <tr>
                <th>Patient Name</th>
                <th>Phone</th>
                <th>Follow-up Date</th>
                <th>Days Until</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $upcoming->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['name']); ?></td>
                <td><?php echo htmlspecialchars($row['phone']); ?></td>
                <td><?php echo $row['followup_date']; ?></td>
                <td>
                    <span class="badge badge-<?php echo $row['days_until'] == 0 ? 'warning' : 'info'; ?>">
                        <?php echo $row['days_until'] == 0 ? 'Today' : $row['days_until'] . ' days'; ?>
                    </span>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<div class="card">
    <div class="card-header">
        <h2>Overdue Follow-ups</h2>
    </div>
    <table>
        <thead>
            <tr>
                <th>Patient Name</th>
                <th>Phone</th>
                <th>Due Date</th>
                <th>Days Overdue</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $overdue->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['name']); ?></td>
                <td><?php echo htmlspecialchars($row['phone']); ?></td>
                <td><?php echo $row['followup_date']; ?></td>
                <td>
                    <span class="badge badge-danger">
                        <?php echo $row['days_overdue']; ?> days overdue
                    </span>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php require_once '../includes/footer.php'; ?>
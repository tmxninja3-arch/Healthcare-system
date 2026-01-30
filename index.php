<?php
require_once 'config/db.php';
require_once 'includes/header.php';

// ALL CALCULATIONS IN SQL ONLY
$stats_sql = "SELECT 
    (SELECT COUNT(*) FROM patients) as total_patients,
    (SELECT COUNT(*) FROM visits) as total_visits,
    (SELECT COUNT(*) FROM visits WHERE visit_date = CURDATE()) as today_visits,
    (SELECT COUNT(*) FROM visits 
     WHERE follow_up_due BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)) as upcoming_followups,
    (SELECT COUNT(*) FROM visits WHERE follow_up_due < CURDATE()) as overdue_followups,
    (SELECT COUNT(*) FROM patients WHERE patient_id NOT IN (SELECT patient_id FROM visits)) as patients_no_visits,
    (SELECT COUNT(*) FROM patients 
     WHERE patient_id NOT IN (
         SELECT patient_id FROM visits WHERE visit_date > DATE_SUB(CURDATE(), INTERVAL 180 DAY)
     )) as inactive_patients";

$stats = $conn->query($stats_sql)->fetch_assoc();

// Recent visits with SQL calculations
$recent_sql = "SELECT 
    v.visit_id,
    p.name,
    DATE_FORMAT(v.visit_date, '%M %d, %Y') as visit_date_formatted,
    DATEDIFF(CURDATE(), v.visit_date) as days_ago,
    DATE_FORMAT(v.follow_up_due, '%M %d, %Y') as followup_formatted,
    CASE 
        WHEN v.follow_up_due < CURDATE() THEN 'Overdue'
        WHEN v.follow_up_due = CURDATE() THEN 'Today'
        ELSE 'Upcoming'
    END as followup_status
FROM visits v
JOIN patients p ON v.patient_id = p.patient_id
ORDER BY v.visit_date DESC
LIMIT 5";

$recent_visits = $conn->query($recent_sql);
?>

<header>
    <h1>Healthcare Management Dashboard</h1>
    <p>Welcome, <?php echo $_SESSION['username']; ?> (<?php echo $_SESSION['role']; ?>)</p>
</header>

<div class="stats-grid">
    <div class="stat-card">
        <h3><?php echo $stats['total_patients']; ?></h3>
        <p>Total Patients</p>
    </div>
    <div class="stat-card">
        <h3><?php echo $stats['total_visits']; ?></h3>
        <p>Total Visits</p>
    </div>
    <div class="stat-card">
        <h3><?php echo $stats['today_visits']; ?></h3>
        <p>Today's Visits</p>
    </div>
    <div class="stat-card">
        <h3><?php echo $stats['upcoming_followups']; ?></h3>
        <p>Upcoming Follow-ups</p>
    </div>
    <div class="stat-card">
        <h3><?php echo $stats['overdue_followups']; ?></h3>
        <p>Overdue Follow-ups</p>
    </div>
    <div class="stat-card">
        <h3><?php echo $stats['inactive_patients']; ?></h3>
        <p>Inactive Patients</p>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h2>Recent Visits</h2>
    </div>
    <table>
        <thead>
            <tr>
                <th>Patient Name</th>
                <th>Visit Date</th>
                <th>Days Ago</th>
                <th>Follow-up Due</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $recent_visits->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['name']); ?></td>
                <td><?php echo $row['visit_date_formatted']; ?></td>
                <td><?php echo $row['days_ago']; ?> days</td>
                <td><?php echo $row['followup_formatted']; ?></td>
                <td>
                    <span class="badge badge-<?php 
                        echo $row['followup_status'] == 'Overdue' ? 'danger' : 
                            ($row['followup_status'] == 'Today' ? 'warning' : 'info'); 
                    ?>">
                        <?php echo $row['followup_status']; ?>
                    </span>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php require_once 'includes/footer.php'; ?>
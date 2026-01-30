<?php
require_once '../config/db.php';
require_once '../includes/header.php';

$patient_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Get patient info with SQL calculations
$patient_sql = "SELECT 
    name,
    (SELECT COUNT(*) FROM visits WHERE patient_id = $patient_id) as total_visits,
    (SELECT DATEDIFF(MAX(visit_date), MIN(visit_date)) FROM visits WHERE patient_id = $patient_id) as days_between_visits
FROM patients 
WHERE patient_id = $patient_id";

$patient = $conn->query($patient_sql)->fetch_assoc();

if (!$patient) {
    echo '<div class="alert alert-danger">Patient not found!</div>';
    require_once '../includes/footer.php';
    exit();
}

// Get all visits for this patient
$visits_sql = "SELECT 
    visit_id,
    DATE_FORMAT(visit_date, '%M %d, %Y') as visit_date,
    consultation_fee,
    lab_fee,
    consultation_fee + lab_fee as total,
    DATE_FORMAT(follow_up_due, '%M %d, %Y') as followup
FROM visits
WHERE patient_id = $patient_id
ORDER BY visit_date DESC";

$visits = $conn->query($visits_sql);
?>

<header>
    <h1>Visit History</h1>
    <p>Patient: <?php echo htmlspecialchars($patient['name']); ?></p>
</header>

<div class="stats-grid">
    <div class="stat-card">
        <h3><?php echo $patient['total_visits']; ?></h3>
        <p>Total Visits</p>
    </div>
    <div class="stat-card">
        <h3><?php echo $patient['days_between_visits'] ?: 0; ?></h3>
        <p>Days Between First & Last Visit</p>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h2>All Visits</h2>
    </div>
    <table>
        <thead>
            <tr>
                <th>Visit ID</th>
                <th>Visit Date</th>
                <th>Consultation Fee</th>
                <th>Lab Fee</th>
                <th>Total</th>
                <th>Follow-up Due</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($visit = $visits->fetch_assoc()): ?>
            <tr>
                <td><?php echo $visit['visit_id']; ?></td>
                <td><?php echo $visit['visit_date']; ?></td>
                <td>$<?php echo number_format($visit['consultation_fee'], 2); ?></td>
                <td>$<?php echo number_format($visit['lab_fee'], 2); ?></td>
                <td>$<?php echo number_format($visit['total'], 2); ?></td>
                <td><?php echo $visit['followup']; ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<div class="actions">
    <a href="../patients/view.php?id=<?php echo $patient_id; ?>" class="btn btn-info">Back to Patient</a>
    <a href="add.php?patient_id=<?php echo $patient_id; ?>" class="btn btn-success">Add New Visit</a>
</div>

<?php require_once '../includes/footer.php'; ?>
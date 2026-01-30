<?php
require_once '../config/db.php';
require_once '../includes/header.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Get patient details with SQL calculations
$sql = "SELECT 
    p.*,
    DATE_FORMAT(p.dob, '%M %d, %Y') as dob_formatted,
    TIMESTAMPDIFF(YEAR, p.dob, CURDATE()) as age,
    DATE_FORMAT(p.join_date, '%M %d, %Y') as join_formatted,
    (SELECT COUNT(*) FROM visits WHERE patient_id = p.patient_id) as total_visits,
    (SELECT DATE_FORMAT(MAX(visit_date), '%M %d, %Y') FROM visits WHERE patient_id = p.patient_id) as last_visit,
    (SELECT DATEDIFF(CURDATE(), MAX(visit_date)) FROM visits WHERE patient_id = p.patient_id) as days_since_visit,
    (SELECT DATE_FORMAT(follow_up_due, '%M %d, %Y') FROM visits WHERE patient_id = p.patient_id ORDER BY visit_date DESC LIMIT 1) as next_followup,
    (SELECT CASE 
        WHEN follow_up_due < CURDATE() THEN 'Overdue'
        WHEN follow_up_due = CURDATE() THEN 'Today'
        ELSE 'Scheduled'
    END FROM visits WHERE patient_id = p.patient_id ORDER BY visit_date DESC LIMIT 1) as followup_status
FROM patients p
WHERE p.patient_id = $id";

$patient = $conn->query($sql)->fetch_assoc();

if (!$patient) {
    echo '<div class="alert alert-danger">Patient not found!</div>';
    require_once '../includes/footer.php';
    exit();
}

// Get visit history
$visits_sql = "SELECT 
    visit_id,
    DATE_FORMAT(visit_date, '%M %d, %Y') as visit_date,
    consultation_fee + lab_fee as total_fee,
    DATE_FORMAT(follow_up_due, '%M %d, %Y') as followup
FROM visits
WHERE patient_id = $id
ORDER BY visit_date DESC";

$visits = $conn->query($visits_sql);
?>

<header>
    <h1>Patient Profile</h1>
</header>

<div class="card">
    <div class="card-header">
        <h2><?php echo htmlspecialchars($patient['name']); ?></h2>
    </div>
    <table>
        <tr>
            <th>Patient ID:</th>
            <td><?php echo $patient['patient_id']; ?></td>
        </tr>
        <tr>
            <th>Date of Birth:</th>
            <td><?php echo $patient['dob_formatted']; ?></td>
        </tr>
        <tr>
            <th>Age:</th>
            <td><?php echo $patient['age']; ?> years</td>
        </tr>
        <tr>
            <th>Join Date:</th>
            <td><?php echo $patient['join_formatted']; ?></td>
        </tr>
        <tr>
            <th>Phone:</th>
            <td><?php echo htmlspecialchars($patient['phone']); ?></td>
        </tr>
        <tr>
            <th>Address:</th>
            <td><?php echo htmlspecialchars($patient['address']); ?></td>
        </tr>
        <tr>
            <th>Total Visits:</th>
            <td><?php echo $patient['total_visits']; ?></td>
        </tr>
        <tr>
            <th>Last Visit:</th>
            <td><?php echo $patient['last_visit'] ?: 'Never'; ?></td>
        </tr>
        <?php if ($patient['days_since_visit']): ?>
        <tr>
            <th>Days Since Last Visit:</th>
            <td><?php echo $patient['days_since_visit']; ?> days</td>
        </tr>
        <?php endif; ?>
        <?php if ($patient['next_followup']): ?>
        <tr>
            <th>Next Follow-up:</th>
            <td>
                <?php echo $patient['next_followup']; ?>
                <span class="badge badge-<?php 
                    echo $patient['followup_status'] == 'Overdue' ? 'danger' : 
                        ($patient['followup_status'] == 'Today' ? 'warning' : 'success'); 
                ?>">
                    <?php echo $patient['followup_status']; ?>
                </span>
            </td>
        </tr>
        <?php endif; ?>
    </table>
</div>

<div class="card">
    <div class="card-header">
        <h2>Visit History</h2>
    </div>
    <table>
        <thead>
            <tr>
                <th>Visit Date</th>
                <th>Total Fee</th>
                <th>Follow-up Due</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($visit = $visits->fetch_assoc()): ?>
            <tr>
                <td><?php echo $visit['visit_date']; ?></td>
                <td>$<?php echo number_format($visit['total_fee'], 2); ?></td>
                <td><?php echo $visit['followup']; ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<div class="actions">
    <a href="edit.php?id=<?php echo $id; ?>" class="btn btn-warning">Edit Patient</a>
    <a href="../visits/add.php?patient_id=<?php echo $id; ?>" class="btn btn-success">Add Visit</a>
    <a href="list.php" class="btn btn-info">Back to List</a>
</div>

<?php require_once '../includes/footer.php'; ?>
<?php
require_once '../config/db.php';
require_once '../includes/header.php';

// Get all patients for dropdown
$patients_sql = "SELECT patient_id, name FROM patients ORDER BY name";
$patients = $conn->query($patients_sql);

$patient_id = isset($_GET['patient_id']) ? intval($_GET['patient_id']) : 0;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $patient_id = intval($_POST['patient_id']);
    $visit_date = mysqli_real_escape_string($conn, $_POST['visit_date']);
    $consultation_fee = floatval($_POST['consultation_fee']);
    $lab_fee = floatval($_POST['lab_fee']);
    
    // SQL to calculate follow-up date (add 7 days)
    $sql = "INSERT INTO visits (patient_id, visit_date, consultation_fee, lab_fee, follow_up_due) 
            VALUES ($patient_id, '$visit_date', $consultation_fee, $lab_fee, DATE_ADD('$visit_date', INTERVAL 7 DAY))";
    
    if ($conn->query($sql)) {
        echo '<div class="alert alert-success">Visit added successfully! Follow-up scheduled for 7 days later.</div>';
    } else {
        echo '<div class="alert alert-danger">Error: ' . $conn->error . '</div>';
    }
}
?>

<header>
    <h1>Add New Visit</h1>
</header>

<div class="card">
    <div class="card-header">
        <h2>Visit Registration Form</h2>
    </div>
    <form method="POST">
        <div class="form-row">
            <div class="form-group">
                <label>Patient *</label>
                <select name="patient_id" class="form-control" required>
                    <option value="">Select Patient</option>
                    <?php while ($patient = $patients->fetch_assoc()): ?>
                    <option value="<?php echo $patient['patient_id']; ?>" 
                            <?php echo $patient_id == $patient['patient_id'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($patient['name']); ?>
                    </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Visit Date *</label>
                <input type="date" name="visit_date" class="form-control" required value="<?php echo date('Y-m-d'); ?>">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Consultation Fee</label>
                <input type="number" name="consultation_fee" class="form-control" step="0.01" value="0">
            </div>
            <div class="form-group">
                <label>Lab Fee</label>
                <input type="number" name="lab_fee" class="form-control" step="0.01" value="0">
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Add Visit</button>
        <a href="list.php" class="btn btn-info">View All Visits</a>
    </form>
</div>

<?php require_once '../includes/footer.php'; ?>
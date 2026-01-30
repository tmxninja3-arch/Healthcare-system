<?php
require_once '../config/db.php';
require_once '../includes/header.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Get patient data
$sql = "SELECT * FROM patients WHERE patient_id = $id";
$result = $conn->query($sql);
$patient = $result->fetch_assoc();

if (!$patient) {
    echo '<div class="alert alert-danger">Patient not found!</div>';
    require_once '../includes/footer.php';
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $dob = mysqli_real_escape_string($conn, $_POST['dob']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    
    $update_sql = "UPDATE patients 
                   SET name='$name', dob='$dob', phone='$phone', address='$address' 
                   WHERE patient_id=$id";
    
    if ($conn->query($update_sql)) {
        echo '<div class="alert alert-success">Patient updated successfully!</div>';
        // Refresh patient data
        $patient = $conn->query($sql)->fetch_assoc();
    } else {
        echo '<div class="alert alert-danger">Error: ' . $conn->error . '</div>';
    }
}
?>

<header>
    <h1>Edit Patient</h1>
</header>

<div class="card">
    <div class="card-header">
        <h2>Edit Patient Information</h2>
    </div>
    <form method="POST">
        <div class="form-row">
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($patient['name']); ?>" required>
            </div>
            <div class="form-group">
                <label>Date of Birth</label>
                <input type="date" name="dob" class="form-control" value="<?php echo $patient['dob']; ?>" required>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Phone</label>
                <input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($patient['phone']); ?>" required>
            </div>
        </div>
        <div class="form-group">
            <label>Address</label>
            <textarea name="address" class="form-control" rows="3"><?php echo htmlspecialchars($patient['address']); ?></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Update Patient</button>
        <a href="view.php?id=<?php echo $id; ?>" class="btn btn-info">View Patient</a>
        <a href="list.php" class="btn btn-warning">Back to List</a>
    </form>
</div>

<?php require_once '../includes/footer.php'; ?>
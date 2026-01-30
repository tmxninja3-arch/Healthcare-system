<?php
require_once '../config/db.php';
require_once '../includes/header.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $dob = mysqli_real_escape_string($conn, $_POST['dob']);
    $join_date = mysqli_real_escape_string($conn, $_POST['join_date']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    
    // Validate dates using SQL
    $validate_sql = "SELECT 
        CASE WHEN '$dob' > CURDATE() THEN 0 ELSE 1 END as valid_dob,
        CASE WHEN '$join_date' > CURDATE() THEN 0 ELSE 1 END as valid_join";
    
    $validation = $conn->query($validate_sql)->fetch_assoc();
    
    if ($validation['valid_dob'] && $validation['valid_join']) {
        $sql = "INSERT INTO patients (name, dob, join_date, phone, address) 
                VALUES ('$name', '$dob', '$join_date', '$phone', '$address')";
        
        if ($conn->query($sql)) {
            echo '<div class="alert alert-success">Patient added successfully!</div>';
        } else {
            echo '<div class="alert alert-danger">Error: ' . $conn->error . '</div>';
        }
    } else {
        echo '<div class="alert alert-danger">Invalid dates! DOB and join date cannot be in future.</div>';
    }
}
?>

<header>
    <h1>Add New Patient</h1>
</header>

<div class="card">
    <div class="card-header">
        <h2>Patient Registration Form</h2>
    </div>
    <form method="POST">
        <div class="form-row">
            <div class="form-group">
                <label>Full Name *</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Date of Birth *</label>
                <input type="date" name="dob" class="form-control" required>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Join Date *</label>
                <input type="date" name="join_date" class="form-control" required value="<?php echo date('Y-m-d'); ?>">
            </div>
            <div class="form-group">
                <label>Phone *</label>
                <input type="text" name="phone" class="form-control" required>
            </div>
        </div>
        <div class="form-group">
            <label>Address</label>
            <textarea name="address" class="form-control" rows="3"></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Add Patient</button>
        <a href="list.php" class="btn btn-info">View All Patients</a>
    </form>
</div>

<?php require_once '../includes/footer.php'; ?>
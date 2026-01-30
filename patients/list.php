<?php
require_once '../config/db.php';
require_once '../includes/header.php';

// Search functionality
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$where = $search ? "WHERE name LIKE '%$search%' OR phone LIKE '%$search%'" : '';

// SQL with all calculations
$sql = "SELECT 
    patient_id,
    name,
    DATE_FORMAT(dob, '%M %d, %Y') as dob_formatted,                -- Age in years
    TIMESTAMPDIFF(YEAR, dob, CURDATE()) as age_years,
    CONCAT(
        TIMESTAMPDIFF(YEAR, dob, CURDATE()), ' years ',          -- Full age with months
        TIMESTAMPDIFF(MONTH, dob, CURDATE()) % 12, ' months'
    ) as full_age,
    DATE_FORMAT(join_date, '%M %d, %Y') as join_formatted,      -- Join date parts
    YEAR(join_date) as join_year,
    MONTHNAME(join_date) as join_month,
    DAY(join_date) as join_day,
    phone,
    (SELECT COUNT(*) FROM visits WHERE visits.patient_id = patients.patient_id) as total_visits
FROM patients
$where
ORDER BY name ASC";

$result = $conn->query($sql);
?>

<header>
    <h1>All Patients</h1>
</header>

<div class="search-box">
    <form method="GET" style="display: inline;">
        <input type="text" name="search" placeholder="Search by name or phone..." value="<?php echo htmlspecialchars($search); ?>">
        <button type="submit" class="btn btn-primary">Search</button>
        <?php if ($search): ?>
            <a href="list.php" class="btn btn-info">Clear</a>
        <?php endif; ?>
    </form>
</div>

<div class="card">
    <div class="card-header">
        <h2>Patient List</h2>
    </div>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>DOB</th>
                <th>Age</th>
                <th>Full Age</th>
                <th>Join Date</th>
                <th>Phone</th>
                <th>Total Visits</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['patient_id']; ?></td>
                <td><?php echo htmlspecialchars($row['name']); ?></td>
                <td><?php echo $row['dob_formatted']; ?></td>
                <td><?php echo $row['age_years']; ?> years</td>
                <td><?php echo $row['full_age']; ?></td>
                <td><?php echo $row['join_formatted']; ?></td>
                <td><?php echo htmlspecialchars($row['phone']); ?></td>
                <td><span class="badge badge-info"><?php echo $row['total_visits']; ?></span></td>
                <td>
                    <a href="view.php?id=<?php echo $row['patient_id']; ?>" class="btn btn-sm btn-info">View</a>
                    <a href="edit.php?id=<?php echo $row['patient_id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php require_once '../includes/footer.php'; ?>
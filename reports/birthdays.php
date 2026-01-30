<?php
require_once '../config/db.php';
require_once '../includes/header.php';

// Birthdays in next 30 days
$upcoming_birthdays_sql = "SELECT 
    name,
    DATE_FORMAT(dob, '%M %d, %Y') as dob_formatted,
    TIMESTAMPDIFF(YEAR, dob, CURDATE()) + 1 as turning_age,
    DATE_FORMAT(
        DATE_ADD(
            dob, 
            INTERVAL TIMESTAMPDIFF(YEAR, dob, CURDATE()) + 1 YEAR
        ), 
        '%M %d, %Y'
    ) as next_birthday,
    DATEDIFF(
        DATE_ADD(
            dob, 
            INTERVAL TIMESTAMPDIFF(YEAR, dob, CURDATE()) + 1 YEAR
        ),
        CURDATE()
    ) as days_until
FROM patients
WHERE 
    DATE_ADD(dob, INTERVAL TIMESTAMPDIFF(YEAR, dob, CURDATE()) + 1 YEAR) 
    BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)
ORDER BY days_until ASC";

$upcoming = $conn->query($upcoming_birthdays_sql);

// Patients turning specific ages this year
$milestone_sql = "SELECT 
    name,
    DATE_FORMAT(dob, '%M %d, %Y') as dob_formatted,
    TIMESTAMPDIFF(YEAR, dob, CONCAT(YEAR(CURDATE()), '-12-31')) as age_this_year
FROM patients
WHERE TIMESTAMPDIFF(YEAR, dob, CONCAT(YEAR(CURDATE()), '-12-31')) IN (40, 50, 60, 70, 80)
ORDER BY dob ASC";

$milestones = $conn->query($milestone_sql);
?>

<header>
    <h1>Birthday Report</h1>
</header>

<div class="card">
    <div class="card-header">
        <h2>Upcoming Birthdays (Next 30 Days)</h2>
    </div>
    <table>
        <thead>
            <tr>
                <th>Patient Name</th>
                <th>Date of Birth</th>
                <th>Turning Age</th>
                <th>Birthday</th>
                <th>Days Until</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $upcoming->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['name']); ?></td>
                <td><?php echo $row['dob_formatted']; ?></td>
                <td><?php echo $row['turning_age']; ?> years</td>
                <td><?php echo $row['next_birthday']; ?></td>
                <td>
                    <span class="badge badge-info">
                        <?php echo $row['days_until']; ?> days
                    </span>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<div class="card">
    <div class="card-header">
        <h2>Milestone Ages This Year</h2>
    </div>
    <table>
        <thead>
            <tr>
                <th>Patient Name</th>
                <th>Date of Birth</th>
                <th>Turning This Year</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $milestones->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['name']); ?></td>
                <td><?php echo $row['dob_formatted']; ?></td>
                <td>
                    <span class="badge badge-warning">
                        <?php echo $row['age_this_year']; ?> years
                    </span>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php require_once '../includes/footer.php'; ?>
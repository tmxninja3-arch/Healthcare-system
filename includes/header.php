<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in for protected pages
$current_page = basename($_SERVER['PHP_SELF']);
$public_pages = ['login.php'];

if (!in_array($current_page, $public_pages) && !isset($_SESSION['user_id'])) {
    header('Location: /healthcare1/auth/login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Healthcare Management System</title>
    <link rel="stylesheet" href="/healthcare1/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <?php if (isset($_SESSION['user_id'])): ?>
    <nav>
        <ul>
            <li><a href="/healthcare1/index.php">Dashboard</a></li>
            <li><a href="/healthcare1/patients/list.php">Patients</a></li>
            <li><a href="/healthcare1/patients/add.php">Add Patient</a></li>
            <li><a href="/healthcare1/visits/list.php">Visits</a></li>
            <li><a href="/healthcare1/visits/add.php">Add Visit</a></li>
            <li><a href="/healthcare1/reports/followups.php">Follow-ups</a></li>
            <li><a href="/healthcare1/reports/monthly.php">Monthly Report</a></li>
            <li><a href="/healthcare1/reports/birthdays.php">Birthdays</a></li>
            <li><a href="/healthcare1/reports/summary.php">Summary</a></li>
            <li><a href="/healthcare1/reports/charts.php">Charts</a></li>
            <li style="margin-left: auto;">
                <a href="/healthcare1/auth/logout.php">Logout (<?php echo $_SESSION['username']; ?>)</a>
            </li>
        </ul>
    </nav>
    <?php endif; ?>
    <div class="container">
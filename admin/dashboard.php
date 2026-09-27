<?php
session_start();
include "../config/database.php";

if(!isset($_SESSION['account_id'])){
    header("Location: /UniShop/");
    exit();
}
$students = $conn->query("SELECT COUNT(*) AS total FROM students");
$students = $students->fetch_assoc()['total'];

$lecturers = $conn->query("SELECT COUNT(*) AS total FROM lecturers");
$lecturers = $lecturers->fetch_assoc()['total'];

$materials = $conn->query("SELECT COUNT(*) AS total FROM materials");
$materials = $materials->fetch_assoc()['total'];

$completed = $conn->query("
SELECT COUNT(*) AS total
FROM orders
WHERE pickup_status='Completed'
");
$completed = $completed->fetch_assoc()['total'];

$pending = $conn->query("
SELECT COUNT(*) AS total
FROM orders
WHERE LOWER(TRIM(pickup_status))='awaiting'
");
$pending = $pending->fetch_assoc()['total'];

$revenue = $conn->query("
SELECT SUM(total_amount) AS total
FROM orders
WHERE payment_status='paid'
");
$revenue = $revenue->fetch_assoc()['total'];

if($revenue==""){
    $revenue=0;
}

$weeklyOrders = $conn->query("
SELECT
DAYNAME(created_at) AS day,
COUNT(*) AS total
FROM orders
WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7
DAY)
GROUP BY DAYNAME(created_at)
");

$days = [];
$totals = [];

$completedOrders = $conn->query("
SELECT COUNT(*) AS total
FROM orders
WHERE pickup_status='Completed'
");
$completedOrders = $completedOrders->fetch_assoc()
['total'];

$awaitingOrders = $conn->query("
SELECT COUNT(*) AS total
FROM orders
WHERE pickup_status='awaiting'
");
$awaitingOrders = $awaitingOrders->fetch_assoc()
['total'];

while($row = $weeklyOrders->fetch_assoc()){
    $days[] = $row['day'];
    $totals[] = $row['total'];
}

$recentOrders = $conn->query("
SELECT
accounts.full_name,
materials.title,
orders.total_amount,
orders.payment_status
FROM orders
JOIN students ON orders.student_id = students.student_id
JOIN accounts ON students.account_id = accounts.account_id
JOIN materials ON orders.material_id = materials.material_id
ORDER BY orders.order_id DESC
LIMIT 5
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>

    <link rel="stylesheet" href="/UniShop/assets/css/dashboard.css">
        <link rel="stylesheet" href="/UniShop/assets/css/sidebar.css">


</head>

<body>

<?php include "../includes/admin_sidebar.php"; ?>

<div class="main-content">

    <?php include "../includes/header.php"; ?>
    <br><br>

<h1>Admin Dashboard</h1>
<p>Overview of the university shopping system</p>

<div class="cards">

    <div class="card">
        <h4>STUDENTS</h4>
        <h2><?php echo $students; ?></h2>
    </div>

    <div class="card">
        <h4>LECTURERS</h4>
        <h2><?php echo $lecturers; ?></h2>
    </div>

    <div class="card">
        <h4>MATERIALS</h4>
        <h2><?php echo $materials; ?></h2>
    </div>

    <div class="card">
        <h4>COMPLETED</h4>
        <h2><?php echo $completed; ?></h2>
    </div>

    <div class="card">
        <h4>PENDING</h4>
        <h2><?php echo $pending; ?></h2>
    </div>

    <div class="card">
        <h4>REVENUE</h4>
        <h2>₦<?php echo number_format($revenue); ?></h2>
    </div>

</div>

<br><br>

<div class="info-panel">
 
    <h2>Administrator Panel</h2> <br>

    <p>
        Welcome to the <strong>UniShop Administrators' Dashboard</strong>.
        This panel provides centralized control over the University Shopping
        System and allows administrators to manage all platform activities.
    </p>
<br>
    <ul>
        <li>Manage student and lecturer accounts.</li>
        <li>Monitor uploaded study materials.</li>
        <li>Track customer orders and payment records.</li>
        <li>Oversee the overall performance of the UniShop platform.</li>
    </ul>
<br>
    <p>
        Use the navigation menu on the left to access the various management
        modules of the system.
    </p>

</div>
<br><br>



</div>
<script>
    const orderLabels = <?php echo
    json_encode($days); ?>;
    const orderData = <?php echo
    json_encode($totals); ?>;

    const completedCount = <?php echo
    $completed; ?>;
    const pendingCount = <?php echo
    $pending; ?>;
    </script>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="/UniShop/assets/js/charts.js"></script>
</body>
</html>
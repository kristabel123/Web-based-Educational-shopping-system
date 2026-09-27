<?php
session_start();
include "../config/database.php";

/* =====================
   AUTH CHECK
===================== */
if(!isset($_SESSION['account_id'])){
    header("Location: /UniShop/login.php");
    exit();
}

if($_SESSION['role'] !== 'lecturer'){
    header("Location: /UniShop/");
    exit();
}

$account_id = $_SESSION['account_id'];

$getLecturer = $conn->query("
SELECT lecturer_id
FROM lecturers
WHERE account_id='$account_id'
");

$lecturer = $getLecturer->fetch_assoc();

$lecturer_id = $lecturer['lecturer_id'];
/* =====================
   DASHBOARD STATS
===================== */

// Materials
$materials = $conn->query("
SELECT COUNT(*) AS total
FROM materials
WHERE lecturer_id='$lecturer_id'
");
$materials = $materials->fetch_assoc()['total'];

// Sales
$sales = $conn->query("
SELECT COUNT(*) AS total
FROM orders o
JOIN materials m ON o.material_id = m.material_id
WHERE m.lecturer_id='$lecturer_id'
");
$sales = $sales->fetch_assoc()['total'];

// Pending
$pending = $conn->query("
SELECT COUNT(*) AS total
FROM orders o
JOIN materials m ON o.material_id = m.material_id
WHERE m.lecturer_id='$lecturer_id'
AND o.pickup_status='awaiting'
");
$pending = $pending->fetch_assoc()['total'];

// Completed
$completed = $conn->query("
SELECT COUNT(*) AS total
FROM orders o
JOIN materials m ON o.material_id = m.material_id
WHERE m.lecturer_id='$lecturer_id'
AND o.pickup_status='Completed'
");
$completed = $completed->fetch_assoc()['total'];

// Revenue
$revenue = $conn->query("
SELECT SUM(o.total_amount) AS total
FROM orders o
JOIN materials m ON o.material_id = m.material_id
WHERE m.lecturer_id='$lecturer_id'
AND o.payment_status='Paid'
");
$revenue = $revenue->fetch_assoc()['total'] ?? 0;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Lecturer Dashboard</title>
    <link rel="stylesheet" href="/UniShop/assets/css/dashboard.css">
    <link rel="stylesheet" href="/UniShop/assets/css/sidebar.css">
</head>

<body>

<?php include "../includes/lecturer_sidebar.php"; ?>

<div class="main-content">

<?php include "../includes/header.php"; ?>
<br>
<div class="page-header">
    
    <h2>Lecturer Dashboard</h2>
    <p>Manage your materials and track sales</p>
</div>

<div class="dashboard-grid">

    <div class="card">
        <h3>Materials</h3>
        <p><?php echo $materials; ?></p>
    </div>

    <div class="card">
        <h3>Total Sales</h3>
        <p><?php echo $sales; ?></p>
    </div>

    <div class="card">
        <h3>Pending Pickups</h3>
        <p><?php echo $pending; ?></p>
    </div>

    <div class="card">
        <h3>Completed</h3>
        <p><?php echo $completed; ?></p>
    </div>

    <div class="card">
        <h3>Revenue</h3>
        <p>₦<?php echo number_format($revenue); ?></p>
    </div>

</div>

<div class="table-card">

<h3>Recent Orders</h3>

<table>
<tr>
    <th>Student</th>
    <th>Material</th>
    <th>Amount</th>
    <th>Payment</th>
    <th>Status</th>
</tr>

<?php
$orders = $conn->query("
SELECT
accounts.full_name,
materials.title,
orders.total_amount,
orders.payment_status,
orders.pickup_status
FROM orders
JOIN materials ON orders.material_id = materials.material_id
JOIN students ON orders.student_id = students.student_id
JOIN accounts ON students.account_id = accounts.account_id
WHERE materials.lecturer_id='$lecturer_id'
ORDER BY orders.order_id DESC
LIMIT 5
");

while($row = $orders->fetch_assoc()){
?>

<tr>
    <td><?php echo $row['full_name']; ?></td>
    <td><?php echo $row['title']; ?></td>
    <td>₦<?php echo number_format($row['total_amount']); ?></td>
    <td><?php echo $row['payment_status']; ?></td>
    <td><?php echo $row['pickup_status']; ?></td>
</tr>

<?php } ?>

</table>

</div>

</div>

</body>
</html>
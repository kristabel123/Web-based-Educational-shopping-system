<?php
session_start();
include "../config/database.php";

if(!isset($_SESSION['account_id'])){
    header("Location: /UniShop/");
    exit();
}

$totalUsers = $conn->query("SELECT COUNT(*) AS total FROM accounts")->fetch_assoc()['total'];

$totalMaterials = $conn->query("SELECT COUNT(*) AS total FROM materials")->fetch_assoc()['total'];

$totalOrders = $conn->query("SELECT COUNT(*) AS total FROM orders")->fetch_assoc()['total'];

$totalRevenue = $conn->query("SELECT SUM(total_amount) AS total FROM orders WHERE payment_status='Paid'")->fetch_assoc()['total'];

if($totalRevenue == ""){
    $totalRevenue = 0;
}

$departments = $conn->query("
SELECT
departments.department_name,
COUNT(materials.material_id) AS total

FROM departments

LEFT JOIN materials
ON departments.department_id = materials.department_id

GROUP BY departments.department_id
");

$orderStatus = $conn->query("
SELECT
payment_status,
COUNT(*) AS total

FROM orders

GROUP BY payment_status
");

$lecturerSales = $conn->query("
SELECT
    accounts.full_name AS lecturer_name,
    materials.title AS material_title,
    SUM(orders.quantity) AS quantity_sold,
    SUM(orders.total_amount) AS amount_earned
FROM orders
JOIN materials
    ON orders.material_id = materials.material_id
JOIN lecturers
    ON materials.lecturer_id = lecturers.lecturer_id
JOIN accounts
    ON lecturers.account_id = accounts.account_id
WHERE orders.payment_status = 'Paid'
GROUP BY materials.lecturer_id, orders.material_id
ORDER BY amount_earned DESC
");

?>

<!DOCTYPE html>
<html>

<head>

<title>Reports</title>

<link rel="stylesheet" href="/UniShop/assets/css/dashboard.css">
    <link rel="stylesheet" href="/UniShop/assets/css/sidebar.css">


<style>

.report-cards{
display:grid;
grid-template-columns:repeat(4,1fr);
gap:20px;
margin-bottom:30px;
}

.card{
background:#fff;
padding:20px;
border-radius:12px;
box-shadow:0 2px 8px rgba(0,0,0,.08);
}

.card h4{
margin:0;
color:#666;
font-size:14px;
}

.card h2{
margin-top:10px;
font-size:30px;
}

.report-grid{
display:grid;
grid-template-columns:1fr 1fr;
gap:20px;
align-items:stretch;
}

.report-box{
background:#fff;
padding:20px;
border-radius:12px;
box-shadow:0 2px 8px rgba(0,0,0,.08);
min-height:300px;
}

.report-box table{
width:100%;
border-collapse:collapse;
margin-top:15px;
}

.report-box th,
.report-box td{
padding:10px;
border-bottom:1px solid #eee;
text-align:left;
}

.report-card{
    background:#fff;
    padding:20px;
    border-radius:12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    margin-top:25px;
}

.report-card h3{
    margin-bottom: 15px;
}

.report-card table{
    width: 100%;
    border-collapse: collapse;
}

.report-card th,
.report-card td{
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid #eee;
}
</style>

</head>

<body>

<?php include "../includes/admin_sidebar.php"; ?>

<div class="main-content">

<?php include "../includes/header.php"; ?>

<br>
<h1>Reports</h1>
<br>
<p>Analytics and system reports</p>

<br>

<div class="report-cards">

<div class="card">
<h4>TOTAL USERS</h4>
<h2><?php echo $totalUsers; ?></h2>
</div>

<div class="card">
<h4>TOTAL MATERIALS</h4>
<h2><?php echo $totalMaterials; ?></h2>
</div>

<div class="card">
<h4>TOTAL ORDERS</h4>
<h2><?php echo $totalOrders; ?></h2>
</div>

<div class="card">
<h4>TOTAL REVENUE</h4>
<h2>₦<?php echo number_format($totalRevenue); ?></h2>
</div>

</div>

<div class="report-grid">

<div class="report-box">

<h3>Materials by Department</h3>

<table>

<tr>

<th>Department</th>

<th>Materials</th>

</tr>

<?php while($row=$departments->fetch_assoc()){ ?>

<tr>

<td><?php echo $row['department_name']; ?></td>

<td><?php echo $row['total']; ?></td>

</tr>

<?php } ?>

</table>

</div>

<div class="right-reports">
    
<div class="report-card">

<h3>Lecturer Sales & Earnings</h3>

<table>

<tr>
    <th>Lecturer</th>
    <th>Material</th>
    <th>Quantity Sold</th>
    <th>Amount Earned</th>
</tr>

<?php while($row=$lecturerSales->fetch_assoc()){ ?>

<tr>

<td><?php echo htmlspecialchars($row['lecturer_name']); ?></td>

<td><?php echo htmlspecialchars($row['material_title']); ?></td>

<td><?php echo $row['quantity_sold']; ?></td>

<td>₦<?php echo number_format($row['amount_earned']); ?></td>

</tr>

<?php } ?>

</table>

</div>

</div>

</div>

</div>

</div>

</body>

</html>
<?php
session_start();
include "../config/database.php";

if(!isset($_SESSION['account_id']) || $_SESSION['role'] != 'lecturer'){
    header("Location: /UniShop/login.php");
    exit();
}

$account_id = $_SESSION['account_id'];

/* GET LECTURER ID */
$getLecturer = $conn->query("
SELECT lecturer_id
FROM lecturers
WHERE account_id='$account_id'
");

$lecturer = $getLecturer->fetch_assoc();
$lecturer_id = $lecturer['lecturer_id'];

/* TOTAL REVENUE */
$revenue = $conn->query("
SELECT SUM(orders.total_amount) AS total

FROM orders

JOIN materials
ON orders.material_id = materials.material_id

WHERE materials.lecturer_id='$lecturer_id'
AND LOWER(orders.payment_status)='paid'
AND orders.pickup_status='Completed'
");

$totalRevenue = $revenue->fetch_assoc()['total'] ?? 0;


/* COMPLETED SALES */
$sales = $conn->query("
SELECT COUNT(*) AS total

FROM orders

JOIN materials
ON orders.material_id = materials.material_id

WHERE materials.lecturer_id='$lecturer_id'
AND orders.pickup_status='Completed'
");

$totalSales = $sales->fetch_assoc()['total'];

/* MATERIALS SOLD */
$materialsSold = $conn->query("
SELECT SUM(orders.quantity) AS total

FROM orders

JOIN materials
ON orders.material_id = materials.material_id

WHERE materials.lecturer_id='$lecturer_id'
AND orders.pickup_status='Completed'
");

$totalMaterials = $materialsSold->fetch_assoc()['total'] ?? 0;


/* AVERAGE SALE */
$average = 0;

if($totalSales > 0){
    $average = $totalRevenue / $totalSales;
}
/* FETCH SALES HISTORY */
$salesHistory = $conn->query("
SELECT
orders.order_id,
orders.created_at,
accounts.full_name,
materials.title,
orders.quantity AS order_quantity,
orders.total_amount,
orders.payment_status,
orders.pickup_status

FROM orders

JOIN materials
ON orders.material_id = materials.material_id

JOIN students
ON orders.student_id = students.student_id

JOIN accounts
ON students.account_id = accounts.account_id

WHERE materials.lecturer_id='$lecturer_id'
AND LOWER(orders.payment_status)='paid'

ORDER BY orders.created_at DESC
");
?>
<!DOCTYPE html>
<html>

<head>

<title>Sales History</title>

<link rel="stylesheet" href="/UniShop/assets/css/dashboard.css">
    <link rel="stylesheet" href="/UniShop/assets/css/sidebar.css">


<style>

.page-top{
display:flex;
justify-content:space-between;
align-items:center;
margin-bottom:25px;
}

.page-top h2{
margin:0;
}

.page-top p{
margin-top:5px;
color:#666;
}

.summary-grid{
display:grid;
grid-template-columns:repeat(4,1fr);
gap:20px;
margin-bottom:30px;
}

.summary-card{
background:white;
padding:20px;
border-radius:12px;
box-shadow:0 2px 8px rgba(0,0,0,.08);
}

.summary-card h4{
margin:0;
font-size:14px;
color:#666;
}

.summary-card h2{
margin-top:12px;
margin-bottom:0;
color:#1e66ff;
}

.table-card{
background:white;
padding:20px;
border-radius:12px;
box-shadow:0 2px 8px rgba(0,0,0,.08);
}

table{
width:100%;
border-collapse:collapse;
}

table th{
background:#1e66ff;
color:white;
padding:14px;
text-align:left;
}

table td{
padding:14px;
border-bottom:1px solid #eee;
}

.paid{
background:#d4edda;
color:#155724;
padding:6px 12px;
border-radius:20px;
font-size:12px;
display:inline-block;
font-weight:bold;
}

.completed{
background:#d4edda;
color:#155724;
padding:6px 12px;
border-radius:20px;
font-size:12px;
display:inline-block;
font-weight:bold;
}

.empty{
text-align:center;
padding:40px;
color:#888;
}

</style>

</head>

<body>

<?php include "../includes/lecturer_sidebar.php"; ?>

<div class="main-content">

<?php include "../includes/header.php"; ?>

<div class="page-top">

<div>
<br>
<h2>Sales History</h2>

<p>View all completed sales for your materials.</p>

</div>

</div>

<div class="summary-grid">

<div class="summary-card">

<h4>Total Revenue</h4>

<h2>₦<?php echo number_format($totalRevenue); ?></h2>

</div>

<div class="summary-card">

<h4>Completed Sales</h4>

<h2><?php echo $totalSales; ?></h2>

</div>

<div class="summary-card">

<h4>Materials Sold</h4>

<h2><?php echo $totalMaterials; ?></h2>

</div>

<div class="summary-card">

<h4>Average Sale</h4>

<h2>₦<?php echo number_format($average); ?></h2>

</div>

</div>

<div class="table-card">

<table>

<tr>

<th>Date</th>
<th>Student</th>
<th>Material</th>
<th>Quantity</th>
<th>Amount</th>
<th>Payment</th>

</tr>

<?php

if($salesHistory->num_rows > 0){

while($row = $salesHistory->fetch_assoc()){

?>

<tr>

<td><?php echo date("Y-m-d", strtotime($row['created_at'])); ?></td>

<td><?php echo $row['full_name']; ?></td>

<td><?php echo $row['title']; ?></td>

<td><?php echo $row['order_quantity']; ?></td>

<td>₦<?php echo number_format($row['total_amount']); ?></td>

<td><span class="paid">Paid</span></td>


</tr>

<?php

}

}else{

?>

<tr>

<td colspan="7" class="empty">

No completed sales found.

</td>

</tr>

<?php } ?>

</table>

</div>

</div>

</body>

</html>
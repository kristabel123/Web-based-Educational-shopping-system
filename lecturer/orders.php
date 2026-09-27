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
/* GET ALL ORDERS FOR THIS LECTURER */

$orders = $conn->query("
SELECT
orders.order_id,
accounts.full_name,
materials.title,
orders.quantity,
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

ORDER BY orders.order_id DESC
");
?>

<!DOCTYPE html>
<html>

<head>

<title>Orders</title>

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

.table-card{
background:#fff;
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
font-size:14px;
}

table td{
padding:14px;
border-top:1px solid #eee;
font-size:14px;
}

.status{
padding:6px 12px;
border-radius:20px;
font-size:12px;
font-weight:bold;
background:#e8f5e9;
color:#2e7d32;
}

.empty{
text-align:center;
padding:40px;
color:#888;
}

.paid{
background:#d4edda;
color:#155724;
padding:6px 12px;
border-radius:20px;
font-size:12px;
font-weight:bold;
display:inline-block;
}

.unpaid{
background:#fde2e2;
color:#c62828;
padding:6px 12px;
border-radius:20px;
font-size:12px;
font-weight:bold;
display:inline-block;
}

.completed{
background:#d4edda;
color:#155724;
padding:6px 12px;
border-radius:20px;
font-size:12px;
font-weight:bold;
}

.pending{
background:#fff3cd;
color:#856404;
padding:6px 12px;
border-radius:20px;
font-size:12px;
font-weight:bold;
}

.btn-view{
    background:#1e66ff;
    color:white;
    padding:6px 12px;
    border-radius:6px;
    text-decoration:none;
    font-size:12px;
    display:inline-block;
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
<h2>Orders</h2>

<p>Manage orders placed for your materials.</p>

</div>

</div>

<div class="table-card">

<table>

<tr>

<th>Student</th>
<th>Material</th>
<th>Quantity</th>
<th>Total</th>
<th>Payment</th>
<th>Pickup</th>
<th>Action</th>

</tr>

<?php

if($orders->num_rows > 0){

while($row = $orders->fetch_assoc()){

?>

<tr>


<td><?php echo $row['full_name']; ?></td>

<td><?php echo $row['title']; ?></td>

<td><?php echo $row['quantity']; ?></td>

<td>₦<?php echo number_format($row['total_amount']); ?></td>

<td>
<?php
if(strtolower(trim($row['payment_status'])) == "paid"){
    echo "<span class='paid'>Paid</span>";
}else{
    echo "<span class='unpaid'>".$row['payment_status']."</span>";
}
?>
</td>

<td>
<?php
if(strtolower(trim($row['pickup_status'])) == "completed"){
    echo "<span class='completed'>Completed</span>";
}else{
    echo "<span class='pending'>Awaiting Pickup</span>";
}
?>
</td>

<td>

<?php if(strtolower(trim($row['pickup_status'])) == "completed"){ ?>

<span class="completed">Completed</span>

<?php } else { ?>

<a
href="order_view.php?id=<?php echo $row['order_id']; ?>"
class="btn-view">

Confirm Pickup

</a>

<?php } ?>

</td>

</tr>

<?php

}

}else{

?>

<tr>

<td colspan="8" class="empty">

No orders available.

</td>

</tr>

<?php } ?>

</table>

</div>

</div>

</body>

</html>
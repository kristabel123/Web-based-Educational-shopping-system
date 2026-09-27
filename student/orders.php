<?php
session_start();
include "../config/database.php";

if(!isset($_SESSION['account_id']) || $_SESSION['role'] != 'student'){
    header("Location: /UniShop/login.php");
    exit();
}

$account_id = $_SESSION['account_id'];

$getStudent = $conn->query("
SELECT student_id
FROM students
WHERE account_id='$account_id'
");

$student = $getStudent->fetch_assoc();
$student_id = $student['student_id'];

$orders = $conn->query("
SELECT
orders.*,
materials.title,
materials.course_code,
materials.material_type,
pickup_schedules.pickup_date,
pickup_schedules.start_time,
pickup_schedules.end_time,
pickup_schedules.location

FROM orders

LEFT JOIN materials
ON orders.material_id = materials.material_id

LEFT JOIN pickup_schedules
ON orders.material_id = pickup_schedules.material_id

WHERE orders.student_id='$student_id'
AND orders.pickup_status='Completed'

ORDER BY orders.created_at DESC
");
?>
<!DOCTYPE html>
<html>

<head>

<title>My Orders</title>

<link rel="stylesheet" href="/UniShop/assets/css/dashboard.css">
<link rel="stylesheet" href="/UniShop/assets/css/sidebar.css">

</head>

<body>

<?php include "../includes/student_sidebar.php"; ?>

<div class="main-content">

<?php include "../includes/header.php"; ?>

<br>
<h2>My Orders</h2>

<p>View all materials you have purchased.</p>
<br>

<div class="section">
    <div class="orders-grid">
    <?php while($row = $orders->fetch_assoc()){ ?>
    <div class="order-card">


<h3>
<?php echo $row['title']; ?>
</h3>

<p>
<strong>
<?php echo $row['course_code']; ?>
•
<?php echo $row['material_type']; ?>
</strong>
</p>

<p>
Quantity:
<?php echo $row['quantity']; ?>
</p>

<p>
Total:
<strong>
₦<?php echo number_format($row['total_amount']); ?>
</strong>
</p>

<p>
Payment:
<?php echo $row['payment_status']; ?>
</p>

<?php if(strtolower(trim($row['pickup_status'])) == "completed"){ ?>

<p>
Pickup: <?php
if(strtolower(trim($row['pickup_status'])) == "completed"){
    echo "Completed";
} else{
    echo "Awaiting Pickup";
}
?>
</p>

<?php } else { ?>

<p>
<strong>Pickup Date:</strong>
<?php
if(!empty($row['pickup_date'])){
    echo date("d M Y", strtotime($row['pickup_date']));
}else{
    echo "Not Scheduled Yet";
}
?>
</p>

<?php if(!empty($row['pickup_date'])){ ?>

<p>
<strong>Pickup Time:</strong>
<?php
echo date("h:i A", strtotime($row['start_time']));
?>
-
<?php
echo date("h:i A", strtotime($row['end_time']));
?>
</p>

<p>
<strong>Pickup Location:</strong>
<?php echo $row['location']; ?>
</p>

<p>
<strong>Pickup Status:</strong>
Awaiting Pickup
</p>

<?php } ?>

<?php } ?>

<p>
Ordered:
<?php echo date("d M Y",strtotime($row['created_at'])); ?>
</p>

</div>

<?php } ?>

</div>

</div>

</body>

</html>
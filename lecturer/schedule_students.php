<?php
session_start();
include "../config/database.php";

if(!isset($_SESSION['account_id']) || $_SESSION['role'] != 'lecturer'){
    header("Location: /UniShop/login.php");
    exit();
}

$lecturer_account = $_SESSION['account_id'];

$getLecturer = $conn->query("
SELECT lecturer_id
FROM lecturers
WHERE account_id='$lecturer_account'
");

$lecturer = $getLecturer->fetch_assoc();
$lecturer_id = $lecturer['lecturer_id'];

$schedule_id = $_GET['id'];

$students = $conn->query("
SELECT
accounts.full_name,
accounts.email,
orders.quantity,
orders.created_at

FROM orders

JOIN students
ON orders.student_id = students.student_id

JOIN accounts
ON students.account_id = accounts.account_id

JOIN pickup_schedules
ON orders.schedule_id = pickup_schedules.schedule_id

WHERE orders.schedule_id='$schedule_id'
AND pickup_schedules.lecturer_id='$lecturer_id'

ORDER BY orders.created_at ASC
");
<!DOCTYPE html>
<html>

<head>

<title>Students for Pickup Schedule</title>

<link rel="stylesheet" href="/UniShop/assets/css/dashboard.css">
<link rel="stylesheet" href="/UniShop/assets/css/sidebar.css">

</head>

<body>

<?php include "../includes/lecturer_sidebar.php"; ?>

<div class="main-content">

<?php include "../includes/header.php"; ?>

<h2>Students Assigned to this Pickup Schedule</h2>

<br>

<div class="table-card">

<table>

<tr>

<th>#</th>
<th>Student</th>
<th>Email</th>
<th>Quantity</th>
<th>Purchased On</th>
<th>Status</th>
<th>Action</th>

</tr>

<?php

$count = 1;

while($row = $students->fetch_assoc()){

?>

<tr>

<td><?php echo $count++; ?></td>

<td><?php echo $row['full_name']; ?></td>

<td><?php echo $row['email']; ?></td>

<td><?php echo $row['quantity']; ?></td>

<td><?php echo $row['created_at']; ?></td>

<td>Awaiting Pickup</td>

<td>

<a
class="btn"
href="mark_picked.php?schedule=<?php echo $schedule_id; ?>&student=<?php echo $row['student_id']; ?>"
onclick="return confirm('Mark this student's material as picked up?')">

Mark Picked Up

</a>

</td>

</tr>

<?php } ?>

</table>

</div>

</div>

</body>

</html>
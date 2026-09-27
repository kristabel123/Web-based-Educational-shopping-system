<?php
session_start();
include "../config/database.php";

if(!isset($_SESSION['account_id']) || $_SESSION['role'] != 'student'){
    header("Location: /UniShop/login.php");
    exit();
}

$account_id = $_SESSION['account_id'];

/* GET STUDENT ID */
$getStudent = $conn->query("
SELECT student_id
FROM students
WHERE account_id='$account_id'
");

$student = $getStudent->fetch_assoc();
$student_id = $student['student_id'];

/* GET PURCHASED MATERIALS SCHEDULES */
$schedules = $conn->query("
SELECT DISTINCT

pickup_schedules.*,
orders.pickup_status,
materials.title,
materials.course_code,
materials.material_type,
accounts.full_name

FROM orders

INNER JOIN materials
ON orders.material_id = materials.material_id

INNER JOIN pickup_schedules
ON materials.material_id = pickup_schedules.material_id

INNER JOIN lecturers
ON pickup_schedules.lecturer_id = lecturers.lecturer_id

INNER JOIN accounts
ON lecturers.account_id = accounts.account_id

WHERE orders.student_id='$student_id'
AND orders.pickup_status='awaiting'

ORDER BY pickup_schedules.pickup_date ASC,
pickup_schedules.start_time ASC
");
?>

<!DOCTYPE html>
<html>

<head>

<title>My Pickup Schedule</title>

<link rel="stylesheet" href="/UniShop/assets/css/dashboard.css">
<link rel="stylesheet" href="/UniShop/assets/css/sidebar.css">

</head>

<body>

<?php include "../includes/student_sidebar.php"; ?>
<div class="main-content">
<?php include "../includes/header.php"; ?>


<br>
<h2>My Pickup Schedule</h2>
<p>View all your scheduled pickups for purchased materials.</p>

<br>
<div class="materials-grid">

<?php if($schedules->num_rows > 0){ ?>

    <?php while($row = $schedules->fetch_assoc()){ ?>

        <div class="material-card">

<h3>📘 <?php echo htmlspecialchars($row['title']); ?></h3>

<p>
<strong>Course:</strong>
<?php echo htmlspecialchars($row['course_code']); ?>
</p>

<p>
<strong>Material Type:</strong>
<?php echo htmlspecialchars($row['material_type']); ?>
</p>

<p>
<strong>Pickup Date:</strong>
<?php echo date("d M Y", strtotime($row['pickup_date'])); ?>
</p>

<p>
<strong>Time:</strong>
<?php
echo date("h:i A", strtotime($row['start_time']));
?>
-
<?php
echo date("h:i A", strtotime($row['end_time']));
?>
</p>

<p>
<strong>Location:</strong>
<?php echo htmlspecialchars($row['location']); ?>
</p>

<p>
<strong>Lecturer:</strong>
<?php echo htmlspecialchars($row['full_name']); ?>
</p>

<?php if(!empty($row['instructions'])){ ?>

<p>
<strong>Instructions:</strong><br>
<?php echo htmlspecialchars($row['instructions']); ?>
</p>

<?php } ?>

<p>
<strong>Status:</strong>

<?php if($row['pickup_status'] == "Completed"){ ?>

<span style="color:green;font-weight:bold;">
Completed
</span>

<?php } else { ?>

<span style="color:#ff9800;font-weight:bold;">
Awaiting Pickup
</span>

<?php } ?>

</p>

</div>
    <?php } ?>

<?php } else { ?>

    <p>No pickup schedules available yet.</p>

<?php } ?>

</div>

</div>

</body>

</html>
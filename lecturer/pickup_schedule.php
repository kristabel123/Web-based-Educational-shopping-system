<?php
session_start();
include "../config/database.php";

if(!isset($_SESSION['account_id']) || $_SESSION['role'] != 'lecturer'){
    header("Location: /UniShop/login.php");
    exit();
}

$account_id = $_SESSION['account_id'];

/* GET LECTURER */

$getLecturer = $conn->query("
SELECT lecturer_id
FROM lecturers
WHERE account_id='$account_id'
LIMIT 1
");

if($getLecturer->num_rows > 0){
    $lecturer = $getLecturer->fetch_assoc();
    $lecturer_id = $lecturer['lecturer_id'];
}else{
    die("No lecturer profile found.");
}

/* DELETE SCHEDULE */

if(isset($_GET['delete'])){

    $id = intval($_GET['delete']);

    $conn->query("
    DELETE FROM pickup_schedules
    WHERE schedule_id='$id'
    AND lecturer_id='$lecturer_id'
    ");

    header("Location: pickup_schedule.php");
    exit();
}

/* FETCH SCHEDULES */

$schedules = $conn->query("
SELECT
pickup_schedules.*,
materials.title

FROM pickup_schedules

INNER JOIN materials
ON pickup_schedules.material_id = materials.material_id

WHERE materials.lecturer_id='$lecturer_id'

ORDER BY pickup_schedules.pickup_date DESC,
pickup_schedules.start_time ASC
");
?>

<!DOCTYPE html>
<html>

<head>

<title>Pickup Schedule</title>

<link rel="stylesheet" href="/UniShop/assets/css/dashboard.css">
<link rel="stylesheet" href="/UniShop/assets/css/sidebar.css">

<style>

.top-bar{
display:flex;
justify-content:space-between;
align-items:center;
margin-bottom:25px;
}

.top-bar h2{
margin:0;
}

.top-bar p{
margin-top:5px;
color:#666;
}

.add-btn{
background:#1e66ff;
color:#fff;
padding:12px 18px;
border-radius:8px;
text-decoration:none;
font-weight:bold;
}

.cards{
display:grid;
grid-template-columns:repeat(auto-fit,minmax(300px,1fr));
gap:20px;
margin-top:25px;
}

.schedule-card{
background:#fff;
padding:20px;
border-radius:12px;
box-shadow:0 2px 8px rgba(0,0,0,.08);
}

.schedule-card h3{
margin-top:0;
color:#1e66ff;
}

.schedule-card p{
margin:10px 0;
}

.actions{
display:flex;
gap:10px;
margin-top:20px;
}

.actions a{
text-decoration:none;
padding:8px 14px;
border-radius:6px;
font-size:13px;
color:white;
}

.edit{
background:#1e66ff;
}

.delete{
background:#dc3545;
}

.empty{
background:#fff;
padding:30px;
border-radius:12px;
text-align:center;
color:#777;
}

</style>

</head>

<body>

<?php include "../includes/lecturer_sidebar.php"; ?>

<div class="main-content">

<?php include "../includes/header.php"; ?>

<div class="top-bar">

<div>
<br>
<h2>Pickup Schedule</h2>

<p>Manage all pickup schedules.</p>

</div>

<a href="add_schedule.php" class="add-btn">
+ Add Schedule
</a>

</div>
<?php if($schedules->num_rows > 0){ ?>

<div class="cards">

<?php while($row = $schedules->fetch_assoc()){ ?>

<div class="schedule-card">

<h3>
<?php echo htmlspecialchars($row['title']); ?>
</h3>

<p>
<strong>Pickup Date:</strong><br>
<?php echo date("d M Y", strtotime($row['pickup_date'])); ?>
</p>

<p>
<strong>Time:</strong><br>
<?php echo date("h:i A", strtotime($row['start_time'])); ?>
-
<?php echo date("h:i A", strtotime($row['end_time'])); ?>
</p>

<p>
<strong>Location:</strong><br>
<?php echo htmlspecialchars($row['location']); ?>
</p>

<p>
<strong>Capacity:</strong>
<?php echo $row['assigned_students']; ?>
/
<?php echo $row['capacity']; ?>
Students
</p>

<?php if(!empty($row['instructions'])){ ?>

<p>

<strong> Instructions:</strong><br>

<?php echo htmlspecialchars($row['instructions']); ?>

</p>

<?php } ?>

<div class="actions">

<a
href="edit_schedule.php?id=<?php echo $row['schedule_id']; ?>"
class="edit">

Edit

</a>

<a
href="?delete=<?php echo $row['schedule_id']; ?>"
class="delete"
onclick="return confirm('Delete this pickup schedule?');">

Delete

</a>

</div>

</div>

<?php } ?>

</div>

<?php }else{ ?>

<div class="empty">

<h3>No Pickup Schedules Yet</h3>

<p>
Click <strong>+ Add Schedule</strong> to create your first pickup schedule.
</p>

</div>

<?php } ?>

</div>

</body>

</html>
<?php
session_start();
include "../config/database.php";

if(!isset($_SESSION['account_id']) || $_SESSION['role'] != 'lecturer'){
    header("Location: /UniShop/login.php");
    exit();
}

$account_id = $_SESSION['account_id'];

$getLecturer = $conn->query("
SELECT lecturer_id
FROM lecturers
WHERE account_id='$account_id'
LIMIT 1
");

if($getLecturer->num_rows == 0){
    die("Lecturer profile not found.");
}

$lecturer = $getLecturer->fetch_assoc();
$lecturer_id = $lecturer['lecturer_id'];

if(!isset($_GET['id'])){
    header("Location: pickup_schedule.php");
    exit();
}

$id = $_GET['id'];

/* GET THE SCHEDULE */
$result = $conn->query("
SELECT *
FROM pickup_schedules
WHERE schedule_id='$id'
AND lecturer_id='$lecturer_id'
");

if($result->num_rows == 0){
    die("Schedule not found.");
}

$schedule = $result->fetch_assoc();

$message = "";

/* UPDATE SCHEDULE */
if(isset($_POST['update'])){

    $pickup_date = $_POST['pickup_date'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $location = $_POST['location'];
    $instructions = $_POST['instructions'];

    $conn->query("
    UPDATE pickup_schedules SET

    pickup_date='$pickup_date',
    start_time='$start_time',
    end_time='$end_time',
    location='$location',
    instructions='$instructions'

    WHERE schedule_id='$id'
    AND lecturer_id='$lecturer_id'
    ");

    header("Location: pickup_schedule.php");
    exit();
}
?>

<!DOCTYPE html>
<html>

<head>

<title>Edit Pickup Schedule</title>

<link rel="stylesheet" href="/UniShop/assets/css/dashboard.css">
    <link rel="stylesheet" href="/UniShop/assets/css/sidebar.css">


<style>

.form-card{

background:#fff;
padding:25px;
border-radius:12px;
max-width:700px;

}

.grid{

display:grid;
grid-template-columns:1fr 1fr;
gap:15px;

}

.full{

grid-column:span 2;

}

label{

display:block;
margin-bottom:6px;
font-size:13px;

}

input,
textarea{

width:100%;
padding:12px;
border:1px solid #ddd;
border-radius:8px;
box-sizing:border-box;

}

textarea{

height:90px;
resize:none;

}

button{

margin-top:20px;
width:100%;
padding:12px;
background:#1e66ff;
color:white;
border:none;
border-radius:8px;
cursor:pointer;

}

</style>

</head>

<body>

<?php include "../includes/lecturer_sidebar.php"; ?>

<div class="main-content">

<?php include "../includes/header.php"; ?>

<h2>Edit Pickup Schedule</h2>

<div class="form-card">

<form method="POST">

<div class="grid">

<div>

<label>Pickup Date</label>

<input
type="date"
name="pickup_date"
value="<?php echo $schedule['pickup_date']; ?>"
required>

</div>

<div></div>

<div>

<label>Start Time</label>

<input
type="time"
name="start_time"
value="<?php echo $schedule['start_time']; ?>"
required>

</div>

<div>

<label>End Time</label>

<input
type="time"
name="end_time"
value="<?php echo $schedule['end_time']; ?>"
required>

</div>

<div class="full">

<label>Location</label>

<input
type="text"
name="location"
value="<?php echo htmlspecialchars($schedule['location']); ?>"
required>

</div>

<div class="full">

<label>Additional Instructions</label>

<textarea
name="instructions"><?php echo htmlspecialchars($schedule['instructions']); ?></textarea>

</div>

</div>

<button
type="submit"
name="update">

Update Schedule

</button>

</form>

</div>

</div>

</body>

</html>
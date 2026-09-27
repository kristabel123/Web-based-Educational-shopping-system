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

$lecturer = $getLecturer->fetch_assoc();
$lecturer_id = $lecturer['lecturer_id'];

//echo "Current Lecturer ID: ".$lecturer_id."<br>";

//$test = $conn->query("
//SELECT material_id,title,lecturer_id
//FROM materials
//WHERE lecturer_id='$lecturer_id'
//");

//echo "Number of materials: ".$test->num_rows;
//exit();

/* GET MATERIALS */

$materials = $conn->query("
SELECT material_id,title
FROM materials
WHERE lecturer_id='$lecturer_id'
ORDER BY title
");

/* SAVE SCHEDULE */

if(isset($_POST['create_schedule'])){

    $material_id = intval($_POST['material_id']);
    $pickup_date = $_POST['pickup_date'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $location = trim($_POST['location']);
    $instructions = trim($_POST['instructions']);
    $capacity = intval($_POST['capacity']);

    $conn->query("
    INSERT INTO pickup_schedules
    (
        lecturer_id,
        material_id,
        pickup_date,
        start_time,
        end_time,
        location,
        instructions,
        capacity,
        assigned_students
    )
    VALUES
    (
        '$lecturer_id',
        '$material_id',
        '$pickup_date',
        '$start_time',
        '$end_time',
        '$location',
        '$instructions',
        '$capacity',
        0
    )
    ");

    /* NOTIFY STUDENTS */

    $students = $conn->query("
    SELECT DISTINCT student_id
    FROM orders
    WHERE material_id='$material_id'
    ");

    while($student = $students->fetch_assoc()){

        $student_id = $student['student_id'];

        $account = $conn->query("
        SELECT account_id
        FROM students
        WHERE student_id='$student_id'
        ");

        if($account->num_rows>0){

            $acc = $account->fetch_assoc();

            $user_id = $acc['account_id'];

            $conn->query("
            INSERT INTO notifications
            (user_id,message)
            VALUES
            (
            '$user_id',
            'A pickup schedule has been created for one of your purchased materials.'
            )
            ");
        }
    }

    header("Location: pickup_schedule.php");
    exit();
}
?>

<!DOCTYPE html>
<html>

<head>

<title>Add Pickup Schedule</title>

<link rel="stylesheet" href="/UniShop/assets/css/dashboard.css">
<link rel="stylesheet" href="/UniShop/assets/css/sidebar.css">

<style>

.form-card{
    background:#fff;
    max-width:800px;
    margin:30px auto;
    padding:30px;
    border-radius:12px;
    box-shadow:0 2px 8px rgba(0,0,0,.08);
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
    font-weight:bold;
}

input,
select,
textarea{
    width:100%;
    padding:12px;
    border:1px solid #ddd;
    border-radius:8px;
    box-sizing:border-box;
}

textarea{
    height:100px;
    resize:none;
}

.btn{
    display:inline-block;
    padding:12px 20px;
    border:none;
    border-radius:8px;
    text-decoration:none;
    cursor:pointer;
    font-weight:bold;
}

.save-btn{
    background:#1e66ff;
    color:#fff;
}

.cancel-btn{
    background:#ddd;
    color:#333;
    margin-right:10px;
}

.actions{
    margin-top:25px;
}

</style>

</head>

<body>

<?php include "../includes/lecturer_sidebar.php"; ?>

<div class="main-content">

<?php include "../includes/header.php"; ?>

<div class="form-card">

<h2>Add Pickup Schedule</h2>

<p>Create a pickup schedule for one of your uploaded materials.</p>

<form method="POST">

<div class="grid">

<div class="full">

<label>Material *</label>

<select name="material_id" required>

<option value="">Select Material</option>

<?php while($material = $materials->fetch_assoc()){ ?>

<option value="<?php echo $material['material_id']; ?>">

<?php echo htmlspecialchars($material['title']); ?>

</option>

<?php } ?>

</select>

</div>

<div>

<label>Pickup Date *</label>

<input type="date" name="pickup_date" required>

</div>

<div>

<label>Start Time *</label>

<input type="time" name="start_time" required>

</div>

<div>

<label>End Time *</label>

<input type="time" name="end_time" required>

</div>

<div class="full">

<label>Pickup Location *</label>

<input
type="text"
name="location"
placeholder="Room 204, Science Building"
required>

</div>

<div class="full">

<label>Instructions</label>

<textarea
name="instructions"
placeholder="Example: Come with your payment receipt"></textarea>

</div>

<div class="full">

<label>Maximum Students *</label>

<input
type="number"
name="capacity"
min="1"
required>

</div>

</div>

<div class="actions">

<a href="pickup_schedule.php" class="btn cancel-btn">
Cancel
</a>

<button
type="submit"
name="create_schedule"
class="btn save-btn">

Create Schedule

</button>

</div>

</form>

</div>

</div>

</body>

</html>
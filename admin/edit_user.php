<?php
// error_reporting(E_ALL);
// ini_set('display_errors', 1);

session_start();
include "../config/database.php";

if(!isset($_SESSION['account_id'])){
    header("Location: /UniShop/");
    exit();
}

$id = $_GET['id'];

if(isset($_POST['update'])){

$fullname = $_POST['fullname'];
$email = $_POST['email'];
$phone = $_POST['phone'];
$department = $_POST['department'];
$role = $_POST['role'];
$status = $_POST['status'];

$conn->query("UPDATE accounts SET

full_name='$fullname',
email='$email',
department_id='$department',
role='$role',
status='$status'

WHERE account_id='$id'");

if($role == "student"){

    $conn->query("
    UPDATE students
    SET
    phone_number='$phone',
    department_id='$department'
    WHERE account_id='$id'
    ");

}elseif($role == "lecturer"){

    $conn->query("
    UPDATE lecturers
    SET
    phone_number='$phone',
    department_id='$department'
    WHERE account_id='$id'
    ");

}

header("Location: manage_users.php");
exit();

}

$user = $conn->query("
SELECT
accounts.*,
accounts.department_id,
COALESCE(students.phone_number, lecturers.phone_number) AS phone_number

FROM accounts

LEFT JOIN students
ON accounts.account_id = students.account_id

LEFT JOIN lecturers
ON accounts.account_id = lecturers.account_id

WHERE accounts.account_id='$id'
");

$user = $user->fetch_assoc();

$departments = $conn->query("SELECT * FROM departments");


?>

<!DOCTYPE html>
<html>

<head>

<title>Edit User</title>

<link rel="stylesheet" href="/UniShop/assets/css/dashboard.css">
    <link rel="stylesheet" href="/UniShop/assets/css/sidebar.css">


<style>

.form-card{
background:#fff;
padding:30px;
border-radius:12px;
box-shadow:0 2px 8px rgba(0,0,0,.08);
max-width:700px;
}

input,select{
width:100%;
padding:12px;
margin-bottom:15px;
border:1px solid #ddd;
border-radius:8px;
}

button{
background:#2563eb;
color:white;
padding:12px 20px;
border:none;
border-radius:8px;
cursor:pointer;
}

</style>

</head>

<body>

<?php include "../includes/admin_sidebar.php"; ?>

<div class="main-content">

<?php include "../includes/header.php"; ?>

<h2>Edit User</h2>

<div class="form-card">

<form method="POST">

<label>Full Name</label>

<input
type="text"
name="fullname"
value="<?php echo $user['full_name']; ?>">

<label>Email</label>

<input
type="email"
name="email"
value="<?php echo $user['email']; ?>">

<label>Phone Number</label>

<input
type="text"
name="phone"
value="<?php echo $user['phone_number']; ?>">

<label>Department</label>

<select name="department">

<?php while($dept = $departments->fetch_assoc()){ ?>

<option
value="<?php echo $dept['department_id']; ?>"
<?php if($user['department_id'] == $dept['department_id']) echo "selected"; ?>>

<?php echo $dept['department_name']; ?>

</option>

<?php } ?>

</select>

<label>Role</label>

<select name="role" disabled>

<option <?php if($user['role']=="admin") echo "selected"; ?>>admin</option>

<option <?php if($user['role']=="lecturer") echo "selected"; ?>>lecturer</option>

<option <?php if($user['role']=="student") echo "selected"; ?>>student</option>

</select>

<input
type="hidden"
name="role"
value="<?php echo $user['role']; ?>">

<label>Status</label>

<select name="status">

<option <?php if($user['status']=="Active") echo "selected"; ?>>Active</option>

<option <?php if($user['status']=="Inactive") echo "selected"; ?>>Inactive</option>

</select>

<button name="update">

Update User

</button>

</form>

</div>

</div>

</body>

</html>
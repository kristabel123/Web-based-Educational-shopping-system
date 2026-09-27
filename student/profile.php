<?php
session_start();
include "../config/database.php";

if(!isset($_SESSION['account_id']) || $_SESSION['role'] != 'student'){
    header("Location: /UniShop/login.php");
    exit();
}

$account_id = $_SESSION['account_id'];

/* GET STUDENT DETAILS */
$student = $conn->query("
SELECT
students.student_id,
accounts.full_name,
accounts.email,
accounts.role,
students.phone_number

FROM students

JOIN accounts
ON students.account_id = accounts.account_id

WHERE students.account_id='$account_id'

LIMIT 1
");

$user = $student->fetch_assoc();

$message = "";

/* UPDATE PROFILE */

if(isset($_POST['save'])){

    $phone = $_POST['phone_number'];

    $conn->query("
    UPDATE students

    SET
    phone_number='$phone'

    WHERE account_id='$account_id'
    ");

    $_SESSION['success'] = "Profile updated successfully.";
    header("Location: profile.php");
    exit();
}
?>

<!DOCTYPE html>
<html>

<head>

<title>My Profile</title>

<link rel="stylesheet" href="/UniShop/assets/css/dashboard.css">
<link rel="stylesheet" href="/UniShop/assets/css/sidebar.css">

<style>

.profile-card{
    background:#fff;
    max-width:700px;
    margin:30px auto;
    padding:30px;
    border-radius:12px;
    box-shadow:0 2px 8px rgba(0,0,0,.08);
}

.profile-card h2{
    margin-top:0;
    color:#1e66ff;
}

.profile-card p{
    color:#666;
    margin-bottom:25px;
}

.form-group{
    margin-bottom:18px;
}

.form-group label{
    display:block;
    margin-bottom:6px;
    font-weight:600;
}

.form-group input{
    width:100%;
    padding:12px;
    border:1px solid #ddd;
    border-radius:8px;
    box-sizing:border-box;
}

input[readonly]{
    background:#f5f5f5;
    color:#666;
}

button{
    background:#1e66ff;
    color:#fff;
    border:none;
    padding:12px 20px;
    border-radius:8px;
    cursor:pointer;
}

.note{
    margin-top:20px;
    padding:15px;
    background:#f8f9fa;
    border-left:4px solid #1e66ff;
    border-radius:6px;
    color:#555;
}

</style>

</head>

<body>

<?php include "../includes/student_sidebar.php"; ?>

<div class="main-content">

<?php include "../includes/header.php"; ?>

<div class="profile-card">

<h2>My Profile</h2>

<p>View your account information and update your contact details.</p>

<?php
if(isset($_SESSION['sucess'])){
    echo "<div class='success'>".
    $_SESSION['success']."</div>";
    unset($_SESSION['success']);
}
?>
<form method="POST">

<div class="form-group">

<label>Student ID</label>

<input
type="text"
value="<?php echo $user['student_id']; ?>"
readonly>

</div>

<div class="form-group">

<label>Full Name</label>

<input
type="text"
value="<?php echo $user['full_name']; ?>"
readonly>

</div>

<div class="form-group">

<label>Email Address</label>

<input
type="email"
value="<?php echo $user['email']; ?>"
readonly>

</div>

<div class="form-group">

<label>Role</label>

<input
type="text"
value="<?php echo ucfirst($user['role']); ?>"
readonly>

</div>

<div class="form-group">

<label>Phone Number</label>

<input
type="text"
name="phone_number"
value="<?php echo $user['phone_number']; ?>"
readonly>

</div>

<button
type="submit"
name="save">

Save Changes

</button>

</form>

<div class="note">

<strong>Note:</strong> Your name, email address and account role are managed by the system administrator. Contact the administrator if any corrections are required.

</div>

</div>

</div>

</body>

</html>
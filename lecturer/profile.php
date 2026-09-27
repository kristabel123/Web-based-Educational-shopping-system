<?php
session_start();
include "../config/database.php";

if(!isset($_SESSION['account_id']) || $_SESSION['role'] != 'lecturer'){
    header("Location: /UniShop/login.php");
    exit();
}

$account_id = $_SESSION['account_id'];

/* GET LECTURER DETAILS */
$lecturer = $conn->query("
SELECT
lecturers.lecturer_id,
accounts.full_name,
accounts.email,
accounts.role,
lecturers.phone_number,
lecturers.office_location

FROM lecturers

JOIN accounts
ON lecturers.account_id = accounts.account_id

WHERE lecturers.account_id='$account_id'

LIMIT 1
");

$user = $lecturer->fetch_assoc();

$message = "";

/* UPDATE PROFILE */

if(isset($_POST['save'])){

    $phone = $_POST['phone_number'];
    $office = $_POST['office_location'];

    $conn->query("
    UPDATE lecturers

    SET
    phone_number='$phone',
    office_location='$office'

    WHERE account_id='$account_id'
    ");

    $message = "Profile updated successfully.";

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
    cursor:not-allowed;
}

button{
    background:#1e66ff;
    color:#fff;
    border:none;
    padding:12px 20px;
    border-radius:8px;
    cursor:pointer;
}

.success{
    color:green;
    margin-bottom:20px;
}

.note{
    margin-top:20px;
    padding:15px;
    background:#f8f9fa;
    border-left:4px solid #1e66ff;
    color:#555;
    border-radius:6px;
}

</style>

</head>

<body>

<?php include "../includes/lecturer_sidebar.php"; ?>

<div class="main-content">

<?php include "../includes/header.php"; ?>

<div class="profile-card">

<h2>My Profile</h2>

<p>View your account information and update your contact details.</p>

<?php
if($message!=""){
    echo "<div class='success'>$message</div>";
}
?>

<form method="POST">
    <div class="form-group">

<label>Staff ID</label>

<input
type="text"
value="<?php echo $user['lecturer_id']; ?>"
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
name="phone"
value="<?php echo $user['phone_number']; ?>"
readonly>

</div>

<div class="form-group">

<label>Office Location</label>

<input
type="text"
name="office_location"
value="<?php echo $user['office_location']; ?>">

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
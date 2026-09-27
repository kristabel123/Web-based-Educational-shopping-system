<?php
session_start();
include "../config/database.php";

if(!isset($_SESSION['account_id']) || $_SESSION['role'] != 'admin'){
    header("Location: /UniShop/login.php");
    exit();
}

$account_id = $_SESSION['account_id'];

/* GET ADMIN DETAILS */

$admin = $conn->query("
SELECT
account_id,
full_name,
email,
role

FROM accounts

WHERE account_id='$account_id'

LIMIT 1
");

$user = $admin->fetch_assoc();
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

<?php include "../includes/admin_sidebar.php"; ?>

<div class="main-content">

<?php include "../includes/header.php"; ?>

<div class="profile-card">

<h2>My Profile</h2>

<p>View your administrator account information.</p>

<form>

<div class="form-group">

<label>Admin ID</label>

<input
type="text"
value="<?php echo $user['account_id']; ?>"
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

</form>

<div class="note">

<strong>Note:</strong> Your name, email address and administrator role are managed by the system. Contact the system developer if any corrections are required.

</div>

</div>

</div>

</body>

</html>
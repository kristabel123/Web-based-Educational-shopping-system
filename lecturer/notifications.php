<?php
session_start();
include "../config/database.php";

if(!isset($_SESSION['account_id'])){
    header("Location: /UniShop/login.php");
    exit();
}

$account_id = $_SESSION['account_id'];

$conn->query("
UPDATE notifications
SET is_read = 1
WHERE user_id='$account_id'
AND is_read = 0
");

$notifications = $conn->query("
SELECT *
FROM notifications
WHERE user_id='$account_id'
ORDER BY created_at DESC
");
?>
<!DOCTYPE html>
<html>

<head>

<title>Notifications</title>

<link rel="stylesheet" href="/UniShop/assets/css/dashboard.css">
<link rel="stylesheet" href="/UniShop/assets/css/sidebar.css">

</head>

<body>

<?php include "../includes/lecturer_sidebar.php"; ?>

<div class="main-content">

<?php include "../includes/header.php"; ?>

<br>
<h2>Notifications</h2>

<br>

<div class="table-card">
    <?php

if($notifications->num_rows > 0){

    while($row = $notifications->fetch_assoc()){

?>

<div style="padding:15px;border-bottom:1px solid #eee;">

    <p style="margin:0;font-weight:500;">
        <?php echo $row['message']; ?>
    </p>

    <small style="color:gray;">
        <?php echo $row['created_at']; ?>
    </small>

</div>

<?php

    }

}else{

?>

<p style="padding:20px;text-align:center;">
No notifications yet.
</p>

<?php } ?>
<?php

if($notifications->num_rows > 0){

    while($row = $notifications->fetch_assoc()){

?>

<div style="padding:15px;border-bottom:1px solid #eee;">

    <p style="margin:0;font-weight:500;">
        <?php echo $row['message']; ?>
    </p>

    <small style="color:gray;">
        <?php echo $row['created_at']; ?>
    </small>

</div>

<?php

    }

}else{

?>

<p style="padding:20px;text-align:center;">
No notifications yet.
</p>

<?php } ?>
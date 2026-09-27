<?php
session_start();
include "../config/database.php";

if(!isset($_SESSION['account_id']) || $_SESSION['role'] != "admin"){
    header("Location: /UniShop/login.php");
    exit();
}

$user_id = $_SESSION['account_id'];

/* Mark all notifications as read */
$conn->query("
UPDATE notifications
SET is_read = 1
WHERE user_id='$user_id'
");

$notifications = $conn->query("
SELECT *
FROM notifications
WHERE user_id='$user_id'
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

<?php include "../includes/admin_sidebar.php"; ?>

<div class="main-content">

<?php include "../includes/header.php"; ?>

<br><br>

<h1>Notifications</h1>

<?php
if($notifications->num_rows > 0){

    while($row = $notifications->fetch_assoc()){
?>

<div class="notification-card">

    <p><?php echo $row['message']; ?></p>

    <small>
        <?php echo date("d M Y, h:i A", strtotime($row['created_at'])); ?>
    </small>

</div>

<?php
    }

}else{
?>

<div class="notification-card">
    <p>No notifications available.</p>
</div>

<?php } ?>

</div>

</body>
</html>
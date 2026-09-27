<?php
session_start();
include "../config/database.php";
if(isset($_GET['read'])){

    $id = $_GET['read'];
    $user_id = $_SESSION['account_id'];

    $conn->query("
        UPDATE notifications
        SET is_read=1
        WHERE notification_id='$id'
        AND user_id='$user_id'
    ");

    header("Location: notifications.php");
    exit();
}
if(isset($_GET['readall'])){

    $user_id = $_SESSION['account_id'];

    $conn->query("
        UPDATE notifications
        SET is_read=1
        WHERE user_id='$user_id'
    ");

    header("Location: notifications.php");
    exit();
}

if(!isset($_SESSION['account_id']) || $_SESSION['role'] != 'student'){
    header("Location: /UniShop/login.php");
    exit();
}

$user_id = $_SESSION['account_id'];

/* Mark all notifications as read */
$conn->query("
UPDATE notifications
SET is_read='1'
WHERE user_id='$user_id'
");

/* Fetch notifications */
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

<?php include "../includes/student_sidebar.php"; ?>

<div class="main-content">

<?php include "../includes/header.php"; ?>

<div class="page-header">
    <div>
        <h2>Notifications</h2>
        <p>View all your recent notifications.</p>
    </div>
</div>

<div class="section">

<div style="margin-bottom:20px;">
    <a href="?readall=1" class="btn">
        Mark All as Read
</a>
</div>
<?php
if($notifications->num_rows > 0){

    while($row = $notifications->fetch_assoc()){
?>

<div class="notification-card <?php echo $row['is_read'] ? '' : 'unread'; ?>">

    <p><?php echo $row['message']; ?></p>

    <small>
        <?php echo date("d M Y h:i A", strtotime($row['created_at'])); ?>
    </small>

    <?php if(!$row['is_read']){ ?>
        <br><br>

        <a href="?read=<?php echo $row['notification_id']; ?>" class="btn">
            Mark as Read
        </a>
    <?php } ?>

</div>

<br>

<?php
    }

}else{
?>

<p>No notifications yet.</p>

<?php } ?>

</div>

</div>

</body>

</html>
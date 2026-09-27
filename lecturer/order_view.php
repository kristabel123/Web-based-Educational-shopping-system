<?php
session_start();
include "../config/database.php";

if(!isset($_SESSION['account_id']) || $_SESSION['role'] != 'lecturer'){
    header("Location: /UniShop/login.php");
    exit();
}

$account_id = $_SESSION['account_id'];

/* GET LECTURER ID */
$getLecturer = $conn->query("
SELECT lecturer_id
FROM lecturers
WHERE account_id='$account_id'
");

$lecturer = $getLecturer->fetch_assoc();
$lecturer_id = $lecturer['lecturer_id'];

/* GET ORDER ID */
if(!isset($_GET['id'])){
    header("Location: orders.php");
    exit();
}

$order_id = $_GET['id'];

/* FETCH ORDER DETAILS */
$order = $conn->query("
SELECT
orders.*,
materials.title,
students.student_id,
accounts.full_name,
pickup_schedules.pickup_date,
pickup_schedules.start_time,
pickup_schedules.end_time,
pickup_schedules.location

FROM orders

JOIN materials
ON orders.material_id = materials.material_id

JOIN students
ON orders.student_id = students.student_id

JOIN accounts
ON students.account_id = accounts.account_id

LEFT JOIN pickup_schedules
ON orders.schedule_id = pickup_schedules.schedule_id

WHERE orders.order_id='$order_id'
AND materials.lecturer_id='$lecturer_id'

LIMIT 1
");

$data = $order->fetch_assoc();

if(!$data){
    echo "Order not found or access denied";
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Order Details</title>
    <link rel="stylesheet" href="/UniShop/assets/css/dashboard.css">
        <link rel="stylesheet" href="/UniShop/assets/css/sidebar.css">


    <style>

        .card{
            background:white;
            padding:25px;
            border-radius:12px;
            box-shadow:0 2px 10px rgba(0,0,0,0.08);
            max-width:700px;
            margin:40px auto;
        }

        .row{
            margin-bottom:12px;
            font-size:15px;
        }

        .label{
            font-weight:bold;
            color:#444;
        }

        .btn{
            display:inline-block;
            padding:10px 15px;
            border-radius:8px;
            text-decoration:none;
            margin-top:15px;
        }

        .confirm{
            background:#1e66ff;
            color:white;
        }

        .back{
            background:#ddd;
            color:#333;
            margin-right:10px;
        }

        .status{
            padding:5px 10px;
            border-radius:20px;
            font-size:12px;
            font-weight:bold;
        }

        .paid{
            background:#d4edda;
            color:#155724;
        }

        .pending{
            background:#fff3cd;
            color:#856404;
        }

        .completed{
            background:#d4edda;
            color:#155724;
        }

    </style>
</head>

<body>

<?php include "../includes/lecturer_sidebar.php"; ?>

<div class="main-content">

<?php include "../includes/header.php"; ?>

<div class="card">

    <h2>Order Details</h2>

    <div class="row">
        <span class="label">Student:</span>
        <?php echo $data['full_name']; ?>
    </div>

    <div class="row">
        <span class="label">Material:</span>
        <?php echo $data['title']; ?>
    </div>

    <div class="row">
        <span class="label">Quantity:</span>
        <?php echo $data['quantity']; ?>
    </div>

    <div class="row">
        <span class="label">Total Amount:</span>
        ₦<?php echo number_format($data['total_amount']); ?>
    </div>

    <div class="row">
        <span class="label">Payment:</span>

        <?php if(strtolower($data['payment_status']) == "paid"){ ?>
            <span class="status paid">Paid</span>
        <?php } else { ?>
            <span class="status pending"><?php echo $data['payment_status']; ?></span>
        <?php } ?>

    </div>

    <div class="row">
        <span class="label">Pickup Status:</span>

        <?php if($data['pickup_status'] == "Completed"){ ?>
            <span class="status completed">Completed</span>
        <?php } else { ?>
            <span class="status pending">Awaiting Pickup</span>
        <?php } ?>

    </div>

    <div class="row">
        <span class="label">Pickup Schedule:</span>
        <?php echo $data['pickup_date'] ?? "Not assigned"; ?>
        <?php echo $data['start_time'] ?? ""; ?> -
        <?php echo $data['end_time'] ?? ""; ?>
    </div>

    <div class="row">
        <span class="label">Location:</span>
        <?php echo $data['location'] ?? "Not assigned"; ?>
    </div>

    <a href="orders.php" class="btn back">Back</a>

    <?php if($data['pickup_status'] != "Completed"){ ?>
        <a href="/UniShop/lecturer/confirm_pickup.php?id=<?php echo $data['order_id']; ?>" class="btn confirm">
            Confirm Pickup
        </a>
    <?php } ?>

</div>

</div>

</body>
</html>
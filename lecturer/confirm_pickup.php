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

if(!isset($_GET['id'])){
    header("Location: orders.php");
    exit();
}

$order_id = $_GET['id'];

/* VERIFY ORDER BELONGS TO THIS LECTURER */
$order = $conn->query("
SELECT
orders.order_id,
orders.pickup_status,
orders.schedule_id

FROM orders

JOIN materials
ON orders.material_id = materials.material_id

WHERE orders.order_id='$order_id'
AND materials.lecturer_id='$lecturer_id'

LIMIT 1
");

if($order->num_rows == 0){
    die("Invalid order.");
}

$data = $order->fetch_assoc();

/* IF NOT ALREADY COMPLETED */
if($data['pickup_status'] != "Completed"){

    // Mark order as completed
    $conn->query("
    UPDATE orders
    SET pickup_status='Completed'
    WHERE order_id='$order_id'
    ");

    // Reduce assigned students
    $conn->query("
    UPDATE pickup_schedules
    SET assigned_students = assigned_students - 1
    WHERE schedule_id='".$data['schedule_id']."'
    AND assigned_students > 0
    ");

    // Get student account
    $student = $conn->query("
    SELECT students.account_id
    FROM orders

    JOIN students
    ON orders.student_id = students.student_id

    WHERE orders.order_id='$order_id'
    LIMIT 1
    ");

    if($student->num_rows > 0){

        $stu = $student->fetch_assoc();
        $user_id = $stu['account_id'];

        // Notify student
        $conn->query("
        INSERT INTO notifications
        (user_id, message)
        VALUES
        (
        '$user_id',
        'Your material has been marked as picked up successfully.'
        )
        ");
    }
}

header("Location: orders.php");
exit();
?>
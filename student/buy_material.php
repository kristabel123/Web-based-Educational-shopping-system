<?php
session_start();
include "../config/database.php";

if(!isset($_SESSION['account_id']) || $_SESSION['role'] != 'student'){
    header("Location: /UniShop/login.php");
    exit();
}

$material_id = $_GET['id'];

$result = $conn->query("
SELECT
materials.*,
departments.department_name,
accounts.full_name

FROM materials

LEFT JOIN lecturers
ON materials.lecturer_id = lecturers.lecturer_id

LEFT JOIN accounts
ON lecturers.account_id = accounts.account_id

LEFT JOIN departments
ON materials.department_id = departments.department_id

WHERE material_id='$material_id'
");

$material = $result->fetch_assoc();
if(isset($_POST['buy'])){

    $quantity = (int)$_POST['quantity'];

    /* CHECK AVAILABLE STOCK */

    if($material['qty_left'] <= 0){
        echo "<script>
        alert('This material is currently out of stock.');
        window.history.back();
        </script>";
        exit();
    }

    if($quantity > $material['qty_left']){
        echo "<script>
        alert('Only ".$material['qty_left']." material(s) are available.');
        window.history.back();
        </script>";
        exit();
    }

    $total_amount = $quantity * $material['price'];

    $account_id = $_SESSION['account_id'];

    $student = $conn->query("
        SELECT student_id
        FROM students
        WHERE account_id='$account_id'
    ");

    $studentData = $student->fetch_assoc();

    $student_id = $studentData['student_id'];

    /* GET LECTURER ACCOUNT */

$lecturer = $conn->query("
SELECT accounts.account_id
FROM materials
JOIN lecturers
ON materials.lecturer_id = lecturers.lecturer_id
JOIN accounts
ON lecturers.account_id = accounts.account_id
WHERE materials.material_id='$material_id'
");

$lecturerData = $lecturer->fetch_assoc();

$lecturer_account_id = $lecturerData['account_id'];

   /* GET NEXT AVAILABLE PICKUP SCHEDULE */

$schedule = $conn->query("
SELECT *
FROM pickup_schedules
WHERE material_id='$material_id'
AND assigned_students < capacity
ORDER BY pickup_date ASC, start_time ASC
LIMIT 1
");

$schedule_id = NULL;

if($schedule->num_rows > 0){

    $scheduleData = $schedule->fetch_assoc();

    $schedule_id = $scheduleData['schedule_id'];

    $conn->query("
    UPDATE pickup_schedules
    SET assigned_students = assigned_students + 1
    WHERE schedule_id='$schedule_id'
    ");

}else{

    echo "<script>
    alert('All pickup schedules for this material are currently full. Please wait for the lecturer to create another pickup schedule.');
    window.history.back();
    </script>";
    exit();

}

/* CREATE ORDER */

$conn->query("
INSERT INTO orders(
    student_id,
    material_id,
    schedule_id,
    quantity,
    total_amount,
    payment_status,
    pickup_status,
    created_at
)
VALUES(
    '$student_id',
    '$material_id',
    ".($schedule_id ? "'$schedule_id'" : "NULL").",
    '$quantity',
    '$total_amount',
    'Paid',
    'awaiting',
    NOW()
)
");

/* REDUCE MATERIAL STOCK */

$conn->query("
UPDATE materials
SET qty_left = qty_left - '$quantity'
WHERE material_id='$material_id'
");

/* CREATE LECTURER NOTIFICATION */

$lecturerMessage = "A student has purchased your ".$material['course_code']." ".$material['material_type'].".";

$conn->query("
INSERT INTO notifications(
    user_id,
    message,
    is_read,
    created_at
)
VALUES(
    '$lecturer_account_id',
    '$lecturerMessage',
    0,
    NOW()
)
");
/* CREATE PAYMENT RECORD */
$conn->query("
INSERT INTO payments(
    student_id,
    order_id,
    amount,
    payment_method,
    status,
    paid_at
)
VALUES(
    '$student_id',
    '".$conn->insert_id."',
    '$total_amount',
    'Cash',
    'Paid',
    NOW()
)
");
    /* CREATE NOTIFICATION */

$message = "Your order for ".$material['course_code']." ".$material['material_type']." has been placed successfully.";

$conn->query("
INSERT INTO notifications(
    user_id,
    message,
    is_read,
    created_at
)
VALUES(
    '$account_id',
    '$message',
    0,
    NOW()
)
");

    echo "<script>
            alert('Order placed successfully!');
            window.location='pickup_schedule.php';
          </script>";
}
?>
<!DOCTYPE html>
<html>

<head>

<title>Buy Material</title>

<link rel="stylesheet" href="/UniShop/assets/css/dashboard.css">
<link rel="stylesheet" href="/UniShop/assets/css/sidebar.css">

</head>

<body>

<?php include "../includes/student_sidebar.php"; ?>

<div class="main-content">

<?php include "../includes/header.php"; ?>

<div class="checkout-card">
<h2 class="checkout-title"><?php echo $material['title']; ?></h2>

<div class="material-tag"><?php echo $material['course_code']; ?> 
<?php echo $material['material_type']; ?></div>

<div class="checkout-info">
<p> <strong>Department: </strong><?php echo $material['department_name']; ?></p>

<p> <strong>Level: </strong><?php echo $material['level']; ?> Level</p>

<p> <strong>Lecturer: </strong><?php echo $material['full_name']; ?></p>

<p> <strong>Available: </strong><?php echo $material['qty_left']; ?></p>

<div class="checkout-price">₦<?php echo number_format($material['price']); ?></div>
<hr><br>
</div>
<input
type="hidden"
id="price"
value="<?php echo $material['price']; ?>">

<form method="POST">

    <label><strong>Quantity</strong></label><br><br>

    <input
        type="number"
        id="quantity"
        name="quantity"
        value="1"
        min="1"
        required>

    <br><br>
    <h3>Total Amount</h3>

    <h2 id="totalAmount" style="color:#2563eb;">₦<?php echo
    number_format($material['price']); ?>
    </h2>

    <button type="submit" name="buy" class="checkout-btn">
        🛒 Confirm Purchase
    </button>

</form>
</div>
<script>

const price = Number(document.getElementById("price").value);

const quantity = document.getElementById("quantity");

const total = document.getElementById("totalAmount");

function updateTotal(){

    let qty = Number(quantity.value);

    total.innerHTML =
        "₦" + (price * qty).toLocaleString();

}

quantity.addEventListener("input", updateTotal);

</script>
</body>
</html>

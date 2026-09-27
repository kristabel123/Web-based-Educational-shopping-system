<?php

session_start();
include "../config/database.php";

if(!isset($_SESSION['account_id']) || $_SESSION['role'] != 'student'){
    header("Location: /UniShop/login.php");
    exit();
}

$account_id = $_SESSION['account_id'];

/* GET STUDENT ID */
$getStudent = $conn->query("
SELECT student_id
FROM students
WHERE account_id='$account_id'
");

$student = $getStudent->fetch_assoc();
$student_id = $student['student_id'] ?? 0;

/* TOTAL AVAILABLE MATERIALS */
$materials = $conn->query("
SELECT COUNT(*) AS total FROM materials
");

$row = $materials->fetch_assoc();
$availableMaterials = $row['total'] ?? 0;

/* MY ORDERS */
$orders = $conn->query("
SELECT COUNT(*) AS total
FROM orders
WHERE student_id='$student_id'
");

$row = $orders->fetch_assoc();
$myOrders = $row['total'] ?? 0;

/* PENDING PICKUPS */
$pending = $conn->query("
SELECT COUNT(*) AS total
FROM orders
WHERE student_id='$student_id'
AND pickup_status='awaiting'
");

$row = $pending->fetch_assoc();
$pendingPickups = $row['total'] ?? 0;


/* COMPLETED ORDERS */
$completed = $conn->query("
SELECT COUNT(*) AS total
FROM orders
WHERE student_id='$student_id'
AND pickup_status='Completed'
");

$row = $completed->fetch_assoc();
$completedOrders = $row['total'] ?? 0;

/* RECENT MATERIALS */
$recent = $conn->query("
SELECT
materials.title,
materials.course_code,
materials.level,
departments.department_name,
materials.created_at

FROM materials

JOIN departments
ON materials.department_id = departments.department_id

ORDER BY materials.created_at DESC

LIMIT 5
");

?>

<!DOCTYPE html>
<html>

<head>

<title>Student Dashboard</title>

<link rel="stylesheet" href="/UniShop/assets/css/dashboard.css">
    <link rel="stylesheet" href="/UniShop/assets/css/sidebar.css">


<style>

    .dashboard-header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:25px;
}

.dashboard-header h2{
    margin:0;
    color:#222;
}

.dashboard-header p{
    margin-top:6px;
    color:#666;
}

.browse-btn{
    background:#1e66ff;
    color:#fff;
    padding:12px 20px;
    border-radius:8px;
    text-decoration:none;
    font-weight:600;
    transition:.3s;
}

.browse-btn:hover{
    background:#1557d6;
}

.browse-btn{
    display:inline-block;
    margin-top:15px;
    padding:10px 16px;
    background:white;
    color:#1e66ff;
    border-radius:8px;
    text-decoration:none;
    font-weight:bold;
}

.cards{
    display:grid;
    grid-template-columns:repeat(4,1fr);
    gap:15px;
    margin-top:20px;
}

.card{
    background:white;
    padding:20px;
    border-radius:12px;
    box-shadow:0 2px 8px rgba(0,0,0,.08);
}

.card h3{
    margin:0;
    color:#1e66ff;
}

.card p{
    margin-top:10px;
    color:#666;
}

.section{
    margin-top:30px;
    background:white;
    padding:20px;
    border-radius:12px;
}

.section-header{
    display:flex;
    justify-content:space-between;
    align-items:center;
}

.section-header a{
    text-decoration:none;
    color:#1e66ff;
    font-weight:bold;
}

.item{
    padding:12px 0;
    border-bottom:1px solid #eee;
    display:flex;
    justify-content:space-between;
}

</style>

</head>

<body>

<?php include "../includes/student_sidebar.php"; ?>

<div class="main-content">

<?php include "../includes/header.php"; ?>

<!-- WELCOME BOX -->
<div class="dashboard-header">
    <div>
<br>
    <h2>Student Dashboard</h2>

    <p>Browse and purchase course materials.</p>
</div>

    <a href="browse_materials.php" class="browse-btn">
        Browse Materials →
    </a>

</div>

<!-- CARDS -->
<div class="cards">

    <div class="card">
        <h3><?php echo $myOrders; ?></h3>
        <p>My Orders</p>
</div>

    <div class="card">
        <h3><?php echo $pendingPickups; ?></h3>
        <p>Pending Pickups</p>
    </div>

    <div class="card">
        <h3><?php echo $completedOrders; ?></h3>
        <p>Completed Orders</p>
    </div>


</div>

<!-- RECENT -->
<div class="section">

    <div class="section-header">

        <h3>Recently Uploaded</h3>

        <a href="browse_materials.php">View All →</a>

    </div>

    <?php while($row = $recent->fetch_assoc()){ ?>

        <div class="item">

            <div>

    <strong><?php echo $row['title']; ?></strong><br>

    <small>
        <?php
        echo $row['course_code']." • ".
             $row['department_name']." • ".
             $row['level']." Level";
        ?>
    </small>

</div>

<small>

<?php
if(!empty($row['created_at'])){
    echo date("M d, Y", strtotime($row['created_at']));
}else{
    echo "Recently Added";
}
?>

</small>

        </div>

    <?php } ?>

</div>

</div>

</body>

</html>
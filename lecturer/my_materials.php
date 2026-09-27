<?php
session_start();
include "../config/database.php";

/* =====================
   AUTH CHECK
===================== */
if(!isset($_SESSION['account_id']) || $_SESSION['role'] !== 'lecturer'){
    header("Location: /UniShop/login.php");
    exit();
}

$account_id = $_SESSION['account_id'];

$getLecturer = $conn->query("
SELECT lecturer_id
FROM lecturers
WHERE account_id='$account_id'
");

$lecturer = $getLecturer->fetch_assoc();

$lecturer_id = $lecturer['lecturer_id'];
/* =====================
   DELETE MATERIAL
===================== */
if(isset($_GET['delete'])){

    $id = $_GET['delete'];

    $conn->query("
        DELETE FROM materials
        WHERE material_id='$id'
        AND lecturer_id='$lecturer_id'
    ");

    header("Location: my_materials.php");
    exit();
}

/* =====================
   GET MATERIALS
===================== */
$materials = $conn->query("
SELECT m.*, d.department_name
FROM materials m
LEFT JOIN departments d ON m.department_id = d.department_id
WHERE m.lecturer_id='$lecturer_id'
ORDER BY m.material_id DESC
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Materials</title>
    <link rel="stylesheet" href="/UniShop/assets/css/dashboard.css">
        <link rel="stylesheet" href="/UniShop/assets/css/sidebar.css">


    <style>
        .table-card{
            background:#fff;
            padding:20px;
            border-radius:10px;
        }

        table{
            width:100%;
            border-collapse:collapse;
        }

        th, td{
            padding:12px;
            border-bottom:1px solid #eee;
            text-align:left;
        }

        th{
            background:#f8f9fb;
        }

        .btn{
            padding:6px 10px;
            border-radius:6px;
            text-decoration:none;
            font-size:13px;
        }

        .edit{
            background:#1e66ff;
            color:#fff;
        }

        .delete{
            background:#ff4d4d;
            color:#fff;
        }

        .top{
            display:flex;
            justify-content:space-between;
            align-items:center;
            margin-bottom:15px;
        }

        .add{
            background:#1e66ff;
            color:#fff;
            padding:10px 15px;
            border-radius:8px;
            text-decoration:none;
        }
    </style>
</head>

<body>

<?php include "../includes/lecturer_sidebar.php"; ?>

<div class="main-content">

<?php include "../includes/header.php"; ?>

<br>
<div class="top">
    
    <h2>My Materials</h2>
    <a href="upload_material.php" class="add">+ Upload New</a>
</div>

<div class="table-card">

<table>

<tr>
    <th>TITLE</th>
    <th>COURSE CODE</th>
    <th>DEPARTMENT</th>
    <th>PRICE</th>
        <th>QTY</th>

    <th>STOCK LEFT</th>
    <th>STATUS</th>
</tr>

<?php while($row = $materials->fetch_assoc()){ ?>

<tr>

    <td><?php echo $row['title']; ?></td>
    <td><?php echo $row['course_code']; ?></td>
    <td><?php echo $row['department_name']; ?></td>
    <td>₦<?php echo number_format($row['price']); ?></td>
<td><?php echo $row['uploaded_qty']; ?></td>
<td><?php echo $row['qty_left']; ?></td>    <td>

<?php if($row['qty_left'] > 0){ ?>

<span style="
background:#dcfce7;
color:#166534;
padding:5px 10px;
border-radius:20px;
font-size:13px;
font-weight:600;
">
In Stock
</span>

<?php }else{ ?>

<span style="
background:#fee2e2;
color:#b91c1c;
padding:5px 10px;
border-radius:20px;
font-size:13px;
font-weight:600;
">
Out of Stock
</span>

<?php } ?>

</td>

    

</tr>

<?php } ?>

</table>

</div>

</div>

</body>
</html>
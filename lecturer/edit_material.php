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
   GET MATERIAL ID
===================== */
if(!isset($_GET['id'])){
    header("Location: my_materials.php");
    exit();
}

$id = $_GET['id'];

/* =====================
   FETCH MATERIAL (SECURE)
===================== */
$result = $conn->query("
SELECT * FROM materials
WHERE material_id='$id'
AND lecturer_id='$lecturer_id'
LIMIT 1
");

if($result->num_rows == 0){
    echo "Material not found or unauthorized access.";
    exit();
}

$material = $result->fetch_assoc();

/* =====================
   GET DROPDOWNS
===================== */
$departments = $conn->query("SELECT * FROM departments");

/* =====================
   UPDATE MATERIAL
===================== */
$message = "";

if(isset($_POST['update'])){

    $title = $_POST['title'];
    $course_code = $_POST['course_code'];
    $price = $_POST['price'];
    $quantity = $_POST['quantity'];
    $department_id = $_POST['department_id'];
    $level = $_POST['level'];
$semester = $_POST['semester'];

    $conn->query("
        UPDATE materials SET
            title='$title',
            course_code='$course_code',
            price='$price',
            quantity='$quantity',
department_id='$department_id',
level='$level',
semester='$semester'
        WHERE material_id='$id'
        AND lecturer_id='$lecturer_id'
    ");

    echo "<script>
alert('Material updated successfully!');
window.location='my_materials.php';
</script>";
exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Material</title>
    <link rel="stylesheet" href="/UniShop/assets/css/dashboard.css">
        <link rel="stylesheet" href="/UniShop/assets/css/sidebar.css">


    <style>
        .form-card{
            background:#fff;
            padding:25px;
            border-radius:12px;
            width:60%;
            box-shadow:0 10px 25px rgba(0,0,0,0.05);
        }

        input, select{
            width:100%;
            padding:12px;
            margin-bottom:12px;
            border:1px solid #ddd;
            border-radius:8px;
        }

        button{
            width:100%;
            padding:12px;
            background:#1e66ff;
            color:#fff;
            border:none;
            border-radius:8px;
            cursor:pointer;
        }

        .success{
            color:green;
            margin-bottom:10px;
        }
    </style>
</head>

<body>

<?php include "../includes/lecturer_sidebar.php"; ?>

<div class="main-content">

<?php include "../includes/header.php"; ?>
<br>
<h2>Edit Material</h2>

<?php if($message != ""){ ?>
    <p class="success"><?php echo $message; ?></p>
<?php } ?>

<div class="form-card">

<form method="POST">

    <label>Material Title</label>
    <input type="text" name="title" value="<?php echo $material['title']; ?>" required>

    <label>Course Code</label>
    <input type="text" name="course_code" value="<?php echo $material['course_code']; ?>" required>

    <label>Department</label>
    <select name="department_id" required>

        <?php while($d = $departments->fetch_assoc()){ ?>
            <option value="<?php echo $d['department_id']; ?>"
                <?php if($d['department_id'] == $material['department_id']) echo "selected"; ?>>
                <?php echo $d['department_name']; ?>
            </option>
        <?php } ?>

    </select>

    <label>Level</label>

<select name="level">

<option value="100" <?php if($material['level']=="100") echo "selected"; ?>>100 Level</option>

<option value="200" <?php if($material['level']=="200") echo "selected"; ?>>200 Level</option>

<option value="300" <?php if($material['level']=="300") echo "selected"; ?>>300 Level</option>

<option value="400" <?php if($material['level']=="400") echo "selected"; ?>>400 Level</option>

<option value="500" <?php if($material['level']=="500") echo "selected"; ?>>500 Level</option>

</select>

<label>Semester</label>

<select name="semester">

<option value="First" <?php if($material['semester']=="First") echo "selected"; ?>>First Semester</option>

<option value="Second" <?php if($material['semester']=="Second") echo "selected"; ?>>Second Semester</option>

</select>

    <label>Price (₦)</label>
    <input type="number" name="price" value="<?php echo $material['price']; ?>" required>

    <label>Quantity</label>
    <input type="number" name="quantity" value="<?php echo $material['quantity']; ?>" required>

    <button type="submit" name="update">Update Material</button>

</form>

</div>

</div>

</body>
</html>
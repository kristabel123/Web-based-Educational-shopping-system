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
LIMIT 1
");

if($getLecturer->num_rows > 0){
    $lecturer = $getLecturer->fetch_assoc();
    $lecturer_id = $lecturer['lecturer_id'];
}else{
    die("No lecturer profile found.");
}

/* =====================
   GET DROPDOWNS
===================== */
$departments = $conn->query("SELECT * FROM departments");
$faculties = $conn->query("SELECT * FROM faculties");

/* =====================
   HANDLE SUBMIT
===================== */
$message = "";

if(isset($_POST['upload'])){

    $title = $_POST['title'];
    $course_code = $_POST['course_code'];
    $course_title = $_POST['course_title'];
    $department_id = $_POST['department_id'];
    $faculty_id = $_POST['faculty_id'];
    $material_type = $_POST['material_type'];
    $level = $_POST['level'];
    $semester = $_POST['semester'];
    $price = $_POST['price'];
    $quantity = $_POST['quantity'];

    /* IMAGE UPLOAD */
   /* $image_name = $_FILES['image']['name'];
    $tmp_name = $_FILES['image']['tmp_name'];

    $folder = "../uploads/" . $image_name;
    move_uploaded_file($tmp_name, $folder); */

    $conn->query("
        INSERT INTO materials (
            lecturer_id,
            title,
            course_code,
            course_title,
            material_type,
            department_id,
            faculty_id,
            level,
            semester,
            price,
            uploaded_qty,
            qty_left,
            created_at
        )
        VALUES (
            '$lecturer_id',
            '$title',
            '$course_code',
            '$course_title',
            '$material_type',
            '$department_id',
            '$faculty_id',
            '$level',
            '$semester',
            '$price',
            '$quantity',
            '$quantity',
            NOW()
        )
    ");

    $message = "Material uploaded successfully!";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Upload Material</title>
    <link rel="stylesheet" href="/UniShop/assets/css/dashboard.css">
        <link rel="stylesheet" href="/UniShop/assets/css/sidebar.css">


    <style>
        .page-title{
            margin-bottom:20px;
        }

        .page-title h2{
            margin:0;
        }

        .page-title p{
            color:#666;
            margin-top:5px;
        }

        .form-card{
            background:#fff;
            padding:25px;
            border-radius:12px;
            width:70%;
            box-shadow:0 10px 25px rgba(0,0,0,0.05);
        }

        .grid{
            display:grid;
            grid-template-columns:1fr 1fr;
            gap:15px;
        }

        input, select{
            width:100%;
            padding:12px;
            border:1px solid #ddd;
            border-radius:8px;
            box-sizing:border-box;
        }

        label{
            font-size:13px;
            margin-bottom:5px;
            display:block;
            color:#444;
        }

        .full{
            grid-column:span 2;
        }

        button{
            width:100%;
            padding:12px;
            margin-top:15px;
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

<div class="page-title">
    <br>
    <h2>Upload Material</h2>
    <p>Add a new course material for students</p>
</div>

<?php if($message != ""){ ?>
    <p class="success"><?php echo $message; ?></p>
<?php } ?>

<div class="form-card">

<form method="POST" enctype="multipart/form-data">

<div class="grid">

    <div>
        <label>Material Title *</label>
        <input type="text" name="title" required>
    </div>

    <div>
        <label>Course Code *</label>
        <input type="text" name="course_code" required>
    </div>

    <div class="full">
        <label>Course Title</label>
        <input type="text" name="course_title">
    </div>

    <div>
        <label>Department *</label>
        <select name="department_id" required>
            <option value="">Select Department</option>
            <?php while($d=$departments->fetch_assoc()){ ?>
                <option value="<?php echo $d['department_id']; ?>">
                    <?php echo $d['department_name']; ?>
                </option>
            <?php } ?>
        </select>
    </div>

    <div>
        <label>Faculty</label>
        <select name="faculty_id">
            <option value="">Select Faculty</option>
            <?php while($f=$faculties->fetch_assoc()){ ?>
                <option value="<?php echo $f['faculty_id']; ?>">
                    <?php echo $f['faculty_name']; ?>
                </option>
            <?php } ?>
        </select>
    </div>
<div class="form-group">
    <label>Material Type *</label>

<select name="material_type" required>

    <option value="">Select Material Type</option>

    <option value="Textbook">Textbook</option>

    <option value="Handout">Handout</option>

    <option value="Practical Manual">Practical Manual</option>

    <option value="Lecture Note">Lecture Guide</option>

    <option value="Past Questions">Past Questions</option>
    <option value="Textbook and Manual">Textbook and Manual</option>

</select>
</div>
    <div>
        <label>Academic Level</label>
        <select name="level">
            <option value="">Select Level</option>
            <option>100</option>
            <option>200</option>
            <option>300</option>
            <option>400</option>
            <option>500</option>
        </select>
    </div>

    <div>
        <label>Semester</label>
        <select name="semester">
            <option value="">Select Semester</option>
            <option>First Semester</option>
            <option>Second Semester</option>
        </select>
    </div>

    <div>
        <label>Price (₦) *</label>
        <input type="number" name="price" required>
    </div>

    <div>
        <label>Quantity Available *</label>
        <input type="number" name="quantity" required>
    </div>

    

</div>

<button type="submit" name="upload">Save Material</button>

</form>

</div>

</div>

</body>
</html>
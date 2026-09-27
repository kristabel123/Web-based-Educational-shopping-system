<?php
session_start();
include "../config/database.php";

if(isset($_POST['save'])){

$fullname=$_POST['fullname'];
$email=$_POST['email'];
$password = $_POST['password'];
$matric=$_POST['matric'];
$department=$_POST['department'];
$level=$_POST['level'];
$phone=$_POST['phone'];

$conn->query("INSERT INTO accounts(full_name,email,password,role,department_id,status)
VALUES('$fullname','$email','$password','student','$department','Active')");

$account_id=$conn->insert_id;

$conn->query("INSERT INTO students(account_id,matric_number,department_id,level,phone_number)
VALUES('$account_id','$matric', '$department','$level','$phone')");

/* Notify Admin */
$admin = $conn->query("
SELECT account_id
FROM accounts
WHERE role='admin'
LIMIT 1
");

if($admin->num_rows > 0){

    $admin_id = $admin->fetch_assoc()['account_id'];

    $conn->query("
    INSERT INTO notifications(user_id, message)
    VALUES(
        '$admin_id',
        'New student \"$fullname\" was added successfully.'
    )
    ");
}

header("Location: manage_users.php");
exit();
}
$departments=$conn->query("SELECT * FROM departments");
?>

<!DOCTYPE html>
<html>
<head>

<title>Add Student</title>

<link rel="stylesheet" href="/UniShop/assets/css/dashboard.css">
    <link rel="stylesheet" href="/UniShop/assets/css/sidebar.css">


<style>

.form-card{
background:#fff;
padding:30px;
border-radius:12px;
box-shadow:0 2px 8px rgba(0,0,0,.08);
max-width:700px;
}

input,select{
width:100%;
padding:12px;
margin-bottom:15px;
border:1px solid #ddd;
border-radius:8px;
}

button{
background:#2563eb;
color:white;
padding:12px 25px;
border:none;
border-radius:8px;
cursor:pointer;
}

</style>

</head>

<body>

<?php include "../includes/admin_sidebar.php"; ?>

<div class="main-content">

<?php include "../includes/header.php"; ?>
<br>

<h2>Add Student</h2>
<br>
<div class="form-card">

<form method="POST">

<input type="text" name="fullname" placeholder="Full Name" required>

<input type="email" name="email" placeholder="Email" required>

<input type="password" name="password" placeholder="Password" required>

<input type="text" name="matric" placeholder="Matric Number" required>


<select name="department">

<?php while($row=$departments->fetch_assoc()){ ?>

<option value="<?php echo $row['department_id']; ?>">
<?php echo $row['department_name']; ?>
</option>

<?php } ?>

</select>

<input type="text" name="level" placeholder="Level (e.g. 100)" required>

<input type="text" name="phone" placeholder="Phone Number">

<button name="save">
Add Student
</button>

</form>

</div>

</div>

</body>

</html>
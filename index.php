<?php
session_start();
include "config/database.php";

/* =========================
   AUTO REDIRECT IF LOGGED IN
========================= */
if(isset($_SESSION['account_id']) && isset($_SESSION['role'])){

    if($_SESSION['role'] == 'admin'){
        header("Location: /UniShop/admin/dashboard.php");
        exit();
    }
    elseif($_SESSION['role'] == 'lecturer'){
        header("Location: /UniShop/lecturer/dashboard.php");
        exit();
    }
    else{
        header("Location: /UniShop/student/dashboard.php");
        exit();
    }
}

/* =========================
   LOGIN PROCESS
========================= */
$error = "";

if(isset($_POST['login'])){

    $email = $_POST['email'];
    $password = $_POST['password'];

    $query = $conn->query("
        SELECT * FROM accounts
        WHERE email='$email'
        AND status='active'
        LIMIT 1
    ");

    if($query && $query->num_rows > 0){

        $user = $query->fetch_assoc();
        echo "ROLE FROM DB: " .$user['role'];


        /* TEMP FIX (since your DB is NOT hashed) */
        if($password == $user['password']){

            // STORE SESSION
           $role = $user['role'];

$_SESSION['account_id'] = $user['account_id'];
$_SESSION['role'] = $role;
$_SESSION['full_name'] = $user['full_name'];

if($role === 'admin'){
    header("Location: /UniShop/admin/dashboard.php");
    exit();
}

elseif($role === 'lecturer'){
    header("Location: /UniShop/lecturer/dashboard.php");
    exit();
}

elseif($role === 'student'){
    header("Location: /UniShop/student/dashboard.php");
    exit();
}

else{
    echo "Invalid role";
    exit();
}

        } else {
            $error = "Incorrect password";
        }

    } else {
        $error = "Email not found";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>UniShop Login</title>

    <style>
        body{
            margin:0;
            font-family: Arial, sans-serif;
            background:#f5f7fb;
            display:flex;
            justify-content:center;
            align-items:center;
            height:100vh;
        }

        .container{
            width:100%;
            max-width:400px;
            text-align:center;
        }

        .logo{
            width:70px;
            height:70px;
            margin-bottom:10px;
        }

        h1{
            margin:0;
            font-size:22px;
        }

        p{
            color:#666;
            font-size:13px;
            margin-bottom:20px;
        }

        .card{
            background:white;
            padding:30px;
            border-radius:12px;
            box-shadow:0 0px 5px rgba(0, 0, 0, 0.82);
            text-align:left;
        }

        label{
            font-size:13px;
            color:#444;
            display:block;
            margin-top:10px;
            margin-bottom:5px;
        }

        input{
            width:100%;
            padding:12px;
            border:1px solid #ddd;
            border-radius:8px;
            box-sizing:border-box;
        }

        .row{
            display:flex;
            justify-content:space-between;
            align-items:center;
        }

        .forgot{
            font-size:12px;
            color:#1e66ff;
            text-decoration:none;
        }

        .btn{
            width:100%;
            margin-top:15px;
            padding:12px;
            background:#1e66ff;
            color:#fff;
            border:none;
            border-radius:8px;
            cursor:pointer;
        }

        .error{
            color:red;
            font-size:13px;
            text-align:center;
            margin-bottom:10px;
        }

        .footer{
            margin-top:15px;
            font-size:12px;
            color:#666;
            text-align:center;
        }
        body{
            margin:0;
            min-height: 100vh;
            background: linear-gradient(rgba(255, 255, 255, 0.75),
            rgba(255, 255, 255, 0.75)),
            url('/UniShop/assets/images/background.png') center/cover no-repeat;
        }
    </style>
</head>

<body>

<div class="container">

    <!-- LOGO -->

    <h1>Educational Shopping System</h1>
    <p>University Materials Shopping Portal - Log in with your credentials</p>

    <div class="card">

        <?php if($error != ""){ ?>
            <div class="error"><?php echo $error; ?></div>
        <?php } ?>

        <form method="POST">

            <label>Email</label>
            <input type="email" name="email" placeholder="Enter email address" required>

            <label>Password</label>

            <div class="row">
                <small></small>
                <a href="#" class="forgot">Forgot password?</a>
            </div>

            <input type="password" name="password" placeholder="Enter password" required>

            <button class="btn" type="submit" name="login">
                Log in
            </button>

        </form>

    </div>

    <div class="footer">
        Contact your administrator for account access
    </div>

</div>

</body>
</html>
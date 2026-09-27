<?php
session_start();
include "../config/database.php";

if(!isset($_SESSION['account_id'])){
    header("Location: /UniShop/");
    exit();
}

if(isset($_GET['id'])){

$id = $_GET['id'];

/* Delete related student record if it exists */
$conn->query("DELETE FROM students WHERE account_id='$id'");

/* Delete related lecturer record if it exists */
$conn->query("DELETE FROM lecturers WHERE account_id='$id'");

/* Delete account */
$conn->query("DELETE FROM accounts WHERE account_id='$id'");

header("Location: manage_users.php");
exit();

}else{

header("Location: manage_users.php");
exit();

}
?>
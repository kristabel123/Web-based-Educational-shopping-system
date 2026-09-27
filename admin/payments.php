<?php
session_start();
include "../config/database.php";

if(!isset($_SESSION['account_id'])){
    header("Location: /UniShop/");
    exit();
}

$payments = $conn->query("
SELECT
payments.payment_id,
accounts.full_name,
materials.title,
payments.amount,
payments.status,
payments.paid_at

FROM payments

JOIN students
ON payments.student_id = students.student_id

JOIN accounts
ON students.account_id = accounts.account_id

JOIN orders
ON payments.order_id = orders.order_id

JOIN materials
ON orders.material_id =
materials.material_id

ORDER BY payments.payment_id DESC
");
?>

<!DOCTYPE html>
<html>

<head>

<title>Payment History</title>

<link rel="stylesheet" href="/UniShop/assets/css/dashboard.css">
    <link rel="stylesheet" href="/UniShop/assets/css/sidebar.css">


</head>

<body>

<?php include "../includes/admin_sidebar.php"; ?>

<div class="main-content">

<?php include "../includes/header.php"; ?>
<br>
<h1>Payment History</h1>
<br>
<p><?php echo $payments->num_rows; ?> payments</p>

<br>

<input
type="text"
id="searchInput"
name="search"
class="search-box"
placeholder="Search payment..."
style="width:420px;padding:12px;border:1px solid #ddd;border-radius:8px;">

<br><br>

<div class="table-card">

<table id="paymentsTable">

<tr>

<th>STUDENT</th>

<th>AMOUNT</th>

<th>MATERIAL</th>

<th>STATUS</th>

<th>PAID DATE</th>

</tr>

<?php

if($payments->num_rows>0){

while($row=$payments->fetch_assoc()){

?>

<tr>

<td><?php echo $row['full_name']; ?></td>

<td>₦<?php echo number_format($row['amount']); ?></td>

<td><?php echo $row['title']; ?></td>

<td>
    <?php echo $row['status'] ?
    ucfirst($row['status']) : "Successful"; ?>
</td>

<td><?php echo $row['paid_at']; ?></td>

</tr>

<?php
}

}else{
?>

<tr>

<td colspan="5" style="text-align:center;padding:40px;">
No payment records found
</td>

</tr>

<?php } ?>

</table>

</div>

</div>

<script>

document.getElementById("searchInput").addEventListener("keyup", function(){

    let input = this.value.toLowerCase();

    let table = document.getElementById("paymentsTable");

    let rows = table.getElementsByTagName("tr");

    for(let i = 1; i < rows.length; i++){

        let rowText = rows[i].textContent.toLowerCase();

        if(rowText.includes(input)){
            rows[i].style.display = "";
        }else{
            rows[i].style.display = "none";
        }

    }

});

</script>

</body>

</html>
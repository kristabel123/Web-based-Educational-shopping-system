<?php
session_start();
include "../config/database.php";

if(!isset($_SESSION['account_id'])){
    header("Location: /UniShop/");
    exit();
}

$search = "";
if(isset($_GET['search'])){
    $search = trim($_GET['search']);
}

$orders = $conn->query("
SELECT
orders.order_id,
accounts.full_name,
materials.title,
orders.quantity,
orders.total_amount,
orders.payment_status,
orders.pickup_status,
orders.created_at

FROM orders

JOIN students
ON orders.student_id = students.student_id

JOIN accounts
ON students.account_id = accounts.account_id

JOIN materials
ON orders.material_id = materials.material_id

WHERE
accounts.full_name LIKE '%$search%'
OR
materials.title LIKE '%$search%'

ORDER BY orders.order_id DESC
");
?>

<!DOCTYPE html>
<html>

<head>

<title>All Orders</title>

<link rel="stylesheet" href="/UniShop/assets/css/dashboard.css">
    <link rel="stylesheet" href="/UniShop/assets/css/sidebar.css">


</head>

<body>

<?php include "../includes/admin_sidebar.php"; ?>

<div class="main-content">

<?php include "../includes/header.php"; ?>
<br>
<h1>All Orders</h1>
<br>
<p><?php echo $orders->num_rows; ?> total orders</p>

<br>

<form method="GET">

<input
type="text"
id="searchInput"
name="search"
placeholder="Search student or material..."
value="<?php echo $search; ?>"
style="width:420px;padding:12px;border:1px solid #ddd;border-radius:8px;">

<button class="btn-blue" type="submit">
Search
</button>

</form>

<br><br>

<div class="table-card">

<table id="ordersTable">

<tr>

<th>STUDENT</th>

<th>MATERIAL</th>

<th>QTY</th>

<th>AMOUNT</th>

<th>PAYMENT</th>

<th>PICKUP</th>

<th>DATE</th>

</tr>

<?php

if($orders->num_rows>0){

while($row=$orders->fetch_assoc()){

?>

<tr>

<td><?php echo $row['full_name']; ?></td>

<td><?php echo $row['title']; ?></td>

<td><?php echo $row['quantity']; ?></td>

<td>₦<?php echo number_format($row['total_amount']); ?></td>

<td><?php echo ucfirst($row['payment_status']); ?></td>

<td><?php echo ucfirst($row['pickup_status']); ?></td>

<td><?php echo date("d/m/Y",strtotime($row['created_at'])); ?></td>

</tr>

<?php

}

}else{

?>

<tr>

<td colspan="7" style="text-align:center;padding:40px;">

No orders found

</td>

</tr>

<?php } ?>

</table>

</div>

</div>

<script>

document.getElementById("searchInput").addEventListener("keyup", function(){

    let input = this.value.toLowerCase();

    let table = document.getElementById("ordersTable");

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
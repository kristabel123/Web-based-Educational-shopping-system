<?php
session_start();
include "../config/database.php";

if(!isset($_SESSION['account_id'])){
    header("Location: /UniShop/");
    exit();
}

$search = isset($_GET['search']) ? $_GET['search'] : '';
$department = isset($_GET['department']) ? $_GET['department'] : '';

$sql = "
SELECT
materials.material_id,
materials.title,
materials.course_code,
materials.price,
materials.uploaded_qty,
materials.qty_left,
accounts.full_name,
departments.department_name

FROM materials

JOIN lecturers
ON materials.lecturer_id = lecturers.lecturer_id

JOIN accounts
ON lecturers.account_id = accounts.account_id

LEFT JOIN departments
ON materials.department_id = departments.department_id

WHERE 1
";

if($search != ""){
$sql .= " AND materials.title LIKE '%$search%'";
}

if($department != ""){
$sql .= " AND materials.department_id='$department'";
}

$sql .= " ORDER BY materials.material_id DESC";

$materials = $conn->query($sql);

$departments = $conn->query("SELECT * FROM departments");
?>

<!DOCTYPE html>
<html>
<head>
    <title>All Materials</title>
    <link rel="stylesheet" href="/UniShop/assets/css/dashboard.css">
        <link rel="stylesheet" href="/UniShop/assets/css/sidebar.css">

</head>

<body>

<?php include "../includes/admin_sidebar.php"; ?>

<div class="main-content">

<?php include "../includes/header.php"; ?>

<div class="page-top">

<div>
    <br>
<h1>All Materials</h1>
<br>
<p><?php echo $materials->num_rows; ?> materials available</p>
</div>

<div></div>

</div>

<form method="GET">

<div class="filters">

<input
type="text"
id="searchInput"
name="search"
class="search-box"
placeholder="Search material title..."
value="<?php echo $search; ?>">

<select
id="departmentFilter"
name="department"
class="filter-box">

<option value="">All Departments</option>

<?php while($dept=$departments->fetch_assoc()){ ?>

<option
value="<?php echo $dept['department_id']; ?>"

<?php if($department==$dept['department_id']) echo "selected"; ?>>

<?php echo $dept['department_name']; ?>

</option>

<?php } ?>

</select>

<button type="submit" class="btn-blue">
Search
</button>

</div>

</form>

<div class="table-card">

<table id="materialsTable">

<tr>
<th>MATERIAL</th>
<th>COURSE CODE</th>
<th>LECTURER</th>
<th>DEPARTMENT</th>
<th>PRICE</th>
<th>STATUS</th>
</tr>

<?php
if($materials->num_rows>0){

while($row=$materials->fetch_assoc()){
?>

<tr>

<td><?php echo $row['title']; ?></td>

<td><?php echo $row['course_code']; ?></td>

<td><?php echo $row['full_name']; ?></td>

<td><?php echo $row['department_name']; ?></td>

<td>₦<?php echo number_format($row['price']); ?></td>


<td>

<?php

if($row['qty_left']>0){

echo "<span class='status'>Available</span>";

}else{

echo "<span class='status' style='background:#ffe3e3;color:#c62828;'>Out of Stock</span>";

}

?>

</td>

</tr>

<?php

}

}else{

?>

<tr>

<td colspan="7" style="text-align:center;padding:40px;">

No materials found

</td>

</tr>

<?php } ?>

</table>

</div>

</div>

<script>

document.getElementById("searchInput").addEventListener("keyup", function(){

    let input = this.value.toLowerCase();

    let table = document.getElementById("materialsTable");

    let rows = table.getElementsByTagName("tr");

    for(let i = 1; i < rows.length; i++){

        let material = rows[i].cells[0].textContent.toLowerCase();

        if(material.startsWith(input)){
            rows[i].style.display = "";
        }else{
            rows[i].style.display = "none";
        }

    }

});

</script>

<script>

document.getElementById("departmentFilter").addEventListener("change", function(){

    let department = this.options[this.selectedIndex].text.toLowerCase();

    let table = document.getElementById("materialsTable");

    let rows = table.getElementsByTagName("tr");

    for(let i = 1; i < rows.length; i++){

        let rowDepartment = rows[i].cells[3].textContent.toLowerCase();

        if(department == "all departments" || rowDepartment == department){
            rows[i].style.display = "";
        }else{
            rows[i].style.display = "none";
        }

    }

});

</script>

</body>
</html>
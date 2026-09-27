<?php
session_start();
include "../config/database.php";

if(!isset($_SESSION['account_id'])){
    header("Location: /UniShop/");
    exit();
}

$search = isset($_GET['search']) ? $_GET['search'] : '';
$department = isset($_GET['department']) ? $_GET['department'] : '';

$search = isset($_GET['search']) ? $_GET['search'] : '';
$role = isset($_GET['role']) ? $_GET['role'] : '';
$department = isset($_GET['department']) ? $_GET['department'] : '';

$sql = "
SELECT
accounts.account_id,
accounts.full_name,
accounts.email,
accounts.role,
accounts.status,
departments.department_name,
accounts.department_id

FROM accounts

LEFT JOIN departments
ON accounts.department_id = departments.department_id

WHERE 1
";

if($search != ""){
    $sql .= " AND (accounts.full_name LIKE '%$search%' OR accounts.email LIKE '%$search%')";
}

if($role != ""){
    $sql .= " AND accounts.role='$role'";
}

if($department != ""){
    $sql .= " AND accounts.department_id='$department'";
}

$sql .= " ORDER BY accounts.account_id DESC";

$users = $conn->query($sql);

$departments = $conn->query("SELECT * FROM departments");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Users</title>
    <link rel="stylesheet" href="/UniShop/assets/css/dashboard.css">
        <link rel="stylesheet" href="/UniShop/assets/css/sidebar.css">

</head>

<body>

<?php include "../includes/admin_sidebar.php"; ?>

<div class="main-content">

<?php include "../includes/header.php"; ?>

<br>

<div class="page-top">

<div>

<h1>Manage Users</h1>

<p><?php echo $users->num_rows; ?> total users</p>

</div>

<div class="top-buttons">

<a href="add_lecturer.php" class="btn-blue">
+ Add Lecturer
</a>

<a href="add_student.php" class="btn-white">
+ Add Student
</a>

</div>

</div>

<form method="GET">

<div class="filters">

<input
type="text"
id="searchInput"
name="search"
class="search-box"
placeholder="Search by name or email..."
value="<?php echo $search; ?>">

<select
id="roleFilter"
name="role"
class="filter-box">

<option value="">All Roles</option>

<option value="admin" <?php if($role=="admin") echo "selected"; ?>>Admin</option>

<option value="lecturer" <?php if($role=="lecturer") echo "selected"; ?>>Lecturer</option>

<option value="student" <?php if($role=="student") echo "selected"; ?>>Student</option>

</select>

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

<br>

<div class="table-card">

<table id="usersTable">

<tr>
    <th>NAME</th>
    <th>EMAIL</th>
    <th>ROLE</th>
    <th>DEPARTMENT</th>
    <th>STATUS</th>
    <th>ACTION</th>
</tr>

<?php while($row = $users->fetch_assoc()){ ?>

<tr
data-role="<?php echo strtolower($row['role']); ?>"
data-department="<?php echo $row['department_id']; ?>">

<td><?php echo $row['full_name']; ?></td>

<td><?php echo $row['email']; ?></td>

<td><?php echo ucfirst($row['role']); ?></td>

<td><?php echo $row['department_name']; ?></td>

<td>
<span class="status">
<?php echo ucfirst($row['status']); ?>
</span>
</td>

<td>

<a href="edit_user.php?id=<?php echo $row['account_id']; ?>">✏️</a>

&nbsp;

<a
href="delete_user.php?id=<?php echo $row['account_id']; ?>"
onclick="return confirm('Are you sure you want to delete this user? This action cannot be undone.');">
🗑️
</a>
</td>

</tr>

<?php } ?>

</table>

</div>

</div>

<script>
const searchInput = document.getElementById("searchInput");
const roleFilter = document.getElementById("roleFilter");
const departmentFilter = document.getElementById("departmentFilter");
const table = document.getElementById("usersTable");

function filterTable(){

    let input = searchInput.value.toLowerCase();
    let role = roleFilter.value.toLowerCase();
    let department = departmentFilter.value;

    let rows = table.getElementsByTagName("tr");

    for(let i = 1; i < rows.length; i++){

        let name = rows[i].cells[0].textContent.toLowerCase();
        let email = rows[i].cells[1].textContent.toLowerCase();
        let rowRole = rows[i].dataset.role;

        let searchMatch =
            name.startsWith(input) ||
            email.startsWith(input);

        let roleMatch =
            role === "" || rowRole === role;

            let rowDepartment = rows[i].dataset.department;

let departmentMatch =
    department === "" || rowDepartment === department;

        if(searchMatch && roleMatch && departmentMatch){
    rows[i].style.display = "";
}else{
    rows[i].style.display = "none";
}

    }

}

searchInput.addEventListener("keyup", filterTable);
roleFilter.addEventListener("change", filterTable);
departmentFilter.addEventListener("change", filterTable);

</script>
</body>
</html>
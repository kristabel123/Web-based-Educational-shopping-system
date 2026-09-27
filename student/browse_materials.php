<?php
session_start();
include "../config/database.php";

if(!isset($_SESSION['account_id']) || $_SESSION['role'] != 'student'){
    header("Location: /UniShop/login.php");
    exit();
}

$search = $_GET['search'] ?? "";
$department = $_GET['department'] ?? "";

/* FETCH DEPARTMENTS */
$departments = $conn->query("
SELECT department_id, department_name
FROM departments
ORDER BY department_name
");

/* FETCH MATERIALS */
$sql = "
SELECT
materials.material_id,
materials.title,
materials.course_code,
materials.material_type,
materials.level,
materials.price,
materials.description,
departments.department_name,
accounts.full_name

FROM materials

LEFT JOIN lecturers
ON materials.lecturer_id = lecturers.lecturer_id

LEFT JOIN accounts
ON lecturers.account_id = accounts.account_id

LEFT JOIN departments
ON materials.department_id = departments.department_id

WHERE 1=1
";

if($search != ""){
    $sql .= " AND (materials.title LIKE '%$search%' OR materials.course_code LIKE '%$search%')";
}

if($department != ""){
    $sql .= " AND materials.department_id='$department'";
}

$sql .= " ORDER BY materials.created_at DESC";

$materials = $conn->query($sql);
?>
<!DOCTYPE html>
<html>

<head>

    <title>Browse Materials</title>

    <link rel="stylesheet" href="/UniShop/assets/css/dashboard.css">
    <link rel="stylesheet" href="/UniShop/assets/css/sidebar.css">

</head>

<body>

<?php include "../includes/student_sidebar.php"; ?>

<div class="main-content">

<?php include "../includes/header.php"; ?>

<br>
<div class="page-header">
    <h2>Browse Materials</h2>
</div>

<form method="GET" class="search-form">

<input
type="text"
id="searchInput"
name="search"
placeholder="Search by title or course code..."
value="<?php echo htmlspecialchars($search); ?>">

<select name="department">

<option value="">All Departments</option>

<?php while($dept = $departments->fetch_assoc()){ ?>

<option
value="<?php echo $dept['department_id']; ?>"
<?php if($department == $dept['department_id']) echo "selected"; ?>>

<?php echo $dept['department_name']; ?>

</option>

<?php } ?>

</select>

<button type="submit" class="buy-btn">
Search
</button>

</form>

<div class="materials-grid">

<?php while($row = $materials->fetch_assoc()){ ?>

<div class="material-card"
data-search="<?php echo
htmlspecialchars($row['title'].' '.
$row['course_code']); ?>">

<h3><?php echo $row['title']; ?></h3>

<p>
<strong><?php echo $row['course_code']; ?>
<?php echo $row['material_type']; ?></strong>
</p>

<p><?php echo $row['department_name']; ?></p>

<p><?php echo $row['level']; ?> Level</p>

<p>By <?php echo $row['full_name']; ?></p>

<div class="material-price">₦<?php echo number_format($row['price']); ?></div>

<a
href="buy_material.php?id=<?php echo $row['material_id']; ?>"
class="buy-btn">

Buy Now

</a>

</div>

<?php } ?>

</div>
</div>
<script>
document.getElementById("searchInput").addEventListener("input", function() {

    let search = this.value.toLowerCase();

    document.querySelectorAll(".material-card").forEach(function(card) {

        let title = card.querySelector("h3").textContent.toLowerCase();
        let courseCode = card.querySelector("strong").textContent.toLowerCase();

        if (title.includes(search) || courseCode.includes(search)) {
            card.style.display = "";
        } else {
            card.style.display = "none";
        }

    });

});
</script>
</body>
</html>
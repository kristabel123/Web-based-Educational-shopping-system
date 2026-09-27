<?php
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<div class="sidebar">

    <div class="logo">
        <h3>Educational Shopping System</h3>
    </div>

    <ul>

        <li><a href="/UniShop/lecturer/dashboard.php"
        class="<?= $currentPage == 'dashboard.php' ? 'active' : '' ?>">Dashboard</a></li>

        <li><a href="/UniShop/lecturer/upload_material.php"
        class="<?= $currentPage == 'upload_material.php' ? 'active' : '' ?>">Upload Material</a></li>

        <li><a href="/UniShop/lecturer/my_materials.php"
        class="<?= $currentPage == 'my_materials.php' ? 'active' : '' ?>">My Materials</a></li>

        <li>
            <a href="/UniShop/lecturer/orders.php"> Orders
</a>
</li>

        <li><a href="/UniShop/lecturer/pickup_schedule.php"
        class="<?= $currentPage == 'pickup_schedule.php' ? 'active' : '' ?>">Pickup Schedule</a></li>

        <li><a href="/UniShop/lecturer/sales_history.php"
        class="<?= $currentPage == 'sales_history.php' ? 'active' : '' ?>">Sales History</a></li>

    </ul>
<div class="logout">
        <a href="/UniShop/logout.php"> Logout </a>
</div>
</div>
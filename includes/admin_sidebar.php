<?php
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<div class="sidebar">

    <div class="logo">
        <h3>Educational Shopping System</h3>
    </div>

    <ul>

        <li><a href="/UniShop/admin/dashboard.php"
        class="<?= $currentPage == 'dashboard.php' ? 'active' : '' ?>">Dashboard</a></li>

        <li><a href="/UniShop/admin/manage_users.php"
        class="<?= $currentPage == 'manage_users.php' ? 'active' : '' ?>">Manage Users</a></li>

        <li><a href="/UniShop/admin/materials.php"
        class="<?= $currentPage == 'materials.php' ? 'active' : '' ?>">Materials</a></li>

        <li><a href="/UniShop/admin/orders.php"
        class="<?= $currentPage == 'orders.php' ? 'active' : '' ?>">Orders</a></li>


        <li><a href="/UniShop/admin/reports.php"
        class="<?= $currentPage == 'reports.php' ? 'active' : '' ?>">Reports</a></li>

    </ul>
<div class="logout">
        <a href="/UniShop/logout.php"> Logout </a>
</div>
</div>
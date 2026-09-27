<?php
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<div class="sidebar">

    <div class="logo">
        <h3>Educational Shopping System</h3>
    </div>

    <ul>

        <li><a href="/UniShop/student/dashboard.php"
        class="<?= $currentPage == 'dashboard.php' ? 'active' : '' ?>">Dashboard</a></li>

        <li><a href="/UniShop/student/browse_materials.php"
        class="<?= $currentPage == 'browse_materials.php' ? 'active' : '' ?>">Browse Materials</a></li>

        <li><a href="/UniShop/student/orders.php"
        class="<?= $currentPage == 'orders.php' ? 'active' : '' ?>">My Orders</a></li>

        <li><a href="/UniShop/student/pickup_schedule.php"
class="<?= $currentPage == 'pickup_schedule.php' ? 'active' : '' ?>">Pickup Schedules</a></li>

        <li><a href="/UniShop/student/notifications.php"
        class="<?= $currentPage == 'notifications.php' ? 'active' : '' ?>">Notifications</a></li>

    </ul>
    <div class="logout">
        <a href="/UniShop/logout.php"> Logout </a>
</div>

</div>
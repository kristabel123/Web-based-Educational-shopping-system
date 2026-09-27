<?php
$notif_count = 0;

if(isset($_SESSION['account_id'])){

    $user_id = $_SESSION['account_id'];

    $result = $conn->query("
        SELECT COUNT(*) AS total
        FROM notifications
        WHERE user_id='$user_id'
        AND is_read=0
    ");

    $row = $result->fetch_assoc();
    $notif_count = $row['total'];
}
?>


<div class="topbar">

    <div class="welcome">

        <h2>Welcome back, <?php echo $_SESSION['full_name']; ?></h2>

        <p>
        <?php
        if($_SESSION['role']=="admin"){
            echo "Administrator • University Portal";
        }elseif($_SESSION['role']=="lecturer"){
            echo "Lecturer • University Portal";
        }else{
            echo "Student • University Portal";
        }
        ?>
        </p>

    </div>

    <div class="profile">

        <div class="notif-wrapper" id="notifWrapper">

            <span class="notification">🔔</span>

            <?php if($notif_count > 0){ ?>
                <span class="notif-badge">
                    <?php echo $notif_count; ?>
                </span>
            <?php } ?>

            <div class="notif-dropdown">

                <h4>Notifications</h4>

                <?php

                $latest = $conn->query("
                    SELECT *
                    FROM notifications
                    WHERE user_id='$user_id'
                    ORDER BY created_at DESC
                    LIMIT 5
                ");

                while($n = $latest->fetch_assoc()){
                ?>

                    <div class="notif-item">

                        <?php echo $n['message']; ?>

                        <small>
                            <?php echo date("d M, H:i", strtotime($n['created_at'])); ?>
                        </small>

                    </div>

                <?php } ?>

                <a href="/UniShop/<?php echo $_SESSION['role']; ?>/notifications.php" class="view-all">
                    View All
                </a>

            </div>

        </div>

        <a href="/UniShop/<?php echo $_SESSION['role']; ?>/profile.php" class="profile-link">

            <div class="profile-info">

                <div class="details">

                    <strong><?php echo $_SESSION['full_name']; ?></strong><br>

                    <small><?php echo ucfirst($_SESSION['role']); ?></small>

                </div>

            </div>

        </a>

    </div>

</div>
<script>
const notifWrapper = document.getElementById("notifWrapper");
const notifDropdown = notifWrapper.querySelector(".notif-dropdown");

notifDropdown.style.display = "none";

notifWrapper.addEventListener("click", function(e){
    e.stopPropagation();

    if(notifDropdown.style.display === "block"){
        notifDropdown.style.display = "none";
    }else{
        notifDropdown.style.display = "block";
    }
});

document.addEventListener("click", function(){
    notifDropdown.style.display = "none";
});
</script>
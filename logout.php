<?php
session_start();

// Remove all session data
session_unset();

// Destroy the session
session_destroy();

// Go back to the login page
header("Location: /UniShop/");
exit();
?>
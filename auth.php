<?php
// This file redirects to the auth controller
$action = isset($_GET['action']) ? '?action=' . $_GET['action'] : '';
$token = isset($_GET['token']) ? '&token=' . $_GET['token'] : '';

// Redirect to the controller
header("Location: controllers/auth.php" . $action . $token);
exit();
?>

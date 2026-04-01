<?php
require_once __DIR__ . '/../includes/config.php';

// Redirection vers login ou dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
} else {
    header("Location: login.php");
}
exit();
?>
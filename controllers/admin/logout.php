<?php
session_start();

// Store the message temporarily
$_SESSION['success'] = 'You have been logged out successfully.';

// Destroy the session AFTER storing the message
session_destroy();

// Redirect
header('Location: /admin');
exit();
?>

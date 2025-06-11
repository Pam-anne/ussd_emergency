<?php
session_start();
require_once '../config.php';
require_once '../db_connect.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit();
}

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: users.php');
    exit();
}

// Get and validate input
$name = trim($_POST['name'] ?? '');
$phoneNumber = trim($_POST['phone_number'] ?? '');
$pin = trim($_POST['pin'] ?? '');

$errors = [];

// Validate name
if (empty($name)) {
    $errors[] = "Name is required";
}

// Validate phone number
if (empty($phoneNumber)) {
    $errors[] = "Phone number is required";
} elseif (!preg_match('/^\+?[0-9]{10,15}$/', $phoneNumber)) {
    $errors[] = "Invalid phone number format";
}

// Validate PIN
if (empty($pin)) {
    $errors[] = "PIN is required";
} elseif (!preg_match('/^[0-9]{4}$/', $pin)) {
    $errors[] = "PIN must be exactly 4 digits";
}

// Check if phone number already exists
$stmt = $pdo->prepare("SELECT id FROM users WHERE phone_number = ?");
$stmt->execute([$phoneNumber]);
if ($stmt->rowCount() > 0) {
    $errors[] = "Phone number already registered";
}

// If there are errors, redirect back with error messages
if (!empty($errors)) {
    $_SESSION['add_user_errors'] = $errors;
    $_SESSION['add_user_data'] = [
        'name' => $name,
        'phone_number' => $phoneNumber
    ];
    header('Location: users.php');
    exit();
}

// Add the user
try {
    $stmt = $pdo->prepare("INSERT INTO users (name, phone_number, pin) VALUES (?, ?, ?)");
    $stmt->execute([$name, $phoneNumber, $pin]);
    
    $_SESSION['success_message'] = "User added successfully";
} catch (PDOException $e) {
    $_SESSION['add_user_errors'] = ["Failed to add user: " . $e->getMessage()];
    $_SESSION['add_user_data'] = [
        'name' => $name,
        'phone_number' => $phoneNumber
    ];
}

header('Location: users.php');
exit(); 
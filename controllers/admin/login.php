<?php
session_start();
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../db_connect.php';

// Check if user is already logged in
if (isset($_SESSION['admin_id'])) {
    header('Location: admin/dashboard');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    
    // Validate input
    if (empty($username) || empty($password)) {
        $_SESSION['error'] = 'Please fill in all fields';
        header('Location: admin');
        exit();
    }

    try {
        // Query the admins table instead of users table
        $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ? AND status = 'active' LIMIT 1");
        $stmt->execute([$username]);
        $admin = $stmt->fetch();

        if ($admin && password_verify($password, $admin['password'])) {
            // Set session variables
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            $_SESSION['admin_role'] = $admin['role'];
            $_SESSION['admin_full_name'] = $admin['full_name'];
            $_SESSION['admin_last_activity'] = time();

            // Update last login time
            $update_stmt = $pdo->prepare("UPDATE admins SET last_login = NOW() WHERE id = ?");
            $update_stmt->execute([$admin['id']]);

            // Log successful login (if activity_logs table exists)
            try {
                $log_stmt = $pdo->prepare("INSERT INTO activity_logs (admin_id, action, created_at) VALUES (?, ?, NOW())");
                $log_stmt->execute([$admin['id'], 'Admin login successful']);
            } catch (PDOException $log_error) {
                // Ignore if activity_logs table doesn't exist yet
                error_log("Could not log activity: " . $log_error->getMessage());
            }

            header('Location: admin/dashboard');
            exit();
        } else {
            $_SESSION['error'] = 'Invalid username or password';
            header('Location: admin');
            exit();
        }
    } catch (PDOException $e) {
        error_log("Login error: " . $e->getMessage());
        $_SESSION['error'] = 'An error occurred. Please try again later.';
        header('Location: admin');
        exit();
    }
}

// If not POST request, show login form
require_once __DIR__ . '/../../views/admin/login.view.php';

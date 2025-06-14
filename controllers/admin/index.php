<?php
session_start();
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../db_connect.php';

require_once __DIR__ . '/../../views/partials/auth_check.php';

// Check if user is admin
if (!isset($_SESSION['admin_id']) || $_SESSION['admin_role'] !== 'admin') {
    error_log("Access denied - redirecting to admin login");
    $_SESSION['error'] = 'Access denied. Please login as admin.';
    header('Location: /admin');
    exit();
}

// Initialize variables with default values
$total_users = 0;
$total_contacts = 0;
$today_registrations = 0;
$recent_users = [];

try {
    // Test database connection first
    if (!$pdo) {
        throw new Exception("Database connection not available");
    }

    // Get total USSD users count (excluding admins)
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM users");
    $stmt->execute();
    $result = $stmt->fetch();
    $total_users = $result ? $result['total'] : 0;

    // Get total emergency contacts count
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM emergency_contacts");
    $stmt->execute();
    $result = $stmt->fetch();
    $total_contacts = $result ? $result['total'] : 0;

    // Get today's new user registrations
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM users WHERE DATE(created_at) = CURDATE()");
    $stmt->execute();
    $result = $stmt->fetch();
    $today_registrations = $result ? $result['total'] : 0;

    // Get recent user registrations
    $stmt = $pdo->prepare("
        SELECT id, phone_number, name, created_at 
        FROM users 
        ORDER BY created_at DESC 
        LIMIT 10
    ");
    $stmt->execute();
    $recent_users = $stmt->fetchAll() ?: [];

    // Get users with most emergency contacts
    $stmt = $pdo->prepare("
        SELECT u.name, u.phone_number, COUNT(ec.id) as contact_count
        FROM users u
        LEFT JOIN emergency_contacts ec ON u.id = ec.user_id
        GROUP BY u.id, u.name, u.phone_number
        HAVING contact_count > 0
        ORDER BY contact_count DESC
        LIMIT 5
    ");
    $stmt->execute();
    $top_users_with_contacts = $stmt->fetchAll() ?: [];

    // Get recent emergency contacts added
    $stmt = $pdo->prepare("
        SELECT ec.contact_name, ec.contact_number, ec.created_at, u.name as user_name, u.phone_number as user_phone
        FROM emergency_contacts ec
        JOIN users u ON ec.user_id = u.id
        ORDER BY ec.created_at DESC
        LIMIT 10
    ");
    $stmt->execute();
    $recent_contacts = $stmt->fetchAll() ?: [];

} catch (Exception $e) {
    error_log("Dashboard error: " . $e->getMessage());
    $_SESSION['error'] = 'An error occurred while loading the dashboard: ' . $e->getMessage();
}

require_once __DIR__ . '/../../views/admin/index.view.php';
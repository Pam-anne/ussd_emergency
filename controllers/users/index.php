<?php
session_start();
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../db_connect.php';

require_once __DIR__ . '/../../views/partials/auth_check.php';

// Check if user is admin
if (!isset($_SESSION['admin_id']) || $_SESSION['admin_role'] !== 'admin') {
    $_SESSION['error'] = 'Access denied. Please login as admin.';
    header('Location: /admin');
    exit();
}

// Initialize variables
$users = [];
$total_users = 0;
$search = '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 20; // Users per page
$offset = ($page - 1) * $limit;

try {
    // Handle search
    if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
        $search = trim($_GET['search']);
    }

    // Build base query
    $whereClause = '';
    $params = [];
    
    if (!empty($search)) {
        $whereClause = "WHERE phone_number LIKE :search OR name LIKE :search";
        $params[':search'] = '%' . $search . '%';
    }

    // Get total count for pagination
    $countSql = "SELECT COUNT(*) as total FROM users $whereClause";
    $countStmt = $pdo->prepare($countSql);
    $countStmt->execute($params);
    $total_users = $countStmt->fetch()['total'];

    // Get users with their emergency contacts count
    $sql = "
        SELECT 
            u.id,
            u.phone_number,
            u.name,
            u.created_at,
            u.pin,
            COUNT(ec.id) as contact_count,
            MAX(ec.created_at) as last_contact_added
        FROM users u
        LEFT JOIN emergency_contacts ec ON u.id = ec.user_id
        $whereClause
        GROUP BY u.id, u.phone_number, u.name, u.created_at, u.pin
        ORDER BY u.created_at DESC
        LIMIT :limit OFFSET :offset
    ";
    
    $stmt = $pdo->prepare($sql);
    
    // Bind search parameters if they exist
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    
    // Bind pagination parameters
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    
    $stmt->execute();
    $users = $stmt->fetchAll();

    // Calculate pagination
    $total_pages = ceil($total_users / $limit);

} catch (Exception $e) {
    error_log("Users page error: " . $e->getMessage());
    $_SESSION['error'] = 'An error occurred while loading users.';
    $users = [];
}

require_once __DIR__ . '/../../views/users/index.view.php';
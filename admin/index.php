<?php
session_start();
require_once '../config.php';
require_once '../db_connect.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit();
}

// Get database connection
$conn = $pdo;

// Get basic statistics
$stmt = $conn->query("SELECT COUNT(*) as total_users FROM users");
$totalUsers = $stmt->fetch(PDO::FETCH_ASSOC)['total_users'];

$stmt = $conn->query("SELECT COUNT(*) as total_contacts FROM emergency_contacts");
$totalContacts = $stmt->fetch(PDO::FETCH_ASSOC)['total_contacts'];

$stmt = $conn->query("SELECT COUNT(*) as total_sessions FROM session_data");
$totalSessions = $stmt->fetch(PDO::FETCH_ASSOC)['total_sessions'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Emergency USSD Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .sidebar {
            min-height: 100vh;
            background: #343a40;
            color: white;
        }
        .nav-link {
            color: rgba(255,255,255,.8);
        }
        .nav-link:hover {
            color: white;
        }
        .stat-card {
            border-radius: 10px;
            transition: transform 0.2s;
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 px-0 sidebar">
                <div class="p-3">
                    <h4>Emergency USSD</h4>
                    <hr>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link active" href="index.php">
                                <i class="fas fa-home"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="users.php">
                                <i class="fas fa-users"></i> Users
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="contacts.php">
                                <i class="fas fa-address-book"></i> Emergency Contacts
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="sessions.php">
                                <i class="fas fa-history"></i> Sessions
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="logs.php">
                                <i class="fas fa-list"></i> System Logs
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="settings.php">
                                <i class="fas fa-cog"></i> Settings
                            </a>
                        </li>
                        <li class="nav-item mt-3">
                            <a class="nav-link text-danger" href="logout.php">
                                <i class="fas fa-sign-out-alt"></i> Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 p-4">
                <h2 class="mb-4">Dashboard</h2>
                
                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card stat-card bg-primary text-white">
                            <div class="card-body">
                                <h5 class="card-title">Total Users</h5>
                                <h2 class="card-text"><?php echo $totalUsers; ?></h2>
                                <p class="card-text"><small>Registered users in the system</small></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card stat-card bg-success text-white">
                            <div class="card-body">
                                <h5 class="card-title">Emergency Contacts</h5>
                                <h2 class="card-text"><?php echo $totalContacts; ?></h2>
                                <p class="card-text"><small>Total emergency contacts</small></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card stat-card bg-info text-white">
                            <div class="card-body">
                                <h5 class="card-title">Active Sessions</h5>
                                <h2 class="card-text"><?php echo $totalSessions; ?></h2>
                                <p class="card-text"><small>Current active sessions</small></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Recent Activity</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Time</th>
                                        <th>User</th>
                                        <th>Action</th>
                                        <th>Details</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    // Get recent activity from session_data
                                    $stmt = $conn->query("
                                        SELECT sd.*, u.name as user_name 
                                        FROM session_data sd 
                                        LEFT JOIN users u ON sd.data_value = u.id 
                                        WHERE sd.data_key = 'logged_in_user_id' 
                                        ORDER BY sd.id DESC LIMIT 5
                                    ");
                                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                        echo "<tr>";
                                        echo "<td>" . date('Y-m-d H:i:s', strtotime($row['created_at'])) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['user_name']) . "</td>";
                                        echo "<td>Login</td>";
                                        echo "<td>User logged in via USSD</td>";
                                        echo "</tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 
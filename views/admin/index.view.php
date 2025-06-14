<?php
require_once __DIR__ . '/../partials/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php require_once __DIR__ . '/../partials/sidebar.php'; ?>

        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">USSD Emergency System Dashboard</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <div class="col-md-3 mb-3">
                        <button type="button" class="btn btn-warning btn-block" onclick="location.reload()">
                            <i class="fas fa-sync"></i> Refresh
                        </button>
                    </div>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-primary shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                        Total USSD Users
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        <?php echo number_format($total_users ?? 0); ?>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-users fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-success shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                        Emergency Contacts
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        <?php echo number_format($total_contacts ?? 0); ?>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-address-book fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-info shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                        Today's Registrations
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        <?php echo number_format($today_registrations ?? 0); ?>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-user-plus fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-warning shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                        Avg Contacts/User
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        <?php
                                        $avg = ($total_users > 0) ? round($total_contacts / $total_users, 1) : 0;
                                        echo $avg;
                                        ?>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Registrations Table -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3 d-flex justify-content-between align-items-center">
                            <h6 class="m-0 font-weight-bold text-primary">Recent User Registrations</h6>
                            <a href="/admin/users" class="btn btn-sm btn-primary">
                                View All Users
                            </a>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Phone Number</th>
                                            <th>Name</th>
                                            <th>Registration Date</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (isset($recent_users) && !empty($recent_users)): ?>
                                            <?php foreach ($recent_users as $user): ?>
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="avatar-sm bg-primary rounded-circle d-flex align-items-center justify-content-center me-2">
                                                                <i class="fas fa-phone text-white"></i>
                                                            </div>
                                                            <?php echo htmlspecialchars($user['phone_number'] ?? 'Unknown'); ?>
                                                        </div>
                                                    </td>
                                                    <td><?php echo htmlspecialchars($user['name'] ?? 'Not Set'); ?></td>
                                                    <td>
                                                        <?php
                                                        $time = $user['created_at'] ?? '';
                                                        if ($time) {
                                                            echo date('M d, Y H:i', strtotime($time));
                                                        } else {
                                                            echo 'Unknown';
                                                        }
                                                        ?>
                                                    </td>
                                                    <td>
                                                        <a href="/admin/users/view?id=<?php echo $user['id']; ?>" class="btn btn-sm btn-info">
                                                            <i class="fas fa-eye"></i> View
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="4" class="text-center py-4">
                                                    <div class="text-muted">
                                                        <i class="fas fa-users fa-2x mb-2"></i>
                                                        <p>No users registered yet</p>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Emergency Contacts -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3 d-flex justify-content-between align-items-center">
                            <h6 class="m-0 font-weight-bold text-success">Recent Emergency Contacts Added</h6>
                            <a href="/admin/contacts" class="btn btn-sm btn-success">
                                View All Contacts
                            </a>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Contact Name</th>
                                            <th>Contact Number</th>
                                            <th>Added By</th>
                                            <th>Date Added</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (isset($recent_contacts) && !empty($recent_contacts)): ?>
                                            <?php foreach ($recent_contacts as $contact): ?>
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="avatar-sm bg-success rounded-circle d-flex align-items-center justify-content-center me-2">
                                                                <i class="fas fa-user text-white"></i>
                                                            </div>
                                                            <?php echo htmlspecialchars($contact['contact_name'] ?? 'Unknown'); ?>
                                                        </div>
                                                    </td>
                                                    <td><?php echo htmlspecialchars($contact['contact_number'] ?? 'Unknown'); ?></td>
                                                    <td>
                                                        <small class="text-muted">
                                                            <?php echo htmlspecialchars($contact['user_name'] ?? 'Unknown'); ?><br>
                                                            <i class="fas fa-phone fa-xs"></i> <?php echo htmlspecialchars($contact['user_phone'] ?? ''); ?>
                                                        </small>
                                                    </td>
                                                    <td>
                                                        <?php
                                                        $time = $contact['created_at'] ?? '';
                                                        if ($time) {
                                                            echo date('M d, Y H:i', strtotime($time));
                                                        } else {
                                                            echo 'Unknown';
                                                        }
                                                        ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="4" class="text-center py-4">
                                                    <div class="text-muted">
                                                        <i class="fas fa-address-book fa-2x mb-2"></i>
                                                        <p>No emergency contacts added yet</p>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
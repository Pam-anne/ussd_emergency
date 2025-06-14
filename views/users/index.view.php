<?php
require_once __DIR__ . '/../partials/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php require_once __DIR__ . '/../partials/sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">USSD Users Management</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <div class="btn-group me-2">
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="location.reload()">
                            <i class="fas fa-refresh"></i> Refresh
                        </button>
                        <a href="/admin/users/add" class="btn btn-sm btn-primary">
                            <i class="fas fa-user-plus"></i> Add User
                        </a>
                    </div>
                </div>
            </div>

            <!-- Statistics Summary -->
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-md-4">
                                    <h4 class="text-primary"><?php echo number_format($total_users); ?></h4>
                                    <p class="text-muted mb-0">Total Users</p>
                                </div>
                                <div class="col-md-4">
                                    <h4 class="text-success">
                                        <?php 
                                        $users_with_contacts = 0;
                                        if (!empty($users)) {
                                            foreach ($users as $user) {
                                                if ($user['contact_count'] > 0) $users_with_contacts++;
                                            }
                                        }
                                        echo number_format($users_with_contacts); 
                                        ?>
                                    </h4>
                                    <p class="text-muted mb-0">Users with Contacts</p>
                                </div>
                                <div class="col-md-4">
                                    <h4 class="text-info">
                                        <?php 
                                        $today_registrations = 0;
                                        if (!empty($users)) {
                                            foreach ($users as $user) {
                                                if (date('Y-m-d', strtotime($user['created_at'])) === date('Y-m-d')) {
                                                    $today_registrations++;
                                                }
                                            }
                                        }
                                        echo number_format($today_registrations); 
                                        ?>
                                    </h4>
                                    <p class="text-muted mb-0">Registered Today</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Search and Filter -->
            <div class="row mb-3">
                <div class="col-md-6">
                    <form method="GET" class="d-flex">
                        <input type="text" class="form-control me-2" name="search" 
                               placeholder="Search by phone or name..." 
                               value="<?php echo htmlspecialchars($search); ?>">
                        <button type="submit" class="btn btn-outline-primary">
                            <i class="fas fa-search"></i>
                        </button>
                        <?php if (!empty($search)): ?>
                            <a href="/admin/users" class="btn btn-outline-secondary ms-2">
                                <i class="fas fa-times"></i>
                            </a>
                        <?php endif; ?>
                    </form>
                </div>
                <div class="col-md-6 text-end">
                    <?php if (!empty($search)): ?>
                        <small class="text-muted">
                            Showing results for "<?php echo htmlspecialchars($search); ?>"
                        </small>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Users Table -->
            <div class="row">
                <div class="col-12">
                    <div class="card shadow">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">
                                USSD Users 
                                <span class="badge bg-primary ms-2"><?php echo number_format($total_users); ?></span>
                            </h6>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($users)): ?>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th width="5%">#</th>
                                                <th width="20%">Phone Number</th>
                                                <th width="20%">Name</th>
                                                <th width="15%">Emergency Contacts</th>
                                                <th width="15%">Registration Date</th>
                                                <th width="15%">Last Contact Added</th>
                                                <th width="10%">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($users as $index => $user): ?>
                                                <tr>
                                                    <td><?php echo $offset + $index + 1; ?></td>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="avatar-sm bg-primary rounded-circle d-flex align-items-center justify-content-center me-2">
                                                                <i class="fas fa-phone text-white fa-xs"></i>
                                                            </div>
                                                            <strong><?php echo htmlspecialchars($user['phone_number']); ?></strong>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <?php if (!empty($user['name'])): ?>
                                                            <?php echo htmlspecialchars($user['name']); ?>
                                                        <?php else: ?>
                                                            <span class="text-muted"><em>Not set</em></span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-<?php echo $user['contact_count'] > 0 ? 'success' : 'secondary'; ?>">
                                                            <?php echo $user['contact_count']; ?> contact<?php echo $user['contact_count'] != 1 ? 's' : ''; ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <small>
                                                            <?php echo date('M d, Y', strtotime($user['created_at'])); ?><br>
                                                            <span class="text-muted"><?php echo date('H:i A', strtotime($user['created_at'])); ?></span>
                                                        </small>
                                                    </td>
                                                    <td>
                                                        <?php if ($user['last_contact_added']): ?>
                                                            <small>
                                                                <?php echo date('M d, Y', strtotime($user['last_contact_added'])); ?>
                                                            </small>
                                                        <?php else: ?>
                                                            <span class="text-muted"><small>Never</small></span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <div class="btn-group btn-group-sm" role="group">
                                                            <a href="/admin/users/view?id=<?php echo $user['id']; ?>" 
                                                               class="btn btn-outline-info" title="View Details">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                            <a href="/admin/users/edit?id=<?php echo $user['id']; ?>" 
                                                               class="btn btn-outline-warning" title="Edit User">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                            <button type="button" class="btn btn-outline-danger" 
                                                                    title="Delete User"
                                                                    onclick="confirmDelete(<?php echo $user['id']; ?>, '<?php echo htmlspecialchars($user['phone_number'], ENT_QUOTES); ?>')">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Pagination -->
                                <?php if ($total_pages > 1): ?>
                                    <nav aria-label="Users pagination" class="mt-3">
                                        <ul class="pagination justify-content-center">
                                            <?php if ($page > 1): ?>
                                                <li class="page-item">
                                                    <a class="page-link" href="?page=<?php echo $page - 1; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>">
                                                        <i class="fas fa-chevron-left"></i> Previous
                                                    </a>
                                                </li>
                                            <?php endif; ?>

                                            <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                                                <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                                    <a class="page-link" href="?page=<?php echo $i; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>">
                                                        <?php echo $i; ?>
                                                    </a>
                                                </li>
                                            <?php endfor; ?>

                                            <?php if ($page < $total_pages): ?>
                                                <li class="page-item">
                                                    <a class="page-link" href="?page=<?php echo $page + 1; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>">
                                                        Next <i class="fas fa-chevron-right"></i>
                                                    </a>
                                                </li>
                                            <?php endif; ?>
                                        </ul>
                                    </nav>

                                    <div class="text-center text-muted mt-2">
                                        <small>
                                            Showing <?php echo $offset + 1; ?> to <?php echo min($offset + $limit, $total_users); ?> 
                                            of <?php echo number_format($total_users); ?> users
                                        </small>
                                    </div>
                                <?php endif; ?>

                            <?php else: ?>
                                <div class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="fas fa-users fa-3x mb-3"></i>
                                        <h5>No Users Found</h5>
                                        <?php if (!empty($search)): ?>
                                            <p>No users match your search criteria.</p>
                                            <a href="/admin/users" class="btn btn-primary">View All Users</a>
                                        <?php else: ?>
                                            <p>No USSD users have registered yet.</p>
                                            <a href="/admin/users/add" class="btn btn-primary">Add First User</a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete user <strong id="deleteUserPhone"></strong>?</p>
                <p class="text-danger"><small>This will also delete all their emergency contacts!</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <a href="#" id="confirmDeleteBtn" class="btn btn-danger">Delete User</a>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete(userId, userPhone) {
    document.getElementById('deleteUserPhone').textContent = userPhone;
    document.getElementById('confirmDeleteBtn').href = '/admin/users/delete?id=' + userId;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}
</script>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
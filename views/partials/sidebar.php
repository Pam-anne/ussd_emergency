<?php
// Get current page from URL for active state
$current_uri = $_SERVER['REQUEST_URI'];
$current_page = trim(parse_url($current_uri, PHP_URL_PATH), '/');
?>

<div class="col-md-3 col-lg-2 px-0 sidebar">
    <div class="p-3">
        <h4>Emergency USSD</h4>
        <hr>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link <?php echo ($current_page == 'admin/dashboard' || $current_page == 'admin') ? 'active' : ''; ?>" 
                   href="/admin/dashboard">
                    <i class="fas fa-home"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo strpos($current_page, 'admin/users') === 0 ? 'active' : ''; ?>" 
                   href="/admin/users">
                    <i class="fas fa-users"></i> Users
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo strpos($current_page, 'admin/contacts') === 0 ? 'active' : ''; ?>" 
                   href="/admin/contacts">
                    <i class="fas fa-address-book"></i> Emergency Contacts
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo strpos($current_page, 'admin/sessions') === 0 ? 'active' : ''; ?>" 
                   href="/admin/sessions">
                    <i class="fas fa-history"></i> Sessions
                </a>
            </li>
            <li class="nav-item mt-3">
                <a class="nav-link text-danger" href="/admin/logout">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </li>
        </ul>
    </div>
</div>
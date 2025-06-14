<?php


$routes = [
    // Admin routes
    'admin' => 'controllers/admin/login.php',
    'admin/dashboard' => 'controllers/admin/index.php',
    'admin/logout' => 'controllers/admin/logout.php',
    
    // User routes
    'admin/users' => 'controllers/users/index.php',
    'admin/users/add' => 'controllers/users/add.php',
    'admin/users/edit' => 'controllers/users/edit.php',
    'admin/users/view' => 'controllers/users/view.php',
    'admin/users/delete' => 'controllers/users/delete.php',

    // Contact routes
    'admin/contacts' => 'controllers/contacts/index.php',
    'admin/contacts/view' => 'controllers/contacts/view.php',
    'admin/contacts/delete' => 'controllers/contacts/delete.php',
    
    // Session routes
    'admin/sessions' => 'controllers/sessions/index.php',
    'admin/sessions/clear' => 'controllers/sessions/clear.php',
    
    // API
    'api/ussd' => 'ussd.php',
    
    // Default route
    '' => 'controllers/admin/index.php'
];

return $routes;

<?php

// Africa's Talking API credentials
define('AT_API_KEY', 'YOUR_API_KEY');
define('AT_USERNAME', 'sandbox');

// Database configuration
$host = 'localhost';
$dbname = 'ussd_emergency';
$username = 'ussd_user';
$password = 'UssdEmergency123!'; // or 'ussd123' if you used the simpler password

// Session timeout in seconds (5 minutes)
define('SESSION_TIMEOUT', 300);


define('ADMIN_USERNAME', 'test1');
define('ADMIN_PASSWORD_HASH', password_hash('test@123', PASSWORD_DEFAULT));
?>
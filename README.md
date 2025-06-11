# USSD Emergency Contacts Application

A comprehensive emergency contacts management system built with PHP and Africa's Talking USSD platform. The system allows users to register, manage emergency contacts, and includes a secure admin panel for system management.

## Features

### User Features
- User registration with PIN protection
- Add and manage emergency contacts
- View emergency contacts
- Emergency access for trusted contacts
- Distress SMS and call functionality
- Secure PIN-based authentication

### Admin Features
- Secure admin dashboard
- User management (add, edit, delete users)
- Emergency contacts management
- System monitoring and statistics
- Session tracking
- PIN reset functionality
- Activity logs

## Prerequisites

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Africa's Talking account
- Web server (Apache/Nginx)
- Composer (for dependency management)

## Setup Instructions

1. **Database Setup**
   - Create a MySQL database
   - Import the `database.sql` file to create the required tables
   - Ensure the database user has appropriate permissions

2. **Configuration**
   - Copy `config.example.php` to `config.php`
   - Update the following in `config.php`:
     - Africa's Talking API credentials
     - Database credentials
     - Admin panel credentials
     - Session timeout settings

3. **Africa's Talking Setup**
   - Log in to your Africa's Talking account
   - Create a new USSD application
   - Set the callback URL to your server's URL (e.g., `https://your-domain.com/index.php`)
   - Note down the service code provided by Africa's Talking

4. **File Upload**
   - Upload all PHP files to your web server
   - Ensure proper file permissions:
     - Web server needs write access to session directory
     - Admin panel files should be properly secured
   - Set up proper URL rewriting if needed

5. **Admin Panel Setup**
   - Access the admin panel at `/admin`
   - Default credentials are set in `config.php`
   - Change default admin credentials immediately after first login

6. **Testing**
   - Test the USSD flow:
     - Dial the USSD code provided by Africa's Talking
     - Test the registration flow
     - Test adding and viewing emergency contacts
   - Test the admin panel:
     - Login functionality
     - User management
     - Emergency contacts management
     - System monitoring

## USSD Flow

1. Welcome Screen
   - 1. Login to My Account
   - 2. Register New Account
   - 3. Emergency Access

2. Registration Flow
   - Enter full name
   - Create 4-digit PIN
   - Add first emergency contact

3. Main Menu (After Login)
   - 1. HELP (Show Contacts)
   - 2. Manage My Contacts

4. Emergency Access
   - Enter registered phone number
   - Enter PIN
   - Access emergency contacts

## Admin Panel

The admin panel provides comprehensive system management:

1. **Dashboard**
   - System statistics
   - Recent activity
   - Quick actions

2. **User Management**
   - View all users
   - Add new users
   - Edit user details
   - Reset user PINs
   - Delete users

3. **Emergency Contacts**
   - View all emergency contacts
   - Manage contact relationships
   - Monitor contact usage

4. **System Monitoring**
   - Active sessions
   - System logs
   - Error tracking

## Security Considerations

- All database queries use prepared statements
- Phone numbers are validated before storage
- PINs are securely stored
- Session timeout implementation
- Admin panel access control
- Input validation and sanitization
- XSS protection
- CSRF protection for admin actions

## File Structure

```
├── admin/                 # Admin panel files
│   ├── index.php         # Admin dashboard
│   ├── login.php         # Admin login
│   ├── users.php         # User management
│   └── ...              # Other admin files
├── config.php            # Configuration file
├── db_connect.php        # Database connection
├── index.php            # Main USSD handler
└── README.md            # This file
```

## Support

For any issues or questions:
1. Check the Africa's Talking documentation
2. Review the system logs
3. Contact your system administrator
4. Submit an issue on the project repository

## License

This project is licensed under the MIT License - see the LICENSE file for details. 
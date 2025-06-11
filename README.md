# USSD Emergency Contacts Application

This is a USSD application built with PHP and Africa's Talking that allows users to register and manage emergency contacts.

## Features

- User registration
- Add emergency contacts
- View emergency contacts
- Secure database storage

## Prerequisites

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Africa's Talking account
- Web server (Apache/Nginx)

## Setup Instructions

1. **Database Setup**
   - Create a MySQL database
   - Import the `database.sql` file to create the required tables

2. **Configuration**
   - Copy `config.php` and update the following:
     - Africa's Talking API credentials
     - Database credentials
     - Session timeout settings

3. **Africa's Talking Setup**
   - Log in to your Africa's Talking account
   - Create a new USSD application
   - Set the callback URL to your server's URL (e.g., `https://your-domain.com/ussd.php`)
   - Note down the service code provided by Africa's Talking

4. **File Upload**
   - Upload all PHP files to your web server
   - Ensure the web server has write permissions for session handling

5. **Testing**
   - Dial the USSD code provided by Africa's Talking
   - Test the registration flow
   - Test adding and viewing emergency contacts

## USSD Flow

1. Welcome Screen
   - 1. Register
   - 2. Manage Emergency Contacts

2. Registration Flow
   - Enter full name
   - Confirmation message

3. Emergency Contacts Management
   - 1. Add New Contact
   - 2. View My Contacts

## Security Considerations

- All database queries use prepared statements to prevent SQL injection
- Phone numbers are validated before storage
- Session timeout is implemented to prevent session hijacking

## Support

For any issues or questions, please contact your system administrator or refer to the Africa's Talking documentation. 
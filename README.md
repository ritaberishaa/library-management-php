# Library Management System

A comprehensive PHP-based library management system with role-based access control, book inventory management, borrowing system, and audit logging.

## Features

### **Authentication & Authorization**
- Role-based access control (Student, Operator, Super Admin)
- Secure login/logout system
- Password management
- Session management

### **Book Management**
- Add, edit, delete books
- Book inventory tracking
- ISBN management
- Author and publication details
- Copy management (total/available copies)

### **User Management**
- User registration and management
- Role assignment (Student, Operator, Super Admin)
- User profile management
- Account activation/deactivation

### **Borrowing System**
- Book borrowing and returning
- Due date tracking
- Overdue book management
- Penalty calculation
- Borrowing history

### **Notification System**
- Real-time notifications
- Overdue book alerts
- Low stock warnings
- System notifications

### **Audit Logging**
- Complete activity tracking
- User action logging
- IP address tracking
- Export functionality
- Search and filter capabilities

### **System Settings**
- Configurable borrowing limits
- Penalty rates
- System preferences
- Audit log management

## Installation

### Prerequisites
- PHP 7.4 or higher
- SQLite support
- Web server (Apache/Nginx)

### Setup Instructions

1. **Clone the repository**
   ```bash
   git clone https://github.com/yourusername/library-management-php.git
   cd library-management-php
   ```

2. **Install dependencies**
   ```bash
   composer install
   ```

3. **Initialize the database**
   ```bash
   php scripts/migrate.php
   php scripts/seed.php
   ```

4. **Configure environment**
   - Copy `.env.example` to `.env` (if available)
   - Update database settings if needed

5. **Set up web server**
   - Point document root to `public/` directory
   - Ensure PHP has write permissions for database

## Default Login Credentials

- **Super Admin**: `admin` / `admin123`
- **Default URL**: `http://localhost/library-management-php/public/`

## Project Structure

```
library-management-php/
├── public/                 # Web accessible files
│   └── index.php          # Application entry point
├── src/
│   ├── Controllers/       # MVC Controllers
│   ├── Models/           # Data models
│   ├── Views/            # Template files
│   └── Core/             # Core framework files
├── sql/                  # Database schema
├── scripts/              # Setup and migration scripts
├── vendor/               # Composer dependencies
└── README.md
```

## User Roles

### **Student**
- Browse available books
- Borrow books (up to limit)
- View borrowing history
- Pay penalties
- Receive notifications

### **Operator**
- Manage books (add, edit, delete)
- Process book borrowings/returns
- Manage student accounts
- View system notifications
- Generate reports

### **Super Admin**
- Full system access
- User management
- System settings
- Audit log management
- All operator functions

## Technology Stack

- **Backend**: PHP 7.4+
- **Database**: SQLite
- **Frontend**: HTML5, CSS3, Bootstrap 5, JavaScript
- **Dependencies**: Composer, PHP DotEnv
- **Architecture**: MVC Pattern

## Security Features

- Password hashing (PHP password_hash)
- CSRF protection
- SQL injection prevention (PDO prepared statements)
- XSS protection (htmlspecialchars)
- Session management
- Role-based access control

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Test thoroughly
5. Submit a pull request

## License

This project is open source and available under the [MIT License](LICENSE).

## Support

For support and questions, please open an issue on GitHub.

---

**Note**: This is a development project. For production use, ensure proper security measures, database optimization, and environment configuration.

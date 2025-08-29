## Online Management System

## Project Overview

The K.N. Raam Hardware Online Management System is a comprehensive web-based solution designed to transform the traditional hardware store operations into a modern, efficient online platform. This system enables customers to browse products, place orders, and communicate with the store, while providing administrators with powerful tools to manage inventory, orders, and customer interactions.

## 🎯 Project Objectives

- **Digital Transformation**: Convert manual paper-based operations to an automated online system
- **Customer Experience**: Provide 24/7 online access to products and services
- **Operational Efficiency**: Streamline inventory management, order processing, and customer communication
- **Business Growth**: Enable expansion through online presence and improved customer service

## ✨ Key Features Implemented

### 🔐 User Authentication & Security
- **Secure Login System**: Password hashing with Argon2id
- **CSRF Protection**: Cross-Site Request Forgery prevention
- **Rate Limiting**: Protection against brute force attacks
- **Session Management**: Secure session handling with timeout
- **Input Validation**: Comprehensive sanitization and validation
- **File Upload Security**: Secure image upload with validation

### 👥 User Management
- **Customer Registration**: Secure account creation with validation
- **User Profiles**: Personal information management
- **Admin Panel**: Comprehensive administrative interface
- **Role-Based Access**: Separate customer and admin functionalities

### 🔍 Search & Discovery
- **Advanced Search**: Product search by name, description, and category
- **Filtering Options**: Price range, category, and availability filters
- **Sorting Options**: Sort by name, price (ascending/descending)
- **Real-time Results**: Dynamic search with instant feedback

### 📦 Product Management
- **Product Catalog**: Comprehensive product listings with images
- **Category Management**: Organized product categorization
- **Stock Management**: Real-time inventory tracking
- **Product Details**: Detailed product information pages
- **Related Products**: Smart product recommendations

### 🛒 Shopping Cart & Orders
- **Shopping Cart**: Add, update, and remove items
- **Order Processing**: Complete checkout workflow
- **Order History**: Customer order tracking
- **Order Status Updates**: Real-time order status notifications

### 💬 Communication System
- **Contact Forms**: Customer inquiry submission
- **Message Management**: Admin-customer communication
- **Email Notifications**: Automated email alerts
- **Message History**: Complete conversation tracking

### 📧 Email Notification System
- **Order Confirmations**: Automatic order confirmation emails
- **Status Updates**: Order status change notifications
- **Message Notifications**: New message alerts for admins
- **Reply Notifications**: Customer reply confirmations

### 🔗 Social Sharing
- **Facebook Integration**: Share products on Facebook
- **Twitter Integration**: Share products on Twitter
- **WhatsApp Integration**: Share products via WhatsApp
- **Link Sharing**: Copy product links to clipboard

### ♿ Accessibility Features
- **Keyboard Navigation**: Full keyboard accessibility
- **Screen Reader Support**: ARIA labels and semantic HTML
- **High Contrast Support**: Enhanced visibility options
- **Reduced Motion**: Respect user motion preferences
- **Focus Management**: Proper focus indicators

### 📱 Responsive Design
- **Mobile-First**: Optimized for mobile devices
- **Tablet Support**: Responsive tablet layouts
- **Desktop Optimization**: Full desktop experience
- **Cross-Browser**: Compatible with all modern browsers

### 🛡️ Security Enhancements
- **SQL Injection Prevention**: Prepared statements
- **XSS Protection**: Input sanitization and output encoding
- **Secure Headers**: Security-focused HTTP headers
- **File Upload Security**: Secure image handling
- **Session Security**: Secure session management

## 🛠️ Technical Stack

### Frontend
- **HTML5**: Semantic markup
- **CSS3**: Modern styling with Bootstrap 5
- **JavaScript**: Enhanced user experience
- **Bootstrap 5**: Responsive framework
- **Bootstrap Icons**: Icon library

### Backend
- **PHP 8.0+**: Server-side scripting
- **MySQL**: Database management
- **Pure PHP**: No external frameworks

### Security
- **Argon2id**: Password hashing
- **CSRF Tokens**: Cross-site request forgery protection
- **Input Validation**: Comprehensive sanitization
- **Session Security**: Secure session handling

## 📋 Requirements Met

### ✅ Core Requirements
- [x] User Authentication System
- [x] User Profile Management
- [x] Advanced Search Functionality
- [x] Content Management System
- [x] Messaging & Communication
- [x] Email Notifications
- [x] Social Sharing Integration
- [x] Payment Processing Support
- [x] Accessibility Features
- [x] Security Measures

### ✅ Business Requirements
- [x] Online Product Catalog
- [x] Inventory Management
- [x] Order Processing
- [x] Customer Communication
- [x] Admin Dashboard
- [x] Sales Analytics
- [x] Customer Management
- [x] Product Management

## 🚀 Installation & Setup

### Prerequisites
- PHP 8.0 or higher
- MySQL 5.7 or higher
- Web server (Apache/Nginx)
- WAMP/XAMPP/LAMP stack

### Installation Steps

1. **Clone/Download the Project**
   ```bash
   git clone [repository-url]
   cd hardware
   ```

2. **Database Setup**
   - Create a MySQL database named `kn_raam_hardware`
   - Import the database schema (if provided)
   - Update database credentials in `includes/db.php`

3. **Configuration**
   - Update database connection settings
   - Configure email settings for notifications
   - Set up file upload permissions

4. **File Permissions**
   ```bash
   chmod 755 uploads/
   chmod 644 includes/db.php
   ```

5. **Admin Account Creation**
   - Access `admin/create_admin.php` to create the first admin account
   - Remove or secure this file after admin creation

## 📁 Project Structure

```
hardware/
├── admin/                 # Admin panel files
│   ├── dashboard.php     # Admin dashboard
│   ├── products.php      # Product management
│   ├── orders.php        # Order management
│   ├── customers.php     # Customer management
│   ├── messages.php      # Message management
│   └── ...
├── assets/               # Static assets
│   ├── css/             # Stylesheets
│   ├── js/              # JavaScript files
│   └── images/          # Images
├── includes/             # PHP includes
│   ├── db.php           # Database connection
│   ├── security.php     # Security functions
│   ├── email_notifications.php # Email system
│   └── ...
├── uploads/              # File uploads
├── index.php            # Homepage
├── products.php         # Product catalog
├── search.php           # Search functionality
├── cart.php             # Shopping cart
├── checkout.php         # Checkout process
├── contact.php          # Contact page
└── ...
```

## 🔧 Configuration

### Database Configuration
Edit `includes/db.php`:
```php
$host = 'localhost';
$username = 'your_username';
$password = 'your_password';
$database = 'kn_raam_hardware';
```

### Email Configuration
The system uses PHP's built-in `mail()` function. Configure your server's mail settings or update the email system to use SMTP.

### Security Settings
- Update CSRF token settings in `includes/security.php`
- Configure session timeout settings
- Set up file upload restrictions

## 👨‍💼 Admin Features

### Dashboard
- Real-time statistics
- Recent orders overview
- Pending messages
- Quick actions

### Product Management
- Add/Edit/Delete products
- Category management
- Image upload
- Stock management

### Order Management
- View all orders
- Update order status
- Order details
- Customer information

### Customer Management
- Customer list
- Customer details
- Order history
- Communication history

### Message Management
- View customer messages
- Reply to messages
- Message history
- Email notifications

## 👤 Customer Features

### Product Browsing
- Product catalog
- Category filtering
- Search functionality
- Product details

### Shopping Cart
- Add to cart
- Update quantities
- Remove items
- Cart summary

### Order Management
- Place orders
- Order history
- Order status tracking
- Order cancellation

### Communication
- Contact form
- Message history
- Reply tracking
- Email notifications

## 🔒 Security Features

### Authentication
- Secure password hashing
- Session management
- Login attempt limiting
- Account lockout protection

### Data Protection
- Input sanitization
- SQL injection prevention
- XSS protection
- CSRF protection

### File Security
- Secure file uploads
- File type validation
- Size restrictions
- Secure file naming

## 📧 Email Notifications

### Customer Notifications
- Order confirmation emails
- Order status updates
- Message reply notifications
- Account creation confirmations

### Admin Notifications
- New order notifications
- New message alerts
- Low stock alerts
- System status updates

## ♿ Accessibility Features

### Keyboard Navigation
- Full keyboard accessibility
- Focus indicators
- Skip navigation links
- Logical tab order

### Screen Reader Support
- ARIA labels
- Semantic HTML
- Alt text for images
- Descriptive link text

### Visual Accessibility
- High contrast support
- Font size options
- Color contrast compliance
- Reduced motion support

## 📱 Responsive Design

### Mobile Optimization
- Touch-friendly interfaces
- Mobile-optimized layouts
- Responsive images
- Mobile navigation

### Cross-Device Compatibility
- Desktop optimization
- Tablet layouts
- Mobile-first design
- Progressive enhancement

## 🚀 Performance Optimization

### Frontend Optimization
- Minified CSS/JS
- Optimized images
- Lazy loading
- Caching strategies

### Backend Optimization
- Database indexing
- Query optimization
- Session management
- File caching

## 🔧 Maintenance

### Regular Tasks
- Database backups
- Log file management
- Security updates
- Performance monitoring

### Monitoring
- Error logging
- Security event logging
- Performance metrics
- User activity tracking

## 📞 Support

For technical support or questions about the system:
- Email: support@knraamhardware.com
- Phone: +94 77 123 4567
- Address: Kurukkalmadam, Batticaloa, Sri Lanka

## 📄 License

This project is developed for K.N. Raam Hardware Shop. All rights reserved.

## 🎉 Conclusion

The K.N. Raam Hardware Online Management System successfully transforms traditional hardware store operations into a modern, efficient, and secure online platform. With comprehensive features covering all aspects of e-commerce and business management, this system provides a solid foundation for business growth and improved customer service.

The system meets all specified requirements and includes additional enhancements for security, accessibility, and user experience, making it a complete solution for modern hardware store management.


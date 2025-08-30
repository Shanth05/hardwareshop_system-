#  Online Management System

A comprehensive online management system for K.N. Raam Hardware shop in Kurukkalmadam, Batticaloa. This system transforms a traditional hardware store into a modern, efficient online business platform.

## 🏗️ **Project Structure**

```
hardware/
├── admin/                          # Admin Panel
│   ├── ajax/                      # AJAX endpoints for admin
│   ├── dashboard.php              # Admin dashboard
│   ├── products.php               # Product management
│   ├── categories.php             # Category management
│   ├── brands.php                 # Brand management
│   ├── orders.php                 # Order management
│   ├── customers.php              # Customer management
│   ├── messages.php               # Message management
│   ├── notifications.php          # Notification management
│   ├── profile.php                # Admin profile
│   ├── manage_admins.php          # Admin user management
│   └── config.php                 # Admin configuration
├── orders/                        # Order processing
│   └── my_orders.php              # Customer order history
├── includes/                      # Shared components
│   ├── db.php                     # Database connection
│   ├── header.php                 # Site header
│   ├── footer.php                 # Site footer
│   ├── navbar.php                 # Navigation bar
│   ├── security.php               # Security functions
│   └── email_notifications.php    # Email notification system
├── assets/                        # Static assets
│   ├── css/                       # Stylesheets
│   ├── js/                        # JavaScript files
│   └── images/                    # Images
├── uploads/                       # File uploads
├── utilities/                     # Utility scripts
│   └── cleanup_expired_tokens.php # Token cleanup utility
├── logs/                          # Log files (empty)
├── index.php                      # Homepage
├── shop.php                       # Product catalog
├── products.php                   # Product listing
├── product_details.php            # Product details
├── cart.php                       # Shopping cart
├── checkout.php                   # Checkout process
├── login.php                      # Customer login
├── register.php                   # Customer registration
├── profile.php                    # Customer profile
├── notifications.php              # Customer notifications
├── contact.php                    # Contact form
├── search.php                     # Product search
├── about.php                      # About page
└── README.md                      # This file
```

## 🚀 **Features**

### **Customer Features**
- ✅ User registration and authentication
- ✅ Product browsing and search
- ✅ Shopping cart functionality
- ✅ Secure checkout process
- ✅ Order tracking and history
- ✅ Profile management
- ✅ Real-time notifications
- ✅ Customer support messaging
- ✅ Password reset functionality

### **Admin Features**
- ✅ Comprehensive dashboard
- ✅ Product management (CRUD)
- ✅ Category and brand management
- ✅ Order management and status updates
- ✅ Customer management
- ✅ Message management
- ✅ Notification system
- ✅ Admin user management
- ✅ Analytics and reporting

### **Technical Features**
- ✅ Responsive design (mobile-friendly)
- ✅ Secure authentication system
- ✅ SQL injection protection
- ✅ XSS protection
- ✅ Email notifications
- ✅ Real-time AJAX updates
- ✅ Facebook-style notifications
- ✅ Payment processing ready
- ✅ Social media integration

## 🛠️ **Technology Stack**

- **Frontend**: HTML5, CSS3, JavaScript, Bootstrap 5
- **Backend**: PHP 7.4+
- **Database**: MySQL 5.7+
- **Server**: Apache/Nginx
- **Additional**: AJAX, jQuery, Bootstrap Icons

## 📋 **Installation**

1. **Clone/Download** the project to your web server
2. **Database Setup**:
   - Create MySQL database: `kn_raam_hardware`
   - Import database schema (if available)
3. **Configuration**:
   - Update database credentials in `includes/db.php`
   - Configure email settings in `includes/email_notifications.php`
4. **Permissions**:
   - Ensure `uploads/` directory is writable
   - Ensure `logs/` directory is writable
5. **Admin Setup**:
   - Access `/admin/create_admin.php` to create first admin
   - Remove `create_admin.php` after setup

## 🔧 **Configuration**

### **Database Configuration**
Edit `includes/db.php`:
```php
$conn = mysqli_connect("localhost", "username", "password", "kn_raam_hardware");
```

### **Email Configuration**
Edit `includes/email_notifications.php`:
```php
// Configure SMTP settings for email notifications
```

## 🚀 **Usage**

### **For Customers**
1. Register/Login at the main site
2. Browse products using search and filters
3. Add items to cart
4. Complete checkout process
5. Track orders and receive notifications

### **For Admins**
1. Login at `/admin/`
2. Manage products, categories, and brands
3. Process orders and update status
4. Respond to customer messages
5. Monitor system notifications

## 🔒 **Security Features**

- ✅ Prepared statements for SQL injection protection
- ✅ Input validation and sanitization
- ✅ Secure session management
- ✅ Password hashing
- ✅ CSRF protection
- ✅ XSS prevention

## 📱 **Mobile Responsive**

The system is fully responsive and works perfectly on:
- Desktop computers
- Tablets
- Mobile phones
- All modern browsers

## 🎯 **Business Benefits**

- **Operational Efficiency**: Automated order processing
- **Customer Experience**: 24/7 online shopping
- **Inventory Management**: Real-time stock tracking
- **Communication**: Direct customer-admin messaging
- **Growth**: Social sharing and marketing features
- **Analytics**: Business performance insights

## 📞 **Support**

For technical support or questions, contact the development team.

## 📄 **License**

This project is developed for K.N. Raam Hardware shop.

---

**Developed with ❤️ for K.N. Raam Hardware**


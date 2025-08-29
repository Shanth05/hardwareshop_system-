-- =====================================================
-- K.N. RAAM HARDWARE - CORRECTED DATABASE SCHEMA
-- Online Management System Database Structure (FIXED)
-- =====================================================

-- Create database if not exists
CREATE DATABASE IF NOT EXISTS kn_raam_hardware;
USE kn_raam_hardware;

-- =====================================================
-- CORE TABLES (Currently Implemented) - CORRECTED
-- =====================================================

-- Users table (customers and admins)
CREATE TABLE IF NOT EXISTS users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    gender ENUM('Male', 'Female', 'Other') NULL,
    contact_no VARCHAR(20) NULL,
    username VARCHAR(50) UNIQUE NOT NULL,
    mail VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    user_type ENUM('customer', 'admin') DEFAULT 'customer',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    is_active TINYINT(1) DEFAULT 1,
    INDEX idx_username (username),
    INDEX idx_email (mail),
    INDEX idx_user_type (user_type)
);

-- Brands table
CREATE TABLE IF NOT EXISTS brands (
    brand_id INT AUTO_INCREMENT PRIMARY KEY,
    brand_name VARCHAR(100) NOT NULL,
    description TEXT NULL,
    logo_image VARCHAR(255) NULL,
    website VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    is_active TINYINT(1) DEFAULT 1,
    INDEX idx_brand_name (brand_name)
);

-- Categories table
CREATE TABLE IF NOT EXISTS categories (
    category_id INT AUTO_INCREMENT PRIMARY KEY,
    category_name VARCHAR(100) NOT NULL,
    description TEXT NULL,
    category_image VARCHAR(255) NULL,
    parent_category_id INT NULL,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    is_active TINYINT(1) DEFAULT 1,
    FOREIGN KEY (parent_category_id) REFERENCES categories(category_id) ON DELETE SET NULL,
    INDEX idx_category_name (category_name),
    INDEX idx_parent_category (parent_category_id)
);

-- Products table
CREATE TABLE IF NOT EXISTS products (
    product_id INT AUTO_INCREMENT PRIMARY KEY,
    product_name VARCHAR(255) NOT NULL,
    brand_id INT NOT NULL,
    category_id INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    description TEXT NULL,
    specifications JSON NULL,
    status ENUM('Available', 'Unavailable', 'Out of Stock', 'Discontinued') DEFAULT 'Available',
    image VARCHAR(255) NULL,
    sku VARCHAR(100) UNIQUE NULL,
    weight DECIMAL(8,2) NULL,
    dimensions VARCHAR(100) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    is_featured TINYINT(1) DEFAULT 0,
    FOREIGN KEY (brand_id) REFERENCES brands(brand_id) ON DELETE RESTRICT,
    FOREIGN KEY (category_id) REFERENCES categories(category_id) ON DELETE RESTRICT,
    INDEX idx_product_name (product_name),
    INDEX idx_brand_id (brand_id),
    INDEX idx_category_id (category_id),
    INDEX idx_status (status),
    INDEX idx_price (price),
    INDEX idx_sku (sku)
);

-- Cart table
CREATE TABLE IF NOT EXISTS cart (
    cart_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    qty INT NOT NULL DEFAULT 1,
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE,
    UNIQUE KEY unique_cart_item (user_id, product_id),
    INDEX idx_user_id (user_id),
    INDEX idx_product_id (product_id)
);

-- Orders table (CORRECTED - using order_status instead of status)
CREATE TABLE IF NOT EXISTS orders (
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    order_number VARCHAR(50) UNIQUE NOT NULL,
    name VARCHAR(255) NOT NULL,
    mail VARCHAR(100) NOT NULL,
    contact_no VARCHAR(20) NOT NULL,
    address TEXT NOT NULL,
    city VARCHAR(100) NULL,
    postal_code VARCHAR(20) NULL,
    payment_method ENUM('Cash on Delivery', 'Online Payment', 'Bank Transfer') NOT NULL,
    total_price DECIMAL(10,2) NOT NULL,
    shipping_cost DECIMAL(8,2) DEFAULT 0.00,
    tax_amount DECIMAL(8,2) DEFAULT 0.00,
    discount_amount DECIMAL(8,2) DEFAULT 0.00,
    final_amount DECIMAL(10,2) NOT NULL,
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    order_status ENUM('Pending', 'Confirmed', 'Processing', 'Shipped', 'Delivered', 'Cancelled', 'Refunded', 'ordered', 'completed', 'cancelled') DEFAULT 'Pending',
    notes TEXT NULL,
    estimated_delivery DATE NULL,
    actual_delivery_date TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE RESTRICT,
    INDEX idx_user_id (user_id),
    INDEX idx_order_number (order_number),
    INDEX idx_order_date (order_date),
    INDEX idx_order_status (order_status)
);

-- Order items table
CREATE TABLE IF NOT EXISTS order_items (
    item_id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    product_name VARCHAR(255) NOT NULL,
    qty INT NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL,
    total_price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(order_id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE RESTRICT,
    INDEX idx_order_id (order_id),
    INDEX idx_product_id (product_id)
);

-- Notifications table
CREATE TABLE IF NOT EXISTS notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type ENUM('admin', 'customer') NOT NULL,
    action VARCHAR(50) NOT NULL,
    message TEXT NOT NULL,
    reference_id INT NULL,
    user_id INT NULL,
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    read_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    INDEX idx_type (type),
    INDEX idx_user_id (user_id),
    INDEX idx_is_read (is_read),
    INDEX idx_created_at (created_at)
);

-- Contact messages table (CORRECTED - added missing fields)
CREATE TABLE IF NOT EXISTS contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL,
    subject VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    status ENUM('Pending', 'Read', 'Replied', 'Closed') DEFAULT 'Pending',
    seen_by_user TINYINT(1) DEFAULT 0,
    priority ENUM('Low', 'Medium', 'High', 'Urgent') DEFAULT 'Medium',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    replied_at TIMESTAMP NULL,
    replied_by INT NULL,
    reply_message TEXT NULL,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE SET NULL,
    FOREIGN KEY (replied_by) REFERENCES users(user_id) ON DELETE SET NULL,
    INDEX idx_status (status),
    INDEX idx_priority (priority),
    INDEX idx_created_at (created_at),
    INDEX idx_user_id (user_id)
);

-- =====================================================
-- ENHANCEMENT TABLES (Recommended Additions)
-- =====================================================

-- Product reviews table
CREATE TABLE IF NOT EXISTS product_reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    user_id INT NOT NULL,
    rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
    review_text TEXT NULL,
    review_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_approved TINYINT(1) DEFAULT 0,
    helpful_votes INT DEFAULT 0,
    not_helpful_votes INT DEFAULT 0,
    FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_product_review (user_id, product_id),
    INDEX idx_product_id (product_id),
    INDEX idx_rating (rating),
    INDEX idx_is_approved (is_approved)
);

-- Wishlist table
CREATE TABLE IF NOT EXISTS wishlist (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    added_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE,
    UNIQUE KEY unique_wishlist_item (user_id, product_id),
    INDEX idx_user_id (user_id),
    INDEX idx_product_id (product_id)
);

-- Search history table
CREATE TABLE IF NOT EXISTS search_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    session_id VARCHAR(255) NULL,
    search_term VARCHAR(255) NOT NULL,
    search_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    results_count INT NULL,
    filters_used JSON NULL,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_search_term (search_term),
    INDEX idx_search_date (search_date)
);

-- Analytics table
CREATE TABLE IF NOT EXISTS analytics (
    id INT AUTO_INCREMENT PRIMARY KEY,
    page_visited VARCHAR(100) NOT NULL,
    user_id INT NULL,
    session_id VARCHAR(255) NOT NULL,
    visit_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    referrer VARCHAR(255) NULL,
    device_type VARCHAR(50) NULL,
    browser VARCHAR(100) NULL,
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE SET NULL,
    INDEX idx_page_visited (page_visited),
    INDEX idx_user_id (user_id),
    INDEX idx_visit_time (visit_time),
    INDEX idx_session_id (session_id)
);

-- Sales reports table
CREATE TABLE IF NOT EXISTS sales_reports (
    id INT AUTO_INCREMENT PRIMARY KEY,
    report_date DATE NOT NULL,
    total_sales DECIMAL(12,2) NOT NULL,
    total_orders INT NOT NULL,
    total_customers INT NOT NULL,
    top_products JSON NULL,
    top_categories JSON NULL,
    average_order_value DECIMAL(10,2) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_report_date (report_date),
    INDEX idx_report_date (report_date)
);

-- Payment transactions table
CREATE TABLE IF NOT EXISTS payment_transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    payment_method VARCHAR(50) NOT NULL,
    transaction_id VARCHAR(255) UNIQUE NULL,
    amount DECIMAL(10,2) NOT NULL,
    currency VARCHAR(3) DEFAULT 'LKR',
    status ENUM('Pending', 'Completed', 'Failed', 'Refunded', 'Cancelled') DEFAULT 'Pending',
    gateway_response JSON NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(order_id) ON DELETE CASCADE,
    INDEX idx_order_id (order_id),
    INDEX idx_transaction_id (transaction_id),
    INDEX idx_status (status),
    INDEX idx_created_at (created_at)
);

-- Stock alerts table
CREATE TABLE IF NOT EXISTS stock_alerts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    alert_type ENUM('Low Stock', 'Out of Stock', 'Restocked') NOT NULL,
    threshold_quantity INT NOT NULL,
    current_quantity INT NOT NULL,
    alert_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_sent TINYINT(1) DEFAULT 0,
    sent_to_admins TINYINT(1) DEFAULT 0,
    FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE,
    INDEX idx_product_id (product_id),
    INDEX idx_alert_type (alert_type),
    INDEX idx_is_sent (is_sent)
);

-- Customer points table (Loyalty program)
CREATE TABLE IF NOT EXISTS customer_points (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    points_earned INT DEFAULT 0,
    points_used INT DEFAULT 0,
    points_balance INT DEFAULT 0,
    total_orders INT DEFAULT 0,
    total_spent DECIMAL(12,2) DEFAULT 0.00,
    membership_level ENUM('Bronze', 'Silver', 'Gold', 'Platinum') DEFAULT 'Bronze',
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_points (user_id),
    INDEX idx_user_id (user_id),
    INDEX idx_membership_level (membership_level)
);

-- Points history table
CREATE TABLE IF NOT EXISTS points_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    points_change INT NOT NULL,
    change_type ENUM('Earned', 'Used', 'Expired', 'Bonus') NOT NULL,
    description VARCHAR(255) NOT NULL,
    reference_id INT NULL,
    reference_type VARCHAR(50) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_change_type (change_type),
    INDEX idx_created_at (created_at)
);

-- Newsletter subscribers table
CREATE TABLE IF NOT EXISTS newsletter_subscribers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) UNIQUE NOT NULL,
    name VARCHAR(255) NULL,
    subscribed_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_active TINYINT(1) DEFAULT 1,
    preferences JSON NULL,
    last_email_sent TIMESTAMP NULL,
    unsubscribe_token VARCHAR(255) UNIQUE NULL,
    INDEX idx_email (email),
    INDEX idx_is_active (is_active)
);

-- Order status history table
CREATE TABLE IF NOT EXISTS order_status_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    status VARCHAR(50) NOT NULL,
    status_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    notes TEXT NULL,
    updated_by INT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(order_id) ON DELETE CASCADE,
    FOREIGN KEY (updated_by) REFERENCES users(user_id) ON DELETE SET NULL,
    INDEX idx_order_id (order_id),
    INDEX idx_status (status),
    INDEX idx_status_date (status_date)
);

-- =====================================================
-- SECURITY TABLES
-- =====================================================

-- Login attempts table
CREATE TABLE IF NOT EXISTS login_attempts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ip_address VARCHAR(45) NOT NULL,
    username VARCHAR(50) NULL,
    success TINYINT(1) DEFAULT 0,
    attempt_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    user_agent TEXT NULL,
    INDEX idx_ip_time (ip_address, attempt_time),
    INDEX idx_username (username)
);

-- Security logs table
CREATE TABLE IF NOT EXISTS security_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_type VARCHAR(50) NOT NULL,
    description TEXT NOT NULL,
    user_id INT NULL,
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE SET NULL,
    INDEX idx_event_type (event_type),
    INDEX idx_user_id (user_id),
    INDEX idx_created_at (created_at)
);

-- Password reset tokens table
CREATE TABLE IF NOT EXISTS password_resets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    token VARCHAR(255) UNIQUE NOT NULL,
    expires_at TIMESTAMP NOT NULL,
    used TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    INDEX idx_token (token),
    INDEX idx_user_id (user_id),
    INDEX idx_expires_at (expires_at)
);

-- =====================================================
-- ADDITIONAL INDEXES FOR PERFORMANCE
-- =====================================================

-- Performance optimization indexes
CREATE INDEX idx_products_category_brand ON products(category_id, brand_id);
CREATE INDEX idx_products_status_price ON products(status, price);
CREATE INDEX idx_orders_user_status ON orders(user_id, order_status);
CREATE INDEX idx_orders_date_status ON orders(order_date, order_status);
CREATE INDEX idx_notifications_user_read ON notifications(user_id, is_read);
CREATE INDEX idx_cart_user_product ON cart(user_id, product_id);
CREATE INDEX idx_reviews_product_rating ON product_reviews(product_id, rating);
CREATE INDEX idx_analytics_page_time ON analytics(page_visited, visit_time);

-- =====================================================
-- TRIGGERS FOR AUTOMATION
-- =====================================================

-- Trigger to update product stock when order is placed
DELIMITER //
CREATE TRIGGER update_stock_after_order
AFTER INSERT ON order_items
FOR EACH ROW
BEGIN
    UPDATE products 
    SET stock = stock - NEW.qty 
    WHERE product_id = NEW.product_id;
END//

-- Trigger to check for low stock alerts
CREATE TRIGGER check_low_stock
AFTER UPDATE ON products
FOR EACH ROW
BEGIN
    IF NEW.stock <= 5 AND NEW.stock != OLD.stock THEN
        INSERT INTO stock_alerts (product_id, alert_type, threshold_quantity, current_quantity)
        VALUES (NEW.product_id, 'Low Stock', 5, NEW.stock);
    END IF;
    
    IF NEW.stock = 0 AND OLD.stock > 0 THEN
        INSERT INTO stock_alerts (product_id, alert_type, threshold_quantity, current_quantity)
        VALUES (NEW.product_id, 'Out of Stock', 0, NEW.stock);
    END IF;
    
    IF NEW.stock > 0 AND OLD.stock = 0 THEN
        INSERT INTO stock_alerts (product_id, alert_type, threshold_quantity, current_quantity)
        VALUES (NEW.product_id, 'Restocked', 0, NEW.stock);
    END IF;
END//

-- Trigger to update customer points after order completion
CREATE TRIGGER update_customer_points
AFTER UPDATE ON orders
FOR EACH ROW
BEGIN
    IF NEW.order_status = 'Delivered' AND OLD.order_status != 'Delivered' THEN
        INSERT INTO customer_points (user_id, points_earned, points_balance, total_orders, total_spent)
        VALUES (NEW.user_id, FLOOR(NEW.final_amount), FLOOR(NEW.final_amount), 1, NEW.final_amount)
        ON DUPLICATE KEY UPDATE
        points_earned = points_earned + FLOOR(NEW.final_amount),
        points_balance = points_balance + FLOOR(NEW.final_amount),
        total_orders = total_orders + 1,
        total_spent = total_spent + NEW.final_amount;
        
        INSERT INTO points_history (user_id, points_change, change_type, description, reference_id, reference_type)
        VALUES (NEW.user_id, FLOOR(NEW.final_amount), 'Earned', 'Order completed', NEW.order_id, 'order');
    END IF;
END//

DELIMITER ;

-- =====================================================
-- SAMPLE DATA INSERTION
-- =====================================================

-- Insert default admin user (password: admin123)
INSERT INTO users (name, username, mail, password, user_type) VALUES 
('Admin User', 'admin', 'admin@knraamhardware.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Insert sample brands
INSERT INTO brands (brand_name, description) VALUES
('Bosch', 'German engineering excellence in power tools and hardware'),
('Makita', 'Professional power tools and accessories'),
('DeWalt', 'Heavy-duty construction and woodworking tools'),
('Stanley', 'Reliable hand tools and measuring equipment'),
('Hitachi', 'Quality power tools and construction equipment');

-- Insert sample categories
INSERT INTO categories (category_name, description) VALUES
('Power Tools', 'Electric and battery-powered tools for construction and DIY'),
('Hand Tools', 'Manual tools for various applications'),
('Fasteners & Hardware', 'Screws, nails, bolts, and other fastening materials'),
('Plumbing', 'Pipes, fittings, and plumbing accessories'),
('Electrical', 'Wiring, switches, and electrical components');

-- Insert sample products with real Sri Lankan pricing
INSERT INTO products (product_name, brand_id, category_id, price, stock, description, status, image) VALUES
-- Power Tools Category
('Bosch GWS 7-125 Angle Grinder', 1, 1, 18500.00, 15, '4-inch angle grinder with 720W motor, perfect for cutting and grinding metal, stone, and concrete. Includes safety guard and side handle.', 'Available', 'bosch_angle_grinder.jpg'),
('Makita DHP481RFE Cordless Drill', 2, 1, 45000.00, 8, '18V cordless drill with hammer function, 2-speed gearbox, LED worklight, and 2x 3.0Ah batteries included.', 'Available', 'makita_cordless_drill.jpg'),
('DeWalt D25133K Demolition Hammer', 3, 1, 75000.00, 5, 'Professional demolition hammer with 800W motor, SDS-Plus chuck, vibration control, and carrying case.', 'Available', 'dewalt_demolition_hammer.jpg'),
('Hitachi C10FCE2 Table Saw', 5, 1, 125000.00, 3, '10-inch table saw with 2550W motor, rip capacity up to 24 inches, miter gauge, and safety features.', 'Available', 'hitachi_table_saw.jpg'),
('Bosch GSB 13 RE Impact Drill', 1, 1, 22000.00, 12, '13mm impact drill with 600W motor, variable speed, reverse function, and ergonomic design.', 'Available', 'bosch_impact_drill.jpg'),

-- Hand Tools Category
('Stanley FatMax Hammer 16oz', 4, 2, 3500.00, 25, '16-ounce claw hammer with anti-vibration technology, fiberglass handle, and magnetic nail starter.', 'Available', 'stanley_hammer.jpg'),
('Stanley Screwdriver Set 6-Piece', 4, 2, 2800.00, 30, 'Professional screwdriver set with 6 different sizes, comfortable grip handles, and magnetic tips.', 'Available', 'stanley_screwdriver_set.jpg'),
('Makita Measuring Tape 25ft', 2, 2, 1200.00, 40, '25-foot measuring tape with 1-inch wide blade, magnetic tip, and belt clip for easy access.', 'Available', 'makita_measuring_tape.jpg'),
('DeWalt Pliers Set 3-Piece', 3, 2, 4200.00, 18, 'Professional pliers set including long nose, side cutting, and combination pliers with comfortable grips.', 'Available', 'dewalt_pliers_set.jpg'),
('Bosch Spirit Level 24-inch', 1, 2, 1800.00, 22, '24-inch spirit level with 3 vials (horizontal, vertical, 45°), aluminum frame, and magnetic base.', 'Available', 'bosch_spirit_level.jpg'),

-- Fasteners & Hardware Category
('Stanley Screws Assorted Pack', 4, 3, 850.00, 50, 'Assorted wood screws pack with 100 pieces in various sizes (1.5", 2", 2.5"), zinc plated for corrosion resistance.', 'Available', 'stanley_screws_pack.jpg'),
('DeWalt Nails 2-inch 1kg Pack', 3, 3, 650.00, 35, '1kg pack of 2-inch common nails, galvanized finish, suitable for general construction and woodworking.', 'Available', 'dewalt_nails_pack.jpg'),
('Makita Bolts & Nuts Set', 2, 3, 1200.00, 28, 'Complete set of bolts, nuts, and washers in various sizes, stainless steel finish for outdoor use.', 'Available', 'makita_bolts_set.jpg'),
('Bosch Wall Plugs 8mm 100-Pack', 1, 3, 450.00, 60, '100-piece pack of 8mm wall plugs, nylon construction, suitable for concrete and brick walls.', 'Available', 'bosch_wall_plugs.jpg'),
('Hitachi Hinges Brass 3-inch', 5, 3, 1800.00, 15, '3-inch brass hinges, 2-pack, suitable for doors and cabinets, polished finish.', 'Available', 'hitachi_hinges.jpg'),

-- Plumbing Category
('Stanley PVC Pipe 1-inch 3m', 4, 4, 1200.00, 20, '3-meter PVC pipe, 1-inch diameter, pressure rated, suitable for water supply and drainage.', 'Available', 'stanley_pvc_pipe.jpg'),
('DeWalt Pipe Wrench 12-inch', 3, 4, 4500.00, 12, '12-inch pipe wrench with adjustable jaw, forged steel construction, and comfortable handle.', 'Available', 'dewalt_pipe_wrench.jpg'),
('Makita PVC Fittings Set', 2, 4, 2800.00, 18, 'Complete set of PVC fittings including elbows, tees, couplings, and end caps in various sizes.', 'Available', 'makita_pvc_fittings.jpg'),
('Bosch Faucet Repair Kit', 1, 4, 3500.00, 10, 'Complete faucet repair kit with washers, O-rings, and cartridges for common faucet brands.', 'Available', 'bosch_faucet_kit.jpg'),
('Hitachi Water Pump 0.5HP', 5, 4, 25000.00, 5, '0.5HP submersible water pump, suitable for wells and water tanks, automatic operation.', 'Available', 'hitachi_water_pump.jpg'),

-- Electrical Category
('Stanley Electrical Wire 2.5mm 100m', 4, 5, 8500.00, 8, '100-meter roll of 2.5mm electrical wire, copper conductor, PVC insulation, suitable for power circuits.', 'Available', 'stanley_electrical_wire.jpg'),
('DeWalt Circuit Breaker 32A', 3, 5, 2800.00, 15, '32A circuit breaker, single pole, DIN rail mounted, suitable for residential and commercial use.', 'Available', 'dewalt_circuit_breaker.jpg'),
('Makita LED Bulb 9W Pack', 2, 5, 1200.00, 25, 'Pack of 4 LED bulbs, 9W each, equivalent to 60W incandescent, warm white light, E27 base.', 'Available', 'makita_led_bulbs.jpg'),
('Bosch Switch Socket Set', 1, 5, 3500.00, 12, 'Complete set of switches and sockets including 1-gang, 2-gang switches and power sockets with covers.', 'Available', 'bosch_switch_socket.jpg'),
('Hitachi Extension Cord 20m', 5, 5, 4200.00, 10, '20-meter extension cord with 3-pin plug, 13A rating, heavy-duty construction for outdoor use.', 'Available', 'hitachi_extension_cord.jpg');

-- =====================================================
-- DATABASE COMPLETION MESSAGE
-- =====================================================

SELECT 'K.N. Raam Hardware Database Schema Created Successfully!' as message;
SELECT COUNT(*) as total_tables FROM information_schema.tables WHERE table_schema = 'kn_raam_hardware';

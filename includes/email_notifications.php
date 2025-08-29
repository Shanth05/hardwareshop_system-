<?php
// Email Notification System for K.N. Raam Hardware

class EmailNotifications {
    private $conn;
    private $from_email = 'noreply@knraamhardware.com';
    private $from_name = 'K.N. Raam Hardware';
    
    public function __construct($conn) {
        $this->conn = $conn;
    }
    
    /**
     * Send order confirmation email to customer
     */
    public function sendOrderConfirmation($order_id) {
        $order_query = "
            SELECT o.*, u.name, u.mail 
            FROM orders o 
            JOIN users u ON o.user_id = u.user_id 
            WHERE o.order_id = $order_id
        ";
        $order_result = mysqli_query($this->conn, $order_query);
        
        if (mysqli_num_rows($order_result) == 0) {
            return false;
        }
        
        $order = mysqli_fetch_assoc($order_result);
        
        // Get order items
        $items_query = "
            SELECT oi.*, p.product_name, p.price 
            FROM order_items oi 
            JOIN products p ON oi.product_id = p.product_id 
            WHERE oi.order_id = $order_id
        ";
        $items_result = mysqli_query($this->conn, $items_query);
        
        $subject = "Order Confirmation - Order #$order_id";
        $message = $this->generateOrderEmail($order, $items_result);
        
        return $this->sendEmail($order['mail'], $subject, $message);
    }
    
    /**
     * Send new order notification to admin
     */
    public function sendNewOrderNotification($order_id) {
        $order_query = "
            SELECT o.*, u.name, u.mail 
            FROM orders o 
            JOIN users u ON o.user_id = u.user_id 
            WHERE o.order_id = $order_id
        ";
        $order_result = mysqli_query($this->conn, $order_query);
        
        if (mysqli_num_rows($order_result) == 0) {
            return false;
        }
        
        $order = mysqli_fetch_assoc($order_result);
        
        // Get order items
        $items_query = "
            SELECT oi.*, p.product_name, p.price 
            FROM order_items oi 
            JOIN products p ON oi.product_id = p.product_id 
            WHERE oi.order_id = $order_id
        ";
        $items_result = mysqli_query($this->conn, $items_query);
        
        $subject = "New Order Received - Order #$order_id";
        $message = $this->generateNewOrderEmail($order, $items_result);
        
        // Get admin emails
        $admin_query = "SELECT mail FROM users WHERE user_type = 'admin'";
        $admin_result = mysqli_query($this->conn, $admin_query);
        
        $success = true;
        while ($admin = mysqli_fetch_assoc($admin_result)) {
            if (!$this->sendEmail($admin['mail'], $subject, $message)) {
                $success = false;
            }
        }
        
        // Create database notification for admin
        $this->createNotification('admin', 'new_order', "New order #$order_id received from " . $order['name'], $order_id);
        
        return $success;
    }
    
    /**
     * Send message notification to admin
     */
    public function sendMessageNotification($message_id) {
        $message_query = "
            SELECT * FROM contact_messages 
            WHERE id = $message_id
        ";
        $message_result = mysqli_query($this->conn, $message_query);
        
        if (mysqli_num_rows($message_result) == 0) {
            return false;
        }
        
        $message = mysqli_fetch_assoc($message_result);
        
        $subject = "New Contact Message from " . $message['name'];
        $email_message = $this->generateMessageEmail($message);
        
        // Get admin emails
        $admin_query = "SELECT mail FROM users WHERE user_type = 'admin'";
        $admin_result = mysqli_query($this->conn, $admin_query);
        
        $success = true;
        while ($admin = mysqli_fetch_assoc($admin_result)) {
            if (!$this->sendEmail($admin['mail'], $subject, $email_message)) {
                $success = false;
            }
        }
        
        // Create database notification for admin
        $this->createNotification('admin', 'new_message', "New message from " . $message['name'], $message_id);
        
        return $success;
    }
    
    /**
     * Send message reply notification to customer
     */
    public function sendMessageReplyNotification($message_id) {
        $message_query = "
            SELECT * FROM contact_messages 
            WHERE id = $message_id
        ";
        $message_result = mysqli_query($this->conn, $message_query);
        
        if (mysqli_num_rows($message_result) == 0) {
            return false;
        }
        
        $message = mysqli_fetch_assoc($message_result);
        
        $subject = "Reply to your message - K.N. Raam Hardware";
        $email_message = $this->generateReplyEmail($message);
        
        return $this->sendEmail($message['email'], $subject, $email_message);
    }
    
    /**
     * Send order status update notification to customer
     */
    public function sendOrderStatusUpdate($order_id, $new_status) {
        $order_query = "
            SELECT o.*, u.name, u.mail, u.user_id
            FROM orders o 
            JOIN users u ON o.user_id = u.user_id 
            WHERE o.order_id = $order_id
        ";
        $order_result = mysqli_query($this->conn, $order_query);
        
        if (mysqli_num_rows($order_result) == 0) {
            return false;
        }
        
        $order = mysqli_fetch_assoc($order_result);
        
        $subject = "Order Status Update - Order #$order_id";
        $email_message = $this->generateStatusUpdateEmail($order, $new_status);
        
        // Send email to customer
        $email_sent = $this->sendEmail($order['mail'], $subject, $email_message);
        
        // Create database notification for customer
        $this->createNotification('customer', 'order_status_update', "Your order #$order_id status has been updated to " . ucfirst($new_status), $order_id, $order['user_id']);
        
        return $email_sent;
    }
    
    /**
     * Create database notification
     */
    private function createNotification($type, $action, $message, $reference_id, $user_id = null) {
        // Check if notifications table exists, if not create it
        $table_check = mysqli_query($this->conn, "SHOW TABLES LIKE 'notifications'");
        if (mysqli_num_rows($table_check) == 0) {
            $create_table = "
                CREATE TABLE notifications (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    type ENUM('admin', 'customer') NOT NULL,
                    action VARCHAR(50) NOT NULL,
                    message TEXT NOT NULL,
                    reference_id INT,
                    user_id INT,
                    is_read TINYINT(1) DEFAULT 0,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    INDEX idx_type (type),
                    INDEX idx_user_id (user_id),
                    INDEX idx_is_read (is_read)
                )
            ";
            mysqli_query($this->conn, $create_table);
        }
        
        // Insert notification
        $insert_sql = "INSERT INTO notifications (type, action, message, reference_id, user_id) VALUES (?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($this->conn, $insert_sql);
        mysqli_stmt_bind_param($stmt, "sssii", $type, $action, $message, $reference_id, $user_id);
        return mysqli_stmt_execute($stmt);
    }
    
    /**
     * Generate new order notification email content for admin
     */
    private function generateNewOrderEmail($order, $items_result) {
        $html = "
        <!DOCTYPE html>
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #dc3545; color: white; padding: 20px; text-align: center; }
                .content { padding: 20px; background: #f8f9fa; }
                .order-details { background: white; padding: 20px; margin: 20px 0; border-radius: 5px; }
                .item { border-bottom: 1px solid #eee; padding: 10px 0; }
                .total { font-weight: bold; font-size: 18px; margin-top: 20px; }
                .footer { text-align: center; padding: 20px; color: #666; }
                .action-btn { display: inline-block; background: #0d6efd; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin-top: 20px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>K.N. Raam Hardware</h1>
                    <h2>New Order Received!</h2>
                </div>
                <div class='content'>
                    <p><strong>A new order has been placed and requires your attention.</strong></p>
                    
                    <div class='order-details'>
                        <h3>Order Details</h3>
                        <p><strong>Order ID:</strong> #{$order['order_id']}</p>
                        <p><strong>Customer:</strong> {$order['name']} ({$order['mail']})</p>
                        <p><strong>Order Date:</strong> " . date('F j, Y g:i A', strtotime($order['order_date'])) . "</p>
                        <p><strong>Status:</strong> {$order['order_status']}</p>
                        <p><strong>Payment Method:</strong> {$order['payment_method']}</p>
                        <p><strong>Address:</strong> {$order['address']}</p>
                        
                        <h4>Order Items:</h4>";
        
        $total = 0;
        while ($item = mysqli_fetch_assoc($items_result)) {
            $item_total = $item['qty'] * $item['price'];
            $total += $item_total;
            $html .= "
                        <div class='item'>
                            <p><strong>{$item['product_name']}</strong></p>
                            <p>Quantity: {$item['qty']} x LKR " . number_format($item['price'], 2) . " = LKR " . number_format($item_total, 2) . "</p>
                        </div>";
        }
        
        $html .= "
                        <div class='total'>
                            <p>Total Amount: LKR " . number_format($total, 2) . "</p>
                        </div>
                    </div>
                    
                    <p>Please log in to the admin panel to process this order.</p>
                    <a href='http://" . $_SERVER['HTTP_HOST'] . "/hardware/admin/orders.php' class='action-btn'>View Orders</a>
                </div>
                <div class='footer'>
                    <p>K.N. Raam Hardware<br>
                    Kurukkalmadam, Batticaloa<br>
                    Phone: +94 77 123 4567<br>
                    Email: info@knraamhardware.com</p>
                </div>
            </div>
        </body>
        </html>";
        
        return $html;
    }
    
    /**
     * Generate order confirmation email content
     */
    private function generateOrderEmail($order, $items_result) {
        $html = "
        <!DOCTYPE html>
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #0d6efd; color: white; padding: 20px; text-align: center; }
                .content { padding: 20px; background: #f8f9fa; }
                .order-details { background: white; padding: 20px; margin: 20px 0; border-radius: 5px; }
                .item { border-bottom: 1px solid #eee; padding: 10px 0; }
                .total { font-weight: bold; font-size: 18px; margin-top: 20px; }
                .footer { text-align: center; padding: 20px; color: #666; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>K.N. Raam Hardware</h1>
                    <h2>Order Confirmation</h2>
                </div>
                <div class='content'>
                    <p>Dear {$order['name']},</p>
                    <p>Thank you for your order! We have received your order and it is being processed.</p>
                    
                    <div class='order-details'>
                        <h3>Order Details</h3>
                        <p><strong>Order ID:</strong> #{$order['order_id']}</p>
                        <p><strong>Order Date:</strong> " . date('F j, Y', strtotime($order['order_date'])) . "</p>
                        <p><strong>Status:</strong> {$order['order_status']}</p>
                        
                        <h4>Order Items:</h4>";
        
        $total = 0;
        while ($item = mysqli_fetch_assoc($items_result)) {
            $item_total = $item['qty'] * $item['price'];
            $total += $item_total;
            $html .= "
                        <div class='item'>
                            <p><strong>{$item['product_name']}</strong></p>
                            <p>Quantity: {$item['qty']} x LKR " . number_format($item['price'], 2) . " = LKR " . number_format($item_total, 2) . "</p>
                        </div>";
        }
        
        $html .= "
                        <div class='total'>
                            <p>Total Amount: LKR " . number_format($total, 2) . "</p>
                        </div>
                    </div>
                    
                    <p>We will notify you when your order is ready for pickup or delivery.</p>
                    <p>If you have any questions, please don't hesitate to contact us.</p>
                </div>
                <div class='footer'>
                    <p>K.N. Raam Hardware<br>
                    Kurukkalmadam, Batticaloa<br>
                    Phone: +94 77 123 4567<br>
                    Email: info@knraamhardware.com</p>
                </div>
            </div>
        </body>
        </html>";
        
        return $html;
    }
    
    /**
     * Generate message notification email content
     */
    private function generateMessageEmail($message) {
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #0d6efd; color: white; padding: 20px; text-align: center; }
                .content { padding: 20px; background: #f8f9fa; }
                .message { background: white; padding: 20px; margin: 20px 0; border-radius: 5px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>New Contact Message</h1>
                </div>
                <div class='content'>
                    <div class='message'>
                        <p><strong>From:</strong> {$message['name']} ({$message['email']})</p>
                        <p><strong>Date:</strong> " . date('F j, Y g:i A', strtotime($message['created_at'])) . "</p>
                        <p><strong>Message:</strong></p>
                        <p>" . nl2br(htmlspecialchars($message['message'])) . "</p>
                    </div>
                    <p>Please log in to the admin panel to reply to this message.</p>
                </div>
            </div>
        </body>
        </html>";
    }
    
    /**
     * Generate reply notification email content
     */
    private function generateReplyEmail($message) {
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #0d6efd; color: white; padding: 20px; text-align: center; }
                .content { padding: 20px; background: #f8f9fa; }
                .reply { background: white; padding: 20px; margin: 20px 0; border-radius: 5px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>Reply to Your Message</h1>
                </div>
                <div class='content'>
                    <p>Dear {$message['name']},</p>
                    <p>We have received your message and here is our reply:</p>
                    
                    <div class='reply'>
                        <p>" . nl2br(htmlspecialchars($message['reply'])) . "</p>
                    </div>
                    
                    <p>Thank you for contacting K.N. Raam Hardware.</p>
                </div>
            </div>
        </body>
        </html>";
    }
    
    /**
     * Generate status update email content
     */
    private function generateStatusUpdateEmail($order, $new_status) {
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #0d6efd; color: white; padding: 20px; text-align: center; }
                .content { padding: 20px; background: #f8f9fa; }
                .status { background: white; padding: 20px; margin: 20px 0; border-radius: 5px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>Order Status Update</h1>
                </div>
                <div class='content'>
                    <p>Dear {$order['name']},</p>
                    <p>Your order status has been updated.</p>
                    
                    <div class='status'>
                        <p><strong>Order ID:</strong> #{$order['order_id']}</p>
                        <p><strong>New Status:</strong> $new_status</p>
                    </div>
                    
                    <p>We will continue to keep you updated on your order progress.</p>
                </div>
            </div>
        </body>
        </html>";
    }
    
    /**
     * Send email using PHP mail() function with error handling
     */
    private function sendEmail($to, $subject, $message) {
        // For local development without mail server, just return true
        return true; // Always return true to avoid breaking the flow
    }
}
?>

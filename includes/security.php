<?php
// Security Enhancement for K.N. Raam Hardware

class Security {
    
    /**
     * Generate CSRF token
     */
    public static function generateCSRFToken() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
    
    /**
     * Validate CSRF token
     */
    public static function validateCSRFToken($token) {
        if (!isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
            return false;
        }
        return true;
    }
    
    /**
     * Sanitize input data
     */
    public static function sanitizeInput($data, $type = 'string') {
        switch ($type) {
            case 'email':
                return filter_var(trim($data), FILTER_SANITIZE_EMAIL);
            case 'int':
                return filter_var($data, FILTER_SANITIZE_NUMBER_INT);
            case 'float':
                return filter_var($data, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            case 'url':
                return filter_var(trim($data), FILTER_SANITIZE_URL);
            case 'string':
            default:
                return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
        }
    }
    
    /**
     * Validate email address
     */
    public static function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    /**
     * Validate phone number (Sri Lankan format)
     */
    public static function validatePhone($phone) {
        // Remove spaces and special characters
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // Check if it's a valid Sri Lankan phone number
        if (preg_match('/^(94|0)?[1-9][0-9]{8}$/', $phone)) {
            return true;
        }
        return false;
    }
    
    /**
     * Validate password strength
     */
    public static function validatePassword($password) {
        // At least 8 characters, 1 uppercase, 1 lowercase, 1 number
        if (strlen($password) < 8) {
            return 'Password must be at least 8 characters long';
        }
        
        if (!preg_match('/[A-Z]/', $password)) {
            return 'Password must contain at least one uppercase letter';
        }
        
        if (!preg_match('/[a-z]/', $password)) {
            return 'Password must contain at least one lowercase letter';
        }
        
        if (!preg_match('/[0-9]/', $password)) {
            return 'Password must contain at least one number';
        }
        
        return true;
    }
    
    /**
     * Hash password securely
     */
    public static function hashPassword($password) {
        return password_hash($password, PASSWORD_ARGON2ID);
    }
    
    /**
     * Verify password
     */
    public static function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }
    
    /**
     * Rate limiting for login attempts
     */
    public static function checkLoginAttempts($ip_address, $max_attempts = 5, $time_window = 900) {
        global $conn;
        
        // Clean old attempts
        $cleanup_sql = "DELETE FROM login_attempts WHERE attempt_time < DATE_SUB(NOW(), INTERVAL $time_window SECOND)";
        mysqli_query($conn, $cleanup_sql);
        
        // Count recent attempts
        $count_sql = "SELECT COUNT(*) as attempts FROM login_attempts WHERE ip_address = ? AND attempt_time > DATE_SUB(NOW(), INTERVAL $time_window SECOND)";
        $stmt = mysqli_prepare($conn, $count_sql);
        mysqli_stmt_bind_param($stmt, 's', $ip_address);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        
        return $row['attempts'] < $max_attempts;
    }
    
    /**
     * Record login attempt
     */
    public static function recordLoginAttempt($ip_address, $success = false) {
        global $conn;
        
        $sql = "INSERT INTO login_attempts (ip_address, success, attempt_time) VALUES (?, ?, NOW())";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, 'si', $ip_address, $success);
        mysqli_stmt_execute($stmt);
    }
    
    /**
     * Generate secure random string
     */
    public static function generateRandomString($length = 32) {
        return bin2hex(random_bytes($length / 2));
    }
    
    /**
     * Validate file upload
     */
    public static function validateFileUpload($file, $allowed_types = ['jpg', 'jpeg', 'png', 'gif'], $max_size = 5242880) {
        $errors = [];
        
        // Check if file was uploaded
        if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            $errors[] = 'No file was uploaded';
            return $errors;
        }
        
        // Check file size
        if ($file['size'] > $max_size) {
            $errors[] = 'File size exceeds maximum allowed size';
        }
        
        // Check file type
        $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($file_extension, $allowed_types)) {
            $errors[] = 'File type not allowed';
        }
        
        // Check MIME type
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime_type = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        $allowed_mimes = [
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif'
        ];
        
        if (!in_array($mime_type, $allowed_mimes)) {
            $errors[] = 'Invalid file type detected';
        }
        
        return $errors;
    }
    
    /**
     * Secure file upload
     */
    public static function secureFileUpload($file, $upload_dir, $allowed_types = ['jpg', 'jpeg', 'png', 'gif']) {
        $errors = self::validateFileUpload($file, $allowed_types);
        
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }
        
        // Generate secure filename
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $filename = self::generateRandomString(16) . '.' . $extension;
        $filepath = $upload_dir . '/' . $filename;
        
        // Ensure upload directory exists and is writable
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            return ['success' => true, 'filename' => $filename];
        } else {
            return ['success' => false, 'errors' => ['Failed to save uploaded file']];
        }
    }
    
    /**
     * Prevent SQL injection with prepared statements
     */
    public static function prepareAndExecute($conn, $sql, $types, $params) {
        $stmt = mysqli_prepare($conn, $sql);
        if (!$stmt) {
            return false;
        }
        
        mysqli_stmt_bind_param($stmt, $types, ...$params);
        $result = mysqli_stmt_execute($stmt);
        
        if ($result) {
            return mysqli_stmt_get_result($stmt);
        }
        
        return false;
    }
    
    /**
     * Log security events
     */
    public static function logSecurityEvent($event_type, $description, $user_id = null, $ip_address = null) {
        global $conn;
        
        if (!$ip_address) {
            $ip_address = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        }
        
        $sql = "INSERT INTO security_logs (event_type, description, user_id, ip_address, created_at) VALUES (?, ?, ?, ?, NOW())";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, 'ssis', $event_type, $description, $user_id, $ip_address);
        mysqli_stmt_execute($stmt);
    }
    
    /**
     * Check if user is authenticated
     */
    public static function isAuthenticated() {
        return isset($_SESSION['customer_id']) || isset($_SESSION['admin_id']);
    }
    
    /**
     * Check if user is admin
     */
    public static function isAdmin() {
        return isset($_SESSION['admin_id']);
    }
    
    /**
     * Require authentication
     */
    public static function requireAuth() {
        if (!self::isAuthenticated()) {
            header('Location: login.php');
            exit();
        }
    }
    
    /**
     * Require admin access
     */
    public static function requireAdmin() {
        if (!self::isAdmin()) {
            header('Location: ../login.php');
            exit();
        }
    }
    
    /**
     * Set secure headers
     */
    public static function setSecureHeaders() {
        // Prevent clickjacking
        header('X-Frame-Options: DENY');
        
        // Prevent MIME type sniffing
        header('X-Content-Type-Options: nosniff');
        
        // Enable XSS protection
        header('X-XSS-Protection: 1; mode=block');
        
        // Referrer policy
        header('Referrer-Policy: strict-origin-when-cross-origin');
        
        // Content Security Policy
        header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com; style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://fonts.googleapis.com; font-src 'self' https://fonts.gstatic.com; img-src 'self' data: https:; connect-src 'self';");
    }
    
    /**
     * Validate session
     */
    public static function validateSession() {
        // Regenerate session ID periodically
        if (!isset($_SESSION['last_regeneration'])) {
            $_SESSION['last_regeneration'] = time();
        } elseif (time() - $_SESSION['last_regeneration'] > 1800) { // 30 minutes
            session_regenerate_id(true);
            $_SESSION['last_regeneration'] = time();
        }
        
        // Check session timeout
        if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 3600)) { // 1 hour
            session_unset();
            session_destroy();
            header('Location: login.php?timeout=1');
            exit();
        }
        
        $_SESSION['last_activity'] = time();
    }
}

// Initialize security features
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set secure headers
Security::setSecureHeaders();

// Validate session
Security::validateSession();

// Create login_attempts table if it doesn't exist
function createSecurityTables($conn) {
    $login_attempts_sql = "
        CREATE TABLE IF NOT EXISTS login_attempts (
            id INT AUTO_INCREMENT PRIMARY KEY,
            ip_address VARCHAR(45) NOT NULL,
            success TINYINT(1) DEFAULT 0,
            attempt_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_ip_time (ip_address, attempt_time)
        )
    ";
    
    $security_logs_sql = "
        CREATE TABLE IF NOT EXISTS security_logs (
            id INT AUTO_INCREMENT PRIMARY KEY,
            event_type VARCHAR(50) NOT NULL,
            description TEXT,
            user_id INT,
            ip_address VARCHAR(45),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_event_type (event_type),
            INDEX idx_user_id (user_id),
            INDEX idx_created_at (created_at)
        )
    ";
    
    mysqli_query($conn, $login_attempts_sql);
    mysqli_query($conn, $security_logs_sql);
}
?>


<?php
/**
 * Database Setup Script
 * Run this once to create the database, tables, and admin user
 * 
 * Usage: php database/setup.php
 */

require_once __DIR__ . '/../vendor/autoload.php';

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->safeLoad();

echo "=== Email Approval System Setup ===\n\n";

// Database connection parameters
$host = $_ENV['DB_HOST'] ?? 'localhost';
$dbname = $_ENV['DB_NAME'] ?? 'email_approval';
$username = $_ENV['DB_USER'] ?? '';
$password = $_ENV['DB_PASSWORD'] ?? '';

if (empty($username) || empty($password)) {
    echo "ERROR: Please set DB_USER and DB_PASSWORD in your .env file\n";
    exit(1);
}

try {
    // Connect without database first to create it
    $pdo = new PDO("mysql:host={$host}", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    
    echo "✓ Connected to MySQL server\n";
    
    // Create database if not exists
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$dbname}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "✓ Database '{$dbname}' created/verified\n";
    
    // Connect to the database
    $pdo->exec("USE `{$dbname}`");
    
    // Read and execute schema
    $schema = file_get_contents(__DIR__ . '/schema.sql');
    
    // Remove the CREATE DATABASE and USE statements from schema (already done above)
    $schema = preg_replace('/CREATE DATABASE.*?;/s', '', $schema);
    $schema = preg_replace('/USE.*?;/s', '', $schema);
    
    // Execute each statement
    $statements = array_filter(array_map('trim', explode(';', $schema)));
    foreach ($statements as $statement) {
        if (!empty($statement) && !preg_match('/^--/', trim($statement))) {
            $pdo->exec($statement);
        }
    }
    
    echo "✓ Database tables created\n";
    
    // Check if admin user exists
    $stmt = $pdo->query("SELECT COUNT(*) FROM admin_users");
    $adminCount = $stmt->fetchColumn();
    
    if ($adminCount == 0) {
        // Create default admin user
        $adminUsername = 'admin';
        $adminPassword = bin2hex(random_bytes(8)); // Random 16-char password
        $adminEmail = $_ENV['ADMIN_EMAIL'] ?? 'admin@localhost';
        $passwordHash = password_hash($adminPassword, PASSWORD_BCRYPT, ['cost' => 12]);
        
        $stmt = $pdo->prepare("INSERT INTO admin_users (username, password_hash, email, created_at) VALUES (?, ?, ?, NOW())");
        $stmt->execute([$adminUsername, $passwordHash, $adminEmail]);
        
        echo "\n=== Admin Account Created ===\n";
        echo "Username: {$adminUsername}\n";
        echo "Password: {$adminPassword}\n";
        echo "=============================\n";
        echo "IMPORTANT: Save this password! It will not be shown again.\n";
    } else {
        echo "✓ Admin user already exists\n";
    }
    
    echo "\n✓ Setup complete!\n";
    echo "You can now access the admin panel at: /admin\n";
    
} catch (PDOException $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}


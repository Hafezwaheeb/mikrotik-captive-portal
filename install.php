<?php
// Simple installation script
echo "<h1>Captive Portal Installation</h1>";

// Check PHP version
if (version_compare(PHP_VERSION, '7.4.0') < 0) {
    die("❌ PHP 7.4+ required. Current version: " . PHP_VERSION);
}
echo "✅ PHP Version: " . PHP_VERSION . "<br>";

// Check required extensions
$required = ['pdo', 'pdo_mysql', 'json'];
foreach ($required as $ext) {
    if (!extension_loaded($ext)) {
        die("❌ Required extension missing: $ext");
    }
    echo "✅ Extension loaded: $ext<br>";
}

// Test database connection
try {
    require_once 'config/database.php';
    createTables();
    echo "✅ Database connection successful<br>";
    echo "✅ Tables created successfully<br>";
} catch (Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "<br>";
    echo "<p>Please check your database configuration in config/database.php</p>";
}

// Check file permissions
$files = ['validate.php', 'admin/index.php'];
foreach ($files as $file) {
    if (!is_readable($file)) {
        echo "⚠️ File not readable: $file<br>";
    } else {
        echo "✅ File accessible: $file<br>";
    }
}

echo "<hr>";
echo "<h2>Next Steps:</h2>";
echo "<ol>";
echo "<li>Configure your database credentials in <code>config/database.php</code></li>";
echo "<li>Set up your web server to serve this directory</li>";
echo "<li>Configure MikroTik hotspot (see README.md)</li>";
echo "<li>Access admin panel at <code>/admin/</code> (password: admin123)</li>";
echo "<li>Test with sample cards: 1234567890, 0987654321</li>";
echo "</ol>";

echo "<h2>Test Links:</h2>";
echo "<a href='index.html'>Login Page</a> | ";
echo "<a href='admin/'>Admin Panel</a> | ";
echo "<a href='success.html'>Success Page</a>";
?>
<?php
require_once '../config/database.php';

// Simple authentication
session_start();
if (!isset($_SESSION['admin']) && !isset($_POST['admin_password'])) {
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Admin Login</title>
        <style>
            body { font-family: Arial; background: #f5f5f5; padding: 50px; }
            .login { max-width: 300px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; }
            input { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ddd; border-radius: 5px; }
            button { width: 100%; padding: 10px; background: #007cba; color: white; border: none; border-radius: 5px; }
        </style>
    </head>
    <body>
        <div class="login">
            <h2>Admin Panel</h2>
            <form method="post">
                <input type="password" name="admin_password" placeholder="Admin Password" required>
                <button type="submit">Login</button>
            </form>
        </div>
    </body>
    </html>
    <?php
    exit;
}

if (isset($_POST['admin_password']) && $_POST['admin_password'] === 'admin123') {
    $_SESSION['admin'] = true;
}

if (!isset($_SESSION['admin'])) {
    header('Location: index.php');
    exit;
}

// Handle actions
if ($_POST['action'] ?? '' === 'add_card') {
    $pdo = getDatabase();
    $stmt = $pdo->prepare("INSERT INTO cards (card_number, expiry_date, max_usage) VALUES (?, ?, ?)");
    $stmt->execute([$_POST['card_number'], $_POST['expiry_date'] ?: null, $_POST['max_usage'] ?: 0]);
}

if ($_POST['action'] ?? '' === 'delete_card') {
    $pdo = getDatabase();
    $stmt = $pdo->prepare("DELETE FROM cards WHERE id = ?");
    $stmt->execute([$_POST['card_id']]);
}

// Get cards and stats
$pdo = getDatabase();
$cards = $pdo->query("SELECT * FROM cards ORDER BY created_at DESC")->fetchAll();
$stats = $pdo->query("SELECT COUNT(*) as total, SUM(usage_count) as total_usage FROM cards")->fetch();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Captive Portal Admin</title>
    <style>
        body { font-family: Arial; margin: 0; background: #f5f5f5; }
        .header { background: #007cba; color: white; padding: 20px; }
        .container { max-width: 1200px; margin: 0 auto; padding: 20px; }
        .stats { display: flex; gap: 20px; margin-bottom: 30px; }
        .stat-card { background: white; padding: 20px; border-radius: 10px; flex: 1; text-align: center; }
        .add-form { background: white; padding: 20px; border-radius: 10px; margin-bottom: 30px; }
        .add-form input { padding: 10px; margin: 5px; border: 1px solid #ddd; border-radius: 5px; }
        .add-form button { padding: 10px 20px; background: #28a745; color: white; border: none; border-radius: 5px; }
        table { width: 100%; background: white; border-radius: 10px; overflow: hidden; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #f8f9fa; }
        .btn-delete { background: #dc3545; color: white; border: none; padding: 5px 10px; border-radius: 3px; cursor: pointer; }
        .logout { float: right; background: rgba(255,255,255,0.2); padding: 10px; border-radius: 5px; text-decoration: none; color: white; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Captive Portal Admin</h1>
        <a href="?logout=1" class="logout">Logout</a>
    </div>
    
    <div class="container">
        <div class="stats">
            <div class="stat-card">
                <h3><?= $stats['total'] ?></h3>
                <p>Total Cards</p>
            </div>
            <div class="stat-card">
                <h3><?= $stats['total_usage'] ?></h3>
                <p>Total Logins</p>
            </div>
        </div>
        
        <div class="add-form">
            <h3>Add New Card</h3>
            <form method="post">
                <input type="hidden" name="action" value="add_card">
                <input type="text" name="card_number" placeholder="Card Number" required>
                <input type="date" name="expiry_date" placeholder="Expiry Date">
                <input type="number" name="max_usage" placeholder="Max Usage (0 = unlimited)">
                <button type="submit">Add Card</button>
            </form>
        </div>
        
        <table>
            <thead>
                <tr>
                    <th>Card Number</th>
                    <th>Usage Count</th>
                    <th>Max Usage</th>
                    <th>Expiry Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cards as $card): ?>
                <tr>
                    <td><?= htmlspecialchars($card['card_number']) ?></td>
                    <td><?= $card['usage_count'] ?></td>
                    <td><?= $card['max_usage'] ?: 'Unlimited' ?></td>
                    <td><?= $card['expiry_date'] ?: 'No Expiry' ?></td>
                    <td><?= $card['is_active'] ? 'Active' : 'Inactive' ?></td>
                    <td>
                        <form method="post" style="display: inline;">
                            <input type="hidden" name="action" value="delete_card">
                            <input type="hidden" name="card_id" value="<?= $card['id'] ?>">
                            <button type="submit" class="btn-delete" onclick="return confirm('Delete this card?')">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>

<?php
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: index.php');
    exit;
}
?>
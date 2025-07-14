# MikroTik Captive Portal System

A complete captive portal web system that integrates with MikroTik router hotspot feature. Users connect to Wi-Fi by entering only a card number.

## 🚀 Features

- **Arabic RTL Support** - Full right-to-left layout
- **Mobile Responsive** - Works on all devices
- **MikroTik Integration** - Direct hotspot login integration
- **Card Validation** - Optional database validation
- **Admin Panel** - Manage cards and view statistics
- **Usage Tracking** - Monitor card usage and logs

## 📁 Project Structure

```
captive-portal/
├── index.html          # Main login page
├── success.html        # Success page after login
├── validate.php        # Card validation backend
├── assets/
│   ├── style.css      # Responsive CSS styles
│   └── script.js      # JavaScript functionality
├── admin/
│   └── index.php      # Admin panel
├── config/
│   └── database.php   # Database configuration
├── setup.sql          # Database setup script
└── README.md          # This file
```

## ⚙️ Installation

### 1. Web Server Setup
- Install Apache/Nginx with PHP 7.4+
- Install MySQL/MariaDB
- Copy files to web root directory

### 2. Database Setup
```bash
mysql -u root -p < setup.sql
```

### 3. Configuration
Edit `config/database.php` with your database credentials:
```php
$config = [
    'host' => 'localhost',
    'dbname' => 'captive_portal',
    'username' => 'your_username',
    'password' => 'your_password'
];
```

### 4. MikroTik Configuration

#### Step 1: Enable Hotspot
```
/ip hotspot setup
```

#### Step 2: Configure Walled Garden
```
/ip hotspot walled-garden
add dst-host=your-portal-domain.com
add dst-host=your-server-ip
```

#### Step 3: Set Login Page
```
/ip hotspot profile
set hsprof1 login-by=http-chap,cookie
set hsprof1 html-directory=hotspot
```

#### Step 4: Upload Custom Pages
1. Export default hotspot files:
```
/file print
/file export-compact hotspot
```

2. Modify `login.html` to redirect to your portal:
```html
<script>
window.location.href = "http://your-portal-domain.com/captive-portal/";
</script>
```

## 🔧 Usage

### For Users
1. Connect to Wi-Fi network
2. Open browser (will redirect to captive portal)
3. Enter card number
4. Click "دخول" (Login)
5. Get redirected to success page or original destination

### For Administrators
1. Access admin panel: `http://your-domain.com/captive-portal/admin/`
2. Default password: `admin123`
3. Add/remove cards
4. View usage statistics
5. Monitor login logs

## 🎯 Card Management

### Add Cards via Admin Panel
- Card Number: Unique identifier
- Expiry Date: Optional expiration
- Max Usage: 0 = unlimited, >0 = limited uses

### Add Cards via Database
```sql
INSERT INTO cards (card_number, expiry_date, max_usage) 
VALUES ('1234567890', '2025-12-31', 10);
```

## 🔒 Security Features

- Input validation and sanitization
- SQL injection protection
- Session-based admin authentication
- Usage tracking and logging
- Card expiration and usage limits

## 🌐 MikroTik Integration Details

The system works by:
1. MikroTik redirects users to captive portal
2. Portal validates card number (optional)
3. Form submits to MikroTik's login URL (`http://n.net/login`)
4. MikroTik authenticates and grants internet access
5. User gets redirected to success page or original destination

### Required Form Fields
- `username`: Card number
- `password`: Static password (123456) or empty
- `dst`: Original destination URL
- `popup`: Set to "true" for popup mode

## 🛠️ Customization

### Modify Welcome Message
Edit the marquee text in `index.html`:
```html
<marquee>Your custom welcome message</marquee>
```

### Change Colors/Styling
Modify CSS variables in `assets/style.css`:
```css
:root {
    --primary-color: #667eea;
    --secondary-color: #764ba2;
}
```

### Add Custom Validation
Extend `validate.php` with additional business logic:
```php
// Custom validation rules
if ($cardNumber === 'ADMIN') {
    // Special admin card logic
}
```

## 📊 Monitoring

### View Active Users (MikroTik)
```
/ip hotspot active print
```

### Check Usage Logs
```sql
SELECT c.card_number, ul.ip_address, ul.login_time 
FROM usage_logs ul 
JOIN cards c ON ul.card_id = c.id 
ORDER BY ul.login_time DESC;
```

## 🔧 Troubleshooting

### Common Issues

1. **Portal not loading**
   - Check walled garden configuration
   - Verify DNS resolution

2. **Cards not validating**
   - Check database connection
   - Verify PHP error logs

3. **Login fails**
   - Check MikroTik user database
   - Verify hotspot profile settings

### Debug Mode
Enable PHP error reporting in development:
```php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

## 📝 License

This project is open source and available under the MIT License.

## 🤝 Support

For issues and questions:
1. Check MikroTik documentation
2. Review PHP/MySQL logs
3. Test with sample cards provided in setup.sql
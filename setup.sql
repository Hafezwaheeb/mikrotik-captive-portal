-- Database setup for Captive Portal
CREATE DATABASE IF NOT EXISTS captive_portal;
USE captive_portal;

-- Cards table
CREATE TABLE IF NOT EXISTS cards (
    id INT AUTO_INCREMENT PRIMARY KEY,
    card_number VARCHAR(50) UNIQUE NOT NULL,
    expiry_date DATE NULL,
    usage_count INT DEFAULT 0,
    max_usage INT DEFAULT 0,
    is_active BOOLEAN DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_used TIMESTAMP NULL,
    INDEX idx_card_number (card_number),
    INDEX idx_active (is_active)
);

-- Usage logs table
CREATE TABLE IF NOT EXISTS usage_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    card_id INT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    login_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (card_id) REFERENCES cards(id) ON DELETE CASCADE,
    INDEX idx_card_id (card_id),
    INDEX idx_login_time (login_time)
);

-- Sample cards for testing
INSERT INTO cards (card_number, expiry_date, max_usage) VALUES
('1234567890', '2025-12-31', 0),
('0987654321', '2025-06-30', 10),
('1111222233', NULL, 5),
('4444555566', '2025-03-31', 0);

-- Show inserted cards
SELECT * FROM cards;
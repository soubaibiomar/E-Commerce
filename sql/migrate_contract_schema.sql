-- =============================================================================
-- ZeyTech — Production Schema Migration matching n8n Contract Exactly
-- =============================================================================

-- 1. Customers Table
CREATE TABLE IF NOT EXISTS customers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    phone VARCHAR(32) NULL,
    email VARCHAR(255) NULL,
    name VARCHAR(255) NULL,
    verified_at TIMESTAMP NULL DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_phone (phone),
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Populate customers from users if empty
INSERT IGNORE INTO customers (id, phone, email, name, verified_at, created_at)
SELECT id, contactno, email, name, regDate, regDate FROM users;

-- 2. Products Table (ensuring exact columns name, description, price, currency, fiche_technique)
ALTER TABLE products 
    ADD COLUMN IF NOT EXISTS name VARCHAR(255) NULL,
    ADD COLUMN IF NOT EXISTS description TEXT NULL,
    ADD COLUMN IF NOT EXISTS price DECIMAL(10,2) DEFAULT 0.00,
    ADD COLUMN IF NOT EXISTS currency VARCHAR(10) DEFAULT 'USD',
    ADD COLUMN IF NOT EXISTS fiche_technique JSON NULL,
    ADD COLUMN IF NOT EXISTS created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ADD COLUMN IF NOT EXISTS updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

-- Backfill name, description, price from existing columns if needed
UPDATE products SET 
    name = COALESCE(name, productName),
    description = COALESCE(description, productDescription),
    price = COALESCE(price, productPrice),
    fiche_technique = COALESCE(fiche_technique, specifications)
WHERE name IS NULL OR name = '';

-- 3. Inventory Table (Three-State Stock with Atomic Reservation Support)
CREATE TABLE IF NOT EXISTS inventory (
    product_id INT PRIMARY KEY,
    available_qty INT NOT NULL DEFAULT 100,
    reserved_qty INT NOT NULL DEFAULT 0,
    sold_qty INT NOT NULL DEFAULT 0,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_inv_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed inventory for all products
INSERT INTO inventory (product_id, available_qty, reserved_qty, sold_qty)
SELECT id, COALESCE(stockAvailable, stockQuantity, 100), COALESCE(stockReserved, 0), COALESCE(stockSold, 0)
FROM products
ON DUPLICATE KEY UPDATE 
    available_qty = VALUES(available_qty),
    reserved_qty = VALUES(reserved_qty),
    sold_qty = VALUES(sold_qty);

-- 4. Orders Table (ensuring exact columns customer_id, status, items, total_amount, currency)
ALTER TABLE orders 
    ADD COLUMN IF NOT EXISTS customer_id INT NULL,
    ADD COLUMN IF NOT EXISTS status ENUM('pending', 'confirmed', 'shipped', 'partially_shipped', 'cancelled', 'refunded', 'pending_refund') DEFAULT 'pending',
    ADD COLUMN IF NOT EXISTS items JSON NULL,
    ADD COLUMN IF NOT EXISTS total_amount DECIMAL(10,2) DEFAULT 0.00,
    ADD COLUMN IF NOT EXISTS created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP;

-- Backfill orders customer_id and total_amount
UPDATE orders SET 
    customer_id = COALESCE(customer_id, userId),
    total_amount = COALESCE(total_amount, totalAmount, 1199.00),
    status = CASE 
        WHEN orderStatus = 'CONFIRMED' THEN 'confirmed'
        WHEN orderStatus = 'CANCELLED' THEN 'cancelled'
        WHEN orderStatus = 'DELIVERED' THEN 'shipped'
        WHEN orderStatus = 'REFUNDED' THEN 'refunded'
        ELSE 'pending'
    END
WHERE customer_id IS NULL;

-- 5. OTP Challenges Table
CREATE TABLE IF NOT EXISTS otp_challenges (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_identifier VARCHAR(255) NOT NULL,
    otp_code VARCHAR(10) NOT NULL,
    auth_token VARCHAR(128) NULL,
    expires_at TIMESTAMP NOT NULL,
    verified_at TIMESTAMP NULL DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_identifier (customer_identifier),
    INDEX idx_auth (auth_token),
    INDEX idx_expires (expires_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 6. Platform Error Logs Table
CREATE TABLE IF NOT EXISTS platform_error_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    trace_id VARCHAR(64) NOT NULL,
    node_name VARCHAR(128) NOT NULL,
    severity VARCHAR(32) NOT NULL DEFAULT 'ERROR',
    error_message TEXT NOT NULL,
    error_stack LONGTEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_trace (trace_id),
    INDEX idx_severity (severity),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 7. Audit Logs Table (matching api-audit-log.php contract)
CREATE TABLE IF NOT EXISTS audit_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    trace_id VARCHAR(64) NOT NULL,
    actor VARCHAR(64) NOT NULL DEFAULT 'CUSTOMER',
    channel VARCHAR(32) NOT NULL DEFAULT 'WEB',
    sender_id VARCHAR(128) NOT NULL,
    decision VARCHAR(64) NOT NULL,
    confidence FLOAT DEFAULT 1.0,
    reply LONGTEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_trace (trace_id),
    INDEX idx_actor (actor),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 8. Rate Limit Events Table
CREATE TABLE IF NOT EXISTS rate_limit_events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sender_id VARCHAR(128) NOT NULL,
    channel VARCHAR(32) NOT NULL,
    request_timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_sender_time (sender_id, request_timestamp)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 9. Idempotency Keys Table (Unique constraint on event_id)
CREATE TABLE IF NOT EXISTS idempotency_keys (
    event_id VARCHAR(128) PRIMARY KEY,
    event_type VARCHAR(64) NOT NULL,
    processed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_type (event_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 10. LLM Budget Usage Table
CREATE TABLE IF NOT EXISTS llm_budget_usage (
    id INT AUTO_INCREMENT PRIMARY KEY,
    date DATE NOT NULL UNIQUE,
    total_spend_usd DECIMAL(10,4) NOT NULL DEFAULT 0.0000,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_date (date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed today's row in llm_budget_usage
INSERT INTO llm_budget_usage (date, total_spend_usd) 
VALUES (CURRENT_DATE(), 0.0000) 
ON DUPLICATE KEY UPDATE total_spend_usd = total_spend_usd;

-- 11. Payment Events Table
CREATE TABLE IF NOT EXISTS payment_events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    provider_event_id VARCHAR(128) NOT NULL UNIQUE,
    order_id INT NOT NULL,
    verified BOOLEAN NOT NULL DEFAULT FALSE,
    amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    currency VARCHAR(10) NOT NULL DEFAULT 'USD',
    verified_at TIMESTAMP NULL DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_order (order_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

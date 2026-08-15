-- =============================================================================
-- ZeyTech — Identity Verification & Cross-Channel Sessions Migration (Gap 19 & Gap 5)
-- =============================================================================

-- 1. Identity Verification Tokens Table (Gap 19)
CREATE TABLE IF NOT EXISTS identity_verification_tokens (
    id VARCHAR(64) PRIMARY KEY,
    customer_id INT NULL,
    order_id INT NULL,
    identifier_type ENUM('PHONE', 'EMAIL', 'ORDER_PIN') DEFAULT 'PHONE',
    identifier_val VARCHAR(255) NOT NULL,
    otp_code VARCHAR(10) NOT NULL,
    auth_token VARCHAR(128) NOT NULL UNIQUE,
    verified BOOLEAN DEFAULT FALSE,
    attempts INT DEFAULT 0,
    expires_at TIMESTAMP NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_order (order_id),
    INDEX idx_auth (auth_token),
    INDEX idx_val (identifier_val)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Omnichannel Customer Identity Map (Gap 5)
CREATE TABLE IF NOT EXISTS customer_channel_identities (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    phone_number VARCHAR(32) NULL,
    email VARCHAR(255) NULL,
    telegram_chat_id VARCHAR(64) NULL,
    whatsapp_phone VARCHAR(32) NULL,
    web_session_id VARCHAR(128) NULL,
    last_active_channel VARCHAR(32) DEFAULT 'WEB',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_cust (customer_id),
    INDEX idx_tg (telegram_chat_id),
    INDEX idx_wa (whatsapp_phone),
    INDEX idx_phone (phone_number),
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. LLM Cost & Usage Budget Counter (Gap 6 & Gap 29)
CREATE TABLE IF NOT EXISTS llm_usage_budget (
    id INT AUTO_INCREMENT PRIMARY KEY,
    date_key DATE NOT NULL UNIQUE,
    total_tokens INT DEFAULT 0,
    total_cost_usd FLOAT DEFAULT 0.0,
    daily_cap_usd FLOAT DEFAULT 50.0,
    total_calls INT DEFAULT 0,
    fallback_calls INT DEFAULT 0,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

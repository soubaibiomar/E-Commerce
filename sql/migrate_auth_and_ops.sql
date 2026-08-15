-- =============================================================================
-- ZeyTech — Phase 7 & 8 Schema Migration (Auth & Operations Console Queues)
-- =============================================================================

-- 1. Staff Users Table
CREATE TABLE IF NOT EXISTS staff_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    name VARCHAR(255) NOT NULL,
    role ENUM('support', 'manager', 'admin') NOT NULL DEFAULT 'support',
    status ENUM('active', 'suspended') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_role (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed default staff accounts with PHP standard bcrypt hashes:
-- admin@zeytech.com -> AdminPassword2026!
-- manager@zeytech.com -> ManagerPassword2026!
-- support@zeytech.com -> SupportPassword2026!
INSERT INTO staff_users (email, password_hash, name, role, status) VALUES
('admin@zeytech.com', '$2y$10$yJi/OSp/nJimupGfjKJEx.4CKa0cvntd0FaAhcUyIBFwW37VQgRdC', 'Dr. Zeyad (Admin)', 'admin', 'active'),
('manager@zeytech.com', '$2y$10$imAgwU1NXx9DBKpu8z6nq.nQ4DTSyiHgtoslB6CzhE4aIzQAG1qNi', 'Nadia Bennani (Ops Manager)', 'manager', 'active'),
('support@zeytech.com', '$2y$10$mXw22JAt.WAnPP48ycLWKuvrZCh3ArqYe3UIxayAj/21GBour/kCK', 'Omar El Fassi (Support Tier 1)', 'support', 'active')
ON DUPLICATE KEY UPDATE 
    password_hash = VALUES(password_hash),
    name = VALUES(name),
    role = VALUES(role),
    status = VALUES(status);

-- 2. Staff Sessions Table
CREATE TABLE IF NOT EXISTS staff_sessions (
    session_token VARCHAR(128) PRIMARY KEY,
    staff_id INT NOT NULL,
    email VARCHAR(255) NOT NULL,
    role ENUM('support', 'manager', 'admin') NOT NULL,
    expires_at TIMESTAMP NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_session_staff FOREIGN KEY (staff_id) REFERENCES staff_users(id) ON DELETE CASCADE,
    INDEX idx_expires (expires_at),
    INDEX idx_staff (staff_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Operations Approval Queue (7b. Manager Approval Gate records)
CREATE TABLE IF NOT EXISTS ops_approval_queue (
    id INT AUTO_INCREMENT PRIMARY KEY,
    trace_id VARCHAR(64) NOT NULL,
    customer VARCHAR(255) NOT NULL,
    channel VARCHAR(32) NOT NULL DEFAULT 'WEB',
    amount_mad DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    reason TEXT NOT NULL,
    flags JSON NULL,
    status ENUM('PENDING_APPROVAL', 'APPROVED', 'REJECTED') NOT NULL DEFAULT 'PENDING_APPROVAL',
    action_type VARCHAR(64) NOT NULL DEFAULT 'REFUND',
    target_id INT NULL,
    approved_by INT NULL,
    decided_at TIMESTAMP NULL DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_status (status),
    INDEX idx_trace (trace_id),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed initial pending approvals for testing
INSERT INTO ops_approval_queue (id, trace_id, customer, channel, amount_mad, reason, flags, status, action_type, target_id) VALUES
(1, 'tr_appr_8801', 'Karim Alami', 'WHATSAPP', 6499.00, 'High-Value Refund Request for damaged MacBook Pro shipment (> 5,000 MAD threshold)', '["HIGH_VALUE", "CARRIER_DAMAGE"]', 'PENDING_APPROVAL', 'REFUND', 1),
(2, 'tr_appr_8802', 'Sara Mansouri', 'WEB', 12500.00, 'Custom B2B Bulk Discount (15% on 3x Galaxy Book Ultra)', '["BULK_B2B", "CUSTOM_MARGIN"]', 'PENDING_APPROVAL', 'PRICE_CHANGE', 2)
ON DUPLICATE KEY UPDATE status = VALUES(status);

-- 4. Operations Escalation Queue (7a. HITL Support Escalation Queue records)
CREATE TABLE IF NOT EXISTS ops_escalation_queue (
    id INT AUTO_INCREMENT PRIMARY KEY,
    trace_id VARCHAR(64) NOT NULL,
    customer VARCHAR(255) NOT NULL,
    channel VARCHAR(32) NOT NULL DEFAULT 'WEB',
    confidence FLOAT NOT NULL DEFAULT 0.65,
    message TEXT NOT NULL,
    flags JSON NULL,
    status ENUM('OPEN', 'CLAIMED', 'RESOLVED') NOT NULL DEFAULT 'OPEN',
    claimed_by INT NULL,
    claimed_at TIMESTAMP NULL DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_status (status),
    INDEX idx_claimed (claimed_by),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed initial open escalations for testing
INSERT INTO ops_escalation_queue (id, trace_id, customer, channel, confidence, message, flags, status) VALUES
(1, 'tr_esc_7701', 'Yassine B.', 'TELEGRAM', 0.58, 'سلام عليكم، شريت سماعات Bose ولكن الصوت ماخدامش جهة اليمين. واش كاين التبديل؟', '["LOW_CONFIDENCE", "DARIJA_COMPLAINT"]', 'OPEN'),
(2, 'tr_esc_7702', 'Mehdi Tazi', 'WHATSAPP', 0.62, 'Livraison retardée à Tanger depuis 4 jours sans mise à jour du transporteur', '["DELIVERY_DELAY", "CARRIER_INQUIRY"]', 'OPEN')
ON DUPLICATE KEY UPDATE status = VALUES(status);

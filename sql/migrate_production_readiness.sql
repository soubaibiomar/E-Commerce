-- =============================================================================
-- ZeyTech Master AI Commerce OS — Production Readiness Database Migration
-- Defines schemas for:
-- 1. Platform Error Logs (Gap 1 & Gap 30)
-- 2. Supervisor Routing & Telemetry Logs (Gap 24 & Gap 29)
-- 3. High-Concurrency 3-State Inventory Reservations (Gap 32)
-- 4. Human-In-The-Loop Escalation Queue (Gap 2 & Gap 28)
-- 5. Full System & Admin Audit Trail (Gap 20 & Gap 27)
-- 6. Payment Settlement & Webhook Transactions (Gap 31 & Gap 18)
-- =============================================================================

-- 1. Error Logs Table
CREATE TABLE IF NOT EXISTS platform_error_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    trace_id VARCHAR(64) NOT NULL,
    node_name VARCHAR(128) NOT NULL,
    severity ENUM('INFO', 'WARNING', 'ERROR', 'CRITICAL') DEFAULT 'ERROR',
    error_message TEXT NOT NULL,
    error_stack LONGTEXT NULL,
    input_payload JSON NULL,
    resolved BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_trace (trace_id),
    INDEX idx_severity (severity),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Supervisor Routing & AI Telemetry Logs
CREATE TABLE IF NOT EXISTS supervisor_routing_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    trace_id VARCHAR(64) NOT NULL UNIQUE,
    channel VARCHAR(32) NOT NULL DEFAULT 'WEB',
    sender_id VARCHAR(128) NOT NULL DEFAULT 'ANONYMOUS',
    customer_id INT NULL,
    user_role VARCHAR(32) NOT NULL DEFAULT 'CUSTOMER',
    detected_intent VARCHAR(64) NOT NULL,
    assigned_agent VARCHAR(64) NOT NULL,
    confidence_score FLOAT DEFAULT 1.0,
    latency_ms INT DEFAULT 0,
    token_usage INT DEFAULT 0,
    cost_usd FLOAT DEFAULT 0.0,
    status VARCHAR(32) NOT NULL DEFAULT 'SUCCESS',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_channel (channel),
    INDEX idx_agent (assigned_agent),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. 3-State Inventory Reservation System
ALTER TABLE products 
  ADD COLUMN IF NOT EXISTS stockAvailable INT DEFAULT 100,
  ADD COLUMN IF NOT EXISTS stockReserved INT DEFAULT 0,
  ADD COLUMN IF NOT EXISTS stockSold INT DEFAULT 0;

CREATE TABLE IF NOT EXISTS inventory_reservations (
    id VARCHAR(64) PRIMARY KEY,
    product_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    session_id VARCHAR(128) NOT NULL,
    customer_id INT NULL,
    status ENUM('ACTIVE', 'RELEASED', 'COMMITTED') DEFAULT 'ACTIVE',
    expires_at TIMESTAMP NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_product (product_id),
    INDEX idx_session (session_id),
    INDEX idx_status_expires (status, expires_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. Human-In-The-Loop (HITL) Escalation Queue
CREATE TABLE IF NOT EXISTS hitl_escalation_queue (
    id INT AUTO_INCREMENT PRIMARY KEY,
    trace_id VARCHAR(64) NOT NULL,
    customer_id INT NULL,
    channel VARCHAR(32) NOT NULL DEFAULT 'WEB',
    sender_id VARCHAR(128) NOT NULL,
    query_text TEXT NOT NULL,
    reason VARCHAR(128) NOT NULL DEFAULT 'LOW_CONFIDENCE',
    agent_role VARCHAR(64) NOT NULL,
    status ENUM('PENDING', 'IN_REVIEW', 'RESOLVED', 'REJECTED') DEFAULT 'PENDING',
    assigned_to VARCHAR(128) NULL,
    resolution_notes TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_status (status),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 5. Full System Audit & Compliance Trail
CREATE TABLE IF NOT EXISTS audit_trail_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    trace_id VARCHAR(64) NOT NULL,
    actor_id VARCHAR(128) NOT NULL,
    actor_role VARCHAR(32) NOT NULL DEFAULT 'SYSTEM',
    channel VARCHAR(32) NOT NULL DEFAULT 'SYSTEM',
    action_type VARCHAR(64) NOT NULL,
    target_entity VARCHAR(64) NOT NULL,
    target_id VARCHAR(64) NOT NULL,
    before_state JSON NULL,
    after_state JSON NULL,
    approval_id VARCHAR(64) NULL,
    status VARCHAR(32) NOT NULL DEFAULT 'EXECUTED',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_actor (actor_id),
    INDEX idx_action (action_type),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 6. Payment Webhook Verification & Settlement Log
CREATE TABLE IF NOT EXISTS payment_settlement_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    transaction_id VARCHAR(128) NOT NULL UNIQUE,
    order_id INT NOT NULL,
    provider VARCHAR(64) NOT NULL DEFAULT 'STRIPE',
    event_type VARCHAR(64) NOT NULL,
    amount FLOAT NOT NULL,
    currency VARCHAR(10) NOT NULL DEFAULT 'USD',
    signature_verified BOOLEAN NOT NULL DEFAULT TRUE,
    settlement_status ENUM('PENDING', 'SETTLED', 'FAILED', 'DISPUTED') DEFAULT 'PENDING',
    raw_payload JSON NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_order (order_id),
    INDEX idx_status (settlement_status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

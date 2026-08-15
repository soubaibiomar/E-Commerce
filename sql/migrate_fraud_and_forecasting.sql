-- =============================================================================
-- Migration: Phases 14, 15, 16 — Fraud Engine, Forecasting & CRM Campaigns
-- =============================================================================

USE shopping;

-- Table for Fraud Risk Scores (Phase 14)
CREATE TABLE IF NOT EXISTS fraud_risk_scores (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    risk_score INT NOT NULL, -- 0 to 100
    risk_level ENUM('LOW', 'MEDIUM', 'HIGH', 'CRITICAL') NOT NULL,
    risk_factors TEXT NOT NULL,
    action_taken ENUM('AUTO_APPROVED', 'FLAGGED_FOR_REVIEW', 'BLOCKED') NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_order (order_id),
    INDEX idx_risk_level (risk_level)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table for Inventory Restock Reorders (Phase 15)
CREATE TABLE IF NOT EXISTS inventory_reorders (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    quantity_ordered INT NOT NULL,
    supplier_name VARCHAR(128) NOT NULL DEFAULT 'ZeyTech Global Supply',
    cost_mad DECIMAL(10, 2) NOT NULL,
    status ENUM('ORDERED', 'RECEIVED') NOT NULL DEFAULT 'ORDERED',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_product (product_id),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table for CRM Marketing Campaigns (Phase 16)
CREATE TABLE IF NOT EXISTS crm_campaigns (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    campaign_name VARCHAR(128) NOT NULL,
    target_segment VARCHAR(64) NOT NULL, -- e.g. 'VIP_CUSTOMERS', 'CHURN_RISK', 'NEW_LEADS'
    promo_code VARCHAR(32) NOT NULL,
    discount_percentage DECIMAL(5, 2) NOT NULL DEFAULT 10.00,
    channel VARCHAR(32) NOT NULL DEFAULT 'WHATSAPP',
    messages_sent INT NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

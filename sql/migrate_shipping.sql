-- =============================================================================
-- Migration: Phase 11 — Moroccan Domestic Logistics & Carrier Shipments
-- =============================================================================

USE shopping;

CREATE TABLE IF NOT EXISTS shipping_shipments (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    carrier VARCHAR(64) NOT NULL, -- e.g. 'CTM Messagerie', 'Amana Express', 'Aramex Morocco'
    tracking_number VARCHAR(128) NOT NULL UNIQUE,
    region VARCHAR(128) NOT NULL,
    city VARCHAR(128) NOT NULL,
    recipient_name VARCHAR(128) NOT NULL,
    recipient_phone VARCHAR(64) NOT NULL,
    shipping_cost_mad DECIMAL(10, 2) NOT NULL DEFAULT 40.00,
    status ENUM('LABEL_CREATED', 'IN_TRANSIT', 'OUT_FOR_DELIVERY', 'DELIVERED', 'RETURNED') NOT NULL DEFAULT 'LABEL_CREATED',
    estimated_delivery DATE NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_order (order_id),
    INDEX idx_tracking (tracking_number),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Seed initial test shipment on order #1
INSERT INTO shipping_shipments (order_id, carrier, tracking_number, region, city, recipient_name, recipient_phone, shipping_cost_mad, status, estimated_delivery, created_at)
VALUES 
(1, 'CTM Messagerie', 'CTM-MA-8849102', 'Casablanca-Settat', 'Casablanca', 'Omar El Fassi', '+212600112233', 35.00, 'IN_TRANSIT', CURDATE() + INTERVAL 1 DAY, NOW() - INTERVAL 4 HOUR)
ON DUPLICATE KEY UPDATE updated_at = NOW();

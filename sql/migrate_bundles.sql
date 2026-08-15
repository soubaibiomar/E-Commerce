-- =============================================================================
-- Migration: Phase 13 — AI Product Bundles & Dynamic Personalization
-- =============================================================================

USE shopping;

CREATE TABLE IF NOT EXISTS product_bundles (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    bundle_name VARCHAR(128) NOT NULL,
    main_product_id INT NOT NULL,
    bundled_product_ids VARCHAR(255) NOT NULL, -- e.g. "2,3"
    discount_percentage DECIMAL(5, 2) NOT NULL DEFAULT 10.00,
    status ENUM('ACTIVE', 'INACTIVE') NOT NULL DEFAULT 'ACTIVE',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_main_product (main_product_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Seed initial test bundles
INSERT INTO product_bundles (bundle_name, main_product_id, bundled_product_ids, discount_percentage, status)
VALUES
('MacBook Pro M3 Executive Bundle (Laptop + Bag + Mouse)', 1, '2,3', 12.50, 'ACTIVE')
ON DUPLICATE KEY UPDATE status = 'ACTIVE';

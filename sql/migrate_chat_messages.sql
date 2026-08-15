-- =============================================================================
-- Migration: Phase 10 — Omnichannel Live Chat Messages
-- =============================================================================

USE shopping;

CREATE TABLE IF NOT EXISTS chat_messages (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    ticket_id BIGINT NULL,
    session_id VARCHAR(128) NOT NULL,
    sender_type ENUM('CUSTOMER', 'AI_AGENT', 'STAFF') NOT NULL,
    sender_name VARCHAR(128) NOT NULL,
    channel VARCHAR(32) NOT NULL DEFAULT 'WEB',
    message TEXT NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_session (session_id),
    INDEX idx_ticket (ticket_id),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Seed initial conversation history for existing test ticket #1 & #2
INSERT INTO chat_messages (ticket_id, session_id, sender_type, sender_name, channel, message, created_at)
VALUES 
(1, 'sess_cust_101', 'CUSTOMER', 'Yassine B.', 'WHATSAPP', 'Salam, 3afak bghit n-3ref wach kayn chi promo 3la had laptop?', NOW() - INTERVAL 15 MINUTE),
(1, 'sess_cust_101', 'AI_AGENT', 'ZeyTech AI Agent', 'WHATSAPP', 'Marhaba Yassine! 3ndna remise dyal 5% ila khditi m3ah sacoche.', NOW() - INTERVAL 14 MINUTE),
(1, 'sess_cust_101', 'CUSTOMER', 'Yassine B.', 'WHATSAPP', 'Bghit n-annuler commande #1042 hit t3atlat chwia.', NOW() - INTERVAL 10 MINUTE),
(2, 'sess_cust_102', 'CUSTOMER', 'Fatima Zahra', 'TELEGRAM', 'Ch7al ghadi yakhod dyal l-waqt bach toussal l Rabat?', NOW() - INTERVAL 25 MINUTE),
(2, 'sess_cust_102', 'AI_AGENT', 'ZeyTech AI Agent', 'TELEGRAM', 'Livraison l Rabat katakhod bin 24h tal 48h max.', NOW() - INTERVAL 24 MINUTE);

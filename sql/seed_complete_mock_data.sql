-- =============================================================================
-- ZeyTech AI Commerce OS — Complete Production Mock Data Seeder
-- Populates all 16 tables with realistic Moroccan enterprise commerce data
-- =============================================================================

USE shopping;

SET FOREIGN_KEY_CHECKS = 0;

-- 1. CATEGORIES & SUBCATEGORIES
TRUNCATE TABLE category;
INSERT INTO category (id, categoryName, categoryDescription, creationDate) VALUES
(1, 'Laptops & Computers', 'High-performance workstations, ultrabooks, and desktop computing.', NOW()),
(2, 'Smartphones & Tablets', 'Flagship 5G mobile devices, foldables, and creative tablets.', NOW()),
(3, 'Audio & Acoustics', 'Noise-canceling headphones, studio monitors, and audiophile gear.', NOW()),
(4, 'Smart Wearables', 'Smartwatches, fitness rings, and biometric health trackers.', NOW()),
(5, 'Gaming & Consoles', 'Next-gen consoles, VR headsets, and high-framerate gaming rigs.', NOW()),
(6, 'Smart Office & Peripherals', 'Ergonomic workspace furniture, 4K reference monitors, and docks.', NOW());

TRUNCATE TABLE subcategory;
INSERT INTO subcategory (id, categoryid, subcategory, creationDate) VALUES
(1, 1, 'MacBooks & macOS', NOW()),
(2, 1, 'Windows Ultrabooks', NOW()),
(3, 2, 'iOS Flagships', NOW()),
(4, 2, 'Android Flagships', NOW()),
(5, 3, 'Over-Ear ANC Headphones', NOW()),
(6, 3, 'True Wireless Earbuds', NOW()),
(7, 4, 'Smartwatches', NOW()),
(8, 5, 'Consoles & VR', NOW()),
(9, 6, '4K Pro Displays', NOW()),
(10, 6, 'Ergonomic Seating', NOW());

-- 2. PRODUCTS (Flagship Catalog with Full Specs JSON)
TRUNCATE TABLE products;
INSERT INTO products (id, category, subCategory, productName, productCompany, productPrice, productPriceBeforeDiscount, productDescription, specifications, productImage1, productImage2, productImage3, shippingCharge, productAvailability, postingDate) VALUES
(1, 1, 1, 'Apple MacBook Pro 16" M3 Max', 'Apple', 3499.00, 3899.00, 
 'The most powerful MacBook Pro ever built. M3 Max 16-Core CPU, 40-Core GPU, 48GB Unified Memory, 1TB SSD. Liquid Retina XDR 120Hz display with 22h battery life. Authentic Moroccan keyboard layout (AZERTY/QWERTY).',
 '{"Processor":"Apple M3 Max (16-core CPU, 40-core GPU)","RAM":"48GB Unified Memory","Storage":"1TB Superfast SSD","Display":"16.2-inch Liquid Retina XDR (3456x2234, 120Hz ProMotion)","Battery":"Up to 22 hours","Keyboard Layout":"French AZERTY / English QWERTY","Hub Location":"Casablanca Hub-A1","Warranty":"1 Year Apple Official Local Warranty"}',
 'macbook_pro16.jpg', 'macbook_pro16_side.jpg', 'macbook_pro16_open.jpg', 0.00, 'In Stock', NOW()),

(2, 1, 2, 'Dell XPS 15 9530 OLED Touch', 'Dell', 2299.00, 2599.00,
 'Premium Windows workstation. Intel Core i9-13900H, RTX 4070 8GB, 32GB DDR5, 1TB NVMe. 3.5K OLED InfinityEdge touch display with CNC aluminum and carbon fiber chassis.',
 '{"Processor":"Intel Core i9-13900H (14 Cores)","Graphics":"NVIDIA GeForce RTX 4070 (8GB GDDR6)","RAM":"32GB DDR5 4800MHz","Storage":"1TB PCIe 4.0 NVMe SSD","Display":"15.6-inch 3.5K (3456x2160) OLED Touchscreen","Weight":"1.92 kg","Warranty":"1 Year Dell ProSupport Morocco"}',
 'dell_xps15.jpg', 'dell_xps15_ports.jpg', 'dell_xps15_keyboard.jpg', 0.00, 'In Stock', NOW()),

(3, 2, 3, 'Apple iPhone 16 Pro Max 512GB', 'Apple', 1399.00, 1499.00,
 'Forged in Titanium. A18 Pro chip, 48MP Fusion camera with 5x telephoto zoom, Camera Control button, and Grade 5 Titanium finish. Full eSIM & Physical nano-SIM support for Inwi, Maroc Telecom & Orange.',
 '{"Processor":"Apple A18 Pro Chip with 6-core GPU","Display":"6.9-inch Super Retina XDR with Always-On (1-120Hz)","Camera System":"48MP Fusion + 48MP Ultra-Wide + 12MP 5x Telephoto","Material":"Grade 5 Titanium with Textured Matte Glass","Connectivity":"5G, Wi-Fi 7, Dual SIM (nano-SIM + eSIM)","Color":"Desert Titanium"}',
 'iphone16_pro.jpg', 'iphone16_pro_back.jpg', 'iphone16_pro_camera.jpg', 0.00, 'In Stock', NOW()),

(4, 2, 4, 'Samsung Galaxy S25 Ultra 512GB', 'Samsung', 1299.00, 1419.00,
 'Galaxy AI flagship with integrated S-Pen. Snapdragon 8 Elite for Galaxy, 200MP Quad Telephoto Camera, 6.8" Dynamic AMOLED 2X flat display, 5000mAh battery with 45W fast charging.',
 '{"Processor":"Snapdragon 8 Elite for Galaxy (3nm)","RAM":"12GB LPDDR5X","Storage":"512GB UFS 4.0","Display":"6.8-inch Dynamic AMOLED 2X (3120x1440, 2600 nits)","Stylus":"Embedded S-Pen with Bluetooth Air Actions","Color":"Titanium Gray"}',
 'samsung_s25_ultra.jpg', 'samsung_s25_side.jpg', 'samsung_s25_pen.jpg', 0.00, 'In Stock', NOW()),

(5, 3, 5, 'Sony WH-1000XM5 Wireless ANC', 'Sony', 399.00, 449.00,
 'Industry-leading noise cancellation powered by Dual Processor V1 and HD QN1. 8 microphones for crystal-clear calls, 30h battery life, LDAC Hi-Res Audio wireless support.',
 '{"Noise Cancellation":"Integrated Processor V1 + HD QN1 with 8 Microphones","Battery Life":"30 Hours (ANC On), 40 Hours (ANC Off)","Charging":"3 min charge = 3 hours playback (USB-PD)","Audio Codecs":"LDAC, AAC, SBC, Hi-Res Audio Wireless","Weight":"250g Ultra-lightweight"}',
 'sony_wh1000xm5.jpg', 'sony_wh1000xm5_case.jpg', 'sony_wh1000xm5_folded.jpg', 0.00, 'In Stock', NOW()),

(6, 3, 6, 'Apple AirPods Max Wireless', 'Apple', 549.00, 599.00,
 'Computational audio with Apple H1 chip in each ear cup. Custom acoustic design, Active Noise Cancellation with Transparency mode, Personalized Spatial Audio with dynamic head tracking.',
 '{"Chipset":"Dual Apple H1 Audio Processors","Audio Tech":"Custom Dynamic Driver, Active Noise Cancellation, Spatial Audio","Materials":"Anodized Aluminum Cups, Breathable Knit Mesh Canopy","Battery":"20 Hours with ANC & Spatial Audio enabled","Color":"Space Gray"}',
 'airpods_max.jpg', 'airpods_max_smart_case.jpg', 'airpods_max_canopy.jpg', 0.00, 'In Stock', NOW()),

(7, 4, 7, 'Apple Watch Ultra 2 GPS + Cellular', 'Apple', 799.00, 849.00,
 'The ultimate sports and adventure watch. 49mm aerospace-grade titanium case, 3000-nit display, dual-frequency precision GPS, Depth gauge, and up to 72 hours in Low Power Mode.',
 '{"Case Size":"49mm Aerospace Titanium Case (100m Water Resistance)","Display":"Always-On Retina OLED (3000 nits Peak Brightness)","Battery":"36 hours normal, up to 72 hours Low Power Mode","Sensors":"ECG, Blood Oxygen, Depth Gauge & Water Temp Sensor","Band":"Orange Ocean Band"}',
 'apple_watch_ultra2.jpg', 'apple_watch_ultra2_side.jpg', 'apple_watch_ultra2_band.jpg', 0.00, 'In Stock', NOW()),

(8, 5, 8, 'Sony PlayStation 5 Pro 2TB Console', 'Sony', 699.00, 749.00,
 'PlayStation Spectral Super Resolution (PSSR) AI upscaling, Advanced Ray Tracing, 2TB high-speed SSD, and 60fps locked 4K gaming performance with DualSense Wireless Controller.',
 '{"GPU Architecture":"Upgraded RDNA Architecture (67% More Compute Units)","Storage":"2TB Custom NVMe SSD","Upscaling":"PlayStation Spectral Super Resolution (PSSR) AI Machine Learning","Controller":"DualSense Wireless Controller with Haptic Feedback","Resolution":"4K at 60/120 FPS"}',
 'ps5_pro.jpg', 'ps5_pro_controller.jpg', 'ps5_pro_vertical.jpg', 0.00, 'In Stock', NOW()),

(9, 6, 9, 'Dell UltraSharp 34" Curved 4K Monitor', 'Dell', 949.00, 1099.00,
 'IPS Black technology with 2000:1 contrast ratio. WQHD (3440x1440) 1900R curve, 90W USB-C hub with RJ45 Ethernet, integrated KVM switch and built-in dual 5W speakers.',
 '{"Screen Size":"34.14-inch Ultrawide Curved (1900R)","Resolution":"WQHD 3440 x 1440 at 60Hz","Panel Tech":"IPS Black (2000:1 Contrast Ratio, 98% DCI-P3)","Connectivity":"USB-C with 90W Power Delivery, DisplayPort 1.4, HDMI 2.1, RJ45","Features":"Built-in KVM Switch & Picture-by-Picture"}',
 'dell_u3423we.jpg', 'dell_u3423we_back.jpg', 'dell_u3423we_ports.jpg', 0.00, 'In Stock', NOW()),

(10, 6, 10, 'Herman Miller Aeron Ergonomic Chair', 'Herman Miller', 1395.00, 1550.00,
 'The benchmark for ergonomic seating. 8Z Pellicle breathable suspension membrane, PostureFit SL adjustable spinal support, fully adjustable armrests, and 12-year 24/7 warranty.',
 '{"Size":"Size B (Medium)","Material":"Pellicle 8Z Elastomeric Mesh (Mineral / Polished Aluminum)","Support System":"PostureFit SL Dual Adjustable Lumbar Support","Tilt Mechanism":"Harmonic 2 Tilt with Forward Seat Angle & Tilt Limiter","Warranty":"12-Year Herman Miller Full Warranty"}',
 'herman_miller_aeron.jpg', 'herman_miller_aeron_back.jpg', 'herman_miller_aeron_arm.jpg', 0.00, 'In Stock', NOW());

-- 3. INVENTORY (3-State Stock at Casablanca Central Hub-A1)
TRUNCATE TABLE inventory;
INSERT INTO inventory (product_id, available_qty, reserved_qty, sold_qty, updated_at) VALUES
(1, 18, 2, 45, NOW()),
(2, 12, 1, 28, NOW()),
(3, 35, 4, 112, NOW()),
(4, 25, 2, 84, NOW()),
(5, 50, 5, 140, NOW()),
(6, 20, 2, 65, NOW()),
(7, 28, 3, 72, NOW()),
(8, 15, 2, 95, NOW()),
(9, 14, 1, 38, NOW()),
(10, 8, 1, 24, NOW());

-- 4. CUSTOMERS & USERS (Realistic Moroccan Personas)
TRUNCATE TABLE users;
INSERT INTO users (id, name, email, contactno, password, shippingAddress, shippingCity, shippingState, shippingPincode, billingAddress, billingCity, billingState, billingPincode, regDate) VALUES
(1, 'Omar El Fassi', 'omar.elfassi@gmail.com', '+212661234567', MD5('CustomerPass2026!'), '45 Boulevard d Anfa, Apt 12', 'Casablanca', 'Casablanca-Settat', '20000', '45 Boulevard d Anfa, Apt 12', 'Casablanca', 'Casablanca-Settat', '20000', NOW() - INTERVAL 45 DAY),
(2, 'Fatima Zahra Bennani', 'f.bennani@techmorocco.ma', '+212662345678', MD5('CustomerPass2026!'), '18 Avenue Mohammed VI, Villa Jasmine', 'Rabat', 'Rabat-Salé-Kénitra', '10000', '18 Avenue Mohammed VI, Villa Jasmine', 'Rabat', 'Rabat-Salé-Kénitra', '10000', NOW() - INTERVAL 30 DAY),
(3, 'Mehdi Chraibi', 'mehdi.chraibi@marrakech-tech.com', '+212663456789', MD5('CustomerPass2026!'), '12 Rue des Banques, Gueliz', 'Marrakech', 'Marrakech-Safi', '40000', '12 Rue des Banques, Gueliz', 'Marrakech', 'Marrakech-Safi', '40000', NOW() - INTERVAL 20 DAY),
(4, 'Salma Idrissi', 'salma.idrissi@tanger-dev.ma', '+212664567890', MD5('CustomerPass2026!'), '88 Boulevard Pasteur, 4eme Etage', 'Tanger', 'Tanger-Tétouan-Al Hoceïma', '90000', '88 Boulevard Pasteur, 4eme Etage', 'Tanger', 'Tanger-Tétouan-Al Hoceïma', '90000', NOW() - INTERVAL 10 DAY),
(5, 'Yassine Alaoui', 'yassine.alaoui@agadir-surf.com', '+212665678901', MD5('CustomerPass2026!'), '24 Avenue Hassan II, Sonaba', 'Agadir', 'Souss-Massa', '80000', '24 Avenue Hassan II, Sonaba', 'Agadir', 'Souss-Massa', '80000', NOW() - INTERVAL 5 DAY);

-- 5. ORDERS (All Statuses: In Transit, Delivered, Processing, Refunded)
TRUNCATE TABLE orders;
INSERT INTO orders (id, userId, productId, quantity, orderDate, paymentMethod, orderStatus, status, paymentStatus, totalAmount) VALUES
(1, 1, 1, 1, NOW() - INTERVAL 1 DAY, 'Credit Card / CMI', 'IN_TRANSIT', 'shipped', 'PAID', 3499.00),
(2, 2, 3, 1, NOW() - INTERVAL 2 DAY, 'Credit Card / CMI', 'DELIVERED', 'confirmed', 'PAID', 1399.00),
(3, 3, 5, 2, NOW() - INTERVAL 3 DAY, 'Cash on Delivery / COD', 'DELIVERED', 'confirmed', 'PAID', 798.00),
(4, 4, 8, 1, NOW() - INTERVAL 4 DAY, 'Credit Card / CMI', 'PROCESSING', 'pending', 'PAID', 699.00),
(5, 5, 7, 1, NOW() - INTERVAL 5 DAY, 'Bank Transfer', 'PENDING_REFUND', 'pending_refund', 'PAID', 799.00);

-- 6. CARRIER SHIPMENTS (CTM, Amana, Aramex across Moroccan Regions)
TRUNCATE TABLE shipping_shipments;
INSERT INTO shipping_shipments (id, order_id, carrier, tracking_number, region, city, recipient_name, recipient_phone, shipping_cost_mad, status, estimated_delivery, created_at) VALUES
(1, 1, 'CTM Messagerie', 'CTM-MA-8849102', 'Casablanca-Settat', 'Casablanca', 'Omar El Fassi', '+212661234567', 35.00, 'IN_TRANSIT', CURDATE() + INTERVAL 1 DAY, NOW() - INTERVAL 12 HOUR),
(2, 2, 'Amana Express', 'AMN-MA-9102834', 'Rabat-Salé-Kénitra', 'Rabat', 'Fatima Zahra Bennani', '+212662345678', 35.00, 'DELIVERED', CURDATE() - INTERVAL 1 DAY, NOW() - INTERVAL 48 HOUR),
(3, 3, 'Aramex Morocco', 'ARX-MA-7201948', 'Marrakech-Safi', 'Marrakech', 'Mehdi Chraibi', '+212663456789', 45.00, 'DELIVERED', CURDATE() - INTERVAL 2 DAY, NOW() - INTERVAL 72 HOUR),
(4, 4, 'CTM Messagerie', 'CTM-MA-3920194', 'Tanger-Tétouan-Al Hoceïma', 'Tanger', 'Salma Idrissi', '+212664567890', 40.00, 'LABEL_CREATED', CURDATE() + INTERVAL 2 DAY, NOW() - INTERVAL 4 HOUR),
(5, 5, 'Amana Express', 'AMN-MA-4820192', 'Souss-Massa', 'Agadir', 'Yassine Alaoui', '+212665678901', 45.00, 'IN_TRANSIT', CURDATE() + INTERVAL 2 DAY, NOW() - INTERVAL 16 HOUR);

-- 7. OPERATIONS MANAGER APPROVAL QUEUE (> 5,000 MAD & Fraud Alerts)
TRUNCATE TABLE ops_approval_queue;
INSERT INTO ops_approval_queue (id, trace_id, customer, channel, amount_mad, reason, flags, status, action_type, target_id, approved_by, decided_at, created_at) VALUES
(1, 'tr_appr_90182', 'Omar El Fassi', 'WHATSAPP', 35689.80, 'High-Value Order: MacBook Pro 16" M3 Max (> 5,000 MAD threshold)', '["HIGH_VALUE_TRANSACTION", "EXPRESS_CTM"]', 'PENDING_APPROVAL', 'ORDER_DISPATCH', 1, NULL, NULL, NOW() - INTERVAL 2 HOUR),
(2, 'tr_appr_90183', 'Karim Mansouri', 'WEB', 7129.80, 'Automated Risk Engine Flag: Score 80/100 (HIGH Risk - New Unrecognized Device)', '["NEW_DEVICE", "VELOCITY_SPIKE"]', 'PENDING_APPROVAL', 'HIGH_RISK_ORDER', 4, NULL, NULL, NOW() - INTERVAL 1 HOUR);

-- 8. OPERATIONS SUPPORT ESCALATION QUEUE (Low Confidence < 0.70 & Sentiment)
TRUNCATE TABLE ops_escalation_queue;
INSERT INTO ops_escalation_queue (id, trace_id, customer, channel, confidence, message, status, claimed_by, claimed_at, created_at) VALUES
(1, 'tr_esc_4401', 'Yassine Alaoui', 'WHATSAPP', 0.58, 'Bghit n-chouf wach momkin n-beddel l-couleur dyal Apple Watch mn Orange l Black?', 'OPEN', NULL, NULL, NOW() - INTERVAL 45 MINUTE),
(2, 'tr_esc_4402', 'Salma Idrissi', 'TELEGRAM', 0.62, 'Est-ce que la livraison CTM à Tanger livre directement à domicile ou en agence?', 'CLAIMED', 3, NOW() - INTERVAL 10 MINUTE, NOW() - INTERVAL 30 MINUTE);

-- 9. LIVE CHAT MESSAGES (Multi-turn Darija & French Support Threads)
TRUNCATE TABLE chat_messages;
INSERT INTO chat_messages (id, ticket_id, session_id, sender_type, sender_name, channel, message, created_at) VALUES
(1, 1, 'sess_yassine_01', 'CUSTOMER', 'Yassine Alaoui', 'WHATSAPP', 'Salam! Bghit n-chouf wach momkin n-beddel l-couleur dyal Apple Watch mn Orange l Black?', NOW() - INTERVAL 45 MINUTE),
(2, 1, 'sess_yassine_01', 'AI_AGENT', 'AI Supervisor', 'WHATSAPP', 'Marhaba Yassine! Ghadi n-connectik m3a un agent human mn support client pour verifier l-stock.', NOW() - INTERVAL 44 MINUTE),
(3, 1, 'sess_yassine_01', 'STAFF', 'Hamza Support', 'WHATSAPP', 'Salam Yassine! M3ak Hamza mn support. Oui bien sûr, 3ndna le modèle Black disponible f Casablanca Hub-A1.', NOW() - INTERVAL 20 MINUTE),
(4, 2, 'sess_salma_02', 'CUSTOMER', 'Salma Idrissi', 'TELEGRAM', 'Est-ce que la livraison CTM à Tanger livre directement à domicile ou en agence?', NOW() - INTERVAL 30 MINUTE),
(5, 2, 'sess_salma_02', 'STAFF', 'Omar El Fassi (Support)', 'TELEGRAM', 'Bonjour Salma! Oui, CTM Messagerie livre directement à votre adresse à Tanger sous 24-48h avec suivi par SMS.', NOW() - INTERVAL 8 MINUTE);

-- 10. PRODUCT BUNDLES (Dynamic Combos with MAD Savings)
TRUNCATE TABLE product_bundles;
INSERT INTO product_bundles (id, bundle_name, main_product_id, bundled_product_ids, discount_percentage, status, created_at) VALUES
(1, 'MacBook Pro Executive Studio Bundle (Laptop + Sony ANC + Dell 4K Display)', 1, '5,9', 15.00, 'ACTIVE', NOW()),
(2, 'iPhone 16 Pro Ultimate Mobile Pack (iPhone + AirPods Max + Apple Watch Ultra)', 3, '6,7', 12.50, 'ACTIVE', NOW()),
(3, 'Ultra Pro Gaming Battlestation (PS5 Pro + Sony ANC Headset)', 8, '5', 10.00, 'ACTIVE', NOW());

-- 11. FRAUD RISK SCORES (Heuristic Breakdown)
TRUNCATE TABLE fraud_risk_scores;
INSERT INTO fraud_risk_scores (id, order_id, risk_score, risk_level, risk_factors, action_taken, created_at) VALUES
(1, 1, 15, 'LOW', '["DOMESTIC_CARD", "VERIFIED_DEVICE", "MATCHING_BILLING_ADDRESS"]', 'AUTO_APPROVED', NOW() - INTERVAL 24 HOUR),
(2, 2, 10, 'LOW', '["REPEAT_CUSTOMER_VIP", "VERIFIED_OTP"]', 'AUTO_APPROVED', NOW() - INTERVAL 48 HOUR),
(3, 4, 80, 'HIGH', '["NEW_UNRECOGNIZED_DEVICE", "HIGH_VALUE_TRANSACTION (> 5000 MAD)", "VELOCITY_SPIKE"]', 'FLAGGED_FOR_REVIEW', NOW() - INTERVAL 1 HOUR);

-- 12. INVENTORY REORDERS (Demand Forecasting PO Replenishments)
TRUNCATE TABLE inventory_reorders;
INSERT INTO inventory_reorders (id, product_id, quantity_ordered, supplier_name, cost_mad, status, created_at) VALUES
(1, 1, 20, 'Apple Official Direct Distribution', 550000.00, 'RECEIVED', NOW() - INTERVAL 7 DAY),
(2, 3, 50, 'Apple Official Direct Distribution', 520000.00, 'RECEIVED', NOW() - INTERVAL 5 DAY),
(3, 8, 30, 'Sony PlayStation Europe & North Africa', 165000.00, 'ORDERED', NOW() - INTERVAL 1 DAY);

-- 13. CRM MARKETING CAMPAIGNS (Personalized Promos & Omnichannel Blasts)
TRUNCATE TABLE crm_campaigns;
INSERT INTO crm_campaigns (id, campaign_name, target_segment, promo_code, discount_percentage, channel, messages_sent, created_at) VALUES
(1, 'Casablanca Summer Tech VIP Drop', 'VIP_HIGH_SPEND', 'ZEY-VIP-SUMMER26', 15.00, 'WHATSAPP', 145, NOW() - INTERVAL 3 DAY),
(2, 'Apple Ecosystem Upgrade Campaign', 'ACTIVE_REGULAR', 'ZEY-APPLE-UPGRADE', 10.00, 'TELEGRAM', 320, NOW() - INTERVAL 1 DAY),
(3, 'Welcome Moroccan Tech Enthusiasts', 'NEW_LEAD', 'ZEY-MARHABA-10', 10.00, 'WHATSAPP', 580, NOW() - INTERVAL 6 HOUR);

-- 14. AUDIT LEDGER (Immutable Traceability)
TRUNCATE TABLE audit_logs;
INSERT INTO audit_logs (id, trace_id, actor, channel, sender_id, decision, confidence, reply, created_at) VALUES
(1, 'tr_init_001', 'SYSTEM_SUPERVISOR', 'SYSTEM', 'system_daemon', 'PLATFORM_ONLINE', 1.00, 'Casablanca Hub-A1 online. Multi-agent cluster nominal.', NOW() - INTERVAL 12 HOUR),
(2, 'tr_ord_001', 'LOGISTICS_ENGINE', 'WEB', 'order_1', 'WAYBILL_GENERATED', 1.00, 'Waybill CTM-MA-8849102 created with CTM Messagerie for Casablanca (Casablanca-Settat). Cost: 35.00 MAD.', NOW() - INTERVAL 10 HOUR),
(3, 'tr_msg_001', 'Dr. Zeyad (Admin)', 'WHATSAPP', 'staff_1', 'STAFF_REPLY_SENT', 1.00, 'Confirmed stock allocation for Order #1.', NOW() - INTERVAL 2 HOUR);

SET FOREIGN_KEY_CHECKS = 1;

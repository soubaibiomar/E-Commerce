USE shopping;

SET FOREIGN_KEY_CHECKS = 0;

DELETE FROM products WHERE id > 0;
DELETE FROM inventory WHERE product_id > 0;

INSERT INTO subcategory (id, categoryid, subcategory, creationDate) VALUES
(11, 1, 'Gaming Laptops & Workstations', NOW()),
(12, 2, 'Creative Pro Tablets', NOW()),
(13, 3, 'Smart Speakers & Home Theater', NOW()),
(14, 5, 'Gaming Keyboards & Custom Gear', NOW()),
(15, 6, 'Cameras & Cinema Drones', NOW())
ON DUPLICATE KEY UPDATE subcategory = VALUES(subcategory);

INSERT INTO products (
  id, category, subCategory, productName, productCompany, productPrice, productPriceBeforeDiscount,
  productDescription, productImage1, productImage2, productImage3, shippingCharge, productAvailability,
  specifications, productModel, ficheTechnique, stockQuantity, warehouseLocation, costPrice, viewsCount,
  stockAvailable, stockReserved, stockSold, name, description, price, currency, fiche_technique
) VALUES (
  1, 1, 1, 'Apple MacBook Pro 16" M3 Max', 'Apple',
  3499, 3999, 'Apple M3 Max chip with 16-core CPU, 40-core GPU, 48GB Unified Memory, 1TB SSD Storage. 16.2-inch Liquid Retina XDR display with ProMotion 120Hz.',
  'img_main.jpg', 'img_angle.jpg', 'img_detail.jpg',
  0, 'In Stock', '{"chip":"Apple M3 Max","cpu":"16-Core","gpu":"40-Core","ram":"48GB Unified","storage":"1TB NVMe SSD","display":"16.2 Liquid Retina XDR 120Hz","ports":["3x Thunderbolt 4","HDMI","SDXC","MagSafe 3"],"weight":"2.16 kg","color":"Space Black"}',
  'MBP16-M3MAX-48-1TB', 'Fiche Technique Officielle:
- Processeur : Puce Apple M3 Max (16 cœurs CPU / 40 cœurs GPU)
- Mémoire Unifiée : 48 Go
- Stockage : 1 To SSD ultra-rapide
- Écran : Liquid Retina XDR 16,2 pouces avec ProMotion 120 Hz
- Autonomie : Jusqu\'à 22 heures
- Garantie : 1 an constructeur Apple Maroc
- توصيل سريع لجميع المدن المغربية مع إمكانية الدفع عند الاستلام.', 100,
  'Casablanca Central Hub-A1', 2800, 1420, 58,
  1, 41, 'Apple MacBook Pro 16" M3 Max', 'Apple M3 Max chip with 16-core CPU, 40-core GPU, 48GB Unified Memory, 1TB SSD Storage.',
  3499, 'USD', '{"processor":"Apple M3 Max 16-Core","memory":"48GB Unified Memory","storage":"1TB SSD","display":"16.2 Liquid Retina XDR 120Hz","warranty":"1 Year Apple Direct Maroc"}'
);

INSERT INTO inventory (product_id, available_qty, reserved_qty, sold_qty)
VALUES (1, 58, 1, 41);

INSERT INTO products (
  id, category, subCategory, productName, productCompany, productPrice, productPriceBeforeDiscount,
  productDescription, productImage1, productImage2, productImage3, shippingCharge, productAvailability,
  specifications, productModel, ficheTechnique, stockQuantity, warehouseLocation, costPrice, viewsCount,
  stockAvailable, stockReserved, stockSold, name, description, price, currency, fiche_technique
) VALUES (
  2, 1, 2, 'Dell XPS 15 9530 OLED Touch', 'Dell',
  2299, 2599, 'Intel Core i9-13900H 14-Core, NVIDIA GeForce RTX 4070 8GB GDDR6, 32GB DDR5 RAM, 1TB PCIe NVMe SSD, 15.6" 3.5K (3456x2160) OLED InfinityEdge Touch Display.',
  'img_main.jpg', 'img_angle.jpg', 'img_detail.jpg',
  0, 'In Stock', '{"processor":"Intel Core i9-13900H","graphics":"NVIDIA RTX 4070 8GB","ram":"32GB DDR5 4800MHz","storage":"1TB M.2 PCIe NVMe","display":"15.6 3.5K OLED Touch","chassis":"CNC Machined Aluminum with Carbon Fiber Palmrest","weight":"1.92 kg"}',
  'XPS-15-9530-OLED', 'Fiche Technique:
- Processeur : Intel Core i9-13900H
- Carte Graphique : NVIDIA GeForce RTX 4070 8GB
- RAM : 32 Go DDR5
- Stockage : 1 To SSD
- Écran : 15.6 3.5K OLED Tactile
- Garantie : 1 an Dell Maroc', 60,
  'Casablanca Central Hub-A1', 1850, 890, 45,
  0, 15, 'Dell XPS 15 9530 OLED Touch', 'Intel Core i9-13900H, RTX 4070, 32GB RAM, 1TB SSD, 3.5K OLED Touch.',
  2299, 'USD', '{"processor":"Intel Core i9-13900H","gpu":"NVIDIA RTX 4070 8GB","ram":"32GB DDR5","storage":"1TB SSD","display":"15.6 3.5K OLED Touch"}'
);

INSERT INTO inventory (product_id, available_qty, reserved_qty, sold_qty)
VALUES (2, 45, 0, 15);

INSERT INTO products (
  id, category, subCategory, productName, productCompany, productPrice, productPriceBeforeDiscount,
  productDescription, productImage1, productImage2, productImage3, shippingCharge, productAvailability,
  specifications, productModel, ficheTechnique, stockQuantity, warehouseLocation, costPrice, viewsCount,
  stockAvailable, stockReserved, stockSold, name, description, price, currency, fiche_technique
) VALUES (
  3, 2, 3, 'Apple iPhone 16 Pro Max 512GB', 'Apple',
  1599, 1749, 'A18 Pro Bionic Chip, Grade 5 Titanium design with textured matte glass back, 6.9-inch Super Retina XDR display with ProMotion and Always-On, 48MP Fusion camera system with 5x optical zoom.',
  'img_main.jpg', 'img_angle.jpg', 'img_detail.jpg',
  0, 'In Stock', '{"chip":"Apple A18 Pro","display":"6.9 Super Retina XDR OLED 120Hz ProMotion","storage":"512GB NVMe","camera":"48MP Main + 48MP Ultra Wide + 12MP 5x Telephoto","material":"Grade 5 Titanium","battery":"Up to 33 hours video playback","color":"Natural Titanium"}',
  'IPH16PM-512-NAT', 'Fiche Technique:
- Puce : Apple A18 Pro avec Neural Engine 16 cœurs
- Stockage : 512 Go
- Écran : 6,9 pouces Super Retina XDR OLED ProMotion
- Caméra : Triple capteur 48 MP avec zoom optique 5x
- Châssis : Titane Grade 5 ultra-résistant
- هاتف أصلي 100% مع ضمان معتمد في المغرب.', 85,
  'Casablanca Central Hub-A1', 1250, 3200, 68,
  0, 17, 'Apple iPhone 16 Pro Max 512GB', 'A18 Pro Bionic Chip, Grade 5 Titanium design, 6.9-inch display, 48MP camera.',
  1599, 'USD', '{"chip":"Apple A18 Pro","storage":"512GB","display":"6.9 Super Retina XDR OLED 120Hz","camera":"48MP Triple Pro System"}'
);

INSERT INTO inventory (product_id, available_qty, reserved_qty, sold_qty)
VALUES (3, 68, 0, 17);

INSERT INTO products (
  id, category, subCategory, productName, productCompany, productPrice, productPriceBeforeDiscount,
  productDescription, productImage1, productImage2, productImage3, shippingCharge, productAvailability,
  specifications, productModel, ficheTechnique, stockQuantity, warehouseLocation, costPrice, viewsCount,
  stockAvailable, stockReserved, stockSold, name, description, price, currency, fiche_technique
) VALUES (
  4, 2, 4, 'Samsung Galaxy S25 Ultra 512GB', 'Samsung',
  1499, 1649, 'Snapdragon 8 Gen 4 for Galaxy, 6.8-inch Dynamic AMOLED 2X 120Hz Anti-Reflective Display, 200MP Quad Telephoto Camera, Built-in S Pen, Titanium Armor Frame.',
  'img_main.jpg', 'img_angle.jpg', 'img_detail.jpg',
  0, 'In Stock', '{"processor":"Snapdragon 8 Gen 4 for Galaxy","ram":"16GB LPDDR5X","storage":"512GB UFS 4.0","display":"6.8 Dynamic AMOLED 2X 1-120Hz Gorilla Armor","camera":"200MP + 50MP + 50MP + 12MP","spen":"Embedded Bluetooth S Pen","battery":"5000mAh with 45W Fast Charging"}',
  'SM-S938B-512', 'Fiche Technique:
- Processeur : Qualcomm Snapdragon 8 Gen 4 for Galaxy
- RAM : 16 Go / Stockage : 512 Go
- Écran : 6,8 Dynamic AMOLED 2X avec verre Gorilla Armor antireflet
- Appareil Photo : 200 MP avec Galaxy AI
- Stylet S Pen intégré
- متوفر للتوصيل الفوري مع كفالة سامسونج المغرب.', 75,
  'Casablanca Central Hub-A1', 1150, 2100, 52,
  0, 23, 'Samsung Galaxy S25 Ultra 512GB', 'Snapdragon 8 Gen 4, 6.8-inch AMOLED 2X, 200MP Camera, S Pen.',
  1499, 'USD', '{"processor":"Snapdragon 8 Gen 4","ram":"16GB","storage":"512GB","camera":"200MP Quad AI Camera","spen":true}'
);

INSERT INTO inventory (product_id, available_qty, reserved_qty, sold_qty)
VALUES (4, 52, 0, 23);

INSERT INTO products (
  id, category, subCategory, productName, productCompany, productPrice, productPriceBeforeDiscount,
  productDescription, productImage1, productImage2, productImage3, shippingCharge, productAvailability,
  specifications, productModel, ficheTechnique, stockQuantity, warehouseLocation, costPrice, viewsCount,
  stockAvailable, stockReserved, stockSold, name, description, price, currency, fiche_technique
) VALUES (
  5, 3, 5, 'Sony WH-1000XM5 Wireless Noise Canceling', 'Sony',
  399, 449, 'Industry-leading noise canceling with two processors and 8 microphones, Auto NC Optimizer, Magnificent sound quality with 30mm carbon fiber driver unit, 30-hour battery life.',
  'img_main.jpg', 'img_angle.jpg', 'img_detail.jpg',
  0, 'In Stock', '{"driver":"30mm Carbon Fiber Composite","anc":"Integrated Processor V1 + HD Noise Canceling Processor QN1 (8 Microphones)","codecs":["LDAC","AAC","SBC"],"battery":"30 Hours (ANC On) / 40 Hours (ANC Off)","multipoint":"2 Devices Simultaneous","weight":"250g"}',
  'WH1000XM5-BLK', 'Fiche Technique:
- Réduction de Bruit : Double processeur HD QN1 + V1 avec 8 micros
- Autonomie : 30 heures avec réduction de bruit activée
- Audio Haute Résolution : Compatible LDAC et Hi-Res Wireless
- Confort : Cuir souple Soft Fit ultra-léger
- Casque audio haute fidélité avec livraison gratuite au Maroc.', 120,
  'Casablanca Central Hub-A1', 270, 1850, 95,
  0, 25, 'Sony WH-1000XM5 Wireless Noise Canceling', 'Industry-leading noise canceling headphones with 30-hour battery life.',
  399, 'USD', '{"driver":"30mm Carbon Fiber","anc":"Dual QN1 + V1 Engine","battery":"30 Hours","codecs":"LDAC / AAC"}'
);

INSERT INTO inventory (product_id, available_qty, reserved_qty, sold_qty)
VALUES (5, 95, 0, 25);

INSERT INTO products (
  id, category, subCategory, productName, productCompany, productPrice, productPriceBeforeDiscount,
  productDescription, productImage1, productImage2, productImage3, shippingCharge, productAvailability,
  specifications, productModel, ficheTechnique, stockQuantity, warehouseLocation, costPrice, viewsCount,
  stockAvailable, stockReserved, stockSold, name, description, price, currency, fiche_technique
) VALUES (
  6, 3, 5, 'Apple AirPods Max Space Gray', 'Apple',
  549, 599, 'Apple-designed dynamic driver provides high-fidelity audio. Active Noise Cancellation with Transparency mode. Spatial audio with dynamic head tracking for theater-like sound.',
  'img_main.jpg', 'img_angle.jpg', 'img_detail.jpg',
  0, 'In Stock', '{"driver":"40mm Apple Custom Dynamic","chip":"Apple H1 Headphone Chip (each ear cup)","anc":"Active Noise Cancellation + Transparency Mode","audio":"Personalized Spatial Audio with dynamic head tracking","materials":"Anodized Aluminum Cups, Stainless Steel Frame, Breathable Knit Mesh Canopy"}',
  'AIRPODS-MAX-SG', 'Fiche Technique:
- Transducteur dynamique conçu par Apple pour un son haute fidélité
- Réduction active du bruit et mode Transparence
- Audio spatial personnalisé avec suivi dynamique des mouvements de la tête
- Coussinets en maille respirante et arceau en acier inoxydable
- Autonomie : 20 heures d\'écoute.', 50,
  'Casablanca Central Hub-A1', 420, 1100, 38,
  0, 12, 'Apple AirPods Max Space Gray', 'High-fidelity audio with Active Noise Cancellation and Spatial Audio.',
  549, 'USD', '{"driver":"40mm Dynamic Driver","anc":true,"spatial_audio":true,"battery":"20 Hours"}'
);

INSERT INTO inventory (product_id, available_qty, reserved_qty, sold_qty)
VALUES (6, 38, 0, 12);

INSERT INTO products (
  id, category, subCategory, productName, productCompany, productPrice, productPriceBeforeDiscount,
  productDescription, productImage1, productImage2, productImage3, shippingCharge, productAvailability,
  specifications, productModel, ficheTechnique, stockQuantity, warehouseLocation, costPrice, viewsCount,
  stockAvailable, stockReserved, stockSold, name, description, price, currency, fiche_technique
) VALUES (
  7, 4, 7, 'Apple Watch Ultra 2 GPS + Cellular 49mm', 'Apple',
  799, 899, 'Rugged 49mm titanium case with sapphire front crystal. Precision dual-frequency GPS. 3000 nits Always-On Retina display. Up to 36 hours of battery life with normal use.',
  'img_main.jpg', 'img_angle.jpg', 'img_detail.jpg',
  0, 'In Stock', '{"case":"49mm Aerospace-Grade Titanium","display":"Always-On Retina OLED up to 3000 nits","chip":"S9 SiP with 64-bit dual-core processor","water_resistance":"100m water resistant / EN13319 dive certified (40m)","sensors":["ECG","Blood Oxygen","Depth Gauge","Water Temperature"],"connectivity":["LTE","Wi-Fi 4","Bluetooth 5.3","Dual-Frequency GPS"]}',
  'AW-ULTRA2-49-TIT', 'Fiche Technique:
- Boîtier : 49 mm en titane aérospatial
- Écran : Retina OLED toujours activé avec luminosité crête de 3 000 nits
- Puce : S9 SiP avec geste Toucher deux fois (Double Tap)
- Étanchéité : Jusqu\'à 100 m et certifiée plongée récréative 40 m
- Autonomie : Jusqu\'à 72 heures en mode économie.', 40,
  'Casablanca Central Hub-A1', 610, 950, 28,
  0, 12, 'Apple Watch Ultra 2 GPS + Cellular 49mm', 'Rugged 49mm titanium smartwatch with dual-frequency GPS and 3000 nits display.',
  799, 'USD', '{"case":"49mm Titanium","display":"3000 nits Retina OLED","water_resistance":"100m / Dive 40m","gps":"Dual-Frequency Precision"}'
);

INSERT INTO inventory (product_id, available_qty, reserved_qty, sold_qty)
VALUES (7, 28, 0, 12);

INSERT INTO products (
  id, category, subCategory, productName, productCompany, productPrice, productPriceBeforeDiscount,
  productDescription, productImage1, productImage2, productImage3, shippingCharge, productAvailability,
  specifications, productModel, ficheTechnique, stockQuantity, warehouseLocation, costPrice, viewsCount,
  stockAvailable, stockReserved, stockSold, name, description, price, currency, fiche_technique
) VALUES (
  8, 5, 8, 'Sony PlayStation 5 Pro 2TB', 'Sony',
  799, 899, 'PlayStation Spectral Super Resolution (PSSR) AI-driven upscaling, Advanced Ray Tracing, 2TB high-speed PCIe 4.0 NVMe SSD, 4K 120Hz & 8K HDR support, DualSense Wireless Controller included.',
  'img_main.jpg', 'img_angle.jpg', 'img_detail.jpg',
  0, 'In Stock', '{"gpu":"Upgraded RDNA Architecture with 67% more Compute Units","storage":"2TB Ultra-High Speed NVMe SSD","upscaling":"PlayStation Spectral Super Resolution (PSSR AI)","raytracing":"Advanced Ray Tracing Engine (2x-3x speed)","video_out":"HDMI 2.1 (Supports 4K 120Hz, 8K HDR, VRR)","controller":"DualSense with Haptic Feedback & Adaptive Triggers"}',
  'PS5-PRO-2TB-ED', 'Fiche Technique:
- Processeur Graphique : GPU boosté avec architecture RDNA avancée (+67% CU)
- Stockage : 2 To SSD NVMe ultra-rapide
- Technologie IA : PlayStation Spectral Super Resolution (PSSR)
- Ray Tracing avancé pour des reflets photoréalistes
- Manette sans fil DualSense avec retours haptiques incluse
- جهاز الألعاب الأقوى في العالم متوفر الآن في المغرب مع ضمان رسمي.', 60,
  'Casablanca Central Hub-A1', 620, 4500, 42,
  0, 18, 'Sony PlayStation 5 Pro 2TB', 'Next-gen console with PSSR AI upscaling, 2TB SSD, and Advanced Ray Tracing.',
  799, 'USD', '{"storage":"2TB NVMe SSD","upscaling":"PSSR AI","raytracing":"Advanced 3x RT","controller":"DualSense Haptic"}'
);

INSERT INTO inventory (product_id, available_qty, reserved_qty, sold_qty)
VALUES (8, 42, 0, 18);

INSERT INTO products (
  id, category, subCategory, productName, productCompany, productPrice, productPriceBeforeDiscount,
  productDescription, productImage1, productImage2, productImage3, shippingCharge, productAvailability,
  specifications, productModel, ficheTechnique, stockQuantity, warehouseLocation, costPrice, viewsCount,
  stockAvailable, stockReserved, stockSold, name, description, price, currency, fiche_technique
) VALUES (
  9, 6, 9, 'Dell UltraSharp 34" Curved USB-C Hub Monitor', 'Dell',
  949, 1099, 'WQHD 3440 x 1440 at 60Hz curved IPS Black display with 2000:1 contrast ratio. Built-in 90W USB-C Power Delivery, RJ45 Ethernet, and KVM switch for multi-system productivity.',
  'img_main.jpg', 'img_angle.jpg', 'img_detail.jpg',
  0, 'In Stock', '{"panel":"34.14 Curved IPS Black (1900R)","resolution":"WQHD 3440 x 1440 at 60 Hz","contrast":"2000:1 True Contrast Ratio","color_gamut":"100% sRGB, 98% DCI-P3","connectivity":["USB-C 90W PD","RJ45 Ethernet (2.5Gbps)","DisplayPort 1.4","2x HDMI 2.1","5x USB-A 10Gbps"],"kvm":"Integrated Auto KVM Switch with PbP/PiP"}',
  'U3423WE-CURVED', 'Fiche Technique:
- Dalle : 34,14" Incurvée IPS Black (1900R)
- Résolution : WQHD 3440 x 1440 avec ratio de contraste 2000:1
- Connectivité Hub : USB-C 90W avec port Ethernet RJ45 2.5 Gbps
- Fonctionnalités : Switch KVM automatique intégré pour contrôler 2 PC
- Écran idéal pour les ingénieurs, designers et traders à Casablanca.', 35,
  'Casablanca Central Hub-A1', 720, 680, 24,
  0, 11, 'Dell UltraSharp 34" Curved USB-C Hub Monitor', '34-inch WQHD Curved IPS Black monitor with 90W USB-C Hub and KVM switch.',
  949, 'USD', '{"panel":"34.14 Curved IPS Black","resolution":"WQHD 3440x1440","power_delivery":"90W USB-C","kvm":true}'
);

INSERT INTO inventory (product_id, available_qty, reserved_qty, sold_qty)
VALUES (9, 24, 0, 11);

INSERT INTO products (
  id, category, subCategory, productName, productCompany, productPrice, productPriceBeforeDiscount,
  productDescription, productImage1, productImage2, productImage3, shippingCharge, productAvailability,
  specifications, productModel, ficheTechnique, stockQuantity, warehouseLocation, costPrice, viewsCount,
  stockAvailable, stockReserved, stockSold, name, description, price, currency, fiche_technique
) VALUES (
  10, 6, 10, 'Herman Miller Aeron Ergonomic Chair', 'Herman Miller',
  1395, 1595, 'The benchmark for ergonomic seating. 8Z Pellicle breathable suspension membrane, PostureFit SL adjustable dual-pad lumbar support, Fully adjustable arms, Forward tilt limiter.',
  'img_main.jpg', 'img_angle.jpg', 'img_detail.jpg',
  0, 'In Stock', '{"material":"8Z Pellicle Elastomeric Suspension (Mineral/Graphite)","lumbar":"PostureFit SL Adjustable Sacral and Lumbar Pads","adjustments":["Pneumatic Height","Tilt Limiter with Forward Angle","3D Adjustable Armrests","Tension Control"],"caster":"Hard Floor & Carpet Multi-Surface Wheels","warranty":"12-Year Herman Miller Manufacturer Warranty"}',
  'AERON-SIZE-B-MIN', 'Fiche Technique:
- Suspension : Membrane 8Z Pellicle respirante brevetée
- Soutien Lombaire : PostureFit SL réglable pour un alignement optimal de la colonne vertébrale
- Accoudoirs : Réglables en hauteur, profondeur et angle pivotant
- Garantie Constructeur : 12 ans certifiée Herman Miller
- كرسي طبي مريح ومثالي للعمل المكتبي الطويل ومبرمجي الحاسوب.', 25,
  'Casablanca Central Hub-A1', 950, 840, 18,
  0, 7, 'Herman Miller Aeron Ergonomic Chair', 'Ergonomic office chair with 8Z Pellicle suspension and PostureFit SL lumbar.',
  1395, 'USD', '{"mesh":"8Z Pellicle Suspension","lumbar":"PostureFit SL","warranty":"12 Years"}'
);

INSERT INTO inventory (product_id, available_qty, reserved_qty, sold_qty)
VALUES (10, 18, 0, 7);

INSERT INTO products (
  id, category, subCategory, productName, productCompany, productPrice, productPriceBeforeDiscount,
  productDescription, productImage1, productImage2, productImage3, shippingCharge, productAvailability,
  specifications, productModel, ficheTechnique, stockQuantity, warehouseLocation, costPrice, viewsCount,
  stockAvailable, stockReserved, stockSold, name, description, price, currency, fiche_technique
) VALUES (
  11, 1, 11, 'Asus ROG Zephyrus G16 (2026) OLED Gaming Laptop', 'Asus',
  2899, 3299, 'Intel Core Ultra 9 185H processor, NVIDIA GeForce RTX 4090 16GB GDDR6 Laptop GPU, 32GB LPDDR5X RAM, 2TB PCIe 4.0 NVMe SSD, 16.0" 2.5K 240Hz 0.2ms ROG Nebula OLED Display, CNC Aluminum Chassis.',
  'img_main.jpg', 'img_angle.jpg', 'img_detail.jpg',
  0, 'In Stock', '{"cpu":"Intel Core Ultra 9 185H","gpu":"NVIDIA RTX 4090 16GB (115W)","ram":"32GB LPDDR5X 7467MHz","storage":"2TB M.2 NVMe PCIe 4.0","display":"16.0 2.5K 240Hz 0.2ms ROG Nebula OLED DisplayHDR True Black 500","chassis":"Precision CNC Milled Aluminum with Slash Lighting","weight":"1.85 kg","battery":"90Wh with 100W USB-C PD"}',
  'ROG-GU605MY-OLED', 'Fiche Technique:
- Processeur : Intel Core Ultra 9 185H avec NPU Intel AI Boost
- Carte Graphique : NVIDIA GeForce RTX 4090 16 Go GDDR6
- Mémoire : 32 Go LPDDR5X 7467 MHz
- Écran : 16 2.5K 240 Hz OLED ROG Nebula avec temps de réponse 0.2 ms
- Châssis : Aluminium CNC Platinum White avec barre d\'éclairage Slash Lighting
- Garantie : 2 ans Asus Maroc.', 45,
  'Casablanca Central Hub-A1', 2300, 1650, 38,
  0, 7, 'Asus ROG Zephyrus G16 (2026) OLED Gaming Laptop', 'Intel Core Ultra 9, RTX 4090, 32GB RAM, 2TB SSD, 16-inch 240Hz OLED display.',
  2899, 'USD', '{"cpu":"Intel Core Ultra 9 185H","gpu":"RTX 4090 16GB","display":"16 240Hz OLED","ram":"32GB LPDDR5X","storage":"2TB NVMe"}'
);

INSERT INTO inventory (product_id, available_qty, reserved_qty, sold_qty)
VALUES (11, 38, 0, 7);

INSERT INTO products (
  id, category, subCategory, productName, productCompany, productPrice, productPriceBeforeDiscount,
  productDescription, productImage1, productImage2, productImage3, shippingCharge, productAvailability,
  specifications, productModel, ficheTechnique, stockQuantity, warehouseLocation, costPrice, viewsCount,
  stockAvailable, stockReserved, stockSold, name, description, price, currency, fiche_technique
) VALUES (
  12, 2, 12, 'Apple iPad Pro 13" M4 Ultra-Thin OLED (1TB, Cellular)', 'Apple',
  1899, 2099, 'Apple M4 chip, Tandem OLED Ultra Retina XDR Display with 1000 nits full-screen brightness, 1TB Storage, 16GB Unified Memory, Wi-Fi 7 + 5G Cellular, Incredibly thin 5.1mm design, Space Black.',
  'img_main.jpg', 'img_angle.jpg', 'img_detail.jpg',
  0, 'In Stock', '{"chip":"Apple M4 10-core CPU / 10-core GPU","display":"13.0 Tandem OLED Ultra Retina XDR 120Hz ProMotion (1600 nits peak)","storage":"1TB Flash NVMe","ram":"16GB Unified Memory","camera":"12MP Wide 4K ProRes + LiDAR Scanner","thickness":"5.1 mm (Thinnest Apple product ever)","connectivity":["5G Sub-6GHz","Wi-Fi 7","Thunderbolt / USB 4"],"weight":"582g"}',
  'IPADPRO13-M4-1TB-5G', 'Fiche Technique:
- Processeur : Puce Apple M4 avec moteur neuronal 38 TOPS
- Écran : Ultra Retina XDR 13 pouces avec technologie révolutionnaire Tandem OLED
- Épaisseur : Seulement 5,1 mm (le produit Apple le plus fin de l\'histoire)
- Connectivité : 5G Cellular + Wi-Fi 7 ultra-rapide
- Compatible avec Apple Pencil Pro et Magic Keyboard.', 50,
  'Casablanca Central Hub-A1', 1520, 2100, 42,
  0, 8, 'Apple iPad Pro 13" M4 Ultra-Thin OLED (1TB, Cellular)', 'Apple M4 chip, 13-inch Tandem OLED display, 1TB SSD, 16GB RAM, 5G Cellular.',
  1899, 'USD', '{"chip":"Apple M4","display":"13.0 Tandem OLED XDR","thickness":"5.1mm","storage":"1TB","cellular":"5G LTE"}'
);

INSERT INTO inventory (product_id, available_qty, reserved_qty, sold_qty)
VALUES (12, 42, 0, 8);

INSERT INTO products (
  id, category, subCategory, productName, productCompany, productPrice, productPriceBeforeDiscount,
  productDescription, productImage1, productImage2, productImage3, shippingCharge, productAvailability,
  specifications, productModel, ficheTechnique, stockQuantity, warehouseLocation, costPrice, viewsCount,
  stockAvailable, stockReserved, stockSold, name, description, price, currency, fiche_technique
) VALUES (
  13, 2, 12, 'Samsung Galaxy Tab S10 Ultra 5G (512GB, Moonstone Gray)', 'Samsung',
  1399, 1549, '14.6" Dynamic AMOLED 2X 120Hz Anti-Reflection Display, MediaTek Dimensity 9300+ 4nm Processor, 16GB RAM, 512GB Storage with MicroSD expandability up to 1.5TB, S Pen included, IP68 Armor Aluminum.',
  'img_main.jpg', 'img_angle.jpg', 'img_detail.jpg',
  0, 'In Stock', '{"processor":"MediaTek Dimensity 9300+ (4nm)","display":"14.6 Dynamic AMOLED 2X 120Hz (2960 x 1848) Anti-Glare","ram":"16GB","storage":"512GB (expandable via MicroSD up to 1.5TB)","durability":"IP68 Water and Dust Resistance + Armor Aluminum Frame","battery":"11200mAh with 45W Super Fast Charging","spen":"Low-latency S Pen included in the box"}',
  'SM-X926B-512-GRY', 'Fiche Technique:
- Processeur : MediaTek Dimensity 9300+ Octa-Core ultra-puissant
- Écran géant : 14,6 pouces Dynamic AMOLED 2X avec traitement antireflet de pointe
- Mémoire : 16 Go RAM / 512 Go de stockage extensible
- Étanchéité : Certification IP68 pour la tablette et le stylet S Pen inclus
- Mode Samsung DeX sans fil pour transformer la tablette en véritable PC de bureau.', 40,
  'Casablanca Central Hub-A1', 1080, 1340, 31,
  0, 9, 'Samsung Galaxy Tab S10 Ultra 5G (512GB, Moonstone Gray)', '14.6-inch Dynamic AMOLED 2X tablet with MediaTek Dimensity 9300+ and S Pen.',
  1399, 'USD', '{"display":"14.6 Dynamic AMOLED 2X","processor":"Dimensity 9300+","storage":"512GB","spen":"Included","ip_rating":"IP68"}'
);

INSERT INTO inventory (product_id, available_qty, reserved_qty, sold_qty)
VALUES (13, 31, 0, 9);

INSERT INTO products (
  id, category, subCategory, productName, productCompany, productPrice, productPriceBeforeDiscount,
  productDescription, productImage1, productImage2, productImage3, shippingCharge, productAvailability,
  specifications, productModel, ficheTechnique, stockQuantity, warehouseLocation, costPrice, viewsCount,
  stockAvailable, stockReserved, stockSold, name, description, price, currency, fiche_technique
) VALUES (
  14, 6, 15, 'Sony Alpha A7 IV Full-Frame Mirrorless Camera + 24-70mm GM II', 'Sony',
  3199, 3499, '33MP Full-Frame Exmor R CMOS back-illuminated sensor, BIONZ XR image processor, 4K 60p 10-Bit 4:2:2 video, S-Cinetone color profile, 759-point Fast Hybrid AF with Real-time Eye AF, FE 24-70mm F2.8 GM II Lens.',
  'img_main.jpg', 'img_angle.jpg', 'img_detail.jpg',
  0, 'In Stock', '{"sensor":"33.0 MP Full-Frame Exmor R BSI CMOS","processor":"BIONZ XR Dual Engine","video":["4K 60p (Super 35)","4K 30p 7K oversampled 10-Bit 4:2:2","S-Cinetone","S-Log3"],"autofocus":"759-Point Phase Detection with Real-time AI Eye AF for Human/Animal/Bird","stabilization":"5-Axis In-Body Image Stabilization (5.5 stops)","lens":"Sony FE 24-70mm F2.8 GM II Pro Zoom"}',
  'ILCE-7M4-KIT-GM2', 'Fiche Technique:
- Capteur : 33 MP Plein Format Exmor R rétroéclairé
- Processeur : BIONZ XR haute performance pour une vitesse de traitement 8x supérieure
- Vidéo Cinéma : 4K 60p 10 bits 4:2:2 avec profils S-Cinetone et S-Log3
- Objectif inclus : Sony FE 24-70mm f/2.8 G Master II (le zoom pro le plus compact et piqué au monde)
- Garantie officielle Sony Maroc 2 ans.', 30,
  'Casablanca Central Hub-A1', 2550, 1980, 22,
  0, 8, 'Sony Alpha A7 IV Full-Frame Mirrorless Camera + 24-70mm GM II', '33MP Full-Frame mirrorless camera with 4K 60p 10-bit video and 24-70mm GM II lens.',
  3199, 'USD', '{"sensor":"33MP Full-Frame Exmor R","video":"4K 60p 10-Bit 4:2:2","lens":"24-70mm F2.8 GM II","stabilization":"5-Axis IBIS"}'
);

INSERT INTO inventory (product_id, available_qty, reserved_qty, sold_qty)
VALUES (14, 22, 0, 8);

INSERT INTO products (
  id, category, subCategory, productName, productCompany, productPrice, productPriceBeforeDiscount,
  productDescription, productImage1, productImage2, productImage3, shippingCharge, productAvailability,
  specifications, productModel, ficheTechnique, stockQuantity, warehouseLocation, costPrice, viewsCount,
  stockAvailable, stockReserved, stockSold, name, description, price, currency, fiche_technique
) VALUES (
  15, 6, 15, 'DJI Mavic 3 Pro Cine Drone (Triple Camera 4K/60fps ProRes)', 'DJI',
  3899, 4299, 'Flagship triple-camera system featuring 4/3 CMOS Hasselblad 20MP Camera + 70mm Medium Tele + 166mm Telephoto, Apple ProRes 422 HQ recording on all three cameras, Built-in 1TB SSD, 43 min flight time, 15km transmission.',
  'img_main.jpg', 'img_angle.jpg', 'img_detail.jpg',
  0, 'In Stock', '{"cameras":["Hasselblad 4/3 CMOS 20MP (24mm equiv)","Medium Tele 1/1.3 CMOS 48MP (70mm equiv)","Telephoto 1/2 CMOS 12MP (166mm equiv)"],"codecs":["Apple ProRes 422 HQ","Apple ProRes 422","Apple ProRes 422 LT","H.264/H.265"],"storage":"Built-in 1TB High-Speed SSD + MicroSD slot","flight_time":"Up to 43 minutes","transmission":"DJI O3+ HD Video up to 15 km","obstacle_sensing":"Omnidirectional APAS 5.0"}',
  'MAVIC3-PRO-CINE', 'Fiche Technique:
- Système Tri-Caméra : Caméra principale Hasselblad 4/3 + Téléobjectif moyen 70 mm + Téléobjectif 166 mm
- Enregistrement Vidéo : Apple ProRes 422 HQ sur les trois caméras avec SSD 1 To intégré
- Autonomie de Vol : Jusqu\'à 43 minutes par batterie
- Détection d\'Obstacles : Omnidirectionnelle APAS 5.0 pour une sécurité maximale
- Outil ultime pour les tournages cinématographiques et documentaires au Maroc.', 25,
  'Casablanca Central Hub-A1', 3100, 2400, 19,
  0, 6, 'DJI Mavic 3 Pro Cine Drone (Triple Camera 4K/60fps ProRes)', 'Triple-camera cinema drone with Apple ProRes 422 HQ and built-in 1TB SSD.',
  3899, 'USD', '{"cameras":"Triple: Hasselblad 4/3 + 70mm + 166mm","codecs":"Apple ProRes 422 HQ","storage":"1TB SSD","flight_time":"43 Mins"}'
);

INSERT INTO inventory (product_id, available_qty, reserved_qty, sold_qty)
VALUES (15, 19, 0, 6);

INSERT INTO products (
  id, category, subCategory, productName, productCompany, productPrice, productPriceBeforeDiscount,
  productDescription, productImage1, productImage2, productImage3, shippingCharge, productAvailability,
  specifications, productModel, ficheTechnique, stockQuantity, warehouseLocation, costPrice, viewsCount,
  stockAvailable, stockReserved, stockSold, name, description, price, currency, fiche_technique
) VALUES (
  16, 3, 5, 'Bose QuietComfort Ultra Wireless Noise Cancelling Headphones', 'Bose',
  429, 479, 'World-class active noise cancelling with CustomTune sound calibration technology, Breakthrough Bose Immersive Audio spatial sound, Ultra-plush synthetic leather cushions, Up to 24-hour battery life.',
  'img_main.jpg', 'img_angle.jpg', 'img_detail.jpg',
  0, 'In Stock', '{"spatial_audio":"Bose Immersive Audio Spatializer","anc":"CustomTune Active Noise Cancelling with Quiet, Aware, and Immersion Modes","battery":"Up to 24 hours (up to 18 hours with Immersive Audio)","connectivity":"Bluetooth 5.3 with Snapdragon Sound and aptX Adaptive","microphones":"10-Microphone array for crystal clear voice isolation"}',
  'QC-ULTRA-BLK', 'Fiche Technique:
- Audio Spatial Immersif : Technologie Bose Immersive Audio qui spatialise le son en face de vous
- Réduction de Bruit : Technologie CustomTune qui calibre le son à la morphologie de vos oreilles
- Autonomie : Jusqu\'à 24 heures d\'écoute en continu (charge rapide 15 min pour 2h30)
- Confort : Coussins ultra-doux en cuir synthétique haut de gamme
- Livraison express 24h avec garantie Bose Maroc.', 70,
  'Casablanca Central Hub-A1', 295, 1420, 56,
  0, 14, 'Bose QuietComfort Ultra Wireless Noise Cancelling Headphones', 'World-class noise cancelling headphones with Bose Immersive Audio spatial sound.',
  429, 'USD', '{"anc":"CustomTune ANC","audio":"Bose Immersive Spatial","battery":"24 Hours","bluetooth":"5.3 aptX Adaptive"}'
);

INSERT INTO inventory (product_id, available_qty, reserved_qty, sold_qty)
VALUES (16, 56, 0, 14);

INSERT INTO products (
  id, category, subCategory, productName, productCompany, productPrice, productPriceBeforeDiscount,
  productDescription, productImage1, productImage2, productImage3, shippingCharge, productAvailability,
  specifications, productModel, ficheTechnique, stockQuantity, warehouseLocation, costPrice, viewsCount,
  stockAvailable, stockReserved, stockSold, name, description, price, currency, fiche_technique
) VALUES (
  17, 3, 13, 'Marshall Stanmore III Bluetooth Home Speaker (Brass & Black)', 'Marshall',
  379, 429, '80W Total Output Power with wider stereo soundstage, Dynamic Loudness DSP, Bluetooth 5.2 ready with 3.5mm AUX and RCA inputs, Iconic vintage textured vinyl and brass control knobs.',
  'img_main.jpg', 'img_angle.jpg', 'img_detail.jpg',
  0, 'In Stock', '{"amplifiers":"1x 50W Class D (Woofer) + 2x 15W Class D (Tweeters)","frequency_response":"45 Hz - 20,000 Hz","max_spl":"97 dB @ 1 m","connectivity":["Bluetooth 5.2","3.5 mm Aux","RCA"],"controls":["Volume","Bass","Treble","Source Selector","Brass Power Switch"],"dimensions":"350 x 203 x 188 mm","weight":"4.25 kg"}',
  'STANMORE-III-BLK', 'Fiche Technique:
- Puissance Audio : 80 Watts RMS (Amplificateurs de classe D dédiés)
- Scène Sonore Élargie : Tweeters inclinés vers l\'extérieur pour une immersion acoustique totale
- Connectivité Polyvalente : Bluetooth 5.2, Entrée Jack 3,5 mm et Entrée RCA pour platine vinyle
- Design Rock\'n\'Roll : Revêtement en vinyle texturé, grille vintage et boutons de réglage en laiton
- متحدث منزلي كلاسيكي فخم بصوت قوي ونقي.', 60,
  'Casablanca Central Hub-A1', 250, 1890, 48,
  0, 12, 'Marshall Stanmore III Bluetooth Home Speaker (Brass & Black)', '80W vintage rock home speaker with room-filling stereo sound and brass knobs.',
  379, 'USD', '{"power":"80W RMS Class D","inputs":"Bluetooth 5.2, RCA, 3.5mm AUX","finish":"Textured Vinyl & Brass"}'
);

INSERT INTO inventory (product_id, available_qty, reserved_qty, sold_qty)
VALUES (17, 48, 0, 12);

INSERT INTO products (
  id, category, subCategory, productName, productCompany, productPrice, productPriceBeforeDiscount,
  productDescription, productImage1, productImage2, productImage3, shippingCharge, productAvailability,
  specifications, productModel, ficheTechnique, stockQuantity, warehouseLocation, costPrice, viewsCount,
  stockAvailable, stockReserved, stockSold, name, description, price, currency, fiche_technique
) VALUES (
  18, 5, 8, 'Nintendo Switch OLED Model - The Legend of Zelda Edition', 'Nintendo',
  359, 399, 'Vibrant 7.0-inch OLED screen with deep contrast and vivid colors, Wide adjustable stand, 64GB internal storage, Enhanced audio speakers, Exclusive golden Zelda Tears of the Kingdom artwork.',
  'img_main.jpg', 'img_angle.jpg', 'img_detail.jpg',
  0, 'In Stock', '{"display":"7.0 OLED Multi-Touch (1280 x 720)","storage":"64GB (expandable via MicroSD up to 2TB)","battery":"4.5 to 9.0 hours (Lithium-ion 4310mAh)","dock":"White/Gold Dock with LAN Port and Hylian Crest","joycons":"Zelda Green & Gold Edition Joy-Con Controllers","weight":"420g with Joy-Cons"}',
  'NSW-OLED-ZELDA-TOTK', 'Fiche Technique:
- Écran : 7,0 pouces OLED avec couleurs éclatantes et noirs profonds
- Design Collecteur : Finitions dorées et symboles Hyliens inspirés de Zelda Tears of the Kingdom
- Station d\'accueil : Port Ethernet LAN filaire intégré pour des parties en ligne ultra-stables
- Stockage : 64 Go de mémoire interne extensible par carte MicroSD
- متعة الألعاب المنزلية والمحمولة مع إصدار زيلدا الحصري.', 65,
  'Casablanca Central Hub-A1', 260, 2750, 49,
  0, 16, 'Nintendo Switch OLED Model - The Legend of Zelda Edition', 'Collector OLED edition console with 7.0-inch screen and Zelda artwork.',
  359, 'USD', '{"display":"7.0 OLED","edition":"Zelda Tears of the Kingdom","storage":"64GB + LAN Dock"}'
);

INSERT INTO inventory (product_id, available_qty, reserved_qty, sold_qty)
VALUES (18, 49, 0, 16);

INSERT INTO products (
  id, category, subCategory, productName, productCompany, productPrice, productPriceBeforeDiscount,
  productDescription, productImage1, productImage2, productImage3, shippingCharge, productAvailability,
  specifications, productModel, ficheTechnique, stockQuantity, warehouseLocation, costPrice, viewsCount,
  stockAvailable, stockReserved, stockSold, name, description, price, currency, fiche_technique
) VALUES (
  19, 6, 9, 'LG UltraGear 45" Curved OLED 240Hz Gaming Monitor (45GS96QB)', 'LG',
  1699, 1899, '45" WQHD (3440 x 1440) Curved OLED (800R), Blistering 240Hz Refresh Rate, 0.03ms (GtG) Response Time, VESA DisplayHDR True Black 400, USB-C 65W Power Delivery, Built-in Speakers with DTS Headphone:X.',
  'img_main.jpg', 'img_angle.jpg', 'img_detail.jpg',
  0, 'In Stock', '{"panel":"44.5 WQHD Curved OLED (800R curvature)","resolution":"3440 x 1440 at 240Hz","response_time":"0.03 ms (GtG)","contrast":"1,500,000:1 True Black","gamut":"DCI-P3 98.5%","ports":["USB-C 65W PD","2x HDMI 2.1","DisplayPort 1.4","2x USB 3.0 Downstream"],"sync":["NVIDIA G-SYNC Compatible","AMD FreeSync Premium Pro"]}',
  '45GS96QB-OLED-240', 'Fiche Technique:
- Dalle Incurvée : 45 pouces OLED ultra-large avec courbure agressive 800R pour une immersion totale
- Fréquence de Rafraîchissement : 240 Hz ultra-fluide avec temps de réponse instantané de 0,03 ms
- Qualité d\'image : VESA DisplayHDR True Black 400 avec 98,5% DCI-P3
- Connectique : USB-C 65W Power Delivery avec haut-parleurs intégrés 7W x 2 DTS:X
- شاشة الألعاب المنحنية الأضخم والأقوى للاعبين المحترفين وصناع المحتوى.', 25,
  'Casablanca Central Hub-A1', 1320, 1150, 18,
  0, 7, 'LG UltraGear 45" Curved OLED 240Hz Gaming Monitor (45GS96QB)', '45-inch 240Hz 0.03ms WQHD Curved 800R OLED gaming monitor with USB-C.',
  1699, 'USD', '{"panel":"45 Curved OLED 800R","refresh_rate":"240Hz / 0.03ms","resolution":"3440 x 1440 WQHD","ports":"USB-C 65W PD + HDMI 2.1"}'
);

INSERT INTO inventory (product_id, available_qty, reserved_qty, sold_qty)
VALUES (19, 18, 0, 7);

INSERT INTO products (
  id, category, subCategory, productName, productCompany, productPrice, productPriceBeforeDiscount,
  productDescription, productImage1, productImage2, productImage3, shippingCharge, productAvailability,
  specifications, productModel, ficheTechnique, stockQuantity, warehouseLocation, costPrice, viewsCount,
  stockAvailable, stockReserved, stockSold, name, description, price, currency, fiche_technique
) VALUES (
  20, 6, 10, 'Secretlab TITAN Evo 2026 Gaming Chair (SoftWeave Plus)', 'Secretlab',
  549, 629, 'Award-winning ergonomic gaming chair. SoftWeave Plus high-breathability fabric (Cookies & Cream), 4-Way L-ADAPT Lumbar Support System, Magnetic Memory Foam Head Pillow with Cooling Gel, 4D Full-Metal Armrests.',
  'img_main.jpg', 'img_angle.jpg', 'img_detail.jpg',
  0, 'In Stock', '{"upholstery":"Secretlab SoftWeave Plus Fabric (High Durability & Breathability)","lumbar":"4-Way L-ADAPT Dynamic Lumbar Support","pillow":"Magnetic Memory Foam Head Pillow with Cooling Gel Layer","armrests":"CloudSwap Full-Metal 4D Armrests with Magnetic Replacement Caps","recline":"165-Degree Full Multi-Tilt Mechanism with Class 4 Hydraulics"}',
  'TITAN-EVO-REG-SW', 'Fiche Technique:
- Revêtement : Tissu SoftWeave Plus doux, respirant et ultra-résistant à l\'usure
- Ergonomie : Système de soutien lombaire L-ADAPT réglable en 4 directions (hauteur et courbure)
- Oreiller de tête : Mousse à mémoire de forme magnétique avec gel rafraîchissant sans sangles
- Accoudoirs 4D entièrement métalliques avec système de changement rapide CloudSwap
- كرسي ألعاب مريح ومثالي لجلسات اللعب والعمل الطويلة.', 50,
  'Casablanca Central Hub-A1', 370, 1920, 39,
  0, 11, 'Secretlab TITAN Evo 2026 Gaming Chair (SoftWeave Plus)', 'Ergonomic gaming chair with SoftWeave Plus fabric and magnetic cooling pillow.',
  549, 'USD', '{"fabric":"SoftWeave Plus Breathable","lumbar":"4-Way L-ADAPT","pillow":"Magnetic Memory Foam Gel","armrests":"4D Full Metal"}'
);

INSERT INTO inventory (product_id, available_qty, reserved_qty, sold_qty)
VALUES (20, 39, 0, 11);

INSERT INTO products (
  id, category, subCategory, productName, productCompany, productPrice, productPriceBeforeDiscount,
  productDescription, productImage1, productImage2, productImage3, shippingCharge, productAvailability,
  specifications, productModel, ficheTechnique, stockQuantity, warehouseLocation, costPrice, viewsCount,
  stockAvailable, stockReserved, stockSold, name, description, price, currency, fiche_technique
) VALUES (
  21, 6, 10, 'Dyson V15 Detect Absolute Cordless Smart Vacuum Cleaner', 'Dyson',
  749, 849, '240 Air Watts of suction power, Laser Slim Fluffy optic cleaner head reveals invisible dust on hard floors, Piezo acoustic sensor counts and sizes particles on LCD screen, Up to 60 minutes run time.',
  'img_main.jpg', 'img_angle.jpg', 'img_detail.jpg',
  0, 'In Stock', '{"suction_power":"240 Air Watts (Dyson Hyperdymium Motor 125,000 RPM)","filtration":"Whole-Machine HEPA Filtration capturing 99.99% of microscopic particles down to 0.1 microns","sensor":"Acoustic Piezo Sensor Particle Counter","runtime":"Up to 60 Minutes Fade-Free Power","heads":["Digital Motorbar Cleaner Head with De-Tangling Vanes","Laser Slim Fluffy Optic Hard Floor Head"],"weight":"3.0 kg"}',
  'DYSON-V15-ABS-YEL', 'Fiche Technique:
- Puissance d\'aspiration : 240 AW propulsée par le moteur Dyson Hyperdymium à 125 000 tr/min
- Tête Optique Laser : Révèle la poussière invisible à l\'œil nu sur les sols durs
- Capteur Piézo : Mesure et compte en continu la taille des particules de poussière affichées sur écran LCD
- Filtration HEPA : Capture 99,99% des particules microscopiques jusqu\'à 0,1 micron
- مكنسة ذكية لاسلكية متطورة لتنظيف عميق وفاخر للمنازل العصرية.', 40,
  'Casablanca Central Hub-A1', 510, 1420, 32,
  0, 8, 'Dyson V15 Detect Absolute Cordless Smart Vacuum Cleaner', 'Cordless vacuum with laser dust detection and real-time particle counting LCD.',
  749, 'USD', '{"power":"240 Air Watts","laser":"Laser Slim Fluffy Optic","filtration":"99.99% HEPA","runtime":"60 Mins"}'
);

INSERT INTO inventory (product_id, available_qty, reserved_qty, sold_qty)
VALUES (21, 32, 0, 8);

INSERT INTO products (
  id, category, subCategory, productName, productCompany, productPrice, productPriceBeforeDiscount,
  productDescription, productImage1, productImage2, productImage3, shippingCharge, productAvailability,
  specifications, productModel, ficheTechnique, stockQuantity, warehouseLocation, costPrice, viewsCount,
  stockAvailable, stockReserved, stockSold, name, description, price, currency, fiche_technique
) VALUES (
  22, 3, 13, 'Sonos Arc Premium Smart Soundbar with Dolby Atmos (Matte Black)', 'Sonos',
  899, 999, '11 high-performance internal drivers including 2 dedicated up-firing height channels, Dolby Atmos 3D acoustic soundstage, Apple AirPlay 2, HDMI eARC, Speech Enhancement for crystal-clear dialogue, Trueplay acoustic tuning.',
  'img_main.jpg', 'img_angle.jpg', 'img_detail.jpg',
  0, 'In Stock', '{"amplifiers":"11 Class-D Digital Amplifiers matched to acoustic architecture","drivers":["8 Elliptical Woofers","3 Silk-Dome Tweeters","2 Dedicated Up-Firing Height Channels"],"audio_formats":["Dolby Atmos","Dolby Digital Plus","Dolby TrueHD","PCM Multichannel"],"connectivity":["HDMI eARC","Optical Audio Adapter included","Apple AirPlay 2","Wi-Fi"],"tuning":"Trueplay Room Acoustic Tuning"}',
  'SONOS-ARC-BLK', 'Fiche Technique:
- Immersion Cinéma : 11 haut-parleurs haute précision avec 2 canaux orientés vers le haut pour Dolby Atmos 3D
- Clarté des dialogues : Fonction Speech Enhancement qui isole les voix pour ne manquer aucun mot
- Calibrage Trueplay : Ajuste la signature acoustique en fonction de l\'agencement de votre salon
- Connectivité : HDMI eARC avec prise en charge complète d\'Apple AirPlay 2 et Spotify Connect
- ساوند بار سينمائي فاخر بتجربة صوتية ثلاثية الأبعاد Dolby Atmos.', 35,
  'Casablanca Central Hub-A1', 670, 1120, 27,
  0, 8, 'Sonos Arc Premium Smart Soundbar with Dolby Atmos (Matte Black)', 'Cinematic smart soundbar with 11 drivers, Dolby Atmos 3D, and HDMI eARC.',
  899, 'USD', '{"drivers":"11 Amplified Drivers","audio":"Dolby Atmos 3D Height Channels","connectivity":"HDMI eARC + AirPlay 2"}'
);

INSERT INTO inventory (product_id, available_qty, reserved_qty, sold_qty)
VALUES (22, 27, 0, 8);

INSERT INTO products (
  id, category, subCategory, productName, productCompany, productPrice, productPriceBeforeDiscount,
  productDescription, productImage1, productImage2, productImage3, shippingCharge, productAvailability,
  specifications, productModel, ficheTechnique, stockQuantity, warehouseLocation, costPrice, viewsCount,
  stockAvailable, stockReserved, stockSold, name, description, price, currency, fiche_technique
) VALUES (
  23, 4, 7, 'Garmin Fenix 8 Solar Multisport GPS Smartwatch (51mm Titanium)', 'Garmin',
  1099, 1199, 'Power Sapphire Solar charging lens providing up to 48 days of battery life, Grade 5 Titanium bezel with DLC coating, Built-in Speaker and Mic for voice calls, TopoActive multi-continent color maps, Bright LED flashlight.',
  'img_main.jpg', 'img_angle.jpg', 'img_detail.jpg',
  0, 'In Stock', '{"lens":"Power Sapphire Solar Charging Crystal","bezel":"Titanium with Diamond-Like Carbon (DLC) Coating","display":"1.4 Sunlight-Visible Transflective MIP (280 x 280)","battery":["Up to 48 Days (Smartwatch with Solar)","Up to 149 Hours (GPS with Solar)"],"sensors":["Multi-Band GNSS with SatIQ","Elevate Gen 5 Heart Rate & ECG","Barometric Altimeter","Pulse Ox"],"features":["Integrated LED Flashlight","Offline TopoActive Maps","40m Dive Rating with Leakproof Buttons"]}',
  'FENIX8-51-SOL-TIT', 'Fiche Technique:
- Verre Solaire Power Sapphire : Recharge solaire offrant jusqu\'à 48 jours d\'autonomie en mode montre connectée
- Boîtier & Lunette : Titane Grade 5 avec revêtement DLC haute résistance aux chocs et rayures
- Cartographie : Cartes TopoActive mondiales préchargées avec guidage GPS multi-bandes ultra-précis SatIQ
- Étanchéité & Plongée : Étanche jusqu\'à 40 mètres avec boutons étanches inductifs
- Lampe torche LED intégrée à intensité variable avec faisceau blanc et rouge.', 30,
  'Casablanca Central Hub-A1', 820, 1750, 24,
  0, 6, 'Garmin Fenix 8 Solar Multisport GPS Smartwatch (51mm Titanium)', 'Expedition GPS smartwatch with solar sapphire lens, titanium bezel, and 48-day battery.',
  1099, 'USD', '{"lens":"Power Sapphire Solar","bezel":"51mm Titanium DLC","battery":"Up to 48 Days","maps":"TopoActive Offline Global"}'
);

INSERT INTO inventory (product_id, available_qty, reserved_qty, sold_qty)
VALUES (23, 24, 0, 6);

INSERT INTO products (
  id, category, subCategory, productName, productCompany, productPrice, productPriceBeforeDiscount,
  productDescription, productImage1, productImage2, productImage3, shippingCharge, productAvailability,
  specifications, productModel, ficheTechnique, stockQuantity, warehouseLocation, costPrice, viewsCount,
  stockAvailable, stockReserved, stockSold, name, description, price, currency, fiche_technique
) VALUES (
  24, 5, 14, 'Keychron Q1 Pro Wireless Custom Mechanical Keyboard (Gateron Red)', 'Keychron',
  219, 249, '75% layout premium custom mechanical keyboard. Full CNC 6063 Aluminum body, Double-Gasket acoustic mount, Hot-Swappable RGB PCB, QMK/VIA key remapping, Bluetooth 5.1 and Type-C wired, Gateron Jupiter Red linear switches.',
  'img_main.jpg', 'img_angle.jpg', 'img_detail.jpg',
  0, 'In Stock', '{"layout":"75% Compact (81 Keys + Programmable Rotary Knob)","chassis":"Full 6063 Solid CNC Aluminum Anodized Chassis","structure":"Double Gasket Acoustic Mount with Sound Absorbing Foam","switches":"Pre-Lubed Gateron Jupiter Red Linear (Hot-Swappable 3/5-pin)","connectivity":["Bluetooth 5.1 (3 Devices)","Type-C Wired 1000Hz Polling Rate"],"keycaps":"KSA Profile Double-Shot PBT Keycaps"}',
  'KEYCHRON-Q1PRO-RED', 'Fiche Technique:
- Châssis : Aluminium massif 6063 usiné par CNC avec finition anodisée haut de gamme
- Structure Double Gasket : Amortissement acoustique double couche pour une frappe douce et un son feutré ("Thock")
- Switches Hot-Swap : Gateron Jupiter Red linéaires lubrifiés d\'usine remplaçables sans soudure
- Personnalisation : Entièrement programmable avec les logiciels open-source QMK et VIA
- Molette rotative programmable en aluminium pour le volume et la luminosité.', 75,
  'Casablanca Central Hub-A1', 140, 2600, 61,
  0, 14, 'Keychron Q1 Pro Wireless Custom Mechanical Keyboard (Gateron Red)', 'Full CNC aluminum 75% mechanical keyboard with hot-swappable switches and QMK/VIA.',
  219, 'USD', '{"chassis":"Full 6063 CNC Aluminum","structure":"Double Gasket Mount","switches":"Hot-Swap Gateron Jupiter Red","software":"QMK / VIA Programmable"}'
);

INSERT INTO inventory (product_id, available_qty, reserved_qty, sold_qty)
VALUES (24, 61, 0, 14);

INSERT INTO products (
  id, category, subCategory, productName, productCompany, productPrice, productPriceBeforeDiscount,
  productDescription, productImage1, productImage2, productImage3, shippingCharge, productAvailability,
  specifications, productModel, ficheTechnique, stockQuantity, warehouseLocation, costPrice, viewsCount,
  stockAvailable, stockReserved, stockSold, name, description, price, currency, fiche_technique
) VALUES (
  25, 6, 15, 'GoPro HERO 13 Black Creator Edition (5.3K60 Video + Volta Grip)', 'GoPro',
  599, 699, '5.3K 60fps / 4K 120fps video, HyperSmooth 6.0 Video Stabilization with 360° Horizon Lock, Volta Battery Grip with over 5 hours of continuous 4K recording, Media Mod directional microphone, Light Mod LED.',
  'img_main.jpg', 'img_angle.jpg', 'img_detail.jpg',
  0, 'In Stock', '{"video":["5.3K 60fps","4K 120fps","2.7K 240fps (8x Slo-Mo)","10-Bit GP-Log & HLG HDR"],"stabilization":"HyperSmooth 6.0 with AutoBoost and 360 Horizon Lock","sensor":"1/1.9 CMOS 27MP Stills in 8:7 Full Sensor Mode","accessories":["Volta Motorized Battery Grip (4900mAh)","Media Mod Directional Mic","Light Mod 200 Lumen Waterproof LED"],"waterproof":"Camera Body 10m (33ft) without housing"}',
  'GOPRO-HERO13-CREATOR', 'Fiche Technique:
- Résolution Vidéo : 5,3K à 60 ips / 4K à 120 ips avec profil 10 bits GP-Log et HLG HDR
- Stabilisation : HyperSmooth 6.0 avec verrouillage de l\'horizon à 360° pour des vidéos d\'une fluidité absolue
- Pack Creator Edition : Poignée batterie Volta (plus de 5 heures d\'enregistrement 4K), Module Médias avec micro directionnel et Torche LED 200 lumens
- Robustesse : Étanche jusqu\'à 10 mètres sans caisson additionnel
- كاميرا الأكشن والتصوير الاحترافي الأكثر شهرة مع جميع ملحقات صناع المحتوى.', 50,
  'Casablanca Central Hub-A1', 410, 3100, 39,
  0, 11, 'GoPro HERO 13 Black Creator Edition (5.3K60 Video + Volta Grip)', 'All-in-one 5.3K60 content creation action camera with Volta grip and directional mic.',
  599, 'USD', '{"video":"5.3K 60fps / 4K 120fps","stabilization":"HyperSmooth 6.0 360 Horizon","pack":"Volta Grip + Media Mod + LED Light"}'
);

INSERT INTO inventory (product_id, available_qty, reserved_qty, sold_qty)
VALUES (25, 39, 0, 11);

SET FOREIGN_KEY_CHECKS = 1;

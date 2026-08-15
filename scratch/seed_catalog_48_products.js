/**
 * ZeyTech AI Commerce OS — Catalog Seeder & Mock Data Generator
 * Seeds 48 realistic, distinct Moroccan electronics/tech products across 6 core categories.
 */
const fs = require('fs');
const path = require('path');

const CATEGORIES = [
    { id: 1, name: 'Laptops & Computers', desc: 'High-performance workstations, ultrabooks, and desktop computing.' },
    { id: 2, name: 'Smartphones & Tablets', desc: 'Flagship 5G mobile devices, foldables, and creative tablets.' },
    { id: 3, name: 'Audio & Acoustics', desc: 'Noise-canceling headphones, studio monitors, and audiophile gear.' },
    { id: 4, name: 'Smart Wearables', desc: 'Smartwatches, fitness rings, and biometric health trackers.' },
    { id: 5, name: 'Gaming & Consoles', desc: 'Next-gen consoles, VR headsets, and high-framerate gaming rigs.' },
    { id: 6, name: 'Smart Office & Peripherals', desc: 'Ergonomic workspace furniture, 4K reference monitors, and docks.' }
];

const SUBCATEGORIES = [
    { id: 1, categoryid: 1, subcategory: 'MacBooks & macOS' },
    { id: 2, categoryid: 1, subcategory: 'Windows Ultrabooks' },
    { id: 11, categoryid: 1, subcategory: 'Gaming Laptops & Workstations' },
    { id: 3, categoryid: 2, subcategory: 'iOS Flagships' },
    { id: 4, categoryid: 2, subcategory: 'Android Flagships' },
    { id: 12, categoryid: 2, subcategory: 'Creative Pro Tablets' },
    { id: 5, categoryid: 3, subcategory: 'Over-Ear ANC Headphones' },
    { id: 6, categoryid: 3, subcategory: 'True Wireless Earbuds' },
    { id: 13, categoryid: 3, subcategory: 'Smart Speakers & Home Theater' },
    { id: 7, categoryid: 4, subcategory: 'Smartwatches' },
    { id: 18, categoryid: 4, subcategory: 'Smart Home & Health Tech' },
    { id: 8, categoryid: 5, subcategory: 'Consoles & VR' },
    { id: 14, categoryid: 5, subcategory: 'Gaming Keyboards & Custom Gear' },
    { id: 17, categoryid: 5, subcategory: 'Esports Mice & Controllers' },
    { id: 9, categoryid: 6, subcategory: '4K Pro Displays' },
    { id: 10, categoryid: 6, subcategory: 'Ergonomic Seating' },
    { id: 15, categoryid: 6, subcategory: 'Cameras & Cinema Drones' },
    { id: 16, categoryid: 6, subcategory: 'Charging & Thunderbolt Docks' }
];

const PRODUCTS = [
    // -------------------------------------------------------------
    // CATEGORY 1: LAPTOPS & COMPUTERS (9 Products)
    // -------------------------------------------------------------
    {
        id: 1,
        category: 1,
        subCategory: 1,
        name: 'Apple MacBook Pro 16" M3 Max',
        company: 'Apple',
        price: 34900,
        oldPrice: 38500,
        sku: 'MBP16-M3MAX-36-1TB',
        shipping: 0,
        availability: 'In Stock',
        stockAvail: 3, // Deliberately low stock to test alert path
        stockRes: 2,
        stockSold: 45,
        desc: 'The definitive pro laptop for software architects, 3D artists, and AI engineers. Powered by the M3 Max chip with a 16-core CPU, 40-core GPU, and up to 22 hours of continuous battery life on a single charge. Features a breathtaking 16.2-inch Liquid Retina XDR display with 1600 nits peak brightness.',
        descFr: 'Le MacBook Pro 16 pouces avec puce M3 Max offre des performances extrêmes pour les développeurs, créateurs 3D et monteurs vidéo 8K. Écran Liquid Retina XDR 120Hz et jusqu\'à 22h d\'autonomie.',
        specs: {
            "Processor": "Apple M3 Max (16-core CPU, 40-core GPU, 16-core Neural Engine)",
            "RAM": "36GB Unified Memory (300GB/s memory bandwidth)",
            "Storage": "1TB NVMe PCIe 4.0 SSD (up to 7.4GB/s read)",
            "Display": "16.2-inch Liquid Retina XDR (3456x2234, 120Hz ProMotion, 1600 nits peak, 1,000,000:1 contrast)",
            "GPU": "Integrated 40-core Apple GPU with Hardware-Accelerated Ray Tracing",
            "Battery_Life": "Up to 22 hours video playback / 15 hours wireless web (100Wh Li-Polymer)",
            "Weight": "2.16 kg (4.8 lbs)",
            "Ports": "3x Thunderbolt 4 (USB-C), HDMI 2.1, SDXC card reader, MagSafe 3, 3.5mm high-impedance jack",
            "OS": "macOS Sonoma (Sequoia ready)"
        }
    },
    {
        id: 2,
        category: 1,
        subCategory: 1,
        name: 'Apple MacBook Air 15" M3',
        company: 'Apple',
        price: 16490,
        oldPrice: 17900,
        sku: 'MBA15-M3-16-512',
        shipping: 0,
        availability: 'In Stock',
        stockAvail: 42,
        stockRes: 3,
        stockSold: 88,
        desc: 'Impossibly thin and remarkably fast, the 15-inch MacBook Air with M3 chip delivers frictionless multitasking in a silent fanless aluminum chassis. Perfect for executives, students, and digital creators on the move.',
        descFr: 'Le MacBook Air 15 pouces avec puce M3 allie un design ultra-fin sans ventilateur à une autonomie remarquable de 18 heures. Écran Liquid Retina lumineux et audio spatial immersif.',
        specs: {
            "Processor": "Apple M3 (8-core CPU, 10-core GPU, 16-core Neural Engine)",
            "RAM": "16GB Unified Memory",
            "Storage": "512GB NVMe SSD",
            "Display": "15.3-inch Liquid Retina IPS (2880x1864, 500 nits, P3 Wide color)",
            "GPU": "Integrated 10-core Apple GPU with Mesh Shading",
            "Battery_Life": "Up to 18 hours video playback (66.5Wh Li-Polymer)",
            "Weight": "1.51 kg (3.3 lbs)",
            "Ports": "2x Thunderbolt / USB4, MagSafe 3 charging port, 3.5mm headphone jack",
            "OS": "macOS Sonoma"
        }
    },
    {
        id: 3,
        category: 1,
        subCategory: 2,
        name: 'Dell XPS 15 9530 OLED Touch',
        company: 'Dell',
        price: 24990,
        oldPrice: 27500,
        sku: 'XPS15-9530-I9-32-1TB',
        shipping: 0,
        availability: 'In Stock',
        stockAvail: 28,
        stockRes: 4,
        stockSold: 34,
        desc: 'Precision crafted from CNC aluminum and carbon fiber, this powerhouse workstation combines a 13th Gen Intel Core i9 processor with NVIDIA GeForce RTX 4070 graphics and a 3.5K OLED touchscreen display.',
        descFr: 'Station de travail portable ultra-premium équipée d\'un écran OLED tactile 3.5K, processeur Intel Core i9-13900H et carte graphique RTX 4070.',
        specs: {
            "Processor": "Intel Core i9-13900H (14 cores, 20 threads, up to 5.4 GHz Turbo)",
            "RAM": "32GB Dual-Channel DDR5 4800MHz (expandable to 64GB)",
            "Storage": "1TB M.2 PCIe Gen 4 NVMe SSD",
            "Display": "15.6-inch 3.5K OLED InfinityEdge Touch (3456x2160, 400 nits, 100% DCI-P3)",
            "GPU": "NVIDIA GeForce RTX 4070 8GB GDDR6 (40W TGP)",
            "Battery_Life": "Up to 9 hours office workflow (86Wh battery)",
            "Weight": "1.92 kg (4.23 lbs)",
            "Ports": "2x Thunderbolt 4 (Type-C), 1x USB-C 3.2 Gen 2, Full-size SD card slot, 3.5mm combo jack",
            "OS": "Windows 11 Pro"
        }
    },
    {
        id: 4,
        category: 1,
        subCategory: 2,
        name: 'Lenovo ThinkPad X1 Carbon Gen 12',
        company: 'Lenovo',
        price: 22500,
        oldPrice: 24800,
        sku: 'TP-X1C12-U7-32-1TB',
        shipping: 0,
        availability: 'In Stock',
        stockAvail: 35,
        stockRes: 1,
        stockSold: 50,
        desc: 'The benchmark of enterprise business computing. Built with carbon fiber and magnesium alloy, featuring Intel Core Ultra 7 with integrated AI NPU, legendary ThinkPad keyboard ergonomics, and military-grade durability.',
        descFr: 'L\'ordinateur professionnel de référence, léger comme une plume (1.09 kg) avec châssis fibre de carbone, processeur Intel Core Ultra 7 avec NPU IA et sécurité d\'entreprise intégrée.',
        specs: {
            "Processor": "Intel Core Ultra 7 155H (16 cores, 22 threads, up to 4.8 GHz with Intel AI Boost NPU)",
            "RAM": "32GB LPDDR5X 6400MHz Soldered",
            "Storage": "1TB PCIe Gen 4 Performance NVMe SSD Opal 2.0",
            "Display": "14-inch 2.8K OLED AG (2880x1800, 120Hz, 400 nits, 100% DCI-P3, HDR500)",
            "GPU": "Intel Arc Graphics (8 Xe-cores)",
            "Battery_Life": "Up to 14 hours battery life (57Wh with Rapid Charge 80% in 60 min)",
            "Weight": "1.09 kg (2.4 lbs)",
            "Ports": "2x Thunderbolt 4, 2x USB-A 3.2 Gen 1, HDMI 2.1, 3.5mm audio jack, Nano-SIM slot",
            "OS": "Windows 11 Pro 64-bit"
        }
    },
    {
        id: 5,
        category: 1,
        subCategory: 11,
        name: 'Asus ROG Zephyrus G16 (2026) OLED',
        company: 'Asus',
        price: 31900,
        oldPrice: 34500,
        sku: 'ROG-G16-U9-32-2TB-4080',
        shipping: 0,
        availability: 'In Stock',
        stockAvail: 19,
        stockRes: 3,
        stockSold: 26,
        desc: 'Ultra-thin CNC aluminum gaming laptop boasting an Intel Core Ultra 9 processor, NVIDIA RTX 4080 GPU, and the world\'s first 2.5K 240Hz ROG Nebula OLED display with G-SYNC support.',
        descFr: 'PC portable gaming d\'exception combinant écran ROG Nebula OLED 240Hz 0.2ms, processeur Intel Core Ultra 9 et carte graphique RTX 4080 dans un châssis aluminium ultra-fin de 1.49 cm.',
        specs: {
            "Processor": "Intel Core Ultra 9 185H (16 cores, 22 threads, up to 5.1 GHz, Intel AI Boost)",
            "RAM": "32GB LPDDR5X 7467MHz Dual-Channel",
            "Storage": "2TB PCIe 4.0 NVMe M.2 SSD",
            "Display": "16.0-inch 2.5K ROG Nebula OLED (2560x1600, 240Hz, 0.2ms, 500 nits, 100% DCI-P3, G-SYNC)",
            "GPU": "NVIDIA GeForce RTX 4080 12GB GDDR6 (115W TGP with Dynamic Boost)",
            "Battery_Life": "Up to 9 hours productivity (90Wh 4-cell Li-ion with 100W USB-C PD support)",
            "Weight": "1.85 kg (4.07 lbs)",
            "Ports": "1x Thunderbolt 4, 1x USB-C 3.2 Gen 2 (DP/PD), 2x USB-A 3.2 Gen 2, HDMI 2.1 FRL, SD Card reader",
            "OS": "Windows 11 Home"
        }
    },
    {
        id: 6,
        category: 1,
        subCategory: 2,
        name: 'HP Spectre x360 14 2-in-1',
        company: 'HP',
        price: 17800,
        oldPrice: 19500,
        sku: 'HP-SPEC14-U7-16-1TB',
        shipping: 0,
        availability: 'In Stock',
        stockAvail: 31,
        stockRes: 2,
        stockSold: 40,
        desc: 'A versatile convertible with 360-degree gem-cut CNC styling, 2.8K OLED touch panel with stylus support, 9MP AI intelligent camera, and Poly Studio quad speakers for executive presentations.',
        descFr: 'Ultrabook 2-en-1 convertible d\'exception avec écran OLED 2.8K tactile, stylet rechargeable inclus, processeur Intel Core Ultra 7 et son immersif Poly Studio.',
        specs: {
            "Processor": "Intel Core Ultra 7 155H (16 cores, up to 4.8 GHz with Intel AI NPU)",
            "RAM": "16GB LPDDR5X 7467MHz",
            "Storage": "1TB PCIe Gen 4 NVMe TLC M.2 SSD",
            "Display": "14.0-inch 2.8K OLED Touch (2880x1800, 48-120Hz VRR, 500 nits HDR, Corning Gorilla Glass NBT)",
            "GPU": "Intel Arc Integrated Graphics",
            "Battery_Life": "Up to 13 hours mixed usage (68Wh Li-ion polymer, 65W USB-C charger)",
            "Weight": "1.44 kg (3.19 lbs)",
            "Ports": "2x Thunderbolt 4 with USB4 Type-C (40Gbps), 1x USB-A 10Gbps, 3.5mm combo audio jack",
            "OS": "Windows 11 Home"
        }
    },
    {
        id: 7,
        category: 1,
        subCategory: 11,
        name: 'Lenovo Legion Pro 7i Gen 9',
        company: 'Lenovo',
        price: 36500,
        oldPrice: 39900,
        sku: 'LEGION-P7-I9-32-1TB-4090',
        shipping: 0,
        availability: 'In Stock',
        stockAvail: 14,
        stockRes: 1,
        stockSold: 19,
        desc: 'Uncompromised desktop-replacement gaming rig. Armed with a 24-core Intel Core i9-14900HX, maximum wattage NVIDIA RTX 4090 (175W), Legion Coldfront vapor chamber cooling, and 240Hz PureSight Gaming display.',
        descFr: 'Monstre de puissance pour gamers et ingénieurs CAO : Core i9-14900HX 24 cœurs, RTX 4090 16GB VRAM 175W et écran WQXGA 240Hz 500 nits calibré X-Rite Pantone.',
        specs: {
            "Processor": "Intel Core i9-14900HX (24 cores, 32 threads, 36MB cache, up to 5.8 GHz Turbo)",
            "RAM": "32GB (2x16GB) Overclocked DDR5 5600MHz",
            "Storage": "1TB PCIe 4.0 NVMe SSD (dual M.2 slots available)",
            "Display": "16.0-inch WQXGA IPS (2560x1600, 240Hz, 500 nits, 100% sRGB, DisplayHDR 400, G-SYNC)",
            "GPU": "NVIDIA GeForce RTX 4090 16GB GDDR6 (175W Max TGP with Boost)",
            "Battery_Life": "Up to 5 hours mixed usage (99.9Wh max FAA airline legal battery, 330W GaN power adapter)",
            "Weight": "2.62 kg (5.77 lbs)",
            "Ports": "1x Thunderbolt 4, 1x USB-C 3.2 Gen 2 (140W PD), 4x USB-A 3.2 Gen 1, HDMI 2.1, RJ-45 Gigabit LAN",
            "OS": "Windows 11 Home"
        }
    },
    {
        id: 8,
        category: 1,
        subCategory: 2,
        name: 'Asus Zenbook 14 OLED UX3405',
        company: 'Asus',
        price: 12900,
        oldPrice: 14200,
        sku: 'ZEN-UX3405-U7-16-1TB',
        shipping: 0,
        availability: 'In Stock',
        stockAvail: 45,
        stockRes: 5,
        stockSold: 67,
        desc: 'The pinnacle of portable productivity. Weighing just 1.2 kg with a 14.9 mm profile, this all-metal ultrabook features a vivid 3K 120Hz ASUS Lumina OLED panel, Intel Core Ultra 7 with built-in AI coprocessor, and 15+ hour battery longevity.',
        descFr: 'Ultrabook ultra-fin (1.2 kg) avec écran d\'exception 3K 120Hz ASUS Lumina OLED, processeur Intel Core Ultra 7 et autonomie supérieure à 15 heures.',
        specs: {
            "Processor": "Intel Core Ultra 7 155H (16 cores, up to 4.8 GHz with Intel AI Boost NPU)",
            "RAM": "16GB LPDDR5X 7467MHz",
            "Storage": "1TB M.2 NVMe PCIe 4.0 SSD",
            "Display": "14.0-inch 3K ASUS Lumina OLED (2880x1800, 16:10, 120Hz, 0.2ms, 600 nits HDR, 100% DCI-P3)",
            "GPU": "Intel Arc Graphics",
            "Battery_Life": "Up to 15 hours battery life (75Wh Li-ion, 65W Type-C fast charge 60% in 49 min)",
            "Weight": "1.20 kg (2.65 lbs)",
            "Ports": "2x Thunderbolt 4 USB-C, 1x USB-A 3.2 Gen 1, 1x HDMI 2.1 TMDS, 3.5mm combo audio jack",
            "OS": "Windows 11 Home"
        }
    },
    {
        id: 9,
        category: 1,
        subCategory: 2,
        name: 'HP Pavilion 15s Silver',
        company: 'HP',
        price: 6890,
        oldPrice: 7500,
        sku: 'HP-PAV15S-I5-16-512',
        shipping: 0,
        availability: 'In Stock',
        stockAvail: 58,
        stockRes: 6,
        stockSold: 112,
        desc: 'Reliable, stylish daily workhorse for home office, academics, and web multitasking. Features a 13th Gen Intel Core i5 processor, 16GB RAM, fast NVMe SSD storage, and numeric keypad in a natural silver finish.',
        descFr: 'PC portable polyvalent et abordable idéal pour étudiants et bureautique : Core i5-1335U, 16GB de mémoire vive, 512GB SSD et écran Full HD antireflet.',
        specs: {
            "Processor": "Intel Core i5-1335U (10 cores, 12 threads, up to 4.6 GHz with Intel Turbo Boost)",
            "RAM": "16GB (2x8GB) DDR4 3200MHz",
            "Storage": "512GB PCIe NVMe M.2 SSD",
            "Display": "15.6-inch Full HD Micro-edge IPS (1920x1080, 250 nits, Anti-glare)",
            "GPU": "Intel Iris Xe Graphics",
            "Battery_Life": "Up to 7.5 hours mixed usage (41Wh 3-cell Li-ion, HP Fast Charge 50% in 45 min)",
            "Weight": "1.69 kg (3.72 lbs)",
            "Ports": "1x USB Type-C 5Gbps, 2x USB Type-A 5Gbps, 1x HDMI 1.4b, 1x AC smart pin, 3.5mm headphone/mic",
            "OS": "Windows 11 Home"
        }
    },

    // -------------------------------------------------------------
    // CATEGORY 2: SMARTPHONES & TABLETS (9 Products)
    // -------------------------------------------------------------
    {
        id: 10,
        category: 2,
        subCategory: 3,
        name: 'Apple iPhone 16 Pro Max 512GB Desert Titanium',
        company: 'Apple',
        price: 18900,
        oldPrice: 20500,
        sku: 'IP16PM-512-DESERT-TI',
        shipping: 0,
        availability: 'In Stock',
        stockAvail: 22,
        stockRes: 4,
        stockSold: 94,
        desc: 'The pinnacle of smartphone engineering with an expanded 6.9-inch Super Retina XDR display, micro-blasted Grade 5 Titanium frame, dedicated Camera Control tactile button, and revolutionary 48MP Fusion camera system.',
        descFr: 'Le fleuron Apple avec écran 6.9 pouces Super Retina XDR, bouton Commande de l\'appareil photo, puce A18 Pro et téléobjectif 5x en titane sablé.',
        specs: {
            "Display": "6.9-inch Super Retina XDR OLED (2868x1320 at 460 ppi, 120Hz ProMotion, Always-On, 2000 nits peak)",
            "Chipset": "Apple A18 Pro (3nm, 6-core CPU, 6-core GPU with Hardware Ray Tracing, 16-core Neural Engine)",
            "RAM": "8GB LPDDR5X",
            "Storage": "512GB NVMe Storage",
            "Camera_Main": "48MP Fusion (f/1.78, 2nd-gen Sensor-shift OIS) + 48MP Ultra-Wide (f/2.2) + 12MP 5x Telephoto (120mm)",
            "Camera_Selfie": "12MP TrueDepth (f/1.9, Autofocus, 4K60 HDR Dolby Vision)",
            "Battery_Capacity": "4685 mAh (Up to 33 hours video playback)",
            "Charging_Speed": "30W Wired Fast Charging (50% in 30 min) + 25W MagSafe Wireless Charging",
            "OS": "iOS 18 with Apple Intelligence",
            "Dimensions_Weight": "163.0 x 77.6 x 8.25 mm, 227 g (Grade 5 Titanium)"
        }
    },
    {
        id: 11,
        category: 2,
        subCategory: 3,
        name: 'Apple iPhone 16 128GB Black',
        company: 'Apple',
        price: 10800,
        oldPrice: 11900,
        sku: 'IP16-128-BLK',
        shipping: 0,
        availability: 'In Stock',
        stockAvail: 55,
        stockRes: 3,
        stockSold: 130,
        desc: 'Equipped with the all-new A18 chip designed for Apple Intelligence, versatile 48MP Fusion dual camera with 2x optical-quality telephoto, Camera Control, and durable color-infused back glass.',
        descFr: 'L\'iPhone 16 intègre la puce A18 ultra-rapide, le bouton Action, la Commande de l\'appareil photo et un capteur Fusion 48 Mpx 2-en-1.',
        specs: {
            "Display": "6.1-inch Super Retina XDR OLED (2556x1179 at 460 ppi, Dynamic Island, 2000 nits peak)",
            "Chipset": "Apple A18 (3nm, 6-core CPU, 5-core GPU, 16-core Neural Engine)",
            "RAM": "8GB LPDDR5X",
            "Storage": "128GB NVMe",
            "Camera_Main": "48MP Fusion (26mm, f/1.6, Sensor-shift OIS) + 12MP Ultra-Wide (13mm, f/2.2, Macro mode)",
            "Camera_Selfie": "12MP TrueDepth (f/1.9, 4K60 Dolby Vision)",
            "Battery_Capacity": "3561 mAh (Up to 22 hours video playback)",
            "Charging_Speed": "25W Wired Fast Charging + 25W MagSafe Qi2",
            "OS": "iOS 18",
            "Dimensions_Weight": "147.6 x 71.6 x 7.80 mm, 170 g (Aerospace-grade Aluminum)"
        }
    },
    {
        id: 12,
        category: 2,
        subCategory: 4,
        name: 'Samsung Galaxy S25 Ultra 512GB Titanium Gray',
        company: 'Samsung',
        price: 16900,
        oldPrice: 18500,
        sku: 'S25U-512-TI-GRY',
        shipping: 0,
        availability: 'In Stock',
        stockAvail: 29,
        stockRes: 2,
        stockSold: 71,
        desc: 'The ultimate AI flagship featuring a 200MP Quad Telephoto camera system, integrated S-Pen stylus, Snapdragon 8 Gen 4 for Galaxy with ray-tracing graphics, and an anti-reflective Gorilla Armor display.',
        descFr: 'Le smartphone Android ultime avec stylet S-Pen intégré, capteur photo 200 Mpx avec zoom optique IA x5/x10 et puce Snapdragon 8 Gen 4 gravée en 3nm.',
        specs: {
            "Display": "6.8-inch Dynamic AMOLED 2X Flat (3120x1440 QHD+, 1-120Hz LTPO, 2600 nits peak, Gorilla Armor)",
            "Chipset": "Qualcomm Snapdragon 8 Gen 4 for Galaxy (3nm Octa-core up to 4.3 GHz)",
            "RAM": "12GB LPDDR5X",
            "Storage": "512GB UFS 4.0",
            "Camera_Main": "200MP Main (f/1.7, OIS) + 50MP 5x Periscope Telephoto + 10MP 3x Telephoto + 50MP Ultra-Wide",
            "Camera_Selfie": "12MP Dual Pixel AF (f/2.2, 4K60 HDR10+)",
            "Battery_Capacity": "5000 mAh (Li-Ion)",
            "Charging_Speed": "45W Fast Charging (65% in 30 min) + 15W Wireless + 4.5W Reverse Wireless",
            "OS": "Android 15 with One UI 7 (7 years OS updates)",
            "Dimensions_Weight": "162.3 x 79.0 x 8.6 mm, 232 g (Grade 5 Titanium Frame, IP68)"
        }
    },
    {
        id: 13,
        category: 2,
        subCategory: 4,
        name: 'Samsung Galaxy Z Fold 6 512GB Silver Shadow',
        company: 'Samsung',
        price: 19500,
        oldPrice: 21900,
        sku: 'ZFOLD6-512-SLV',
        shipping: 0,
        availability: 'In Stock',
        stockAvail: 16,
        stockRes: 1,
        stockSold: 24,
        desc: 'Unfold PC-grade productivity in your pocket. Thinner, lighter Armor Aluminum hinge, 7.6-inch Dynamic AMOLED 2X interior folding screen, dual-screen interpreter, and Galaxy AI multi-window orchestration.',
        descFr: 'L\'expérience pliable par excellence : écran intérieur immersif 7.6 pouces 120Hz, Galaxy AI, châssis Armor Aluminium affiné et triple capteur photo 50 Mpx.',
        specs: {
            "Display": "Main: 7.6-inch Foldable Dynamic AMOLED 2X (2160x1856, 1-120Hz, 2600 nits) | Cover: 6.3-inch (2376x968)",
            "Chipset": "Qualcomm Snapdragon 8 Gen 3 for Galaxy (4nm, Octa-core 3.39 GHz)",
            "RAM": "12GB LPDDR5X",
            "Storage": "512GB UFS 4.0",
            "Camera_Main": "50MP Wide (f/1.8, Dual Pixel OIS) + 10MP 3x Telephoto + 12MP Ultra-Wide",
            "Camera_Selfie": "Cover: 10MP (f/2.2) | Under Display: 4MP (f/1.8)",
            "Battery_Capacity": "4400 mAh Dual-cell battery",
            "Charging_Speed": "25W Super Fast Charging (50% in 30 min) + 15W Fast Wireless Charging 2.0",
            "OS": "Android 14 with One UI 6.1.1 (S-Pen Fold Edition support)",
            "Dimensions_Weight": "Folded: 153.5 x 68.1 x 12.1 mm | Unfolded: 153.5 x 132.6 x 5.6 mm, 239 g"
        }
    },
    {
        id: 14,
        category: 2,
        subCategory: 4,
        name: 'Google Pixel 9 Pro XL 256GB Obsidian',
        company: 'Google',
        price: 13200,
        oldPrice: 14500,
        sku: 'PIXEL9PXL-256-OBS',
        shipping: 0,
        availability: 'In Stock',
        stockAvail: 33,
        stockRes: 2,
        stockSold: 48,
        desc: 'Engineered by Google with the custom Tensor G4 chip, 16GB RAM for on-device Gemini Nano multimodal AI, pro triple camera system with Super Res Zoom up to 30x, and 7 years of Pixel Feature Drops.',
        descFr: 'Le smartphone IA référence de Google : puce Tensor G4, 16GB de RAM pour l\'IA Gemini Nano, capteur photo pro 50 Mpx et 7 ans de mises à jour Android garanties.',
        specs: {
            "Display": "6.8-inch Super Actua LTPO OLED (1344x2992 at 486 ppi, 1-120Hz, 3000 nits peak, Gorilla Glass Victus 2)",
            "Chipset": "Google Tensor G4 (4nm with Titan M2 security coprocessor)",
            "RAM": "16GB LPDDR5X",
            "Storage": "256GB UFS 3.1",
            "Camera_Main": "50MP Octa PD Wide (f/1.68, OIS) + 48MP Quad PD Ultra-Wide with Macro + 48MP 5x Telephoto (30x Super Res)",
            "Camera_Selfie": "42MP Dual PD Selfie camera with Autofocus (103° ultrawide field of view)",
            "Battery_Capacity": "5060 mAh (24+ hour battery life, Extreme Battery Saver up to 100 hours)",
            "Charging_Speed": "37W Fast USB-C Charging (70% in 30 min) + 23W Pixel Stand Wireless",
            "OS": "Android 15 (7 years OS & security updates)",
            "Dimensions_Weight": "162.8 x 76.6 x 8.5 mm, 221 g (Polished Aluminum & Matte Glass, IP68)"
        }
    },
    {
        id: 15,
        category: 2,
        subCategory: 4,
        name: 'Xiaomi 14 Ultra 512GB Leica Kit',
        company: 'Xiaomi',
        price: 14500,
        oldPrice: 15900,
        sku: 'XM14U-512-LEICA',
        shipping: 0,
        availability: 'In Stock',
        stockAvail: 20,
        stockRes: 1,
        stockSold: 31,
        desc: 'A true professional camera that happens to be a smartphone. Co-engineered with Leica, featuring a 1-inch LYT-900 sensor with stepless variable aperture (f/1.63 - f/4.0), quad 50MP Leica Summilux lenses, and 8K video recording.',
        descFr: 'Véritable appareil photo expert intégré : capteur géant 1 pouce Sony LYT-900, optiques Leica Summilux à ouverture variable et recharge ultra-rapide 90W.',
        specs: {
            "Display": "6.73-inch WQHD+ AMOLED (3200x1440, 1-120Hz LTPO, 3000 nits, Dolby Vision, Xiaomi Shield Glass)",
            "Chipset": "Qualcomm Snapdragon 8 Gen 3 (4nm, Octa-core up to 3.3 GHz)",
            "RAM": "16GB LPDDR5X",
            "Storage": "512GB UFS 4.0",
            "Camera_Main": "50MP 1-inch Sony LYT-900 (f/1.63-f/4.0 stepless OIS) + 50MP 3.2x Tele + 50MP 5x Periscope + 50MP Ultra-Wide",
            "Camera_Selfie": "32MP in-display selfie camera (f/2.0, 4K60 recording)",
            "Battery_Capacity": "5000 mAh Xiaomi Surge Battery",
            "Charging_Speed": "90W HyperCharge (100% in 33 min) + 80W Wireless HyperCharge (100% in 46 min)",
            "OS": "Xiaomi HyperOS based on Android 14",
            "Dimensions_Weight": "161.4 x 75.3 x 9.2 mm, 219.8 g (High-density Nano-tech Vegan Leather, IP68)"
        }
    },
    {
        id: 16,
        category: 2,
        subCategory: 4,
        name: 'Xiaomi Redmi Note 13 Pro+ 5G (512GB)',
        company: 'Xiaomi',
        price: 4200,
        oldPrice: 4600,
        sku: 'RN13PP-512-MIDNIGHT',
        shipping: 0,
        availability: 'In Stock',
        stockAvail: 74,
        stockRes: 8,
        stockSold: 160,
        desc: 'The best value mid-range champion in Morocco. Features a flagship 200MP OIS camera, 1.5K 120Hz curved AMOLED display, MediaTek Dimensity 7200-Ultra 5G processor, IP68 water resistance, and 120W HyperCharge.',
        descFr: 'Le roi du rapport qualité/prix : capteur photo 200 Mpx OIS, écran incurvé AMOLED 1.5K 120Hz, étanchéité IP68 et charge ultra-rapide 120W (100% en 19 min).',
        specs: {
            "Display": "6.67-inch 1.5K CrystalRes 3D Curved AMOLED (2712x1220, 120Hz, 1800 nits peak, Gorilla Glass Victus)",
            "Chipset": "MediaTek Dimensity 7200-Ultra (4nm 5G, Octa-core up to 2.8 GHz)",
            "RAM": "12GB LPDDR5",
            "Storage": "512GB UFS 3.1",
            "Camera_Main": "200MP Samsung ISOCELL HP3 (f/1.65, 1/1.4\" sensor, OIS + EIS, 4x lossless zoom) + 8MP Ultra-wide + 2MP Macro",
            "Camera_Selfie": "16MP Front camera (f/2.4)",
            "Battery_Capacity": "5000 mAh",
            "Charging_Speed": "120W HyperCharge (100% in 19 mins via included GaN charger)",
            "OS": "MIUI 14 / HyperOS upgradeable",
            "Dimensions_Weight": "161.4 x 74.2 x 8.9 mm, 204.5 g (IP68 dust/water resistant)"
        }
    },
    {
        id: 17,
        category: 2,
        subCategory: 12,
        name: 'Apple iPad Pro 13" M4 Ultra-Thin OLED (512GB)',
        company: 'Apple',
        price: 17400,
        oldPrice: 18900,
        sku: 'IPADPRO13-M4-512-CELL',
        shipping: 0,
        availability: 'In Stock',
        stockAvail: 26,
        stockRes: 2,
        stockSold: 39,
        desc: 'The thinnest Apple product ever made at just 5.1 mm. Powered by the groundbreaking M4 chip with 38 TOPS Neural Engine, Tandem OLED Ultra Retina XDR display, Apple Pencil Pro support, and 5G cellular connectivity.',
        descFr: 'La tablette professionnelle ultime : puce M4 surpuissante, écran Ultra Retina XDR Tandem OLED révolutionnaire et design ultra-fin de seulement 5.1 mm.',
        specs: {
            "Display": "13.0-inch Tandem OLED Ultra Retina XDR (2752x2064 at 264 ppi, 10-120Hz ProMotion, 1600 nits peak HDR)",
            "Chipset": "Apple M4 (9-core CPU, 10-core GPU with Ray Tracing, 16-core Neural Engine 38 TOPS)",
            "RAM": "8GB Unified Memory",
            "Storage": "512GB NVMe Storage",
            "Camera_Main": "12MP Wide camera (f/1.8, 4K ProRes video, LiDAR Scanner for AR depth)",
            "Camera_Selfie": "Landscape 12MP Ultra-Wide front camera with Center Stage (f/2.4, Face ID)",
            "Battery_Capacity": "38.99 Wh rechargeable lithium-polymer (Up to 10 hours web/video)",
            "Charging_Speed": "30W Fast Charging via Thunderbolt 3 / USB4 port",
            "OS": "iPadOS 17.5 (iPadOS 18 ready with Apple Pencil Pro haptic squeeze)",
            "Dimensions_Weight": "281.6 x 215.5 x 5.1 mm, 582 g (100% Recycled Aluminum Enclosure)"
        }
    },
    {
        id: 18,
        category: 2,
        subCategory: 12,
        name: 'Samsung Galaxy Tab S10 Ultra 5G (512GB)',
        company: 'Samsung',
        price: 14900,
        oldPrice: 16200,
        sku: 'TABS10U-512-SPEN',
        shipping: 0,
        availability: 'In Stock',
        stockAvail: 21,
        stockRes: 2,
        stockSold: 28,
        desc: 'Massive 14.6-inch Dynamic AMOLED 2X productivity tablet equipped with an IP68-certified S-Pen, anti-reflective display coating, MediaTek Dimensity 9300+ powerhouse processor, and DeX workstation desktop mode.',
        descFr: 'Tablette grand format 14.6 pouces AMOLED 2X 120Hz avec stylet S-Pen étanche inclus, mode Samsung DeX pour remplacer un PC et connectivité 5G ultra-rapide.',
        specs: {
            "Display": "14.6-inch Dynamic AMOLED 2X (2960x1848 WQXGA+, 120Hz, Anti-Reflection coating, HDR10+)",
            "Chipset": "MediaTek Dimensity 9300+ (4nm Octa-core up to 3.4 GHz with APU 790 AI Engine)",
            "RAM": "12GB LPDDR5X",
            "Storage": "512GB UFS 4.0 (expandable up to 1.5TB via MicroSD card)",
            "Camera_Main": "13MP Wide AF + 8MP Ultra-Wide rear dual cameras (4K30 recording)",
            "Camera_Selfie": "Dual 12MP Wide + 12MP Ultra-Wide landscape cameras with Auto Framing",
            "Battery_Capacity": "11,200 mAh Long-lasting battery",
            "Charging_Speed": "45W Super Fast Charging 2.0 (Full charge in approx 90 min)",
            "OS": "Android 14 with One UI 6.1.1 (Galaxy AI Sketch to Image & Note Assist)",
            "Dimensions_Weight": "326.4 x 208.6 x 5.4 mm, 723 g (Enhanced Armor Aluminum, IP68 Tablet & S-Pen)"
        }
    },

    // -------------------------------------------------------------
    // CATEGORY 3: AUDIO & ACOUSTICS (8 Products)
    // -------------------------------------------------------------
    {
        id: 19,
        category: 3,
        subCategory: 5,
        name: 'Sony WH-1000XM5 Wireless Noise Canceling',
        company: 'Sony',
        price: 4190,
        oldPrice: 4690,
        sku: 'SONY-WH1000XM5-BLK',
        shipping: 0,
        availability: 'In Stock',
        stockAvail: 64,
        stockRes: 4,
        stockSold: 140,
        desc: 'Industry-leading noise cancellation powered by two proprietary processors and 8 microphones. Features Auto NC Optimizer, Hi-Res Audio wireless streaming via LDAC, crystal-clear 4-mic beamforming calls, and 30-hour battery stamina.',
        descFr: 'Le casque à réduction de bruit active n°1 mondial : 8 micros, double processeur V1/QN1, son Hi-Res Audio LDAC et 30 heures d\'autonomie avec charge rapide.',
        specs: {
            "Driver_Size": "30mm Precision-engineered Carbon Fiber composite dome",
            "Frequency_Response": "4 Hz - 40,000 Hz (Hi-Res Audio Certified wired / LDAC wireless)",
            "Battery_Life": "30 hours with ANC ON (40 hours with ANC OFF, 3 min charge = 3 hours playback)",
            "Connectivity": "Bluetooth 5.2 (LDAC, AAC, SBC, Multi-point pairing 2 devices) + 3.5mm analog cable",
            "Active_Noise_Cancellation": "Yes (Dual Processor: Integrated Processor V1 + HD Noise Cancelling Processor QN1 with 8 microphones)",
            "Weight": "250 g (Soft fit leather with stepless slider)"
        }
    },
    {
        id: 20,
        category: 3,
        subCategory: 5,
        name: 'Apple AirPods Max Space Gray USB-C',
        company: 'Apple',
        price: 5900,
        oldPrice: 6500,
        sku: 'APMAX-USBC-GRY',
        shipping: 0,
        availability: 'In Stock',
        stockAvail: 27,
        stockRes: 2,
        stockSold: 58,
        desc: 'The ultimate high-fidelity over-ear headphone. Crafted with an acoustically engineered canopy knit mesh, memory foam ear cushions, dual Apple H1 computational audio chips, Personalized Spatial Audio with dynamic head tracking, and USB-C charging.',
        descFr: 'Casque circum-auriculaire haute fidélité avec réduction active du bruit de niveau pro, audio spatial personnalisé avec suivi dynamique des mouvements de la tête et port USB-C.',
        specs: {
            "Driver_Size": "40mm Apple-designed custom dynamic driver (dual neodymium ring magnet motor)",
            "Frequency_Response": "10 Hz - 20,000 Hz with Total Harmonic Distortion under 1%",
            "Battery_Life": "Up to 20 hours with Active Noise Cancellation or Transparency mode enabled",
            "Connectivity": "Bluetooth 5.0 + USB-C lossless wired audio playback support",
            "Active_Noise_Cancellation": "Yes (Pro-level Active Noise Cancellation with Transparency mode and Adaptive EQ)",
            "Weight": "384.8 g (Stainless steel headband and anodized aluminum earcups)"
        }
    },
    {
        id: 21,
        category: 3,
        subCategory: 5,
        name: 'Bose QuietComfort Ultra Wireless ANC',
        company: 'Bose',
        price: 4490,
        oldPrice: 4990,
        sku: 'BOSE-QCULTRA-BLK',
        shipping: 0,
        availability: 'In Stock',
        stockAvail: 38,
        stockRes: 3,
        stockSold: 62,
        desc: 'World-class noise cancellation combined with groundbreaking Bose Immersive Audio spatial sound. CustomTune technology automatically tailors audio performance to the unique shape of your ears for unmatched acoustic fidelity.',
        descFr: 'Casque Bose sans fil ultra-confortable avec audio immersif spatialisé, technologie de calibrage CustomTune et réduction de bruit active légendaire.',
        specs: {
            "Driver_Size": "35mm Custom TriPort Acoustic Headphone Structure",
            "Frequency_Response": "20 Hz - 20,000 Hz (aptX Adaptive, Snapdragon Sound Certified, AAC, SBC)",
            "Battery_Life": "Up to 24 hours playback (Up to 18 hours with Immersive Audio, 15 min charge = 2.5 hours)",
            "Connectivity": "Bluetooth 5.3 (Multipoint connection) + 2.5mm to 3.5mm audio cable",
            "Active_Noise_Cancellation": "Yes (Quiet Mode, Aware Mode with ActiveSense, and Immersion Mode)",
            "Weight": "252 g (Plush protein leather ear cushions and cast aluminum arms)"
        }
    },
    {
        id: 22,
        category: 3,
        subCategory: 5,
        name: 'Sennheiser Momentum 4 Wireless Audiophile',
        company: 'Sennheiser',
        price: 3490,
        oldPrice: 3890,
        sku: 'SENN-M4-BLK',
        shipping: 0,
        availability: 'In Stock',
        stockAvail: 41,
        stockRes: 2,
        stockSold: 53,
        desc: 'Audiophile-inspired 42mm transducer system delivering Sennheiser signature sound with class-leading 60-hour battery life. Features Adaptive Noise Cancellation, Transparency Mode, and customizable Sound Personalization.',
        descFr: 'Casque sans fil audiophile avec haut-parleur de 42 mm, égaliseur dynamique et une autonomie record de 60 heures avec réduction de bruit active.',
        specs: {
            "Driver_Size": "42mm Sennheiser Audiophile Dynamic Transducer",
            "Frequency_Response": "6 Hz - 22,000 Hz (aptX, aptX Adaptive, AAC, SBC)",
            "Battery_Life": "Up to 60 hours continuous music playback via Bluetooth with ANC ON (700mAh battery)",
            "Connectivity": "Bluetooth 5.2 (Class 1, 10mW, Multi-point) + 3.5mm analog cable + USB-C Digital DAC audio",
            "Active_Noise_Cancellation": "Yes (Hybrid Adaptive Active Noise Cancellation with adjustable Transparency Mode)",
            "Weight": "293 g"
        }
    },
    {
        id: 23,
        category: 3,
        subCategory: 6,
        name: 'Apple AirPods Pro 2 USB-C MagSafe',
        company: 'Apple',
        price: 2890,
        oldPrice: 3190,
        sku: 'APP2-USBC-MAGSAFE',
        shipping: 0,
        availability: 'In Stock',
        stockAvail: 82,
        stockRes: 6,
        stockSold: 210,
        desc: 'Powered by the H2 chip, AirPods Pro 2 feature up to 2x more Active Noise Cancellation, Adaptive Audio that automatically tailors noise control to your environment, Conversation Awareness, and USB-C MagSafe charging case.',
        descFr: 'Écouteurs sans fil référence avec puce H2, réduction active du bruit 2x plus performante, audio adaptatif intelligent et boîtier de charge MagSafe USB-C avec haut-parleur Localiser.',
        specs: {
            "Driver_Size": "Custom high-excursion Apple dynamic driver with custom high dynamic range amplifier",
            "Frequency_Response": "20 Hz - 20,000 Hz with Adaptive EQ",
            "Battery_Life": "Up to 6 hours listening time with ANC ON (Up to 30 hours total with MagSafe USB-C charging case)",
            "Connectivity": "Bluetooth 5.3 + Apple H2 headphone chip in earbuds, Apple U1/H1 chip in case",
            "Active_Noise_Cancellation": "Yes (Up to 2x more ANC, Adaptive Audio, Transparency mode, Conversation Awareness)",
            "Weight": "Earbuds: 5.3 g each | MagSafe Case: 50.8 g (IP54 dust, sweat, and water resistant)"
        }
    },
    {
        id: 24,
        category: 3,
        subCategory: 6,
        name: 'Sony WF-1000XM5 True Wireless Earbuds',
        company: 'Sony',
        price: 2790,
        oldPrice: 3090,
        sku: 'SONY-WF1000XM5-BLK',
        shipping: 0,
        availability: 'In Stock',
        stockAvail: 59,
        stockRes: 4,
        stockSold: 115,
        desc: 'Astonishing sound quality and noise cancelling performance in an ultra-compact earbud. Equipped with Dynamic Driver X, dual feedback microphones, Integrated Processor V2, and AI-based bone conduction voice pickup.',
        descFr: 'Écouteurs intra-auriculaires True Wireless avec technologie Dynamic Driver X, processeur HD QN2e, son haute résolution LDAC et réduction de bruit active de pointe.',
        specs: {
            "Driver_Size": "8.4mm Dynamic Driver X with multi-material dome structure",
            "Frequency_Response": "20 Hz - 40,000 Hz (LDAC 96kHz/990kbps Hi-Res Wireless, AAC, SBC, LC3)",
            "Battery_Life": "Up to 8 hours with ANC ON (Up to 24 hours with wireless charging case, 3 min quick charge = 60 min)",
            "Connectivity": "Bluetooth 5.3 (Multi-point 2 devices, LE Audio ready)",
            "Active_Noise_Cancellation": "Yes (HD Noise Cancelling Processor QN2e + Integrated Processor V2 with 6 mics)",
            "Weight": "Earbuds: 5.9 g each | Case: 39 g (Qi Wireless charging + IPX4 water resistance)"
        }
    },
    {
        id: 25,
        category: 3,
        subCategory: 13,
        name: 'Marshall Stanmore III Bluetooth Home Speaker',
        company: 'Marshall',
        price: 3900,
        oldPrice: 4290,
        sku: 'MARSHALL-STAN3-BLK',
        shipping: 0,
        availability: 'In Stock',
        stockAvail: 34,
        stockRes: 3,
        stockSold: 47,
        desc: 'Iconic vintage rock-and-roll styling paired with an expansive soundstage. Features outward-angled tweeters, updated waveguides, Dynamic Loudness tone compensation, and Bluetooth 5.2 next-gen LE Audio readiness.',
        descFr: 'Enceinte résidentielle Bluetooth emblématique au son puissant et immersif avec potentiomètres en laiton analogiques, amplification 80W Classe D et entrées RCA/Aux.',
        specs: {
            "Driver_Size": "1x 50W Class D Woofer + 2x 15W Class D Tweeters (80W Total Output Power)",
            "Frequency_Response": "45 Hz - 20,000 Hz (Max SPL: 97 dB @ 1 m, Bass-reflex cabinet)",
            "Battery_Life": "Mains-powered AC 100-240V (50/60Hz Home Speaker)",
            "Connectivity": "Bluetooth 5.2 (10m range, LE Audio ready) + 3.5 mm Aux Input + RCA analog inputs",
            "Active_Noise_Cancellation": "No (Acoustic placement Dynamic Loudness room compensation)",
            "Weight": "4.25 kg (9.37 lbs, 350 x 203 x 188 mm)"
        }
    },
    {
        id: 26,
        category: 3,
        subCategory: 13,
        name: 'Sonos Arc Premium Smart Soundbar with Dolby Atmos',
        company: 'Sonos',
        price: 9400,
        oldPrice: 10500,
        sku: 'SONOS-ARC-BLK',
        shipping: 0,
        availability: 'In Stock',
        stockAvail: 18,
        stockRes: 2,
        stockSold: 32,
        desc: 'Bring movies, gaming, and concerts to life with realistic 3D Dolby Atmos sound. Eleven high-performance drivers including custom elliptical woofers and angled side tweeters create an ultra-wide soundstage with crystal-clear dialogue tuning.',
        descFr: 'Barre de son cinéma maison haut de gamme avec son spatial Dolby Atmos 3D, 11 haut-parleurs calibrés, optimisation vocale Speech Enhancement et connexion HDMI eARC.',
        specs: {
            "Driver_Size": "11 Class-D digital amplifiers (8 elliptical woofers + 3 silk-dome angled tweeters)",
            "Frequency_Response": "35 Hz - 20,000 Hz with Trueplay acoustic room tuning software",
            "Battery_Life": "Mains-powered AC 100-240V",
            "Connectivity": "HDMI eARC, Optical audio adapter, Wi-Fi 802.11b/g/n (2.4 GHz), Apple AirPlay 2, Ethernet port",
            "Active_Noise_Cancellation": "No (Far-field microphone array with beamforming for voice control and echo cancellation)",
            "Weight": "6.25 kg (13.78 lbs, 1141.7 x 87 x 115.7 mm)"
        }
    },

    // -------------------------------------------------------------
    // CATEGORY 4: ACCESSORIES & DOCKS (8 Products)
    // -------------------------------------------------------------
    {
        id: 27,
        category: 6,
        subCategory: 16,
        name: 'Anker 737 GaNPrime 140W 3-Port Power Bank (24000mAh)',
        company: 'Anker',
        price: 1290,
        oldPrice: 1450,
        sku: 'ANKER-737-140W',
        shipping: 40,
        availability: 'In Stock',
        stockAvail: 52,
        stockRes: 4,
        stockSold: 110,
        desc: 'Ultra-powerful two-way fast charging portable battery equipped with the latest Power Delivery 3.1 and bi-directional GaN technology. Capable of fast-charging a 16" MacBook Pro to 50% in just 40 minutes while displaying real-time power draw on a smart digital screen.',
        descFr: 'Batterie externe surpuissante de 24 000 mAh délivrant jusqu\'à 140W en USB-C PD 3.1 avec écran couleur intelligent affichant la puissance et l\'autonomie en temps réel.',
        specs: {
            "Type": "Ultra-High Output Power Delivery 3.1 Portable Power Bank",
            "Compatibility": "Universal (MacBook Pro, Dell XPS, iPhone 16, Galaxy S25, Steam Deck, iPad Pro)",
            "Power_Output_or_Sensors": "140W Max Single Port Output (2x USB-C PD 3.1 + 1x USB-A 18W, Total 140W concurrent)",
            "Material": "Flame-retardant poly-carbonate casing with ActiveShield 2.0 dynamic temperature monitoring",
            "Dimensions_Weight": "155.8 x 54.6 x 49.6 mm, 630 g (24,000 mAh / 86.4 Wh airline approved)",
            "Warranty": "2-Year Official ZeyTech Hub-A1 Warranty"
        }
    },
    {
        id: 28,
        category: 6,
        subCategory: 16,
        name: 'Ugreen Nexode 100W 4-Port GaN Desktop Fast Charger',
        company: 'Ugreen',
        price: 690,
        oldPrice: 790,
        sku: 'UGREEN-NEX-100W',
        shipping: 35,
        availability: 'In Stock',
        stockAvail: 78,
        stockRes: 5,
        stockSold: 165,
        desc: 'Clean up your desk with one compact powerhouse. Delivers up to 100W of high-speed charging across 3 USB-C and 1 USB-A ports with GaNFast technology and intelligent thermal management.',
        descFr: 'Chargeur de bureau 4 ports (3x USB-C + 1x USB-A) délivrant 100W GaN pour alimenter simultanément un ordinateur portable, un smartphone et des accessoires.',
        specs: {
            "Type": "4-Port GaN Desktop Multi-Device Fast Charger",
            "Compatibility": "Universal fast charging (PD 3.0, QC 4.0+, PPS 45W Samsung 2.0, AFC, FCP)",
            "Power_Output_or_Sensors": "100W Max USB-C1/C2 (Simultaneous 65W + 30W multi-device power distribution)",
            "Material": "Thermal-efficient GaN III semiconductor architecture in matte space gray enclosure",
            "Dimensions_Weight": "69 x 69 x 33 mm, 215 g",
            "Warranty": "1-Year Official Replacement Guarantee"
        }
    },
    {
        id: 29,
        category: 6,
        subCategory: 16,
        name: 'CalDigit TS4 Thunderbolt 4 18-Port Workstation Dock',
        company: 'CalDigit',
        price: 3800,
        oldPrice: 4200,
        sku: 'CALDIGIT-TS4-18P',
        shipping: 0,
        availability: 'In Stock',
        stockAvail: 19,
        stockRes: 2,
        stockSold: 35,
        desc: 'The ultimate connectivity dock for professional creative studios. Offers an unprecedented 18 ports of I/O, 98W laptop host power delivery, 2.5 Gigabit Ethernet, dual 6K 60Hz display support, and UHS-II SD card reading.',
        descFr: 'La station d\'accueil Thunderbolt 4 la plus complète au monde : 18 ports, alimentation 98W pour ordinateur portable, port Ethernet 2.5 GbE et support double écran 6K.',
        specs: {
            "Type": "Thunderbolt 4 / USB4 18-Port High-Bandwidth Docking Station",
            "Compatibility": "Universal (macOS 11.4+, Windows 10/11 with Thunderbolt 4, Thunderbolt 3, or USB4 host)",
            "Power_Output_or_Sensors": "98W Host Power Delivery (Charges laptops under full CPU/GPU rendering loads)",
            "Material": "Solid Extruded Anodized Aluminum with Passive Heat Sink Dissipation",
            "Dimensions_Weight": "198 x 113 x 42 mm, 640 g (Includes 230W Power Adapter and 0.8m 40Gbps TB4 Cable)",
            "Warranty": "2-Year Official ZeyTech Hub-A1 Direct Manufacturer Warranty"
        }
    },
    {
        id: 30,
        category: 6,
        subCategory: 16,
        name: 'Satechi 3-in-1 Foldable MagSafe Wireless Charging Stand',
        company: 'Satechi',
        price: 1150,
        oldPrice: 1290,
        sku: 'SATECHI-3IN1-MAG',
        shipping: 35,
        availability: 'In Stock',
        stockAvail: 46,
        stockRes: 3,
        stockSold: 84,
        desc: 'Elevate your nightstand or desk. Fast-charges your iPhone (15W MagSafe), Apple Watch Ultra (fast charger module), and AirPods Pro simultaneously on a premium space gray aluminum foldable travel stand.',
        descFr: 'Station de charge sans fil 3-en-1 pliable en aluminium spatial certifiée MagSafe 15W officiel pour iPhone, Apple Watch et AirPods.',
        specs: {
            "Type": "3-in-1 Foldable Official Qi2 / MagSafe Wireless Charging Station",
            "Compatibility": "iPhone 12 through iPhone 16 series, Apple Watch Series 1-10 & Ultra, AirPods Pro / AirPods 3/4",
            "Power_Output_or_Sensors": "15W MagSafe Phone Pad + 5W Apple Watch Fast Charger + 5W AirPods Wireless Tray",
            "Material": "Aerospace-grade Machined Aluminum with Vegan Leather touchpoints",
            "Dimensions_Weight": "145 x 75 x 22 mm (Folded), 310 g (Includes 45W USB-C PD Adapter and International Plugs)",
            "Warranty": "2-Year Manufacturer Warranty"
        }
    },
    {
        id: 31,
        category: 6,
        subCategory: 16,
        name: 'Baseus Blade 100W Ultra-Thin Laptop Power Bank (20000mAh)',
        company: 'Baseus',
        price: 890,
        oldPrice: 990,
        sku: 'BASEUS-BLADE-100W',
        shipping: 40,
        availability: 'In Stock',
        stockAvail: 63,
        stockRes: 4,
        stockSold: 120,
        desc: 'Sleek 18mm ultra-thin flat profile designed to slide effortlessly into any laptop sleeve alongside your computer. Delivers 100W Power Delivery with dual USB-C ports and digital real-time battery status.',
        descFr: 'Batterie externe ultra-plate (18 mm) d\'une capacité de 20 000 mAh délivrant 100W pour recharger les ordinateurs portables et consoles nomades.',
        specs: {
            "Type": "Ultra-Slim 100W Dual Type-C Laptop Power Bank",
            "Compatibility": "MacBook Pro/Air, Lenovo ThinkPad, Dell XPS, HP Spectre, ASUS Zenbook, iPad Pro, Steam Deck",
            "Power_Output_or_Sensors": "100W Single Port (USB-C1/C2: 5V/3A, 9V/3A, 12V/3A, 15V/3A, 20V/5A) + 2x USB-A 30W",
            "Material": "Silicon carbide textured casing with LED real-time voltage/current/time-remaining display",
            "Dimensions_Weight": "162 x 143 x 18 mm, 490 g (74Wh Airline Carry-On Safe)",
            "Warranty": "1-Year Official Warranty"
        }
    },
    {
        id: 32,
        category: 6,
        subCategory: 16,
        name: 'Anker PowerLine III Flow USB-C to USB-C 240W (2m)',
        company: 'Anker',
        price: 220,
        oldPrice: 250,
        sku: 'ANKER-FLOW-240W-2M',
        shipping: 25,
        availability: 'In Stock',
        stockAvail: 140,
        stockRes: 10,
        stockSold: 320,
        desc: 'Super soft, tangle-free silicone cable certified for the latest EPR 240W Power Delivery specification. Built with graphene shielding, 25,000-bend lifespan rating, and ultra-durable aluminum connector jackets.',
        descFr: 'Câble USB-C vers USB-C en silicone ultra-souple anti-nœuds supportant la charge ultra-rapide jusqu\'à 240W (EPR PD 3.1). Longueur 2 mètres.',
        specs: {
            "Type": "240W EPR USB-C to USB-C High-Power Silicone Charging Cable",
            "Compatibility": "All USB-C devices (Laptops up to 240W, Phones, Tablets, Drones, Consoles)",
            "Power_Output_or_Sensors": "240W (48V/5A Power Delivery 3.1 with E-Marker smart chip) + 480Mbps Data Transfer",
            "Material": "Food-grade skin-soft silicone jacket with 25,000-bend reinforced stress points",
            "Dimensions_Weight": "Length: 2.0 meters (6.6 ft), Weight: 62 g",
            "Warranty": "Lifetime Replacement Guarantee"
        }
    },
    {
        id: 33,
        category: 6,
        subCategory: 16,
        name: 'Tomtoc 360 Defender Armor Laptop Sleeve 16"',
        company: 'Tomtoc',
        price: 420,
        oldPrice: 480,
        sku: 'TOMTOC-360-16-BLK',
        shipping: 30,
        availability: 'In Stock',
        stockAvail: 85,
        stockRes: 5,
        stockSold: 145,
        desc: 'Military-grade CornerArmor patent technology shields your 16-inch laptop from drops and bumps. Features water-resistant Cordura recycled fabric, high-density fleece interior padding, and front accessory pocket.',
        descFr: 'Housse de protection renforcée pour ordinateur portable 16 pouces avec technologie CornerArmor anti-choc certifiée normes militaires et tissu Cordura imperméable.',
        specs: {
            "Type": "Military-Grade Drop-Tested Protective Laptop Sleeve",
            "Compatibility": "16-inch MacBook Pro M1/M2/M3, 15-inch MacBook Air, Dell XPS 15, ThinkPad X1 15/16",
            "Power_Output_or_Sensors": "CornerArmor Technology (4 reinforced shock-absorbing corners passing MIL-STD-810H drop test)",
            "Material": "Water-resistant Cordura 420D Ballistic Nylon with plush thick faux-fur interior lining",
            "Dimensions_Weight": "Internal: 359 x 248 x 18 mm | External: 385 x 275 x 32 mm, 380 g",
            "Warranty": "2-Year Tomtoc Guarantee"
        }
    },
    {
        id: 34,
        category: 6,
        subCategory: 16,
        name: 'Belkin Connect 11-in-1 Pro USB-C Multiport Dock',
        company: 'Belkin',
        price: 1450,
        oldPrice: 1600,
        sku: 'BELKIN-11IN1-PRO',
        shipping: 35,
        availability: 'In Stock',
        stockAvail: 49,
        stockRes: 3,
        stockSold: 77,
        desc: 'Transform your laptop into an ergonomic desktop workstation. Doubles as a laptop riser while expanding a single USB-C port into dual 4K HDMI/VGA video outputs, 100W PD passthrough, Gigabit LAN, and SD/MicroSD slots.',
        descFr: 'Hub multiport 11-en-1 servant également de support incliné ergonomique : double affichage 4K HDMI/VGA, charge 100W PD, Ethernet Gigabit et lecteurs de cartes SD/TF.',
        specs: {
            "Type": "11-in-1 Multiport USB-C Dock & Ergonomic Laptop Stand",
            "Compatibility": "Universal (Windows, macOS, iPadOS, ChromeOS laptops with full-function USB-C)",
            "Power_Output_or_Sensors": "100W Power Delivery 3.0 Passthrough (85W power to host laptop)",
            "Material": "Brushed Aluminum wedge design acting as an ergonomic typing riser and passive cooling pad",
            "Dimensions_Weight": "260 x 90 x 23 mm, 348 g",
            "Warranty": "2-Year Official Belkin Connected Equipment Warranty"
        }
    },

    // -------------------------------------------------------------
    // CATEGORY 5: GAMING & CONSOLES (8 Products)
    // -------------------------------------------------------------
    {
        id: 35,
        category: 5,
        subCategory: 8,
        name: 'Sony PlayStation 5 Pro 2TB Digital Console',
        company: 'Sony',
        price: 9200,
        oldPrice: 9900,
        sku: 'PS5-PRO-2TB',
        shipping: 0,
        availability: 'In Stock',
        stockAvail: 2, // Deliberately low stock to trigger inventory alert path
        stockRes: 1,
        stockSold: 65,
        desc: 'The most powerful PlayStation console ever built. Features an upgraded GPU with 67% more Compute Units, Advanced Ray Tracing, PlayStation Spectral Super Resolution (PSSR) AI upscaling, and 2TB ultra-fast SSD storage.',
        descFr: 'La console nouvelle génération ultime : GPU surpuissant avec Ray Tracing avancé, technologie d\'upscaling IA PSSR 4K à 60/120 FPS et 2To de stockage SSD ultra-rapide.',
        specs: {
            "Device_Type": "Next-Generation 4K/8K High-Framerate Gaming Console",
            "Connectivity": "Wi-Fi 7 (802.11be), Bluetooth 5.1, Gigabit Ethernet, 2x USB-C (10Gbps), 2x USB-A (10Gbps), HDMI 2.1",
            "Sensor_or_Switch": "Custom AMD Zen 2 8-core/16-thread CPU up to 3.85 GHz + RDNA 3 GPU (16.7 TFLOPS with AI PSSR)",
            "Polling_Rate": "Up to 120 FPS at 4K / 8K output support with VRR and ALLM",
            "RGB_Lighting": "Signature PlayStation ambient LED accent light bar",
            "Weight": "3.1 kg (Includes DualSense Wireless Controller, Astro\'s Playroom pre-installed)"
        }
    },
    {
        id: 36,
        category: 5,
        subCategory: 8,
        name: 'Nintendo Switch OLED Model Mario Red Edition',
        company: 'Nintendo',
        price: 3800,
        oldPrice: 4100,
        sku: 'NSW-OLED-MARIO-RED',
        shipping: 0,
        availability: 'Out of Stock', // Deliberately OUT OF STOCK to test out-of-stock guard
        stockAvail: 0,
        stockRes: 0,
        stockSold: 92,
        desc: 'Special edition system featuring the iconic Mario Red color scheme on the console, dock, and Joy-Con controllers. Features a vibrant 7-inch OLED screen, wide adjustable stand, wired LAN dock, and 64GB internal storage.',
        descFr: 'Édition spéciale rouge Mario avec écran OLED éclatant de 7 pouces, support ajustable large, station d\'accueil avec port Ethernet filaire et son amélioré en mode portable.',
        specs: {
            "Device_Type": "Hybrid Handheld & Home Entertainment Gaming Console",
            "Connectivity": "Wi-Fi 5 (802.11ac), Bluetooth 4.1, USB Type-C charging, HDMI 2.0 (TV Mode), 3.5mm stereo jack",
            "Sensor_or_Switch": "NVIDIA Custom Tegra processor with high-contrast 7.0-inch OLED capacitive touchscreen",
            "Polling_Rate": "60 FPS native display refresh (1080p 60Hz docked / 720p 60Hz handheld)",
            "RGB_Lighting": "None (Collector Edition Matte Mario Red Finish with silhouette easter egg)",
            "Weight": "Console with Joy-Cons: 420 g (0.93 lbs)"
        }
    },
    {
        id: 37,
        category: 5,
        subCategory: 14,
        name: 'Keychron Q1 Pro Wireless Custom Mechanical Keyboard',
        company: 'Keychron',
        price: 2190,
        oldPrice: 2450,
        sku: 'KEYCHRON-Q1PRO-RED',
        shipping: 0,
        availability: 'In Stock',
        stockAvail: 44,
        stockRes: 3,
        stockSold: 73,
        desc: 'A full-metal QMK/VIA wireless custom mechanical keyboard crafted from 6063 CNC aluminum. Features double-gasket acoustic dampening, pre-lubed Gateron Jupiter Red switches, hot-swappable PCB, and south-facing RGB.',
        descFr: 'Clavier mécanique sans fil premium en aluminium CNC 6063 avec structure Double-Gasket, switches Gateron Jupiter Red lubrifiés d\'usine et rétroéclairage RGB personnalisable VIA/QMK.',
        specs: {
            "Device_Type": "Custom CNC Aluminum Wireless Mechanical Keyboard (75% Exploded Layout)",
            "Connectivity": "Triple-mode: Bluetooth 5.1 (3 devices), 2.4GHz Wireless, and Type-C Wired (1000Hz)",
            "Sensor_or_Switch": "Pre-lubed Gateron Jupiter Red Linear Switches (45g actuation, Hot-Swappable 3/5-pin PCB)",
            "Polling_Rate": "1000 Hz (Wired & 2.4G) / 90 Hz (Bluetooth)",
            "RGB_Lighting": "South-facing RGB with 22 dynamic animation presets and QMK/VIA keymapping",
            "Weight": "1750 g (Solid double-gasket acoustic dampening with KSA profile double-shot PBT keycaps)"
        }
    },
    {
        id: 38,
        category: 5,
        subCategory: 17,
        name: 'Logitech G Pro X Superlight 2 Wireless Gaming Mouse',
        company: 'Logitech G',
        price: 1590,
        oldPrice: 1750,
        sku: 'LOGI-GPX2-BLK',
        shipping: 35,
        availability: 'In Stock',
        stockAvail: 67,
        stockRes: 5,
        stockSold: 154,
        desc: 'The gold standard of esports mice, weighing just 60 grams. Features revolutionary LIGHTFORCE hybrid optical-mechanical switches, HERO 2 sensor with 32,000 DPI and true 4,000Hz polling rate wireless capability.',
        descFr: 'La souris esport n°1 au monde : seulement 60 grammes, capteur HERO 2 de 32 000 DPI avec taux de rapport 4000 Hz sans fil et switches hybrides optique-mécanique LIGHTFORCE.',
        specs: {
            "Device_Type": "Ultra-Lightweight Competitive Esports Wireless Gaming Mouse",
            "Connectivity": "LIGHTSPEED Wireless 2.4GHz + USB-C Wired charging (95-hour battery life)",
            "Sensor_or_Switch": "HERO 2 Optical Sensor (100 - 32,000 DPI, 500+ IPS, 40G acceleration) + LIGHTFORCE Hybrid Switches",
            "Polling_Rate": "Up to 4000 Hz / 0.25ms response time wireless report rate",
            "RGB_Lighting": "None (Optimized for maximum battery efficiency and minimum weight)",
            "Weight": "60 g (Zero-additive PTFE glides)"
        }
    },
    {
        id: 39,
        category: 5,
        subCategory: 17,
        name: 'Razer DeathAdder V3 Pro Wireless Ergonomic Mouse',
        company: 'Razer',
        price: 1450,
        oldPrice: 1600,
        sku: 'RAZER-DAV3PRO-BLK',
        shipping: 35,
        availability: 'In Stock',
        stockAvail: 51,
        stockRes: 4,
        stockSold: 98,
        desc: 'Refined ergonomic form factor trusted by top esports pros worldwide. Weighs 63g with Razer Focus Pro 30K optical sensor, Gen-3 optical mouse switches rated for 90 million clicks, and up to 90 hours of continuous gameplay.',
        descFr: 'Souris gaming ergonomique ultra-légère (63 g) avec capteur optique Focus Pro 30 000 DPI, switches optiques Gen-3 ultra-rapides et 90 heures d\'autonomie.',
        specs: {
            "Device_Type": "Ergonomic Right-Handed Esports Wireless Gaming Mouse",
            "Connectivity": "Razer HyperSpeed Wireless (2.4GHz) + Speedflex Type-C Charging Cable",
            "Sensor_or_Switch": "Focus Pro 30K Optical Sensor (30,000 DPI, 750 IPS, 70G) + Optical Mouse Switches Gen-3 (0.2ms)",
            "Polling_Rate": "1000 Hz standard (Upgradable to 8000 Hz with HyperPolling Wireless Dongle)",
            "RGB_Lighting": "None (Esports stealth matte black coating)",
            "Weight": "63 g (100% PTFE mouse feet)"
        }
    },
    {
        id: 40,
        category: 5,
        subCategory: 8,
        name: 'SteelSeries Arctis Nova Pro Wireless Headset',
        company: 'SteelSeries',
        price: 3750,
        oldPrice: 4100,
        sku: 'STEEL-NOVA-PRO-WL',
        shipping: 0,
        availability: 'In Stock',
        stockAvail: 25,
        stockRes: 2,
        stockSold: 46,
        desc: 'Almighty Audio meets Multi-System Connect. Equipped with premium Hi-Res certified neodymium drivers, 4-mic Active Noise Cancellation, Infinity Power System with hot-swappable dual batteries, and OLED wireless base station.',
        descFr: 'Casque gaming sans fil ultime avec réduction active du bruit, station d\'accueil OLED multi-système (PC/PS5/Switch) et double batterie interchangeable à chaud pour un jeu non-stop.',
        specs: {
            "Device_Type": "Premium Multi-System Wireless Gaming Headset with OLED DAC Base Station",
            "Connectivity": "Simultaneous 2.4GHz Lossless Gaming Wireless + Bluetooth 5.0 + 3.5mm Wired",
            "Sensor_or_Switch": "40mm Premium Hi-Res Neodymium Magnetic Drivers + ClearCast Gen 2 AI Noise-Cancelling Mic",
            "Polling_Rate": "96 kHz / 24-bit audio resolution (Frequency Response: 10 Hz - 40,000 Hz)",
            "RGB_Lighting": "OLED Base Station Display with EQ tuning and tactile volume/mixing dial",
            "Weight": "338 g (PVD coated steel headband with ComfortMAX height-adjustable suspension strap)"
        }
    },
    {
        id: 41,
        category: 5,
        subCategory: 17,
        name: 'Sony DualSense Edge Wireless Pro Controller',
        company: 'Sony',
        price: 2390,
        oldPrice: 2650,
        sku: 'SONY-DUALSENSE-EDGE',
        shipping: 0,
        availability: 'In Stock',
        stockAvail: 36,
        stockRes: 3,
        stockSold: 81,
        desc: 'Built for high performance and personalization. Features swappable analog stick modules, remappable back paddle buttons, adjustable trigger travel stops, customizable dead zones, and on-controller profile switching.',
        descFr: 'Manette pro officielle PS5 et PC avec modules de sticks remplaçables, gâchettes ajustables, palettes arrière configurables et profils de commande personnalisés à la volée.',
        specs: {
            "Device_Type": "Professional Customizable Wireless Gaming Controller (PS5 & PC)",
            "Connectivity": "Bluetooth Wireless & Low-latency USB Type-C Braided Wired (with lockable housing)",
            "Sensor_or_Switch": "Haptic Feedback dual actuators + Dynamic Adaptive Triggers + Replaceable Stick Modules",
            "Polling_Rate": "1000 Hz report rate with sub-millisecond input lag on PC & PS5",
            "RGB_Lighting": "DualSense interactive light bar and player indicator LEDs",
            "Weight": "325 g (Includes hard carrying case, 4 back buttons, 6 stick caps, and braided cable)"
        }
    },
    {
        id: 42,
        category: 5,
        subCategory: 9,
        name: 'ASUS ROG Swift OLED PG27AQDM 240Hz Gaming Monitor',
        company: 'Asus',
        price: 9800,
        oldPrice: 10900,
        sku: 'ASUS-PG27AQDM-240HZ',
        shipping: 0,
        availability: 'In Stock',
        stockAvail: 15,
        stockRes: 1,
        stockSold: 29,
        desc: '27-inch 1440p QHD gaming monitor featuring a custom heatsink-cooled OLED panel, 240Hz refresh rate, near-instantaneous 0.03ms response time, 1000 nits peak HDR brightness, and anti-glare micro-texture coating.',
        descFr: 'Écran gaming 27 pouces QHD OLED à 240Hz avec temps de réponse ultra-rapide de 0.03ms, dissipation thermique passive avancée et luminosité de pointe de 1000 nits.',
        specs: {
            "Device_Type": "27-inch QHD 240Hz 0.03ms OLED Esports Reference Gaming Monitor",
            "Connectivity": "2x HDMI 2.0, 1x DisplayPort 1.4 (DSC), 2x USB 3.2 Gen 1 Type-A Hub, 3.5mm Earphone jack",
            "Sensor_or_Switch": "26.5-inch 16:9 OLED Panel (2560x1440 QHD, 99% DCI-P3, Delta E < 2 color accuracy)",
            "Polling_Rate": "240 Hz native refresh rate (0.03ms GtG response time, G-SYNC Compatible & FreeSync Premium)",
            "RGB_Lighting": "Aura Sync ambient rear lighting with ROG desktop logo projector",
            "Weight": "6.9 kg with ergonomic stand (Height, Tilt, Swivel, Pivot adjust)"
        }
    },

    // -------------------------------------------------------------
    // CATEGORY 6: SMART WEARABLES & SMART HOME (6 Products)
    // -------------------------------------------------------------
    {
        id: 43,
        category: 4,
        subCategory: 7,
        name: 'Apple Watch Ultra 2 GPS + Cellular 49mm Titanium',
        company: 'Apple',
        price: 8900,
        oldPrice: 9700,
        sku: 'AW-ULTRA2-49-TI',
        shipping: 0,
        availability: 'In Stock',
        stockAvail: 24,
        stockRes: 2,
        stockSold: 62,
        desc: 'The ultimate sports and adventure watch. Powered by the S9 SiP with double-tap gesture control, 3000-nit Always-On Retina display, precision dual-frequency GPS, depth gauge for diving to 40m, and up to 72 hours of battery in Low Power Mode.',
        descFr: 'La montre connectée la plus résistante d\'Apple : boîtier titane 49 mm, écran lumineux 3000 nits, GPS double fréquence ultra-précis et autonomie jusqu\'à 72h.',
        specs: {
            "Type": "Ultra-Rugged Adventure & Multisport Cellular Smartwatch",
            "Compatibility": "iPhone XS or later with iOS 17 or later",
            "Power_Output_or_Sensors": "S9 SiP, Blood Oxygen, ECG, Temperature sensing, Dual-frequency GPS (L1/L5), Depth gauge (40m)",
            "Material": "Aerospace-grade Titanium case with flat sapphire crystal display and customizable Action button",
            "Dimensions_Weight": "49 x 44 x 14.4 mm, 61.4 g (100m Water Resistance, EN13319 dive certified, MIL-STD 810H)",
            "Warranty": "2-Year Official Apple Warranty"
        }
    },
    {
        id: 44,
        category: 4,
        subCategory: 7,
        name: 'Garmin Fenix 8 Solar 51mm Titanium GPS Watch',
        company: 'Garmin',
        price: 11200,
        oldPrice: 12400,
        sku: 'GARMIN-FENIX8-SOLAR',
        shipping: 0,
        availability: 'In Stock',
        stockAvail: 4, // Deliberately low stock to trigger low-stock alert path
        stockRes: 1,
        stockSold: 21,
        desc: 'Built for athletes who push beyond limits. Features a Power Sapphire solar charging lens delivering up to 48 days of battery life, built-in LED flashlight, speaker and mic for voice commands, and TopoActive worldwide mapping.',
        descFr: 'Montre GPS multisport solaire haut de gamme en titane avec écran Power Sapphire 51 mm, lampe torche LED intégrée, cartographie TopoActive et jusqu\'à 48 jours d\'autonomie.',
        specs: {
            "Type": "Solar-Powered Multisport GPS Expedition Smartwatch (51mm)",
            "Compatibility": "iOS & Android via Garmin Connect App",
            "Power_Output_or_Sensors": "Elevate Gen 5 Heart Rate, Pulse Ox, Multi-band GNSS SatIQ, Built-in Mic & Speaker, LED Flashlight",
            "Material": "DLC Titanium Bezel, Sapphire Crystal Lens, Fiber-reinforced Polymer case with metal rear cover",
            "Dimensions_Weight": "51 x 51 x 15.4 mm, 95 g (10 ATM Water Resistance / 40m Dive rating with leakproof buttons)",
            "Warranty": "2-Year Official ZeyTech Warranty"
        }
    },
    {
        id: 45,
        category: 4,
        subCategory: 7,
        name: 'Samsung Galaxy Watch 7 Pro 45mm LTE',
        company: 'Samsung',
        price: 3900,
        oldPrice: 4400,
        sku: 'GW7PRO-45-LTE',
        shipping: 0,
        availability: 'In Stock',
        stockAvail: 37,
        stockRes: 3,
        stockSold: 70,
        desc: 'Advanced wellness intelligence on your wrist. Powered by an efficient 3nm processor, BioActive Sensor with Energy Score insights, dual-frequency GPS tracking, sapphire crystal glass, and standalone 4G LTE calling.',
        descFr: 'Montre connectée santé et sport avec processeur 3nm ultra-fluide, capteur BioActive IA, score d\'énergie personnalisé, boîtier titane et connectivité 4G LTE autonome.',
        specs: {
            "Type": "Advanced Health Intelligence & LTE Connected Smartwatch",
            "Compatibility": "Android 11.0 or higher with 1.5GB+ RAM (Samsung Health)",
            "Power_Output_or_Sensors": "3nm Exynos W1000 chip, BioActive Sensor (ECG, BIA body composition, HR, Sleep apnea detection), Dual GPS",
            "Material": "Grade 4 Titanium Case with Sapphire Crystal display and sport magnetic D-Buckle band",
            "Dimensions_Weight": "45.4 x 45.4 x 10.5 mm, 46.5 g (IP68, 5ATM, MIL-STD-810H certified)",
            "Warranty": "1-Year Official Samsung Warranty"
        }
    },
    {
        id: 46,
        category: 4,
        subCategory: 18,
        name: 'Oura Ring Gen 3 Horizon Smart Health & Sleep Tracker',
        company: 'Oura',
        price: 3600,
        oldPrice: 3990,
        sku: 'OURA-R3-HORIZON-BLK',
        shipping: 0,
        availability: 'In Stock',
        stockAvail: 28,
        stockRes: 2,
        stockSold: 49,
        desc: 'Seamless, screen-free holistic health tracking. Crafted from durable titanium, the Horizon ring monitors sleep architecture, continuous heart rate, blood oxygen (SpO2), skin temperature trends, and daytime stress resilience.',
        descFr: 'Bague connectée en titane ultra-légère pour le suivi scientifique du sommeil, de la fréquence cardiaque en continu, du stress et de la récupération physique sans écran distrayant.',
        specs: {
            "Type": "Screen-Free Titanium Biometric Health & Sleep Tracking Ring",
            "Compatibility": "iOS & Android (Oura App integration with Apple Health & Google Health Connect)",
            "Power_Output_or_Sensors": "Red & Infrared PPG sensors, Green LEDs (24/7 HR), Negative Temperature Coefficient (NTC) sensors, 3D Accel",
            "Material": "Non-allergenic, non-metallic seamless inner molding with ultra-light Titanium PVD Horizon exterior",
            "Dimensions_Weight": "Width: 7.9 mm, Thickness: 2.55 mm, Weight: 4 to 6 g (Water resistant to 100 meters)",
            "Warranty": "1-Year Official Oura Warranty"
        }
    },
    {
        id: 47,
        category: 4,
        subCategory: 18,
        name: 'Philips Hue White & Color Ambiance Starter Kit (3x E27 + Bridge)',
        company: 'Philips Hue',
        price: 1850,
        oldPrice: 2100,
        sku: 'HUE-WCA-STARTER-3P',
        shipping: 0,
        availability: 'In Stock',
        stockAvail: 43,
        stockRes: 4,
        stockSold: 88,
        desc: 'Transform your room atmosphere with 16 million colors and tunable white lighting. Includes 3 smart E27 LED bulbs (1100 lumens each), the Hue Bridge hub for instant local response, and Hue Smart Dimmer Switch with Matter support.',
        descFr: 'Kit de démarrage éclairage connecté avec 3 ampoules LED E27 1100 lumens 16 millions de couleurs, pont Hue Bridge compatible Matter/HomeKit et télécommande variateur.',
        specs: {
            "Type": "Matter & Zigbee Connected Smart Ambient Lighting Starter Kit",
            "Compatibility": "Apple HomeKit, Google Assistant, Amazon Alexa, Matter smart home ecosystem",
            "Power_Output_or_Sensors": "3x 9.5W E27 LED Bulbs (1100 lumens each @ 4000K, 25,000 hour lifespan, 2000K - 6500K tunable white)",
            "Material": "High-durability thermal plastic and glass diffuser with Hue Zigbee Bridge and Smart Button Switch",
            "Dimensions_Weight": "Bulb: 110 x 60 mm, 72 g each | Bridge: 90 x 90 x 26 mm",
            "Warranty": "2-Year Official Philips Warranty"
        }
    },
    {
        id: 48,
        category: 4,
        subCategory: 18,
        name: 'Aqara M3 Matter Smart Home Hub & 360 Security Kit',
        company: 'Aqara',
        price: 1400,
        oldPrice: 1590,
        sku: 'AQARA-M3-SEC-KIT',
        shipping: 35,
        availability: 'In Stock',
        stockAvail: 32,
        stockRes: 2,
        stockSold: 56,
        desc: 'The brain of your next-gen smart home. The Hub M3 features Matter controller & Thread border router support, 360-degree infrared blaster, Power over Ethernet (PoE), and includes 2 door/window sensors and 1 motion/light sensor.',
        descFr: 'Hub domotique universel Matter et Thread avec alimentation PoE, émetteur infrarouge 360°, sirène d\'alarme intégrée et pack de capteurs de sécurité porte/mouvement.',
        specs: {
            "Type": "Matter Controller & Thread Border Router Smart Hub with Sensor Bundle",
            "Compatibility": "Matter, Thread, Zigbee 3.0, Bluetooth 5.1, Apple Home, Google Home, Home Assistant",
            "Power_Output_or_Sensors": "PoE (Power over Ethernet 802.3af) or USB-C (5V/2A), 95dB Security Siren, 360° IR Blaster",
            "Material": "Matte UV-resistant dark polymer housing with local automation edge computing engine",
            "Dimensions_Weight": "105 x 105 x 36.5 mm, 210 g",
            "Warranty": "1-Year Official Warranty"
        }
    }
];

function generateSvgPlaceholder(product, imgIndex) {
    const p = product;
    const catColors = {
        1: { bg: '#0b1329', accent: '#c79a44', light: '#d9b567', icon: '💻', tag: 'LAPTOP WORKSTATION' },
        2: { bg: '#0d1830', accent: '#38bdf8', light: '#7dd3fc', icon: '📱', tag: '5G SMARTPHONE / TABLET' },
        3: { bg: '#101726', accent: '#a855f7', light: '#c084fc', icon: '🎧', tag: 'STUDIO ACOUSTICS' },
        4: { bg: '#091a24', accent: '#22c55e', light: '#4ade80', icon: '⌚', tag: 'BIOMETRIC WEARABLE' },
        5: { bg: '#1c102a', accent: '#f43f5e', light: '#fb7185', icon: '🎮', tag: 'ESPORTS GAMING RIG' },
        6: { bg: '#0c1626', accent: '#eab308', light: '#fde047', icon: '⚡', tag: 'PRO ACCESSORY / DOCK' }
    };
    const c = catColors[p.category] || catColors[1];
    const angleText = imgIndex === 1 ? 'STUDIO MAIN VIEW' : (imgIndex === 2 ? 'TECHNICAL ISOMETRIC' : 'DETAIL SPEC VIEW');

    return `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 800 800" width="800" height="800">
  <defs>
    <radialGradient id="bgGrad_${p.id}_${imgIndex}" cx="50%" cy="45%" r="65%">
      <stop offset="0%" stop-color="#182744" />
      <stop offset="60%" stop-color="${c.bg}" />
      <stop offset="100%" stop-color="#050811" />
    </radialGradient>
    <linearGradient id="goldGrad_${p.id}_${imgIndex}" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" stop-color="${c.accent}" />
      <stop offset="100%" stop-color="${c.light}" />
    </linearGradient>
    <filter id="glow_${p.id}_${imgIndex}" x="-20%" y="-20%" width="140%" height="140%">
      <feGaussianBlur stdDeviation="18" result="blur" />
      <feComposite in="SourceGraphic" in2="blur" operator="over" />
    </filter>
  </defs>

  <!-- Background -->
  <rect width="800" height="800" fill="url(#bgGrad_${p.id}_${imgIndex})" />

  <!-- Grid Blueprint lines -->
  <g stroke="rgba(142, 162, 191, 0.08)" stroke-width="1">
    <line x1="100" y1="0" x2="100" y2="800" />
    <line x1="200" y1="0" x2="200" y2="800" />
    <line x1="300" y1="0" x2="300" y2="800" />
    <line x1="400" y1="0" x2="400" y2="800" />
    <line x1="500" y1="0" x2="500" y2="800" />
    <line x1="600" y1="0" x2="600" y2="800" />
    <line x1="700" y1="0" x2="700" y2="800" />
    <line x1="0" y1="100" x2="800" y2="100" />
    <line x1="0" y1="200" x2="800" y2="200" />
    <line x1="0" y1="300" x2="800" y2="300" />
    <line x1="0" y1="400" x2="800" y2="400" />
    <line x1="0" y1="500" x2="800" y2="500" />
    <line x1="0" y1="600" x2="800" y2="600" />
    <line x1="0" y1="700" x2="800" y2="700" />
  </g>

  <!-- Outer Frame -->
  <rect x="24" y="24" width="752" height="752" rx="4" fill="none" stroke="rgba(142, 162, 191, 0.2)" stroke-width="1" />
  <rect x="32" y="32" width="736" height="736" rx="2" fill="none" stroke="rgba(199, 154, 68, 0.25)" stroke-width="1" />

  <!-- Top Metadata Ledger -->
  <text x="50" y="70" font-family="'Space Mono', monospace" font-size="12" fill="${c.light}" letter-spacing="2">ZEYTECH LOGISTICS // CASABLANCA HUB-A1</text>
  <text x="750" y="70" text-anchor="end" font-family="'Space Mono', monospace" font-size="12" fill="#8ea2bf">SKU: ${p.sku}</text>

  <!-- Hexagram Brand Structural Mark -->
  <g transform="translate(400, 370)">
    <!-- Aura Circle -->
    <circle r="170" fill="none" stroke="rgba(199, 154, 68, 0.12)" stroke-width="1.5" stroke-dasharray="6,4" />
    <circle r="130" fill="rgba(12, 21, 38, 0.7)" stroke="rgba(142, 162, 191, 0.25)" stroke-width="1" />

    <!-- Hexagram Triangles -->
    <polygon points="0,-100 86,50 -86,50" fill="none" stroke="${c.accent}" stroke-width="2" opacity="0.85" />
    <polygon points="0,100 86,-50 -86,-50" fill="none" stroke="${c.light}" stroke-width="2" opacity="0.85" />

    <!-- Center Icon Emoji -->
    <text x="0" y="24" text-anchor="middle" font-size="64" filter="url(#glow_${p.id}_${imgIndex})">${c.icon}</text>
  </g>

  <!-- Product Tag & Brand -->
  <g transform="translate(400, 560)">
    <!-- Badge -->
    <rect x="-140" y="-20" width="280" height="28" rx="2" fill="rgba(17, 29, 51, 0.85)" stroke="rgba(199, 154, 68, 0.4)" stroke-width="1" />
    <text x="0" y="-2" text-anchor="middle" font-family="'Space Mono', monospace" font-size="11" font-weight="bold" fill="${c.light}" letter-spacing="1">[${c.tag}]</text>
  </g>

  <!-- Product Name & Editorial Headline -->
  <text x="400" y="625" text-anchor="middle" font-family="'Fraunces', Georgia, serif" font-size="24" font-weight="bold" fill="#f2efe6">${p.name.replace(/&/g, '&amp;')}</text>
  <text x="400" y="655" text-anchor="middle" font-family="'Space Mono', monospace" font-size="13" fill="#8ea2bf">${p.company.toUpperCase()} &bull; ${angleText}</text>

  <!-- Bottom Price & Official Certification Footer -->
  <line x1="50" y1="695" x2="750" y2="695" stroke="rgba(142, 162, 191, 0.2)" stroke-width="1" />
  <text x="50" y="732" font-family="'Fraunces', Georgia, serif" font-size="20" font-weight="bold" fill="url(#goldGrad_${p.id}_${imgIndex})">${p.price.toLocaleString()} MAD</text>
  <text x="750" y="732" text-anchor="end" font-family="'Space Mono', monospace" font-size="11" fill="#8ea2bf">100% GENUINE &bull; CASABLANCA FULFILLMENT</text>
</svg>`;
}

function escapeSql(str) {
    if (str === null || str === undefined) return 'NULL';
    return "'" + String(str).replace(/'/g, "''").replace(/\\/g, '\\\\') + "'";
}

function generateSqlAndImages() {
    console.log('🎨 Generating SVGs and SQL Seeding Script...');
    const baseImgDir = path.resolve(__dirname, '../shopping/admin/productimages');

    let sql = `-- ZeyTech AI Commerce OS — Complete Catalog Seed (48 Products)
SET FOREIGN_KEY_CHECKS=0;

-- Add description_fr if not exists
DROP PROCEDURE IF EXISTS AddDescFrCol;
DELIMITER //
CREATE PROCEDURE AddDescFrCol()
BEGIN
    IF NOT EXISTS (
        SELECT * FROM information_schema.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE()
        AND TABLE_NAME = 'products'
        AND COLUMN_NAME = 'description_fr'
    ) THEN
        ALTER TABLE products ADD COLUMN description_fr TEXT NULL AFTER productDescription;
    END IF;
END //
DELIMITER ;
CALL AddDescFrCol();
DROP PROCEDURE AddDescFrCol;

-- Clean existing test placeholder rows beyond 48
DELETE FROM products WHERE id > 48;
DELETE FROM inventory WHERE product_id > 48;

`;

    // Category Inserts
    for (const cat of CATEGORIES) {
        sql += `INSERT INTO category (id, categoryName, categoryDescription) VALUES (${cat.id}, ${escapeSql(cat.name)}, ${escapeSql(cat.desc)}) ON DUPLICATE KEY UPDATE categoryName=VALUES(categoryName), categoryDescription=VALUES(categoryDescription);\n`;
    }

    // Subcategory Inserts
    for (const sub of SUBCATEGORIES) {
        sql += `INSERT INTO subcategory (id, categoryid, subcategory) VALUES (${sub.id}, ${sub.categoryid}, ${escapeSql(sub.subcategory)}) ON DUPLICATE KEY UPDATE categoryid=VALUES(categoryid), subcategory=VALUES(subcategory);\n`;
    }

    sql += '\n-- Seed Products & 3-State Inventory\n';

    let count = 0;
    for (const p of PRODUCTS) {
        count++;
        const prodImgDir = path.join(baseImgDir, String(p.id));
        if (!fs.existsSync(prodImgDir)) {
            fs.mkdirSync(prodImgDir, { recursive: true });
        }

        const img1 = `product_${p.id}_1.svg`;
        const img2 = `product_${p.id}_2.svg`;
        const img3 = `product_${p.id}_3.svg`;

        fs.writeFileSync(path.join(prodImgDir, img1), generateSvgPlaceholder(p, 1), 'utf8');
        fs.writeFileSync(path.join(prodImgDir, img2), generateSvgPlaceholder(p, 2), 'utf8');
        fs.writeFileSync(path.join(prodImgDir, img3), generateSvgPlaceholder(p, 3), 'utf8');

        const specsJson = JSON.stringify(p.specs);
        const totalQty = p.stockAvail + p.stockRes + p.stockSold;

        sql += `INSERT INTO products (
    id, category, subCategory, productName, name, productCompany,
    productPrice, price, productPriceBeforeDiscount, productDescription,
    description, description_fr, productImage1, productImage2, productImage3,
    shippingCharge, productAvailability, productModel, specifications,
    ficheTechnique, fiche_technique, stockQuantity, stockAvailable,
    stockReserved, stockSold, warehouseLocation, currency
) VALUES (
    ${p.id}, ${p.category}, ${p.subCategory}, ${escapeSql(p.name)}, ${escapeSql(p.name)}, ${escapeSql(p.company)},
    ${p.price}, ${p.price}, ${p.oldPrice}, ${escapeSql(p.desc)},
    ${escapeSql(p.desc)}, ${escapeSql(p.descFr)}, ${escapeSql(img1)}, ${escapeSql(img2)}, ${escapeSql(img3)},
    ${p.shipping}, ${escapeSql(p.availability)}, ${escapeSql(p.sku)}, ${escapeSql(specsJson)},
    ${escapeSql(specsJson)}, ${escapeSql(specsJson)}, ${totalQty}, ${p.stockAvail},
    ${p.stockRes}, ${p.stockSold}, 'Hub-A1', 'MAD'
) ON DUPLICATE KEY UPDATE
    category=VALUES(category),
    subCategory=VALUES(subCategory),
    productName=VALUES(productName),
    name=VALUES(name),
    productCompany=VALUES(productCompany),
    productPrice=VALUES(productPrice),
    price=VALUES(price),
    productPriceBeforeDiscount=VALUES(productPriceBeforeDiscount),
    productDescription=VALUES(productDescription),
    description=VALUES(description),
    description_fr=VALUES(description_fr),
    productImage1=VALUES(productImage1),
    productImage2=VALUES(productImage2),
    productImage3=VALUES(productImage3),
    shippingCharge=VALUES(shippingCharge),
    productAvailability=VALUES(productAvailability),
    productModel=VALUES(productModel),
    specifications=VALUES(specifications),
    ficheTechnique=VALUES(ficheTechnique),
    fiche_technique=VALUES(fiche_technique),
    stockQuantity=VALUES(stockQuantity),
    stockAvailable=VALUES(stockAvailable),
    stockReserved=VALUES(stockReserved),
    stockSold=VALUES(stockSold),
    warehouseLocation=VALUES(warehouseLocation),
    currency=VALUES(currency);\n`;

        sql += `INSERT INTO inventory (product_id, available_qty, reserved_qty, sold_qty) VALUES (${p.id}, ${p.stockAvail}, ${p.stockRes}, ${p.stockSold}) ON DUPLICATE KEY UPDATE available_qty=VALUES(available_qty), reserved_qty=VALUES(reserved_qty), sold_qty=VALUES(sold_qty);\n`;
    }

    sql += '\nSET FOREIGN_KEY_CHECKS=1;\n';

    const sqlPath = path.resolve(__dirname, '../sql/seed_catalog_48_products.sql');
    fs.writeFileSync(sqlPath, sql, 'utf8');
    console.log(`✅ Generated SQL file: ${sqlPath}`);
    console.log(`✅ Generated ${count * 3} SVG images in ${baseImgDir}`);
}

generateSqlAndImages();

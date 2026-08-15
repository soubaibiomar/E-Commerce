import os
import json
import shutil

brain_dir = r"C:\Users\omar\.gemini\antigravity-ide\brain\b69f5ff1-9064-4a0e-af77-d487561057af"
product_images_dir = r"d:\Online Shopping\shopping\admin\productimages"

# Mapping generated photos
generated_photos = {
    1: "iphone_15_pro_max_1786716923145.jpg",
    2: "samsung_s24_ultra_1786716943155.jpg",
    3: "macbook_pro_16_m3_1786716956447.jpg",
    5: "sony_wh1000xm5_headphones_1786716968501.jpg",
    6: "bose_qc_ultra_earbuds_1786717139019.jpg",
    7: "apple_watch_ultra_2_1786717042111.jpg",
    8: "lg_oled_c3_tv_1786717157657.jpg",
    10: "ps5_slim_console_1786716983599.jpg",
    14: "nike_air_jordan1_chicago_1786716995764.jpg",
    16: "herman_miller_aeron_1786717010043.jpg",
    17: "dyson_v15_detect_1786717025368.jpg",
    19: "atomic_habits_book_1786717176247.jpg",
}

# Detailed Fiches Techniques
specs_data = {
    1: {
        "Brand": "Apple",
        "Model": "iPhone 15 Pro Max (256GB, Natural Titanium)",
        "Processor": "Apple A17 Pro (3nm architecture, 6-core CPU, 6-core GPU, 16-core Neural Engine)",
        "Display": "6.7-inch Super Retina XDR OLED, ProMotion 120Hz, HDR10, Dolby Vision, 2000 nits peak brightness",
        "Camera System": "Pro triple system: 48MP Main (f/1.78, sensor-shift OIS) + 12MP Ultra-Wide (120° FOV) + 12MP 5x Telephoto (120mm)",
        "Video Recording": "4K ProRes at 60 fps, Log video recording, Spatial Video capture, Action Mode, Cinematic mode",
        "Storage & RAM": "256GB NVMe high-speed storage, 8GB LPDDR5 RAM",
        "Battery & Charging": "4,422 mAh, Fast charge 50% in 30 min (20W+), 15W MagSafe wireless, Qi2 wireless",
        "Build & Materials": "Aerospace-grade Grade 5 Titanium frame, Ceramic Shield front, textured matte glass back",
        "Water & Dust Resistance": "IP68 rated (maximum depth of 6 meters up to 30 minutes) under IEC standard 60529",
        "Connectivity": "5G NR, Wi-Fi 6E (802.11ax), Bluetooth 5.3, Second-gen Ultra Wideband (UWB), USB-C (USB 3 up to 10Gb/s)",
        "Dimensions & Weight": "159.9 x 76.7 x 8.25 mm | 221 grams",
        "Operating System": "iOS 17 with Dynamic Island, StandBy mode, and interactive widgets",
        "Warranty": "1-Year Apple Limited Warranty with 90 days complimentary technical support",
        "In The Box": "iPhone 15 Pro Max, USB-C Charge Cable (1 m), Documentation"
    },
    2: {
        "Brand": "Samsung",
        "Model": "Galaxy S24 Ultra 5G (512GB, Titanium Gray)",
        "Processor": "Qualcomm Snapdragon 8 Gen 3 for Galaxy (4nm, Octa-Core up to 3.39GHz)",
        "Display": "6.8-inch Dynamic AMOLED 2X, Quad HD+ (3120 x 1440), 1-120Hz LTPO Adaptive Refresh, 2600 nits peak, Corning Gorilla Armor",
        "Camera System": "Quad ProVisual: 200MP Wide OIS + 50MP 5x Optical Periscope OIS + 10MP 3x Telephoto + 12MP Ultra-Wide",
        "AI Features": "Galaxy AI suite: Circle to Search with Google, Live Translate on calls, Note Assist, Generative Photo Edit",
        "Stylus": "Integrated Bluetooth S-Pen with Air Command, remote shutter, and 2.8ms low latency",
        "Storage & RAM": "512GB UFS 4.0 Storage, 12GB LPDDR5X RAM",
        "Battery & Charging": "5,000 mAh, 45W Super Fast Charging 2.0 (65% in 30 min), 15W Fast Wireless Charging 2.0, Wireless PowerShare",
        "Build & Durability": "Titanium alloy frame, Armor Aluminum structure, IP68 water and dust resistance",
        "Connectivity": "5G Dual-SIM, Wi-Fi 7, Bluetooth 5.3, UWB, NFC, USB Type-C 3.2 Gen 1 (DisplayPort out)",
        "Dimensions & Weight": "162.3 x 79.0 x 8.6 mm | 232 grams",
        "Software": "One UI 6.1 on Android 14 (7 generations of OS upgrades & 7 years security updates guaranteed)",
        "Warranty": "1-Year Samsung Manufacturer Warranty",
        "In The Box": "Galaxy S24 Ultra, Built-in S-Pen, USB-C to USB-C Cable, SIM Ejection Pin, Quick Start Guide"
    },
    3: {
        "Brand": "Apple",
        "Model": "MacBook Pro 16-inch (M3 Max, Space Black)",
        "Processor": "Apple M3 Max chip (16-core CPU with 12 performance cores and 4 efficiency cores)",
        "Graphics": "40-core GPU with Hardware-accelerated Ray Tracing, Mesh Shading, and Dynamic Caching",
        "Neural Engine": "16-core Neural Engine with 18 trillion operations per second throughput",
        "Unified Memory": "36GB unified memory (300GB/s memory bandwidth, configurable up to 128GB)",
        "Storage": "1TB PCIe Gen 4 SSD (up to 7.4GB/s sequential read performance)",
        "Display": "16.2-inch Liquid Retina XDR (3456 x 2234), 120Hz ProMotion, 1000 nits sustained, 1600 nits peak HDR, 1,000,000:1 contrast",
        "Audio & Mics": "Six-speaker sound system with force-cancelling woofers, Dolby Atmos spatial audio, studio-quality 3-mic array",
        "Camera": "1080p FaceTime HD camera with advanced image signal processor and computational video",
        "Ports": "3x Thunderbolt 4 (USB-C), HDMI 2.1 port (up to 8K at 60Hz), SDXC card slot, MagSafe 3 charging port, 3.5mm headphone jack",
        "Battery & Power": "100-watt-hour lithium-polymer battery, up to 22 hours Apple TV app playback, 140W USB-C Power Adapter",
        "Dimensions & Weight": "35.57 x 24.81 x 1.68 cm | 2.16 kg (4.8 lbs)",
        "Finish": "Space Black anodized aluminum with breakthrough chemistry reducing fingerprint visibility",
        "In The Box": "16-inch MacBook Pro, 140W USB-C Power Adapter, USB-C to MagSafe 3 Cable (2 m)"
    },
    4: {
        "Brand": "Dell",
        "Model": "XPS 15 9530 OLED InfinityEdge Laptop",
        "Processor": "13th Gen Intel Core i9-13900H (14 cores: 6P + 8E, 20 threads, up to 5.40 GHz Turbo, 24MB Cache)",
        "Graphics": "NVIDIA GeForce RTX 4070 Laptop GPU with 8GB GDDR6 dedicated VRAM",
        "Memory": "32GB Dual-Channel DDR5 at 4800MHz (2x 16GB, user upgradeable to 64GB)",
        "Storage": "1TB M.2 PCIe NVMe Gen 4 Solid State Drive",
        "Display": "15.6-inch 3.5K (3456 x 2160) OLED InfinityEdge Touchscreen, 400 nits, 100% DCI-P3 color gamut, DisplayHDR 500",
        "Audio": "Studio quality tuning with Waves Nx 3D audio, Quad-speaker design (2x 2.5W woofers + 2x 1.5W tweeters)",
        "Keyboard & Trackpad": "Backlit keyboard with fingerprint reader in power key, precision glass seamless touchpad",
        "Ports": "2x Thunderbolt 4 (USB-C) with DisplayPort & Power Delivery, 1x USB-C 3.2 Gen 2, Full-size SD card reader v6.0, 3.5mm jack",
        "Battery & Adapter": "6-cell 86Whr integrated battery, 130W Type-C AC Power Adapter",
        "Chassis": "CNC machined aluminum in Platinum Silver with black carbon fiber composite palm rest",
        "Weight & Dimensions": "344.72 x 230.14 x 18.00 mm | 1.92 kg (4.23 lbs)",
        "In The Box": "Dell XPS 15 laptop, 130W USB-C Power Adapter, USB-C to USB-A/HDMI dongle"
    },
    5: {
        "Brand": "Sony",
        "Model": "WH-1000XM5 Wireless Noise-Cancelling Headphones",
        "Type": "Over-Ear Closed Dynamic Headphones",
        "Noise Cancellation": "Industry-leading Dual Processor V1 + HD QN1 with Auto NC Optimizer and 8 microphones",
        "Driver Unit": "30mm specially engineered Carbon Fiber composite dome with lightweight design",
        "Audio Codecs": "LDAC, AAC, SBC (Hi-Res Audio and Hi-Res Audio Wireless certified with DSEE Extreme AI upscaling)",
        "Frequency Response": "4 Hz – 40,000 Hz (Active playback) | 20 Hz – 40,000 Hz (LDAC 96kHz 990kbps)",
        "Microphones": "4 beamforming microphones with AI-based noise reduction for crystal-clear calls",
        "Battery Life": "Up to 30 hours continuous playback (NC ON), up to 40 hours (NC OFF)",
        "Quick Charging": "USB-PD compatible: 3 minutes charging gives 3 hours of full playback",
        "Connectivity": "Bluetooth 5.2, Multipoint connection (simultaneous 2-device pairing), Google Fast Pair, Swift Pair, 3.5mm cable",
        "Smart Features": "Speak-to-Chat automatic pause, wearing detection sensor, Quick Attention mode, capacitive touch sensor",
        "Weight": "250 grams (8.82 oz)",
        "In The Box": "WH-1000XM5 Headphones, Collapsible Carrying Case, 3.5mm Connection Cable (1.2m), USB-C Charging Cable"
    },
    6: {
        "Brand": "Bose",
        "Model": "QuietComfort Ultra Earbuds",
        "Type": "True Wireless In-Ear Noise-Cancelling Earbuds",
        "Audio Technology": "Bose Immersive Audio (Spatialized soundstage), CustomTune individualized sound calibration",
        "Noise Cancelling": "World-class Active Noise Cancellation with Quiet Mode, Aware Mode (ActiveSense), and Immersion Mode",
        "Microphones": "Advanced 4-microphone array per earbud with wind-block and ambient noise filtering",
        "Battery Life": "Up to 6 hours continuous audio (up to 4 hours with Immersive Audio ON); Case provides 3 additional charges (24 hrs total)",
        "Quick Charge": "20 minutes in case gives 2 hours playback",
        "Water Resistance": "IPX4 water-resistant rating (sweat and weather resistant)",
        "Connectivity": "Bluetooth 5.3, Snapdragon Sound certified with Qualcomm aptX Adaptive codec, Bose SimpleSync",
        "Controls": "Touch surfaces on both earbuds for volume swipe, track skip, ANC modes, and calls",
        "Weight": "7.7 grams per earbud | 59.8 grams charging case",
        "In The Box": "2x Bose QC Ultra Earbuds, Bose Fit Kit (3 pairs of ear tips & 3 pairs of stability bands), Charging Case, USB-C Cable"
    },
    7: {
        "Brand": "Apple",
        "Model": "Apple Watch Ultra 2 (GPS + Cellular, 49mm)",
        "Processor": "Apple S9 SiP with 64-bit dual-core processor, 4-core Neural Engine, 64GB onboard storage",
        "Display": "49mm Always-On Retina LTPO OLED, flat sapphire crystal, 3000 nits peak brightness, 1 nit minimum brightness",
        "Case & Bezel": "49mm Aerospace-grade Titanium case with raised bezel edge protection, Action Button in International Orange",
        "Sensors": "Depth gauge with water temperature sensor (EN13319 certified), Electrical heart sensor (ECG), Blood Oxygen (SpO2), Compass, Always-on altimeter",
        "Diving Specs": "100m water resistant, Recreational scuba diving and freediving to 40m with Oceanic+ app integration",
        "Battery Life": "Up to 36 hours normal use, up to 72 hours in Low Power Mode, fast magnetic charging",
        "GPS & Safety": "Precision dual-frequency GPS (L1 and L5), 86-decibel Emergency Siren audible up to 180 meters, Crash Detection, Fall Detection",
        "Weight & Dimensions": "49 x 44 x 14.4 mm | 61.4 grams",
        "Connectivity": "LTE & UMTS, Wi-Fi 4 (802.11n), Bluetooth 5.3, Second-gen Ultra Wideband (UWB)",
        "In The Box": "Apple Watch Ultra 2 Titanium Case, Orange Ocean Band, Apple Watch Magnetic Fast Charger to USB-C Cable (1m)"
    },
    8: {
        "Brand": "LG Electronics",
        "Model": "LG OLED evo C3 Series 65-Inch 4K Smart TV (OLED65C3PUA)",
        "Display Type": "Self-lighting 4K OLED evo Panel with Brightness Booster",
        "Resolution & Refresh": "3840 x 2160 (4K Ultra HD) | Native 120Hz Refresh Rate",
        "Processor": "α9 AI Processor 4K Gen6 with AI Super Upscaling 4K and OLED Dynamic Tone Mapping Pro",
        "HDR Support": "Dolby Vision, HDR10, HLG, Filmmaker Mode with ambient light compensation",
        "Gaming Capabilities": "4x HDMI 2.1 inputs (48Gbps 4K 120Hz), 0.1ms response time, NVIDIA G-Sync, AMD FreeSync Premium, VRR, ALLM, Game Optimizer",
        "Audio System": "40W 2.2 Channel Audio with Dolby Atmos, AI Sound Pro (Virtual 9.1.2 Up-mix), WOW Orchestra soundbar sync",
        "Smart OS": "webOS 23 with Magic Remote, ThinQ AI, Apple AirPlay 2, Apple Home, Alexa & Google Assistant built-in",
        "Connectivity": "4x HDMI 2.1, 3x USB 2.0, eARC (HDMI 2), Optical Digital Out, Wi-Fi 5 (802.11ac), Bluetooth 5.0, Ethernet LAN",
        "Dimensions & Weight": "144.1 x 82.6 x 4.5 cm (Without stand) | 16.6 kg",
        "In The Box": "LG 65\" C3 OLED TV, Magic Remote Control with batteries, Power Cable, Stand Base, Quick Setup Guide"
    },
    9: {
        "Brand": "GoPro",
        "Model": "HERO12 Black Action Camera",
        "Sensor": "1/1.9-inch CMOS Sensor with 27MP photo resolution and 8:7 aspect ratio capture",
        "Video Resolutions": "5.3K at 60fps, 4K at 120fps, 2.7K at 240fps (8x Super Slow Motion), 1080p at 240fps",
        "Color & HDR": "High Dynamic Range (HDR) Video + Photo, GP-Log Encoding with 10-bit color depth (over 1 billion colors)",
        "Stabilization": "HyperSmooth 6.0 video stabilization with 360° Horizon Lock (AutoBoost horizon leveling)",
        "Audio & Wireless": "Bluetooth Audio support (connect AirPods & wireless mics), 3 built-in mics with advanced wind-noise reduction",
        "Waterproofing": "Rugged build waterproof to 33ft (10m) without external housing",
        "Screens": "Front 1.4-inch color LCD preview screen + Rear 2.27-inch responsive touch screen",
        "Battery": "1720mAh Enduro cold-weather rechargeable battery (delivers up to 2x longer runtimes)",
        "Dimensions & Weight": "71.8 x 50.8 x 33.6 mm | 154 grams",
        "In The Box": "GoPro HERO12 Black, Enduro Battery, Curved Adhesive Mount, Mounting Buckle + Thumbscrew, USB-C Cable"
    },
    10: {
        "Brand": "Sony Interactive Entertainment",
        "Model": "PlayStation 5 Slim Console (Disc Edition, 1TB SSD)",
        "Main Processor": "x86-64-AMD Ryzen Zen 2, 8 Cores / 16 Threads at up to 3.5 GHz (variable frequency)",
        "Graphics Engine": "AMD Radeon RDNA 2-based graphics engine with Ray Tracing Acceleration, up to 2.23 GHz (10.3 TFLOPS)",
        "System Memory": "16GB GDDR6 with 448 GB/s unified bandwidth",
        "Storage": "1TB Custom NVMe SSD (5.5 GB/s raw read bandwidth) + Internal M.2 NVMe SSD expansion slot",
        "Optical Drive": "Ultra HD Blu-ray Disc Drive (removable/attachable design), supports up to 100GB discs",
        "Video Output": "Supports 4K 120Hz TVs, 8K TVs, VRR (Variable Refresh Rate as specified by HDMI ver. 2.1)",
        "Audio": "Tempest 3D AudioTech hardware engine for spatial acoustic immersion",
        "Controller": "DualSense Wireless Controller with Haptic Feedback, Adaptive Triggers, built-in mic & motion sensors",
        "Dimensions & Weight": "Approx. 358 × 96 × 216 mm | 3.2 kg (30% volume reduction vs original PS5)",
        "In The Box": "PlayStation 5 Slim Console, DualSense Wireless Controller, 1TB SSD, 2 Horizontal Stand Feet, HDMI Cable, AC Power Cord, USB Cable, ASTRO's PLAYROOM (Pre-installed game)"
    },
    11: {
        "Brand": "Nintendo",
        "Model": "Nintendo Switch – OLED Model (Neon Blue/Neon Red)",
        "Display": "7.0-inch multi-touch OLED screen (1280 x 720 resolution in handheld, up to 1080p 60fps docked via HDMI)",
        "Processor": "Custom NVIDIA Tegra processor with Maxwell-based architecture",
        "Storage": "64GB of internal storage (expandable up to 2TB via microSD/microSDHC/microSDXC card)",
        "Audio": "Enhanced stereo speakers with crisp acoustics in handheld and tabletop modes",
        "Stand": "Wide, sturdy adjustable tabletop stand with multiple viewing angles",
        "Dock Features": "Includes 2x USB 2.0 ports, 1x HDMI port, and a built-in wired LAN port for stable online play",
        "Controllers": "Joy-Con (L) and Joy-Con (R) with HD Rumble and IR Motion Camera",
        "Battery Life": "4310mAh Lithium-ion battery, approx. 4.5 to 9.0 hours runtime depending on game intensity",
        "Weight & Dimensions": "102 x 242 x 13.9 mm (with Joy-Cons attached) | 420 grams",
        "In The Box": "Nintendo Switch OLED Console, Joy-Con (L)/(R), Nintendo Switch Dock with LAN, Joy-Con Grip, Joy-Con Straps, High-Speed HDMI Cable, AC Adapter"
    },
    12: {
        "Brand": "Logitech G",
        "Model": "G PRO X Superlight 2 Lightspeed Wireless Gaming Mouse",
        "Sensor": "HERO 2 Next-Gen Optical Sensor (100 – 32,000 DPI, 500+ IPS, >40G max acceleration)",
        "Weight": "60 grams (Ultra-lightweight tournament design without honeycomb holes)",
        "Switches": "LIGHTFORCE Hybrid Optical-Mechanical Switches (Optical speed + crisp mechanical click feel)",
        "Polling Rate": "LIGHTSPEED wireless with true 4,000Hz (4K) reporting rate (0.25ms response time)",
        "Battery Life": "Up to 95 hours of continuous motion on a single charge",
        "Skates & Feet": "Zero-additive PTFE mouse feet for ultra-smooth glide on cloth and hard mousepads",
        "Onboard Memory": "Advanced onboard profile memory configurable via Logitech G HUB software",
        "Charging Port": "USB-C fast charging & POWERPLAY wireless charging compatible",
        "In The Box": "PRO X Superlight 2 Mouse, LIGHTSPEED Wireless Adapter, USB-A to USB-C Charging/Data Cable, Adapter Extension, Optional Grip Tape, PTFE Aperture Door"
    },
    13: {
        "Brand": "Keychron",
        "Model": "Keychron Q1 Pro QMK/VIA Wireless Custom Mechanical Keyboard",
        "Layout": "75% Compact Layout (81 Keys + Programmable Rotary Encoder Knob)",
        "Body Material": "Full CNC Machined 6063 Aluminum body with anodized and sandblasted finish",
        "Mounting Structure": "Double-Gasket Design with integrated sound-absorbing silicone and case foam",
        "Switches & Sockets": "Keychron K Pro Pre-Lubed Mechanical Switches, Hot-Swappable (compatible with 3-pin and 5-pin MX switches)",
        "Keycaps": "KSA Profile Double-Shot PBT Keycaps (non-shine-through, oil-resistant)",
        "Backlighting": "South-Facing RGB LEDs with 22 dynamic lighting effects and per-key customization",
        "Connectivity": "Wireless Bluetooth 5.1 (pair up to 3 devices) + Type-C Wired (1000Hz polling rate in wired mode)",
        "Battery": "4000 mAh rechargeable li-polymer battery, up to 300 hours typing (backlight off)",
        "Software": "QMK and VIA open-source firmware for remapping keys, macros, and shortcut customization",
        "Compatibility": "macOS, Windows, and Linux with physical toggle switch and OS keycaps included",
        "Dimensions & Weight": "327.5 x 145 mm, Front height 22.6 mm | 1,735 grams (Solid aluminum weight)"
    },
    14: {
        "Brand": "Nike / Jordan",
        "Model": "Air Jordan 1 Retro High OG 'Chicago Lost & Found'",
        "Style Code": "DZ5485-612",
        "Colorway": "Varsity Red / Black / Sail / Muslin",
        "Upper Materials": "Premium full-grain leather upper with aged cracked leather collar and vintage-finished sail midpanels",
        "Cushioning": "Encapsulated Nike Air-Sole unit in the heel for lightweight shock absorption",
        "Outsole": "Solid rubber cupsole with deep flex grooves and iconic concentric pivot circle traction pattern",
        "Ankle Support": "High-cut padded collar providing structured ankle lockdown and classic retro aesthetics",
        "Details": "Debossed Air Jordan Wings logo on collar lateral side, woven Nike Air tongue label, perforated toe box",
        "Origin": "Tribute to the original 1985 release discovered in mom-and-pop sneaker stockrooms",
        "In The Box": "Pair of Air Jordan 1 Retro High OG sneakers, vintage-styled mismatched replacement box lid, vintage receipt printout, extra black and white laces"
    },
    15: {
        "Brand": "Adidas",
        "Model": "Ultraboost Light Running Shoes",
        "Category": "Neutral Performance Road Running Shoes",
        "Midsole Technology": "Light BOOST – 30% lighter BOOST capsule material delivering maximum energy return",
        "Torsion System": "Linear Energy Push (LEP) system integrated into outsole for increased forefoot stiffness and snappy stride",
        "Upper Material": "Adidas PRIMEKNIT+ textile upper made with at least 50% Parley Ocean Plastic (ocean-bound recycled waste)",
        "Outsole": "Continental™ Better Rubber outsole providing extraordinary multi-surface grip in wet and dry conditions",
        "Drop & Stack": "10 mm midsole drop (Heel: 30 mm / Forefoot: 20 mm)",
        "Weight": "299 grams (Men's size US 9)",
        "Closure": "Standard lace closure with molded external heel counter for optimal Achilles tendon fit",
        "Recommended For": "Daily road running, marathon training, active lifestyle, all-day comfort"
    },
    16: {
        "Brand": "Herman Miller",
        "Model": "Aeron Ergonomic Office Chair (Remastered, Size B Medium, Graphite)",
        "Suspension Material": "8Z Pellicle elastomeric suspension across seat and backrest with 8 variable tension zones",
        "Spine Support": "Dual PostureFit SL adjustable pads stabilizing the sacrum and supporting lumbar lordosis curve",
        "Tilt Mechanism": "Harmonic 2 Tilt with balanced recline, tilt limiter, and forward seat angle adjustment (5° forward pitch)",
        "Armrests": "Fully adjustable 4D armrests (height, depth, pivot angle, and width) with soft polyurethane arm pads",
        "Casters": "2.5-inch dual-wheel hard floor and carpet casters with quiet roll technology",
        "Weight Capacity": "Tested and rated for users up to 159 kg (350 lbs)",
        "Environmental Specs": "Cradle to Cradle Certified V3 Silver, up to 91% recyclable, containing 52% recycled materials",
        "Warranty": "12-Year Herman Miller Manufacturer 24/7 Multi-Shift Warranty (Includes parts and labor)"
    },
    17: {
        "Brand": "Dyson",
        "Model": "Dyson V15 Detect Total Clean Cordless Vacuum Cleaner",
        "Motor": "Dyson Hyperdymium™ motor spinning at up to 125,000 RPM generating 240 Air Watts of suction",
        "Filtration": "Whole-machine 5-stage advanced HEPA filtration capturing 99.99% of particles as small as 0.3 microns",
        "Laser Illumination": "Fluffy Optic™ cleaner head reveals 2x more invisible microscopic dust on hard wood and tile floors",
        "Piezo Sensor": "Acoustic sensor continuously counts and measures particle sizes, automatically ramping up suction when needed",
        "LCD Display": "Real-time screen displays scientific dust count proof, run-time countdown, power mode, and maintenance alerts",
        "Battery & Run Time": "Click-in 7-cell battery delivering up to 60 minutes of fade-free floor cleaning",
        "Bin Capacity": "0.77 liters with 'point and shoot' hygienic bin emptying mechanism",
        "Weight": "3.0 kg (6.61 lbs)",
        "Included Attachments": "Fluffy Optic cleaner head, Digital Motorbar cleaner head, Hair screw tool (anti-tangle), Combination tool, Crevice tool, Wand clip, Wall dok, Charger"
    },
    18: {
        "Brand": "Nordic Craft",
        "Model": "Solid European Oak Minimalist King Platform Bed",
        "Wood Material": "100% FSC-Certified Solid European White Oak (No particleboard or MDF)",
        "Finish": "Eco-friendly non-toxic matte organic protective oil highlighting natural grain and medullary rays",
        "Headboard Features": "Angled comfort headboard with hidden integrated warm white LED indirect ambient lighting strip (touch-dimmable)",
        "Platform Base": "Solid beechwood sprung slatted base engineered for optimal mattress ventilation and spine support",
        "Joinery": "Precision German concealed steel tension brackets for squeak-free rock-solid stability",
        "Dimensions": "218 cm Length x 192 cm Width x 95 cm Headboard Height (Fits Standard King Mattress 180 x 200 cm)",
        "Under-bed Clearance": "20 cm clearance ideal for storage bins and robot vacuum cleaners",
        "Assembly": "Flat-packed with easy 20-minute 2-person modular assembly hardware and tools included"
    },
    19: {
        "Brand": "Penguin Random House",
        "Title": "Atomic Habits: An Easy & Proven Way to Build Good Habits & Break Bad Ones",
        "Author": "James Clear",
        "Format": "Hardcover Collector's Edition with Embossed Dust Jacket",
        "ISBN-13": "978-0735211292",
        "Pages": "320 pages",
        "Publisher": "Avery / Penguin Random House",
        "Language": "English",
        "Dimensions": "15.9 x 3.0 x 23.5 cm | Weight: 490 grams",
        "Key Frameworks Covered": "The 4 Laws of Behavior Change (Make it Obvious, Make it Attractive, Make it Easy, Make it Satisfying), Habit Stacking, Environment Design, 1% Marginal Gains",
        "Accolades": "#1 New York Times Bestseller, over 15 million copies sold in 50+ languages worldwide"
    },
    20: {
        "Brand": "Harriman House",
        "Title": "The Psychology of Money: Timeless lessons on wealth, greed, and happiness",
        "Author": "Morgan Housel",
        "Format": "Paperback / Deluxe Edition",
        "ISBN-13": "978-0857197689",
        "Pages": "256 pages",
        "Publisher": "Harriman House Publishing",
        "Language": "English",
        "Dimensions": "14.0 x 2.2 x 21.6 cm | Weight: 340 grams",
        "Key Themes": "Behavioral Finance, Compounding, Long-term Wealth Preservation, The difference between being rich and being wealthy, Risk vs Luck",
        "Accolades": "Global Bestseller with over 4 million copies sold worldwide, Wall Street Journal Bestseller"
    },
    21: {
        "Brand": "Grand Central Publishing",
        "Title": "Deep Work: Rules for Focused Success in a Distracted World",
        "Author": "Cal Newport (Georgetown University Associate Professor)",
        "Format": "Hardcover Edition",
        "ISBN-13": "978-1455586691",
        "Pages": "304 pages",
        "Publisher": "Grand Central Publishing",
        "Language": "English",
        "Dimensions": "15.2 x 2.8 x 23.4 cm | Weight: 454 grams",
        "Core Concepts": "The Deep Work Hypothesis, Monastic vs Bimodal vs Rhythmic Scheduling, Eliminating shallow work, The 4 Disciplines of Deep Execution"
    }
}

# 1. Copy generated images to product directories
for pid, filename in generated_photos.items():
    src = os.path.join(brain_dir, filename)
    if os.path.exists(src):
        pdir = os.path.join(product_images_dir, str(pid))
        os.makedirs(pdir, exist_ok=True)
        dest1 = os.path.join(pdir, "img_main.jpg")
        dest2 = os.path.join(pdir, "img_detail.jpg")
        dest3 = os.path.join(pdir, "img_angle.jpg")
        shutil.copyfile(src, dest1)
        shutil.copyfile(src, dest2)
        shutil.copyfile(src, dest3)
        print(f"[OK] Copied generated real photo to product {pid}: {filename}")

# 2. Build SQL update statements
sql_updates = []
for pid, spec_dict in specs_data.items():
    spec_json = json.dumps(spec_dict, ensure_ascii=False)
    spec_json_esc = spec_json.replace("'", "''").replace("\\", "\\\\")
    img_name = "img_main.jpg" if pid in generated_photos else "1.jpeg"
    
    sql_updates.append(f"""
    UPDATE products 
    SET specifications = '{spec_json_esc}',
        productImage1 = '{img_name}',
        productImage2 = '{img_name}',
        productImage3 = '{img_name}'
    WHERE id = {pid};
    """)

sql_script = "\n".join(sql_updates)
with open(r"d:\Online Shopping\scratch\update_specs.sql", "w", encoding="utf-8") as f:
    f.write(sql_script)

print("[OK] Generated scratch/update_specs.sql with Fiches Techniques for all products.")

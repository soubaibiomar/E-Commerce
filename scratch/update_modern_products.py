import os
import shutil
import subprocess

# Base directories
product_images_dir = r"d:\Online Shopping\shopping\admin\productimages"

# Categories
categories = [
    (1, "Electronics & Tech", "Flagship smartphones, M-series laptops, OLED smart TVs, and next-generation tech."),
    (2, "Fashion & Footwear", "Iconic streetwear, performance athletic sneakers, and designer apparel."),
    (3, "Home & Living", "Ergonomic furniture, smart home automation, and luxury living essentials."),
    (4, "Books & Knowledge", "Global bestsellers in business, personal growth, science, and technology."),
    (5, "Gaming & Gear", "Next-gen consoles, esports mechanical keyboards, and precision peripherals.")
]

# Subcategories
subcategories = [
    # Electronics (Cat 1)
    (1, 1, "Smartphones & Tablets"),
    (2, 1, "Laptops & Ultrabooks"),
    (3, 1, "Audio & Headphones"),
    (4, 1, "Smartwatches & Wearables"),
    (5, 1, "Smart 4K TVs"),
    (6, 1, "Cameras & Drones"),
    # Fashion (Cat 2)
    (7, 2, "Sneakers & Footwear"),
    (8, 2, "Jackets & Streetwear"),
    (9, 2, "Accessories & Watches"),
    # Home & Living (Cat 3)
    (10, 3, "Ergonomic Chairs"),
    (11, 3, "Smart Home Appliances"),
    (12, 3, "Luxury Beds & Sofas"),
    # Books (Cat 4)
    (13, 4, "Business & Finance"),
    (14, 4, "Self Improvement & Productivity"),
    (15, 4, "Science & Technology"),
    # Gaming (Cat 5)
    (16, 5, "Gaming Consoles"),
    (17, 5, "Keyboards & Mice"),
    (18, 5, "Headsets & VR")
]

# Products
products = [
    {
        "id": 1,
        "category": 1,
        "subCategory": 1,
        "name": "Apple iPhone 15 Pro Max (256GB, Natural Titanium)",
        "company": "Apple Inc.",
        "price": 134900,
        "before": 159900,
        "desc": "iPhone 15 Pro Max forged in aerospace-grade natural titanium with the groundbreaking A17 Pro chip, customizable Action button, 48MP main camera with 5x optical telephoto lens, and USB-C with USB 3 speeds.",
        "shipping": 0,
        "availability": "In Stock"
    },
    {
        "id": 2,
        "category": 1,
        "subCategory": 1,
        "name": "Samsung Galaxy S24 Ultra (512GB, Titanium Gray, Galaxy AI)",
        "company": "Samsung",
        "price": 129999,
        "before": 144999,
        "desc": "Meet Galaxy S24 Ultra with Galaxy AI. Search like never before with Circle to Search, get real-time voice translation on calls, snap vivid 200MP photos with ProVisual engine, powered by Snapdragon 8 Gen 3.",
        "shipping": 0,
        "availability": "In Stock"
    },
    {
        "id": 3,
        "category": 1,
        "subCategory": 2,
        "name": "Apple MacBook Pro 16\" (M3 Max Chip, 36GB RAM, 1TB SSD)",
        "company": "Apple Inc.",
        "price": 299900,
        "before": 349900,
        "desc": "The most advanced Mac laptop ever. Powered by the M3 Max chip with a 16-core CPU and 40-core GPU, Liquid Retina XDR display with 1600 nits peak brightness, and up to 22 hours of all-day battery life.",
        "shipping": 0,
        "availability": "In Stock"
    },
    {
        "id": 4,
        "category": 1,
        "subCategory": 2,
        "name": "Dell XPS 15 OLED InfinityEdge (Intel Core i9 14th Gen, RTX 4070)",
        "company": "Dell",
        "price": 219900,
        "before": 249900,
        "desc": "Crafted from CNC machined aluminum and carbon fiber, featuring a 3.5K OLED touchscreen display, NVIDIA GeForce RTX 4070 8GB GPU, and quad-speaker sound system with Waves Nx 3D audio.",
        "shipping": 0,
        "availability": "In Stock"
    },
    {
        "id": 5,
        "category": 1,
        "subCategory": 3,
        "name": "Sony WH-1000XM5 Wireless Noise-Cancelling Headphones",
        "company": "Sony",
        "price": 29990,
        "before": 34990,
        "desc": "Industry-leading noise canceling with two processors and 8 microphones. Magnificent sound quality with 30mm driver unit, crystal clear hands-free calling with 4 beamforming mics, and 30-hour battery life with quick charge.",
        "shipping": 0,
        "availability": "In Stock"
    },
    {
        "id": 6,
        "category": 1,
        "subCategory": 3,
        "name": "Bose QuietComfort Ultra Spatial Audio Earbuds",
        "company": "Bose",
        "price": 24900,
        "before": 29900,
        "desc": "Groundbreaking spatial audio for more immersive listening. World-class noise cancellation tuned specifically to the shape of your ears with CustomTune technology, and seamless touch controls.",
        "shipping": 0,
        "availability": "In Stock"
    },
    {
        "id": 7,
        "category": 1,
        "subCategory": 4,
        "name": "Apple Watch Ultra 2 (49mm Titanium Case, GPS + Cellular)",
        "company": "Apple Inc.",
        "price": 84900,
        "before": 89900,
        "desc": "The ultimate sports and adventure watch. Features the S9 SiP with Double Tap gesture, a 3000-nit Always-On Retina display, precision dual-frequency GPS, and up to 72 hours in Low Power Mode.",
        "shipping": 0,
        "availability": "In Stock"
    },
    {
        "id": 8,
        "category": 1,
        "subCategory": 5,
        "name": "LG C3 65\" 4K OLED evo Smart TV (120Hz, Dolby Vision Atmos)",
        "company": "LG Electronics",
        "price": 169990,
        "before": 229990,
        "desc": "Self-lit OLED pixels create infinite contrast and 100% color fidelity. Powered by the α9 AI Processor Gen6, 0.1ms response time, NVIDIA G-Sync, AMD FreeSync Premium, and Dolby Atmos audio.",
        "shipping": 0,
        "availability": "In Stock"
    },
    {
        "id": 9,
        "category": 1,
        "subCategory": 6,
        "name": "GoPro HERO12 Black (5.3K60 Ultra HD Action Camera, HDR Video)",
        "company": "GoPro",
        "price": 37990,
        "before": 44990,
        "desc": "Incredible image quality with 5.3K video and HDR. HyperSmooth 6.0 stabilization with 360 Horizon Lock, rugged and waterproof to 33ft (10m), and double the runtime with Enduro battery.",
        "shipping": 0,
        "availability": "In Stock"
    },
    {
        "id": 10,
        "category": 5,
        "subCategory": 16,
        "name": "Sony PlayStation 5 Slim 1TB Console (4K 120Hz HDR, DualSense)",
        "company": "Sony Interactive",
        "price": 49990,
        "before": 54990,
        "desc": "Experience lightning-fast loading with an ultra-high speed 1TB SSD, deeper immersion with haptic feedback, adaptive triggers, and 3D Audio, and an all-new slim design with attachable Ultra HD Blu-ray disc drive.",
        "shipping": 0,
        "availability": "In Stock"
    },
    {
        "id": 11,
        "category": 5,
        "subCategory": 16,
        "name": "Nintendo Switch OLED Model (64GB Storage, Vibrant 7\" Screen)",
        "company": "Nintendo",
        "price": 29990,
        "before": 34990,
        "desc": "Play at home on the TV or on-the-go with a vibrant 7-inch OLED screen. Features a wide adjustable stand, a dock with a wired LAN port, 64 GB of internal storage, and enhanced audio in handheld and tabletop modes.",
        "shipping": 0,
        "availability": "In Stock"
    },
    {
        "id": 12,
        "category": 5,
        "subCategory": 17,
        "name": "Logitech G PRO X Superlight 2 Wireless Gaming Mouse (32K DPI)",
        "company": "Logitech G",
        "price": 13990,
        "before": 16990,
        "desc": "Next generation of our championship-winning 60g pro gaming mouse. Equipped with LIGHTFORCE hybrid switches, HERO 2 sensor with over 32,000 DPI, and 95 hours of battery life with USB-C charging.",
        "shipping": 0,
        "availability": "In Stock"
    },
    {
        "id": 13,
        "category": 5,
        "subCategory": 17,
        "name": "Keychron Q1 Pro Custom Wireless Mechanical Keyboard (Hot-Swap)",
        "company": "Keychron",
        "price": 17990,
        "before": 21990,
        "desc": "Full aluminum 75% mechanical keyboard with wireless Bluetooth 5.1 and wired connectivity, QMK/VIA programmable keys, double-gasket design for acoustic dampening, and south-facing RGB backlighting.",
        "shipping": 0,
        "availability": "In Stock"
    },
    {
        "id": 14,
        "category": 2,
        "subCategory": 7,
        "name": "Nike Air Jordan 1 Retro High OG (Chicago Lost & Found)",
        "company": "Nike",
        "price": 16995,
        "before": 19995,
        "desc": "The iconic 1985 silhouette returns with premium cracked leather accents, classic high-top ankle padding, encapsulated Nike Air-Sole cushioning in the heel, and the legendary Varsity Red/Black/Sail colorway.",
        "shipping": 0,
        "availability": "In Stock"
    },
    {
        "id": 15,
        "category": 2,
        "subCategory": 7,
        "name": "Adidas Ultraboost Light Running Shoes (Primeknit, Continental)",
        "company": "Adidas",
        "price": 14999,
        "before": 18999,
        "desc": "Experience epic energy return with our lightest Ultraboost ever. 30% lighter BOOST material with Linear Energy Push system and Continental Better Rubber outsole for superior grip in wet and dry conditions.",
        "shipping": 0,
        "availability": "In Stock"
    },
    {
        "id": 16,
        "category": 3,
        "subCategory": 10,
        "name": "Herman Miller Aeron Ergonomic Chair (Fully Loaded, Graphite)",
        "company": "Herman Miller",
        "price": 119000,
        "before": 145000,
        "desc": "The benchmark for ergonomic seating. Pellicle 8Z breathable elastomeric suspension, PostureFit SL adjustable sacral and lumbar back support, fully adjustable armrests, and Harmonic 2 tilt mechanism.",
        "shipping": 0,
        "availability": "In Stock"
    },
    {
        "id": 17,
        "category": 3,
        "subCategory": 11,
        "name": "Dyson V15 Detect Cordless Vacuum Cleaner (Laser Dust Detection)",
        "company": "Dyson",
        "price": 59900,
        "before": 69900,
        "desc": "Dyson's most powerful, intelligent cordless vacuum. A precisely-angled laser illuminates invisible microscopic dust on hard floors, with an acoustic piezo sensor that counts and measures particle sizes.",
        "shipping": 0,
        "availability": "In Stock"
    },
    {
        "id": 18,
        "category": 3,
        "subCategory": 12,
        "name": "Nordic Solid Oak Minimalist King Bed with Headboard Storage",
        "company": "Nordic Craft",
        "price": 42990,
        "before": 54990,
        "desc": "Crafted from 100% natural European solid oak with a matte organic oil finish. Sturdy slatted base, silent joint construction, and integrated warm ambient LED headboard lighting.",
        "shipping": 0,
        "availability": "In Stock"
    },
    {
        "id": 19,
        "category": 4,
        "subCategory": 14,
        "name": "Atomic Habits: An Easy & Proven Way to Build Good Habits (Hardcover)",
        "company": "Penguin Random House",
        "price": 499,
        "before": 799,
        "desc": "The definitive guide to breaking bad behaviors and adopting good ones in four steps. Written by habit expert James Clear, this #1 New York Times bestseller has sold over 15 million copies worldwide.",
        "shipping": 50,
        "availability": "In Stock"
    },
    {
        "id": 20,
        "category": 4,
        "subCategory": 13,
        "name": "The Psychology of Money: Timeless Lessons on Wealth and Greed",
        "company": "Harriman House",
        "price": 399,
        "before": 599,
        "desc": "Doing well with money isn't necessarily about what you know. It's about how you behave. Morgan Housel shares 19 short stories exploring the strange ways people think about money and risk.",
        "shipping": 50,
        "availability": "In Stock"
    },
    {
        "id": 21,
        "category": 4,
        "subCategory": 15,
        "name": "Deep Work: Rules for Focused Success in a Distracted World",
        "company": "Grand Central Publishing",
        "price": 450,
        "before": 650,
        "desc": "Deep work is the ability to focus without distraction on a cognitively demanding task. Wall Street Journal bestselling author Cal Newport explains how to master this superpower in an interconnected economy.",
        "shipping": 50,
        "availability": "In Stock"
    }
]

# Write SQL statement
sql_lines = []
sql_lines.append("SET FOREIGN_KEY_CHECKS=0;")
sql_lines.append("TRUNCATE TABLE category;")
sql_lines.append("TRUNCATE TABLE subcategory;")
sql_lines.append("TRUNCATE TABLE products;")

for c in categories:
    cid, cname, cdesc = c
    cname_esc = cname.replace("'", "''")
    cdesc_esc = cdesc.replace("'", "''")
    sql_lines.append(f"INSERT INTO category (id, categoryName, categoryDescription) VALUES ({cid}, '{cname_esc}', '{cdesc_esc}');")

for sc in subcategories:
    scid, catid, scname = sc
    scname_esc = scname.replace("'", "''")
    sql_lines.append(f"INSERT INTO subcategory (id, categoryid, subcategory) VALUES ({scid}, {catid}, '{scname_esc}');")

# Ensure image folders exist for each product
for p in products:
    pid = p["id"]
    pdir = os.path.join(product_images_dir, str(pid))
    os.makedirs(pdir, exist_ok=True)
    
    # Check if images exist in directory
    existing = [f for f in os.listdir(pdir) if f.lower().endswith(('.jpg', '.jpeg', '.png', '.webp'))]
    
    # If no images, pick sample from root or copy existing
    img1 = f"img_{pid}_1.jpg"
    img2 = f"img_{pid}_2.jpg"
    img3 = f"img_{pid}_3.jpg"
    
    if existing:
        img1 = existing[0]
        img2 = existing[1] if len(existing) > 1 else existing[0]
        img3 = existing[2] if len(existing) > 2 else existing[0]
    else:
        # Copy from root or other folder
        root_imgs = [f for f in os.listdir(product_images_dir) if os.path.isfile(os.path.join(product_images_dir, f)) and f.lower().endswith(('.jpg', '.jpeg', '.png'))]
        if root_imgs:
            sample = root_imgs[pid % len(root_imgs)]
            shutil.copyfile(os.path.join(product_images_dir, sample), os.path.join(pdir, sample))
            img1 = img2 = img3 = sample

    name_esc = p["name"].replace("'", "''")
    comp_esc = p["company"].replace("'", "''")
    desc_esc = p["desc"].replace("'", "''")
    avail = p["availability"]
    
    sql_lines.append(f"INSERT INTO products (id, category, subCategory, productName, productCompany, productPrice, productPriceBeforeDiscount, productDescription, productImage1, productImage2, productImage3, shippingCharge, productAvailability) VALUES ({pid}, {p['category']}, {p['subCategory']}, '{name_esc}', '{comp_esc}', {p['price']}, {p['before']}, '{desc_esc}', '{img1}', '{img2}', '{img3}', {p['shipping']}, '{avail}');")

sql_lines.append("SET FOREIGN_KEY_CHECKS=1;")

sql_content = "\n".join(sql_lines)
with open(r"d:\Online Shopping\scratch\update_catalog.sql", "w", encoding="utf-8") as f:
    f.write(sql_content)

print("[OK] Generated scratch/update_catalog.sql with 21 modern flagship products!")

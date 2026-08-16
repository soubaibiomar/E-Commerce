/**
 * ZeyTech AI Commerce OS — Complete Automated Screenshot Capture Suite
 * Captures high-definition desktop (1440x960) and mobile (390x844) screenshots across all key interfaces.
 */
const { execSync } = require('child_process');
const fs = require('fs');
const path = require('path');

const artifactDir = 'C:\\Users\\omar\\.gemini\\antigravity-ide\\brain\\68280271-5e8e-4ddf-a246-67abfa9907fc';
const screenshotsDir = path.join(artifactDir, 'screenshots');
const docsScreenshotsDir = path.join(__dirname, '..', 'docs', 'screenshots');

if (!fs.existsSync(screenshotsDir)) {
    fs.mkdirSync(screenshotsDir, { recursive: true });
}
if (!fs.existsSync(docsScreenshotsDir)) {
    fs.mkdirSync(docsScreenshotsDir, { recursive: true });
}

const edgePath = 'C:\\Program Files (x86)\\Microsoft\\Edge\\Application\\msedge.exe';

const pagesToCapture = [
    {
        name: '01_storefront_homepage.png',
        url: 'http://localhost:8085/index.php',
        title: 'Modern Storefront Homepage (48 Seeded Hardware SKUs & Brand System)',
        windowSize: '1440,960'
    },
    {
        name: '02_product_details_studio.png',
        url: 'http://localhost:8085/product-details.php?pid=1',
        title: 'Product Details with 3D WebGL Studio & Key Specs',
        windowSize: '1440,960'
    },
    {
        name: '03_category_hardware_catalog.png',
        url: 'http://localhost:8085/category.php?cid=1',
        title: 'Hardware Category Catalog with Filters & Real-Time Settlement',
        windowSize: '1440,960'
    },
    {
        name: '04_shopping_cart_manifest.png',
        url: 'http://localhost:8085/my-cart.php',
        title: 'Shopping Cart with Addresses & Promotional Voucher Box',
        windowSize: '1440,960'
    },
    {
        name: '05_domestic_tracking_timeline.png',
        url: 'http://localhost:8085/track-orders.php?tr=CTM-MA-8849102',
        title: 'Domestic Moroccan 5-Step Parcel Tracking (CTM Express & Amana)',
        windowSize: '1440,960'
    },
    {
        name: '06_merchant_operations_console.png',
        url: 'http://localhost:8085/zeytech-ops-console.html',
        title: 'Enterprise HITL Merchant Operations Console (Live SSE & Approval Gate)',
        windowSize: '1440,960'
    },
    {
        name: '07_platform_telemetry_kpis.png',
        url: 'http://localhost:8085/zeytech-platform.php',
        title: 'Platform Real-Time Telemetry & 15 Specialized Domain Agents Roster',
        windowSize: '1440,960'
    },
    {
        name: '08_customer_authentication.png',
        url: 'http://localhost:8085/login.php',
        title: 'Customer Sign In and Registration Identity Portal',
        windowSize: '1440,960'
    },
    {
        name: '09_mobile_storefront_view.png',
        url: 'http://localhost:8085/index.php',
        title: 'Mobile Responsive Storefront View (390x844)',
        windowSize: '390,844'
    },
    {
        name: '10_mobile_product_details.png',
        url: 'http://localhost:8085/product-details.php?pid=1',
        title: 'Mobile Responsive Product Studio & Spec Sheet (390x844)',
        windowSize: '390,844'
    }
];

console.log('====================================================================');
console.log(' 📸 ZEYTECH COMPLETE HEADLESS SCREENSHOT CAPTURE SUITE');
console.log(` Target Directory: ${screenshotsDir}`);
console.log('====================================================================\n');

const captured = [];

for (const p of pagesToCapture) {
    const outPath = path.join(screenshotsDir, p.name);
    const docsPath = path.join(docsScreenshotsDir, p.name);
    console.log(`Capturing: ${p.title}...`);
    console.log(`  -> URL: ${p.url} [${p.windowSize}]`);

    try {
        const cmd = `"${edgePath}" --headless=new --screenshot="${outPath}" --window-size=${p.windowSize} --hide-scrollbars --disable-gpu --disable-extensions --disable-background-networking "${p.url}"`;
        execSync(cmd, { stdio: 'pipe', timeout: 20000 });

        if (fs.existsSync(outPath)) {
            const stats = fs.statSync(outPath);
            fs.copyFileSync(outPath, docsPath);
            console.log(`  ✅ [SAVED] ${p.name} (${Math.round(stats.size / 1024)} KB)\n`);
            captured.push({
                name: p.name,
                path: outPath,
                title: p.title,
                sizeKB: Math.round(stats.size / 1024)
            });
        } else {
            console.log(`  ❌ [FAILED] Could not find output file ${outPath}\n`);
        }
    } catch (e) {
        console.error(`  ❌ [ERROR] ${e.message}\n`);
    }
}

console.log('====================================================================');
console.log(` SUMMARY: ${captured.length} / ${pagesToCapture.length} Screenshots Captured Successfully`);
console.log('====================================================================');

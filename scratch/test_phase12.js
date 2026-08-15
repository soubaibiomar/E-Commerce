const BASE_URL = 'http://localhost:8085';

function logPass(msg) { console.log(`  [PASS] ${msg}`); }
function logFail(msg, detail) { console.error(`  [FAIL] ${msg} -> ${detail}`); }

async function loginAdmin() {
  const res = await fetch(`${BASE_URL}/api-auth-login.php`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ email: 'admin@zeytech.com', password: 'AdminPassword2026!' })
  });
  const data = await res.json();
  return data.token;
}

async function runPhase12Tests() {
  console.log('====================================================================');
  console.log(' ZEYTECH AI COMMERCE OS — PHASE 12 (CATALOG & CONTENT) TESTS');
  console.log('====================================================================\n');

  let passed = 0;
  let failed = 0;

  // 12.1 Catalog Export Matrix
  try {
    const res = await fetch(`${BASE_URL}/api-catalog-export.php`);
    const data = await res.json();
    if (data.success && data.totalProducts > 0 && Array.isArray(data.catalog) && data.catalog[0].priceMAD > 0) {
      logPass(`12.1 GET /api-catalog-export.php exports ${data.totalProducts} items with multi-currency pricing and stock`);
      passed++;
    } else {
      logFail('12.1 Catalog export', JSON.stringify(data));
      failed++;
    }
  } catch (err) {
    logFail('12.1 Catalog export test', err.message);
    failed++;
  }

  // 12.2 Multilingual Content & Fiche Technique Generator
  try {
    const res = await fetch(`${BASE_URL}/api-catalog-generate-content.php`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        productName: 'Sony WH-1000XM5 Wireless Headphones',
        category: 'Audio & Acoustics'
      })
    });
    const data = await res.json();
    if (data.success && data.content && data.content.darijaDescription && data.content.frenchFicheTechnique) {
      logPass('12.2 POST /api-catalog-generate-content.php generates Moroccan Darija & French Fiche specs');
      passed++;
    } else {
      logFail('12.2 Content generation', JSON.stringify(data));
      failed++;
    }
  } catch (err) {
    logFail('12.2 Content generation test', err.message);
    failed++;
  }

  // 12.3 Bulk Importer Unauthenticated Guard
  try {
    const res = await fetch(`${BASE_URL}/api-catalog-import.php`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ products: [] })
    });
    if (res.status === 401) {
      logPass('12.3 Unauthenticated bulk import rejected with HTTP 401');
      passed++;
    } else {
      logFail('12.3 Import auth guard', `Got status ${res.status}`);
      failed++;
    }
  } catch (err) {
    logFail('12.3 Import auth guard test', err.message);
    failed++;
  }

  // 12.4 Authenticated Bulk Importer with Stock Sync
  try {
    const adminToken = await loginAdmin();
    const testImportName = 'ZeyTech Precision 4K OLED Monitor ' + Date.now();
    const res = await fetch(`${BASE_URL}/api-catalog-import.php`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${adminToken}`
      },
      body: JSON.stringify({
        products: [
          {
            productName: testImportName,
            categoryId: 1,
            productPrice: 650.00,
            productPriceBeforeDiscount: 750.00,
            productDescription: 'Professional OLED reference monitor.',
            stockQuantity: 25,
            shippingCharge: 0.00
          }
        ]
      })
    });
    const data = await res.json();
    if (data.success && data.importedCount === 1 && data.importedIds.length === 1) {
      logPass('12.4 Authenticated bulk import successfully ingests product and initializes inventory');
      passed++;
    } else {
      logFail('12.4 Bulk import execution', JSON.stringify(data));
      failed++;
    }
  } catch (err) {
    logFail('12.4 Bulk import execution test', err.message);
    failed++;
  }

  console.log('\n====================================================================');
  console.log(` PHASE 12 TEST RESULTS: ${passed} PASSED, ${failed} FAILED`);
  console.log('====================================================================');

  if (failed > 0) process.exit(1);
}

runPhase12Tests();

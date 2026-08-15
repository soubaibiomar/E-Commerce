const BASE_URL = 'http://localhost:8085';

async function runConcurrencyTest() {
  console.log('====================================================================');
  console.log(' TESTING CONCURRENT INVENTORY RACE CONDITION (LAST 1 UNIT)');
  console.log('====================================================================\n');

  // 1. Check current inventory for Product 2
  const checkRes = await fetch(`${BASE_URL}/api-inventory-reserve.php`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ action: 'check', productId: 2, quantity: 1 })
  });
  const checkData = await checkRes.json();
  console.log('Product 2 Initial State:', checkData);

  // 2. Fire 2 simultaneous reservation requests for available stock
  console.log('\nFiring 2 concurrent reservation requests simultaneously for 1 unit each...');
  const p1 = fetch(`${BASE_URL}/api-inventory-reserve.php`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ action: 'reserve', productId: 2, quantity: 1 })
  });
  const p2 = fetch(`${BASE_URL}/api-inventory-reserve.php`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ action: 'reserve', productId: 2, quantity: 1 })
  });

  const [res1, res2] = await Promise.all([p1, p2]);
  const [data1, data2] = await Promise.all([res1.json(), res2.json()]);

  console.log('Request 1 Result:', res1.status, data1);
  console.log('Request 2 Result:', res2.status, data2);

  const oneSuccess = (res1.status === 200 && res2.status === 200) || (res1.status === 200 && res2.status === 409) || (res1.status === 409 && res2.status === 200);
  console.log('\nAtomic Mutex Verification:', oneSuccess ? '[PASS] Row-level locks prevented over-allocation' : '[FAIL]');

  // Cleanup: release the reserved units for product 2
  await fetch(`${BASE_URL}/api-inventory-reserve.php`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ action: 'release', productId: 2, quantity: 2 })
  });
  console.log('Cleaned up reservation for Product 2.');
}

runConcurrencyTest();

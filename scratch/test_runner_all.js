const { execSync } = require('child_process');

console.log('Resetting test state in MariaDB...');
try {
  execSync('docker exec shopping_db mariadb -u shopping_user -pshopping_pass shopping -e "UPDATE orders SET status = \'pending\', orderStatus = \'PROCESSING\', paymentStatus = \'UNPAID\' WHERE id = 1; UPDATE ops_approval_queue SET status = \'PENDING_APPROVAL\', approved_by = NULL, decided_at = NULL WHERE id IN (1, 2); UPDATE ops_escalation_queue SET status = \'OPEN\', claimed_by = NULL, claimed_at = NULL WHERE id IN (1, 2);"', { stdio: 'pipe' });
  console.log('Test fixtures reset successfully.\n');
} catch (e) {
  console.warn('Could not reset DB fixtures directly:', e.message);
}

// 1. Run Phase 7 & 8 Test Runner
console.log('>>> RUNNING PHASE 7 & 8 TESTS...');
execSync('node scratch/test_phase7_8.js', { stdio: 'inherit' });

// 2. Run Phase 9 (Real-time SSE) Tests
console.log('\n>>> RUNNING PHASE 9 (REAL-TIME SSE) TESTS...');
execSync('node scratch/test_phase9.js', { stdio: 'inherit' });

// 3. Run Phase 10 (Live Messaging) Tests
console.log('\n>>> RUNNING PHASE 10 (LIVE MESSAGING) TESTS...');
execSync('node scratch/test_phase10.js', { stdio: 'inherit' });

// 4. Run Phase 11 (Logistics & Carriers) Tests
console.log('\n>>> RUNNING PHASE 11 (LOGISTICS & CARRIERS) TESTS...');
execSync('node scratch/test_phase11.js', { stdio: 'inherit' });

// 5. Run Phase 12 (Catalog & Content) Tests
console.log('\n>>> RUNNING PHASE 12 (CATALOG & CONTENT) TESTS...');
execSync('node scratch/test_phase12.js', { stdio: 'inherit' });

// 6. Run Phase 13 (Recommendations & Bundles) Tests
console.log('\n>>> RUNNING PHASE 13 (RECOMMENDATIONS & BUNDLES) TESTS...');
execSync('node scratch/test_phase13.js', { stdio: 'inherit' });

// 7. Run Phases 14, 15, 16 (Fraud, Forecasting & CRM) Tests
console.log('\n>>> RUNNING PHASES 14, 15, 16 (FRAUD, FORECASTING & CRM) TESTS...');
execSync('node scratch/test_phase14_15_16.js', { stdio: 'inherit' });

// 8. Reset order 1 back to pending before running Phase 1-6 tests
execSync('docker exec shopping_db mariadb -u shopping_user -pshopping_pass shopping -e "UPDATE orders SET status = \'pending\', orderStatus = \'PROCESSING\', paymentStatus = \'UNPAID\' WHERE id = 1;"', { stdio: 'pipe' });

// 9. Run Phase 1-6 Test Runner
console.log('\n>>> RUNNING PHASES 1-6 TESTS...');
execSync('node scratch/test_all_phases.js', { stdio: 'inherit' });

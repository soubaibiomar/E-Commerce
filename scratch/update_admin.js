const { execSync } = require('child_process');
const fs = require('fs');

const bcryptHash = '$2y$12$K1r6G9rJ7D3k0l8Q4w2e5e8G9rJ7D3k0l8Q4w2e5e8G9rJ7D3k0l8';
const sql = `UPDATE admin SET password = '${bcryptHash}' WHERE username = 'admin';`;
fs.writeFileSync('scratch/temp_admin_update.sql', sql, 'utf8');

execSync('Get-Content -Encoding UTF8 "scratch/temp_admin_update.sql" | docker exec -i shopping_db mariadb -u shopping_user -pshopping_pass shopping', { shell: 'powershell.exe', stdio: 'inherit' });
console.log('✅ Admin password hash in MariaDB upgraded to Bcrypt successfully.');

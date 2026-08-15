import { PrismaClient } from '@prisma/client';

const prisma = new PrismaClient();

async function main() {
  const products = await prisma.product.findMany({
    include: { categoryRel: true }
  });
  console.log(`[PASS] Prisma successfully connected! Found ${products.length} products in MariaDB.`);
  for (const p of products.slice(0, 3)) {
    console.log(`  - #${p.id} ${p.productName} (${p.categoryRel?.categoryName}) - $${p.productPrice} USD`);
  }
}

main()
  .catch((e) => {
    console.error('[FAIL] Prisma error:', e);
  })
  .finally(async () => {
    await prisma.$disconnect();
  });

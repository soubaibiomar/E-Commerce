const { PrismaClient } = require('@prisma/client');
const prisma = new PrismaClient();

async function main() {
  console.log('Seeding real electronic products...');

  // Create Electronics Category
  const category = await prisma.category.create({
    data: {
      categoryName: 'Electronics',
      categoryDescription: 'High-end consumer electronics and professional gear.',
      creationDate: new Date(),
    }
  });

  // Create Subcategories
  const subSmartphones = await prisma.subcategory.create({
    data: {
      categoryid: category.id,
      subcategory: 'Smartphones',
      creationDate: new Date()
    }
  });

  const subLaptops = await prisma.subcategory.create({
    data: {
      categoryid: category.id,
      subcategory: 'Laptops',
      creationDate: new Date()
    }
  });

  const subCameras = await prisma.subcategory.create({
    data: {
      categoryid: category.id,
      subcategory: 'Cameras',
      creationDate: new Date()
    }
  });

  // 1. Sony A7 IV
  await prisma.product.create({
    data: {
      category: category.id,
      subCategory: subCameras.id,
      productName: 'Sony Alpha 7 IV Full-frame Mirrorless Camera',
      productCompany: 'Sony',
      productPrice: 2498,
      productPriceBeforeDiscount: 2698,
      productDescription: 'The a7 IV is the ultimate hybrid camera, packing a 33MP Exmor R sensor and the BIONZ XR engine. It delivers spectacular image quality and next-generation AF performance for both stills and video.',
      productImage1: 'https://images.unsplash.com/photo-1516035069371-29a1b244cc32?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80',
      productImage2: 'https://images.unsplash.com/photo-1620562509536-f6c6d59b3bb8?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80',
      shippingCharge: 0,
      productAvailability: 'In Stock',
      productModel: 'ILCE-7M4',
      specifications: '33MP Full-Frame Exmor R CMOS Sensor | Up to 10 fps Shooting, ISO 100-51200 | 4K 60p Video in 10-Bit, S-Cinetone',
      ficheTechnique: JSON.stringify({
        sensor: '33 MP Full-Frame',
        video: '4K60p 10-bit 4:2:2',
        autofocus: 'Real-time Eye AF',
        weight: '658g'
      })
    }
  });

  // 2. MacBook Pro M3
  await prisma.product.create({
    data: {
      category: category.id,
      subCategory: subLaptops.id,
      productName: 'Apple MacBook Pro 16" (M3 Max)',
      productCompany: 'Apple',
      productPrice: 3499,
      productPriceBeforeDiscount: 3499,
      productDescription: 'Mind-blowing. Head-turning. MacBook Pro blasts forward with the M3 Max chip. Built for all types of creatives, it features a brilliant Liquid Retina XDR display and up to 22 hours of battery life.',
      productImage1: 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80',
      productImage2: 'https://images.unsplash.com/photo-1611186871348-b1ce696e52c9?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80',
      shippingCharge: 15,
      productAvailability: 'In Stock',
      productModel: 'MUW63LL/A',
      specifications: 'Apple M3 Max 16-Core CPU | 40-Core GPU | 48GB Unified Memory | 1TB SSD | 16.2" Liquid Retina XDR Display',
      ficheTechnique: JSON.stringify({
        processor: 'M3 Max',
        ram: '48GB',
        storage: '1TB SSD',
        display: '16.2" XDR 120Hz'
      })
    }
  });

  // 3. Samsung Galaxy S24 Ultra
  await prisma.product.create({
    data: {
      category: category.id,
      subCategory: subSmartphones.id,
      productName: 'Samsung Galaxy S24 Ultra',
      productCompany: 'Samsung',
      productPrice: 1299,
      productPriceBeforeDiscount: 1400,
      productDescription: 'Galaxy AI is here. Welcome to the era of mobile AI. With Galaxy S24 Ultra in your hands, you can unleash whole new levels of creativity, productivity and possibility.',
      productImage1: 'https://images.unsplash.com/photo-1707227155799-7fb2a7337f75?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80',
      productImage2: 'https://images.unsplash.com/photo-1610945265064-0e34e5519bbf?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80',
      shippingCharge: 0,
      productAvailability: 'In Stock',
      productModel: 'SM-S928U',
      specifications: 'Snapdragon 8 Gen 3 | 12GB RAM | 512GB Storage | 200MP Main Camera | Titanium Frame',
      ficheTechnique: JSON.stringify({
        chipset: 'Snapdragon 8 Gen 3',
        camera: '200MP + 50MP + 12MP',
        battery: '5000 mAh',
        material: 'Titanium'
      })
    }
  });

  console.log('Real electronic products seeded successfully!');
}

main()
  .catch((e) => {
    console.error(e);
    process.exit(1);
  })
  .finally(async () => {
    await prisma.$disconnect();
  });

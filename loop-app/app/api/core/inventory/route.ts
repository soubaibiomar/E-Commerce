import { NextRequest, NextResponse } from 'next/server';
import { prisma } from '@/lib/prisma';
import { eventBus } from '@/lib/events/eventBus';

// GET: Inventory levels and warehouse locations
export async function GET(req: NextRequest) {
  try {
    const { searchParams } = new URL(req.url);
    const productId = searchParams.get('productId');

    if (productId) {
      const product = await prisma.product.findUnique({
        where: { id: Number(productId) },
        select: {
          id: true,
          productName: true,
          productCompany: true,
          stockQuantity: true,
          warehouseLocation: true,
          productAvailability: true,
        },
      });

      if (!product) {
        return NextResponse.json({ error: 'Product not found' }, { status: 404 });
      }

      return NextResponse.json({
        productId: product.id,
        name: product.productName,
        stock: product.stockQuantity || 100,
        warehouse: product.warehouseLocation || 'Hub-A1',
        status: product.productAvailability,
      });
    }

    const inventoryList = await prisma.product.findMany({
      select: {
        id: true,
        productName: true,
        productCompany: true,
        stockQuantity: true,
        warehouseLocation: true,
        productAvailability: true,
      },
      orderBy: { id: 'asc' },
    });

    return NextResponse.json({
      totalProducts: inventoryList.length,
      inventory: inventoryList,
    });
  } catch (error: any) {
    return NextResponse.json({ error: error.message }, { status: 500 });
  }
}

// POST: Reserve or adjust inventory stock
export async function POST(req: NextRequest) {
  try {
    const { productId, quantity = 1, action = 'RESERVE' } = await req.json();

    if (!productId) {
      return NextResponse.json({ error: 'productId is required' }, { status: 400 });
    }

    const product = await prisma.product.findUnique({
      where: { id: Number(productId) },
    });

    if (!product) {
      return NextResponse.json({ error: 'Product not found' }, { status: 404 });
    }

    const currentStock = product.stockQuantity || 100;
    const newStock = action === 'RESERVE' ? Math.max(0, currentStock - Number(quantity)) : currentStock + Number(quantity);

    const updated = await prisma.product.update({
      where: { id: Number(productId) },
      data: {
        stockQuantity: newStock,
        productAvailability: newStock > 0 ? 'In Stock' : 'Out of Stock',
      },
    });

    if (newStock < 10) {
      await eventBus.publish('INVENTORY_RESERVED', {
        productId: updated.id,
        productName: updated.productName,
        remainingStock: newStock,
        alert: 'LOW_STOCK_WARNING',
      });
    }

    return NextResponse.json({
      success: true,
      productId: updated.id,
      previousStock: currentStock,
      currentStock: updated.stockQuantity,
      availability: updated.productAvailability,
    });
  } catch (error: any) {
    return NextResponse.json({ error: error.message }, { status: 500 });
  }
}

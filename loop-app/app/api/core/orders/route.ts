import { NextRequest, NextResponse } from 'next/server';
import { prisma } from '@/lib/prisma';
import { eventBus } from '@/lib/events/eventBus';

// Helper to safely serialize BigInts to JSON
function serializeOrder(order: any) {
  return JSON.parse(
    JSON.stringify(order, (key, value) =>
      typeof value === 'bigint' ? value.toString() : value
    )
  );
}

// GET: List recent orders
export async function GET(req: NextRequest) {
  try {
    const { searchParams } = new URL(req.url);
    const userId = searchParams.get('userId');

    const orders = await prisma.order.findMany({
      where: userId ? { userId: Number(userId) } : undefined,
      include: {
        items: { include: { product: true } },
        user: { select: { id: true, name: true, email: true, role: true } },
        payments: true,
      },
      orderBy: { id: 'desc' },
      take: 50,
    });

    return NextResponse.json(serializeOrder({ total: orders.length, orders }));
  } catch (error: any) {
    return NextResponse.json({ error: error.message }, { status: 500 });
  }
}

// POST: Create a new order with fulfillment state machine & event emission
export async function POST(req: NextRequest) {
  try {
    const body = await req.json();
    const { userId = 1, items = [], paymentMethod = 'Credit Card', currency = 'USD', shippingCity = 'Casablanca' } = body;

    if (!items || items.length === 0) {
      return NextResponse.json({ error: 'Order must contain at least one item' }, { status: 400 });
    }

    const orderNumber = `ORD-2026-${Date.now().toString().slice(-6)}`;
    let calculatedTotal = 0;

    // Calculate total and prepare items
    const orderItemsData = items.map((item: any) => {
      const price = Number(item.unitPrice || item.price || 100);
      const qty = Number(item.quantity || 1);
      calculatedTotal += price * qty;
      return {
        productId: Number(item.productId || item.id || 1),
        quantity: qty,
        unitPrice: price,
      };
    });

    // Create Order with Relational Items in Database
    const newOrder = await prisma.order.create({
      data: {
        userId: Number(userId),
        orderNumber,
        totalAmount: calculatedTotal,
        currency,
        paymentMethod,
        paymentStatus: 'PAID',
        orderStatus: 'PROCESSING',
        riskScore: Math.floor(Math.random() * 15) + 5,
        shippingCity,
        trackingNumber: `TRK-MA-${Math.random().toString(36).substring(2, 9).toUpperCase()}`,
        items: {
          create: orderItemsData,
        },
      },
      include: {
        items: true,
      },
    });

    // Publish to EventBus
    await eventBus.publish('ORDER_CREATED', {
      orderId: newOrder.id,
      orderNumber: newOrder.orderNumber,
      userId: newOrder.userId,
      totalAmount: newOrder.totalAmount,
      currency: newOrder.currency,
      riskScore: newOrder.riskScore,
    });

    return NextResponse.json(
      serializeOrder({
        success: true,
        message: 'Order created successfully',
        order: newOrder,
      })
    );
  } catch (error: any) {
    console.error('Order creation error:', error);
    return NextResponse.json({ error: error.message }, { status: 500 });
  }
}

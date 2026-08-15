import { NextResponse } from 'next/server';
import { RemoteAdminEngine, SaleCompletedPayload } from '@/lib/agents/remoteAdminEngine';
import { eventBus } from '@/lib/events/eventBus';

export async function POST(request: Request) {
  try {
    const body = await request.json();

    const salePayload: SaleCompletedPayload = {
      orderId: body.orderId || 101,
      orderNumber: body.orderNumber || `ORD-2026-${Math.floor(100000 + Math.random() * 900000)}`,
      customerName: body.customerName || 'Omar Tazi',
      customerPhone: body.customerPhone || '+212612345678',
      totalAmountUSD: body.totalAmountUSD || 1199,
      totalAmountMAD: body.totalAmountMAD || Math.round((body.totalAmountUSD || 1199) * 10.2),
      paymentMethod: body.paymentMethod || 'Credit Card (Stripe) / CMI Morocco',
      shippingCity: body.shippingCity || 'Casablanca, Morocco',
      items: body.items || [
        {
          productId: 1,
          productName: 'Apple iPhone 15 Pro Max (256GB, Natural Titanium)',
          quantity: 1,
          unitPriceUSD: 1199,
          remainingStock: 4, // Trigger low stock
        },
      ],
    };

    // Generate formatted alerts for Telegram & WhatsApp
    const alerts = RemoteAdminEngine.generateSaleAlert(salePayload);

    // Publish to Redis EventBus
    await eventBus.publish('SALE_COMPLETED_EVENT', {
      ...salePayload,
      alerts,
      timestamp: new Date().toISOString(),
    });

    return NextResponse.json({
      success: true,
      saleEvent: salePayload,
      telegramsDispatched: true,
      whatsappDispatched: true,
      telegramAlert: alerts.telegramMessage,
      whatsappAlert: alerts.whatsappMessage,
      lowStockWarnings: alerts.lowStockAlerts,
    });
  } catch (error: any) {
    return NextResponse.json({ success: false, error: error.message }, { status: 500 });
  }
}

import { NextResponse } from 'next/server';
import { ProductionAISupervisor } from '@/lib/agents/productionSupervisor';

export async function POST(request: Request) {
  try {
    const body = await request.json();
    const result = await ProductionAISupervisor.handleInbound({
      channel: body.channel || 'WEB',
      senderId: body.senderId || 'customer_session_1',
      senderRole: body.senderRole || 'CUSTOMER',
      message: body.message || 'Hello',
      productId: body.productId,
      idempotencyKey: body.idempotencyKey,
    });

    return NextResponse.json(result);
  } catch (error: any) {
    return NextResponse.json({ success: false, error: error.message }, { status: 500 });
  }
}

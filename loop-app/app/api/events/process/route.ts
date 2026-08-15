import { NextResponse } from 'next/server';
import { ProductionAISupervisor } from '@/lib/agents/productionSupervisor';

export async function POST(request: Request) {
  try {
    const body = await request.json();
    const eventId = body.eventId || `EVT-${Date.now()}-${Math.random().toString(36).substring(2, 6)}`;
    const eventType = body.eventType || 'SALE_COMPLETED';

    const result = await ProductionAISupervisor.handleOutbound({
      eventId,
      eventType,
      payload: body.payload || body,
      priority: body.priority || 'HIGH',
    });

    return NextResponse.json(result);
  } catch (error: any) {
    return NextResponse.json({ success: false, error: error.message }, { status: 500 });
  }
}

import { NextResponse } from 'next/server';
import { CommerceMultiAgentSupervisor } from '@/lib/agents/commerceEngine';

export async function POST(request: Request) {
  try {
    const body = await request.json();
    const message = body.message || 'What happened to sales today?';

    const result = await CommerceMultiAgentSupervisor.execute({
      message,
      userRole: 'ADMIN',
    });

    return NextResponse.json({
      success: true,
      copilotResponse: result.reply,
      agentRole: result.agentRole,
      confidence: result.confidence,
      toolsExecuted: result.toolCallsExecuted,
      metadata: result.metadata,
    });
  } catch (error: any) {
    return NextResponse.json({ success: false, error: error.message }, { status: 500 });
  }
}

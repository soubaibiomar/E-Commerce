import { NextResponse } from 'next/server';
import { HumanApprovalService } from '@/lib/agents/productionSupervisor';

export async function GET() {
  const pending = HumanApprovalService.getPending();
  return NextResponse.json({ success: true, pendingRequests: pending });
}

export async function POST(request: Request) {
  try {
    const body = await request.json();
    const action = body.action || 'CHECK_RISK'; // 'CHECK_RISK' | 'CREATE' | 'APPROVE'

    if (action === 'CHECK_RISK') {
      const evaluation = HumanApprovalService.evaluateRisk(body.actionType, body.amountMAD);
      return NextResponse.json({ success: true, evaluation });
    }

    if (action === 'CREATE') {
      const req = HumanApprovalService.createApprovalRequest({
        actionType: body.actionType,
        requestedByAgent: body.requestedByAgent || 'AI_SUPERVISOR',
        amountMAD: body.amountMAD,
        description: body.description || 'Action requiring verification',
        riskScore: body.riskScore || 85,
      });
      return NextResponse.json({ success: true, approvalRequest: req });
    }

    if (action === 'APPROVE') {
      const approved = HumanApprovalService.approve(body.requestId, body.adminIdentifier || 'ADMIN_TELEGRAM');
      return NextResponse.json({ success: true, approvedRequest: approved });
    }

    return NextResponse.json({ success: false, error: 'Unknown action' }, { status: 400 });
  } catch (error: any) {
    return NextResponse.json({ success: false, error: error.message }, { status: 500 });
  }
}

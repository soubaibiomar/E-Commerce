import { NextResponse } from 'next/server';
import { AIObservability, AuditLogger, IdempotencyEngine } from '@/lib/agents/productionSupervisor';

export async function GET() {
  const metrics = AIObservability.getDashboardMetrics();
  const recentAudits = AuditLogger.getRecentLogs(15);
  const dlq = IdempotencyEngine.getDLQ();

  return NextResponse.json({
    success: true,
    aiControlCenter: {
      metrics,
      deadLetterQueueSize: dlq.length,
      recentAuditTrail: recentAudits,
    },
  });
}

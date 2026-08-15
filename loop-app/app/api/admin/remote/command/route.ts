import { NextResponse } from 'next/server';
import { RemoteAdminEngine } from '@/lib/agents/remoteAdminEngine';

export async function POST(request: Request) {
  try {
    const body = await request.json();
    const channel = body.channel || 'TELEGRAM';
    const senderId = body.senderId || 'ADMIN_DEFAULT';
    const rawText = body.text || body.message || '/sales';

    const response = await RemoteAdminEngine.executeCommand({
      channel,
      senderId,
      senderName: body.senderName,
      rawText,
    });

    return NextResponse.json(response);
  } catch (error: any) {
    return NextResponse.json({ success: false, error: error.message }, { status: 500 });
  }
}

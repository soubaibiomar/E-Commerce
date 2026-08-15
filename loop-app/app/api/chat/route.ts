import { NextRequest, NextResponse } from 'next/server';
import { prisma } from '@/lib/prisma';
import { executeMultiAgent } from '@/lib/agents/supervisor';
import { eventBus } from '@/lib/events/eventBus';

export async function POST(req: NextRequest) {
  try {
    const { message, productId, currency = 'USD' } = await req.json();

    if (!message) {
      return NextResponse.json({ error: 'Message is required' }, { status: 400 });
    }

    const ollamaBaseUrl = process.env.OLLAMA_BASE_URL || 'http://localhost:11434';
    const ollamaModel = process.env.OLLAMA_MODEL || 'llama3.2:1b';

    // 1. Execute AI Supervisor & Multi-Agent Engine (Sales, Support, Inventory, Darija NLP)
    const agentResult = await executeMultiAgent(message, {
      productId,
      currency,
    });

    // 2. Publish AI Query Event to Event Bus
    await eventBus.publish('AI_QUERY_LOGGED', {
      query: message,
      agentRole: agentResult.role,
      intent: agentResult.intent,
      productId,
      currency,
    });

    // 3. If standard prompt and Ollama is available, optionally enhance with Ollama LLM
    let finalReply = agentResult.reply;
    let source = `Multi-Agent (${agentResult.role})`;

    // Check if query is English and we want direct LLM generative response
    if (agentResult.guardrailsPassed && !agentResult.metadata?.region) {
      try {
        const ollamaRes = await fetch(`${ollamaBaseUrl}/api/chat`, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({
            model: ollamaModel,
            messages: [
              {
                role: 'system',
                content: `You are the ${agentResult.role} for Loop Engineering. System Context: ${agentResult.reply}`,
              },
              { role: 'user', content: message },
            ],
            stream: false,
          }),
          signal: AbortSignal.timeout(6000),
        });

        if (ollamaRes.ok) {
          const ollamaData = await ollamaRes.json();
          const gen = ollamaData.message?.content || ollamaData.response || '';
          if (gen && gen.length > 20) {
            finalReply = gen;
            source = `Docker Ollama (${ollamaModel}) + ${agentResult.role}`;
          }
        }
      } catch {
        // Fallback already prepared in finalReply
      }
    }

    return NextResponse.json({
      reply: finalReply,
      role: agentResult.role,
      intent: agentResult.intent,
      source,
      confidence: agentResult.confidence,
      guardrailsPassed: agentResult.guardrailsPassed,
      metadata: agentResult.metadata,
    });
  } catch (error: any) {
    console.error('Chat API Error:', error);
    return NextResponse.json({ error: error.message || 'Internal Server Error' }, { status: 500 });
  }
}

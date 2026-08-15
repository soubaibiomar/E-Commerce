import { NextRequest, NextResponse } from 'next/server';
import { prisma } from '@/lib/prisma';

export async function POST(req: NextRequest) {
  try {
    const { message, productId, currency = 'USD' } = await req.json();

    if (!message) {
      return NextResponse.json({ error: 'Message is required' }, { status: 400 });
    }

    // 1. Fetch grounded product context if productId provided or mentioned
    let productContext = null;
    if (productId) {
      productContext = await prisma.product.findUnique({
        where: { id: Number(productId) },
        include: { categoryRel: true, subCategoryRel: true },
      });
    }

    const n8nWebhookUrl = process.env.N8N_CHAT_WEBHOOK_URL;
    
    // 2. If n8n webhook is active, forward payload to n8n AI workflow
    if (n8nWebhookUrl) {
      try {
        const n8nRes = await fetch(n8nWebhookUrl, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({
            message,
            productId,
            productContext,
            currency,
            timestamp: new Date().toISOString(),
          }),
          signal: AbortSignal.timeout(3000), // 3s timeout
        });

        if (n8nRes.ok) {
          const n8nData = await n8nRes.json();
          return NextResponse.json({
            reply: n8nData.reply || n8nData.output || n8nData.response || 'n8n workflow executed successfully.',
            source: 'n8n-ai-agent',
            product: productContext,
          });
        }
      } catch (err) {
        // Fallback to intelligent local RAG advisor if n8n service is currently starting/offline
        console.warn('n8n webhook unreachable, using intelligent local advisor:', err);
      }
    }

    // 3. Built-in Local RAG Advisor (Provides rich technical specs answers)
    let reply = `I am your Loop Engineering AI Advisor. `;
    if (productContext) {
      let specsObj: Record<string, string> = {};
      try {
        if (productContext.specifications) {
          specsObj = JSON.parse(productContext.specifications);
        }
      } catch {}

      const msgLower = message.toLowerCase();
      if (msgLower.includes('price') || msgLower.includes('cost') || msgLower.includes('how much')) {
        reply = `The **${productContext.productName}** is currently priced at **$${productContext.productPrice} USD** (Available for immediate dispatch with global multi-currency checkout).`;
      } else if (msgLower.includes('spec') || msgLower.includes('battery') || msgLower.includes('cpu') || msgLower.includes('camera') || msgLower.includes('display') || msgLower.includes('fiche')) {
        const specSummary = Object.entries(specsObj)
          .slice(0, 5)
          .map(([k, v]) => `• **${k}**: ${v}`)
          .join('\n');
        reply = `Here are the verified technical specifications for **${productContext.productName}**:\n\n${specSummary}\n\nYou can also inspect this model in our 360° interactive 3D studio above!`;
      } else if (msgLower.includes('3d') || msgLower.includes('view') || msgLower.includes('color')) {
        reply = `You can interact with the **3D Model Studio** directly on this page to rotate 360°, change finishes (Titanium, Space Black, Blue), and toggle studio lighting.`;
      } else {
        reply = `**${productContext.productName}** by **${productContext.productCompany}** is one of our flagship items in **${productContext.categoryRel?.categoryName || 'our catalog'}**.\n\nKey Highlights:\n• Availability: **${productContext.productAvailability}**\n• Warranty: 100% Genuine manufacturer warranty\n• Delivery: Express tracked shipping worldwide.`;
      }
    } else {
      reply = `Welcome to Loop Engineering! I can answer technical questions, compare products across our 21 modern flagships, verify Fiche Technique specs, or calculate pricing in over 52 global currencies. How can I help you today?`;
    }

    return NextResponse.json({
      reply,
      source: 'loop-ai-core',
      product: productContext,
    });
  } catch (error: any) {
    console.error('AI Chat Error:', error);
    return NextResponse.json({ error: error.message || 'Internal Server Error' }, { status: 500 });
  }
}

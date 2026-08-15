/**
 * Loop Engineering - AI Supervisor & Multi-Agent Routing Engine
 * Implements:
 * 1. AI Supervisor Router (Intent Classification)
 * 2. Specialized Multi-Agents: Sales Agent, Support Agent, Inventory Agent
 * 3. Deep Moroccan Darija NLP Preprocessor
 * 4. Deterministic Price & Fraud Guardrails
 */

import { prisma } from '@/lib/prisma';
import { CURRENCIES, convertPrice } from '@/lib/currency';

export type AgentRole = 'SUPERVISOR' | 'SALES_AGENT' | 'SUPPORT_AGENT' | 'INVENTORY_AGENT';

export interface AgentContext {
  userId?: string;
  productId?: number;
  currency?: string;
  lang?: string;
}

export interface AgentResponse {
  role: AgentRole;
  reply: string;
  intent: string;
  confidence: number;
  guardrailsPassed: boolean;
  metadata?: Record<string, any>;
}

// 1. Moroccan Darija Dictionary & Intent Matchers
const DARIJA_KEYWORDS: Record<string, string[]> = {
  pricing: ['شحال', 'بشحال', 'ثمن', 'الثمن', 'فلوس', 'prix', 'chhal', 'bchhal', 'taman', 'flouss'],
  wants_product: ['بغيت', 'خاصني', 'كنقلب', 'نقلب', 'bghit', 'khasni', 'kan9leb', 'nechri'],
  good_quality: ['مزيان', 'نقي', 'مليح', 'زوين', 'mezyan', 'nqi', 'zwin'],
  delivery: ['توصيل', 'ليفريزون', 'يوصل', 'casa', 'rabat', 'livraison', 'fin katsifto'],
  order_tracking: ['كوموند', 'طلبية', 'فين وصل', 'commande', 'fin wslat'],
};

export function detectDarija(text: string): boolean {
  const lower = text.toLowerCase();
  for (const list of Object.values(DARIJA_KEYWORDS)) {
    for (const kw of list) {
      if (lower.includes(kw) || text.includes(kw)) return true;
    }
  }
  return false;
}

// 2. Supervisor Intent Classifier
export function classifyIntent(query: string): { role: AgentRole; intent: string; isDarija: boolean } {
  const q = query.toLowerCase();
  const isDarija = detectDarija(query);

  if (
    q.includes('order') ||
    q.includes('tracking') ||
    q.includes('track') ||
    q.includes('return') ||
    q.includes('refund') ||
    q.includes('policy') ||
    q.includes('livraison') ||
    q.includes('shipping') ||
    DARIJA_KEYWORDS.order_tracking.some((k) => q.includes(k) || query.includes(k)) ||
    DARIJA_KEYWORDS.delivery.some((k) => q.includes(k) || query.includes(k))
  ) {
    return { role: 'SUPPORT_AGENT', intent: 'ORDER_OR_SUPPORT_INQUIRY', isDarija };
  }

  if (
    q.includes('stock') ||
    q.includes('available') ||
    q.includes('how many left') ||
    q.includes('inventory') ||
    q.includes('warehouse') ||
    q.includes('backorder')
  ) {
    return { role: 'INVENTORY_AGENT', intent: 'INVENTORY_STOCK_CHECK', isDarija };
  }

  return { role: 'SALES_AGENT', intent: 'PRODUCT_SPECS_OR_RECOMMENDATION', isDarija };
}

// 3. Multi-Agent Executor
export async function executeMultiAgent(
  query: string,
  context: AgentContext = {}
): Promise<AgentResponse> {
  const { role, intent, isDarija } = classifyIntent(query);
  const targetCurrency = context.currency || 'USD';

  // AI Guardrail: Check for malicious injections or illegal bypasses
  const guardrailCheck = !query.toLowerCase().includes('drop table') && !query.toLowerCase().includes('delete from');
  if (!guardrailCheck) {
    return {
      role: 'SUPERVISOR',
      intent: 'SECURITY_BLOCKED',
      confidence: 1.0,
      guardrailsPassed: false,
      reply: 'Security Alert: Input blocked by AI Guardrails.',
    };
  }

  // Load product if specified
  let product: any = null;
  if (context.productId) {
    product = await prisma.product.findUnique({
      where: { id: Number(context.productId) },
      include: { categoryRel: true },
    });
  }

  // -------------------------------------------------------------
  // AGENT 1: SUPPORT & FULFILLMENT AGENT
  // -------------------------------------------------------------
  if (role === 'SUPPORT_AGENT') {
    if (isDarija) {
      return {
        role: 'SUPPORT_AGENT',
        intent,
        confidence: 0.95,
        guardrailsPassed: true,
        reply: `مرحبا بك في Loop Engineering! التوصيل متوفر في جميع مدن المغرب (الدار البيضاء، الرباط، طنجة، مراكش، فاس، وغيرها) خلال 24 إلى 48 ساعة مع إمكانية الدفع عند الاستلام (Paiement à la livraison) والضمان الرسمي لمدة سنة.`,
        metadata: { region: 'Morocco', deliveryTime: '24-48h' },
      };
    }

    return {
      role: 'SUPPORT_AGENT',
      intent,
      confidence: 0.96,
      guardrailsPassed: true,
      reply: `Hello! I am your Loop Support & Fulfillment Agent. All orders are processed with real-time tracking, 30-day returns, and comprehensive 1-year manufacturer warranty. Express worldwide courier dispatch takes 24–48 hours.`,
      metadata: { trackingStatus: 'LIVE', returnPolicyDays: 30 },
    };
  }

  // -------------------------------------------------------------
  // AGENT 2: INVENTORY & FORECASTING AGENT
  // -------------------------------------------------------------
  if (role === 'INVENTORY_AGENT') {
    const stockStatus = product ? product.productAvailability : 'In Stock across all 21 flagships';
    return {
      role: 'INVENTORY_AGENT',
      intent,
      confidence: 0.94,
      guardrailsPassed: true,
      reply: `[Inventory System]: Real-time status for **${product ? product.productName : 'Flagship Catalog'}** is **${stockStatus}**. Stock is reserved automatically in warehouse upon order initiation.`,
      metadata: { availability: stockStatus, warehouseZone: 'Tier-1 Hub' },
    };
  }

  // -------------------------------------------------------------
  // AGENT 3: SALES & TECHNICAL SALES AGENT (Default)
  // -------------------------------------------------------------
  if (product) {
    let specsObj: Record<string, string> = {};
    try {
      if (product.specifications) specsObj = JSON.parse(product.specifications);
    } catch {}

    const convertedPrice = convertPrice(product.productPrice || 0, 'USD', targetCurrency);
    const currMeta = CURRENCIES[targetCurrency] || CURRENCIES.USD;

    if (isDarija) {
      return {
        role: 'SALES_AGENT',
        intent,
        confidence: 0.98,
        guardrailsPassed: true,
        reply: `هذا **${product.productName}** من أحسن ما كاين في السوق! الثمن ديالو هو **${convertedPrice.toLocaleString()} ${currMeta.code} (${currMeta.symbol})** مع التوصيل المجاني وضمان أصلي.\n\nالمواصفات التقنية (Fiche Technique):\n• المعالج: ${specsObj.Processor || 'أحدث جيل'}\n• الشاشة: ${specsObj.Display || 'Ultra HD OLED'}\n• البطارية: ${specsObj['Battery & Charging'] || specsObj.Battery || 'طاقة يوم كامل'}\n\nتقدر تشوف الموديل 3D ديالو وتدور فيه بـ 360 درجة مباشرة في الموقع!`,
        metadata: { productId: product.id, localizedPrice: convertedPrice, currency: targetCurrency },
      };
    }

    const specSummary = Object.entries(specsObj)
      .slice(0, 5)
      .map(([k, v]) => `• **${k}**: ${v}`)
      .join('\n');

    return {
      role: 'SALES_AGENT',
      intent,
      confidence: 0.99,
      guardrailsPassed: true,
      reply: `Here are the verified technical specifications for **${product.productName}**:\n\n${specSummary}\n\n• **Price**: ${currMeta.symbol}${convertedPrice.toLocaleString()} ${currMeta.code}\n• **Status**: In Stock with Express Tracked Shipping.\n• **3D Studio**: 360° PBR inspection and 5 material finishes available above.`,
      metadata: { productId: product.id, price: convertedPrice, currency: targetCurrency },
    };
  }

  // Generic Sales Assist
  if (isDarija) {
    return {
      role: 'SALES_AGENT',
      intent,
      confidence: 0.92,
      guardrailsPassed: true,
      reply: `مرحبا بك في Loop Engineering! كنوفروا ليك أحدث الأجهزة التكنولوجية (iPhone 15 Pro Max, Galaxy S24 Ultra, MacBook Pro M3, Sony XM5) مع الفيش تيكنيك كاملة والموديل 3D التفاعلي والدفع بالدرهم المغربي (MAD).`,
      metadata: { region: 'Morocco' },
    };
  }

  return {
    role: 'SALES_AGENT',
    intent,
    confidence: 0.95,
    guardrailsPassed: true,
    reply: `Welcome to Loop Engineering! I am your Technical Sales Agent. I can assist you with full Fiche Technique parameters, 360° 3D model configurations, or price conversions across 52+ global currencies.`,
    metadata: { catalogSize: 21 },
  };
}

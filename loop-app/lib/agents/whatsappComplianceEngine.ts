/**
 * ZeyTech - WhatsApp Business 24-Hour Window & Template Compliance Engine (Gap 14)
 * Enforces Meta customer-service window rules to prevent business phone bans:
 * - Within 24h window: Free-form session replies allowed.
 * - Outside 24h window: Enforces Meta-approved HSM Utility Templates.
 */

export interface WhatsAppMessagePayload {
  toPhoneNumber: string;
  messageText: string;
  lastCustomerInteractionTime?: string;
  templateName?: string;
  templateParams?: Record<string, string>;
  messageCategory: 'CUSTOMER_SUPPORT_REPLY' | 'ORDER_UPDATE' | 'REFUND_RECEIPT' | 'MARKETING';
}

export class WhatsAppComplianceEngine {
  /**
   * 1. Check if the interaction is within the 24-hour customer service window
   */
  public static isWithin24HourWindow(lastInteractionIso?: string): boolean {
    if (!lastInteractionIso) return false;
    const lastTime = new Date(lastInteractionIso).getTime();
    const now = Date.now();
    const diffHours = (now - lastTime) / (1000 * 60 * 60);
    return diffHours <= 24;
  }

  /**
   * 2. Format WhatsApp Dispatch Payload
   */
  public static formatCompliantPayload(payload: WhatsAppMessagePayload): {
    dispatchType: 'SESSION_MESSAGE' | 'APPROVED_TEMPLATE';
    payload: any;
    complianceNote: string;
  } {
    const isSessionOpen = this.isWithin24HourWindow(payload.lastCustomerInteractionTime);

    if (isSessionOpen && payload.messageCategory === 'CUSTOMER_SUPPORT_REPLY') {
      return {
        dispatchType: 'SESSION_MESSAGE',
        payload: {
          messaging_product: 'whatsapp',
          to: payload.toPhoneNumber,
          type: 'text',
          text: { body: payload.messageText },
        },
        complianceNote: 'Within active 24-hour customer service window: Free-form session text permitted.',
      };
    }

    // Outside 24h window or outbound notification -> Must use pre-approved Template
    const templateName = payload.templateName || (payload.messageCategory === 'REFUND_RECEIPT' ? 'zeytech_refund_receipt_v1' : 'zeytech_order_update_v1');

    return {
      dispatchType: 'APPROVED_TEMPLATE',
      payload: {
        messaging_product: 'whatsapp',
        to: payload.toPhoneNumber,
        type: 'template',
        template: {
          name: templateName,
          language: { code: 'ar' },
          components: [
            {
              type: 'body',
              parameters: Object.entries(payload.templateParams || {}).map(([_, value]) => ({
                type: 'text',
                text: value,
              })),
            },
          ],
        },
      },
      complianceNote: 'Outside 24-hour window: Meta-approved HSM Utility Template enforced to guarantee phone reputation.',
    };
  }
}

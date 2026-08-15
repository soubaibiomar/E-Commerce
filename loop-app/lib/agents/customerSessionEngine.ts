/**
 * ZeyTech - Cross-Channel Customer Session & Continuity Engine (Gap 5)
 * Standardizes session memory across Web, Telegram, and WhatsApp using customer phone/email.
 */

export interface CustomerSessionContext {
  customerIdentifier: string; // Unified phone e.g. "+212612345678" or email
  customerName?: string;
  preferredLanguage: 'darija' | 'english' | 'french' | 'arabic';
  currentCartItems: Array<{ productId: number; productName: string; quantity: number }>;
  lastViewedProductId?: number;
  recentQueries: string[];
  activeChannel: 'WEB' | 'TELEGRAM' | 'WHATSAPP';
  lastActiveTimestamp: string;
}

export class CustomerSessionEngine {
  private static sessions = new Map<string, CustomerSessionContext>();

  /**
   * 1. Resolve or Create Unified Session Key
   */
  public static resolveCustomerKey(rawIdentifier: string): string {
    const clean = rawIdentifier.trim().toLowerCase();
    if (clean.includes('@')) {
      return `customer:email:${clean}`;
    }
    // Clean phone numbers
    const digitsOnly = clean.replace(/\D/g, '');
    if (digitsOnly.length >= 8) {
      return `customer:phone:${digitsOnly.slice(-9)}`;
    }
    return `customer:anon:${clean}`;
  }

  /**
   * 2. Get or Initialize Session Context
   */
  public static getSession(identifier: string): CustomerSessionContext {
    const key = this.resolveCustomerKey(identifier);
    if (!this.sessions.has(key)) {
      this.sessions.set(key, {
        customerIdentifier: key,
        preferredLanguage: 'english',
        currentCartItems: [],
        recentQueries: [],
        activeChannel: 'WEB',
        lastActiveTimestamp: new Date().toISOString(),
      });
    }
    const session = this.sessions.get(key)!;
    session.lastActiveTimestamp = new Date().toISOString();
    return session;
  }

  /**
   * 3. Record Interaction & Cross-Channel Handoff
   */
  public static recordInteraction(identifier: string, channel: 'WEB' | 'TELEGRAM' | 'WHATSAPP', query: string, productId?: number): CustomerSessionContext {
    const session = this.getSession(identifier);
    session.activeChannel = channel;
    session.recentQueries.push(query);
    if (session.recentQueries.length > 10) session.recentQueries.shift();

    if (productId) {
      session.lastViewedProductId = productId;
    }

    // Detect language preference
    if (/[\u0600-\u06FF]/.test(query) || query.includes('شحال') || query.includes('درهم')) {
      session.preferredLanguage = 'darija';
    }

    return session;
  }

  /**
   * 4. Sync Cart State from Web to Telegram/WhatsApp
   */
  public static updateCart(identifier: string, items: Array<{ productId: number; productName: string; quantity: number }>): void {
    const session = this.getSession(identifier);
    session.currentCartItems = items;
  }
}

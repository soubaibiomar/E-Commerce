/**
 * Loop Engineering - Event-Driven Architecture (Event Bus)
 * Provides asynchronous event dispatching for:
 * - ORDER_CREATED
 * - INVENTORY_RESERVED
 * - AI_QUERY_LOGGED
 * - FRAUD_ALERT
 * - NOTIFICATION_DISPATCHED
 */

export type EventType =
  | 'DAILY_AI_REPORT_GENERATED'
  | 'SALE_COMPLETED_EVENT'
  | (string & {})
  | 'ORDER_CREATED'
  | 'INVENTORY_RESERVED'
  | 'AI_QUERY_LOGGED'
  | 'FRAUD_ALERT'
  | 'NOTIFICATION_DISPATCHED';

export interface LoopEvent<T = any> {
  id: string;
  type: EventType;
  timestamp: string;
  payload: T;
}

type EventHandler<T = any> = (event: LoopEvent<T>) => Promise<void> | void;

class EventBus {
  private handlers: Map<EventType, EventHandler[]> = new Map();
  private eventLog: LoopEvent[] = [];

  constructor() {
    this.initDefaultSubscribers();
  }

  public subscribe<T = any>(type: EventType, handler: EventHandler<T>): void {
    if (!this.handlers.has(type)) {
      this.handlers.set(type, []);
    }
    this.handlers.get(type)!.push(handler);
  }

  public async publish<T = any>(type: EventType, payload: T): Promise<LoopEvent<T>> {
    const event: LoopEvent<T> = {
      id: `evt_${Date.now()}_${Math.random().toString(36).substring(2, 7)}`,
      type,
      timestamp: new Date().toISOString(),
      payload,
    };

    this.eventLog.push(event);
    if (this.eventLog.length > 500) this.eventLog.shift(); // Keep bounded in memory

    const subs = this.handlers.get(type) || [];
    await Promise.allSettled(subs.map((handler) => handler(event)));

    return event;
  }

  public getRecentEvents(): LoopEvent[] {
    return [...this.eventLog].reverse();
  }

  private initDefaultSubscribers(): void {
    // Subscriber 1: Notification Dispatcher
    this.subscribe('ORDER_CREATED', async (evt) => {
      console.log(`[EventBus] Ã°Å¸â€œÂ© Notification Service: Dispatching order confirmation for Order #${evt.payload.orderId || evt.id}`);
    });

    // Subscriber 2: Inventory Reservation
    this.subscribe('ORDER_CREATED', async (evt) => {
      console.log(`[EventBus] Ã°Å¸â€œÂ¦ Inventory Service: Stock reserved in Tier-1 warehouse for product #${evt.payload.productId || 'N/A'}`);
    });

    // Subscriber 3: Fraud Engine
    this.subscribe('ORDER_CREATED', async (evt) => {
      const riskScore = evt.payload.riskScore || 5;
      if (riskScore > 75) {
        console.warn(`[EventBus] Ã°Å¸Å¡Â¨ Fraud Engine: High risk detected (${riskScore}/100) for event ${evt.id}`);
      }
    });

    // Subscriber 4: Analytics Logger
    this.subscribe('AI_QUERY_LOGGED', async (evt) => {
      console.log(`[EventBus] Ã°Å¸â€œÅ  Analytics Engine: Logged AI query via ${evt.payload.agentRole || 'Supervisor'}`);
    });
  }
}

// Global Singleton
const globalForBus = global as unknown as { eventBus: EventBus };
export const eventBus = globalForBus.eventBus || new EventBus();
if (process.env.NODE_ENV !== 'production') globalForBus.eventBus = eventBus;

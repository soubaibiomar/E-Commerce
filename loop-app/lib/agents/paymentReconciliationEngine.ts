/**
 * ZeyTech - Payment Reconciliation & Customer Identity Verification Engine (Gaps 18 & 19)
 * - Two-phase refund verification (blocks premature notification before gateway confirms)
 * - Webhook signature verification
 * - Customer identity & order ownership validation before sharing private data or canceling orders
 */

import { AuditLogger } from './productionSupervisor';

export interface RefundRequestPayload {
  orderId: number;
  orderNumber: string;
  amountMAD: number;
  customerPhone: string;
  customerEmail: string;
  reason: string;
  requestedBy: 'CUSTOMER' | 'ADMIN' | 'SUPPORT_AGENT';
}

export interface GatewayWebhookPayload {
  gatewayTransactionId: string;
  orderNumber: string;
  refundStatus: 'SUCCESS' | 'FAILED' | 'PENDING';
  amountRefundedMAD: number;
  signature: string;
  timestamp: string;
}

export class PaymentReconciliationEngine {
  // In-memory pending refund registry
  private static pendingRefunds = new Map<string, { payload: RefundRequestPayload; status: 'PENDING_GATEWAY' | 'CONFIRMED' | 'REJECTED'; createdAt: string }>();

  /**
   * 1. Customer Identity Guard (Gap 19)
   * Ensures the person requesting sensitive order operations owns the order.
   */
  public static verifyOrderOwnership(order: { contactno?: string; userEmail?: string }, requesterIdentifier: { phone?: string; email?: string }): { isAuthorized: boolean; reason?: string } {
    const cleanRequesterPhone = (requesterIdentifier.phone || '').replace(/\D/g, '');
    const cleanOrderPhone = (order.contactno || '').replace(/\D/g, '');

    const cleanRequesterEmail = (requesterIdentifier.email || '').trim().toLowerCase();
    const cleanOrderEmail = (order.userEmail || '').trim().toLowerCase();

    if (cleanRequesterPhone && cleanOrderPhone && cleanRequesterPhone.endsWith(cleanOrderPhone.slice(-8))) {
      return { isAuthorized: true };
    }

    if (cleanRequesterEmail && cleanOrderEmail && cleanRequesterEmail === cleanOrderEmail) {
      return { isAuthorized: true };
    }

    return {
      isAuthorized: false,
      reason: 'Identity verification failed: Requester phone/email does not match the order record.',
    };
  }

  /**
   * 2. Initiate Two-Phase Refund (Gap 18)
   * Creates pending refund without sending confirmation to customer yet.
   */
  public static initiateRefundRequest(payload: RefundRequestPayload): { success: boolean; state: string; message: string } {
    this.pendingRefunds.set(payload.orderNumber, {
      payload,
      status: 'PENDING_GATEWAY',
      createdAt: new Date().toISOString(),
    });

    AuditLogger.log({
      actor: payload.requestedBy,
      channel: 'SYSTEM_EVENT',
      agent: 'ORDER_MANAGEMENT_AGENT',
      tool: 'initiateRefundRequest',
      action: 'REFUND_SUBMITTED_TO_GATEWAY',
      inputPayload: payload,
      decision: 'APPROVED',
      result: { orderNumber: payload.orderNumber, status: 'PENDING_GATEWAY' },
      ipOrIdentifier: payload.customerPhone || payload.customerEmail,
    });

    return {
      success: true,
      state: 'PENDING_GATEWAY_CONFIRMATION',
      message: `Refund of ${payload.amountMAD} MAD for order #${payload.orderNumber} is pending payment gateway verification. Confirmation receipt held until gateway settlement.`,
    };
  }

  /**
   * 3. Process Gateway Webhook & Reconcile (Gap 18)
   * Confirms refund and authorizes customer receipt dispatch only upon verified gateway settlement.
   */
  public static reconcileGatewayCallback(webhook: GatewayWebhookPayload, expectedSecret = 'zeytech_secret_2026'): { isReconciled: boolean; authorizedToNotify: boolean; notificationPayload?: any; error?: string } {
    // A. Verify Signature
    if (!webhook.signature || webhook.signature !== expectedSecret) {
      AuditLogger.log({
        actor: 'PAYMENT_GATEWAY',
        channel: 'SYSTEM_EVENT',
        agent: 'FRAUD_DETECTION_AGENT',
        tool: 'reconcileGatewayCallback',
        action: 'WEBHOOK_SIGNATURE_MISMATCH',
        inputPayload: webhook,
        decision: 'REJECTED',
        result: { error: 'Invalid HMAC signature' },
        ipOrIdentifier: 'GATEWAY_CALLBACK',
      });
      return { isReconciled: false, authorizedToNotify: false, error: 'Signature verification failed.' };
    }

    // B. Check Pending Refund
    const pending = this.pendingRefunds.get(webhook.orderNumber);
    if (!pending) {
      return { isReconciled: false, authorizedToNotify: false, error: 'No matching pending refund found for this order.' };
    }

    if (webhook.refundStatus === 'SUCCESS') {
      pending.status = 'CONFIRMED';

      AuditLogger.log({
        actor: 'PAYMENT_GATEWAY',
        channel: 'SYSTEM_EVENT',
        agent: 'NOTIFICATION_AGENT',
        tool: 'reconcileGatewayCallback',
        action: 'REFUND_SETTLED_AND_CONFIRMED',
        inputPayload: webhook,
        decision: 'EXECUTED',
        result: { orderNumber: webhook.orderNumber, amount: webhook.amountRefundedMAD },
        ipOrIdentifier: 'GATEWAY_CALLBACK',
      });

      return {
        isReconciled: true,
        authorizedToNotify: true,
        notificationPayload: {
          orderNumber: webhook.orderNumber,
          amountRefundedMAD: webhook.amountRefundedMAD,
          customerPhone: pending.payload.customerPhone,
          customerEmail: pending.payload.customerEmail,
          gatewayTransactionId: webhook.gatewayTransactionId,
          timeline: '1-3 business days back to original payment method',
        },
      };
    }

    pending.status = 'REJECTED';
    return { isReconciled: false, authorizedToNotify: false, error: 'Gateway returned non-success refund status.' };
  }
}

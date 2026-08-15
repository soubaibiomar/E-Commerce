/**
 * Loop Engineering - Intelligent Fraud Detection Engine
 * Evaluates orders and transactions for anomalies, high velocity, and chargeback risks.
 */

export interface FraudEvaluation {
  riskScore: number; // 0 to 100
  riskLevel: 'LOW' | 'MEDIUM' | 'HIGH';
  flags: string[];
  recommendation: 'APPROVE' | 'FLAG_FOR_MANUAL_REVIEW' | 'REJECT';
}

export function evaluateOrderRisk(orderData: {
  totalAmount: number;
  currency?: string;
  shippingCity?: string;
  itemCount?: number;
  userOrderCount?: number;
}): FraudEvaluation {
  let score = 5;
  const flags: string[] = [];

  // 1. High single order volume check
  if (orderData.totalAmount > 500000) {
    score += 45;
    flags.push('Unusually large transaction value (> $5,000 USD)');
  } else if (orderData.totalAmount > 200000) {
    score += 15;
    flags.push('High transaction value');
  }

  // 2. High item count velocity
  if ((orderData.itemCount || 1) > 10) {
    score += 25;
    flags.push('High velocity bulk item count');
  }

  // 3. New account large initial purchase
  if ((orderData.userOrderCount || 0) === 0 && orderData.totalAmount > 100000) {
    score += 20;
    flags.push('First-time buyer with high initial basket value');
  }

  const riskScore = Math.min(100, Math.max(0, score));
  let riskLevel: 'LOW' | 'MEDIUM' | 'HIGH' = 'LOW';
  let recommendation: 'APPROVE' | 'FLAG_FOR_MANUAL_REVIEW' | 'REJECT' = 'APPROVE';

  if (riskScore >= 70) {
    riskLevel = 'HIGH';
    recommendation = 'REJECT';
  } else if (riskScore >= 35) {
    riskLevel = 'MEDIUM';
    recommendation = 'FLAG_FOR_MANUAL_REVIEW';
  }

  return {
    riskScore,
    riskLevel,
    flags,
    recommendation,
  };
}

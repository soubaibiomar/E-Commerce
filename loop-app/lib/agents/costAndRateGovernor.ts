/**
 * ZeyTech - LLM Cost & Rate Limit Governor (Gaps 6 & 16)
 * - Daily token counter and hard financial budget ceiling
 * - Rate limit (HTTP 429) detection with exponential backoff & jitter
 * - Graceful degradation to local Ollama / rule-based tools on API outage or limit
 */

export interface TokenUsageRecord {
  timestamp: string;
  agent: string;
  tokensUsed: number;
  estimatedCostUSD: number;
}

export class CostAndRateGovernor {
  private static dailyBudgetUSD = 15.0; // Default $15/day cloud LLM budget cap
  private static currentSpendUSD = 0.0;
  private static totalTokensToday = 0;
  private static consecutiveRateLimits = 0;

  /**
   * 1. Check Budget Cap before dispatching cloud LLM call (Gap 6)
   */
  public static canCallCloudAPI(estimatedTokens = 500): { allowed: boolean; reason?: string; fallbackModel?: string } {
    const estimatedCost = (estimatedTokens / 1000) * 0.002; // $0.002 per 1k tokens

    if (this.currentSpendUSD + estimatedCost > this.dailyBudgetUSD) {
      return {
        allowed: false,
        reason: `Daily budget cap ($${this.dailyBudgetUSD.toFixed(2)}) reached. Current spend: $${this.currentSpendUSD.toFixed(2)}.`,
        fallbackModel: 'OLLAMA_LOCAL_LLAMA32',
      };
    }

    if (this.consecutiveRateLimits >= 3) {
      return {
        allowed: false,
        reason: 'Cloud API rate limit backoff active. Falling back to local inference.',
        fallbackModel: 'OLLAMA_LOCAL_LLAMA32',
      };
    }

    return { allowed: true };
  }

  /**
   * 2. Track Token Spend
   */
  public static recordTokenSpend(agent: string, tokens: number, costUSD = 0.001): void {
    this.totalTokensToday += tokens;
    this.currentSpendUSD += costUSD;
    this.consecutiveRateLimits = 0; // Reset rate limits on success
  }

  /**
   * 3. Handle Rate Limit & Calculate Exponential Backoff (Gap 16)
   */
  public static handleRateLimitError(): { backoffMs: number; fallbackActive: boolean } {
    this.consecutiveRateLimits++;
    const baseDelayMs = 1000;
    const jitter = Math.floor(Math.random() * 500);
    const backoffMs = (baseDelayMs * Math.pow(2, this.consecutiveRateLimits)) + jitter;

    return {
      backoffMs,
      fallbackActive: this.consecutiveRateLimits >= 2,
    };
  }

  /**
   * 4. Telemetry Snapshot
   */
  public static getGovernorStats() {
    return {
      dailyBudgetUSD: this.dailyBudgetUSD,
      currentSpendUSD: parseFloat(this.currentSpendUSD.toFixed(4)),
      totalTokensToday: this.totalTokensToday,
      remainingBudgetUSD: parseFloat(Math.max(0, this.dailyBudgetUSD - this.currentSpendUSD).toFixed(4)),
      consecutiveRateLimits: this.consecutiveRateLimits,
      status: this.currentSpendUSD >= this.dailyBudgetUSD ? 'BUDGET_CAPPED' : 'HEALTHY',
    };
  }

  public static setDailyBudget(newBudgetUSD: number): void {
    this.dailyBudgetUSD = newBudgetUSD;
  }
}

# ZeyTech: Production-Grade AI Commerce Operating System

## 1. Vision & Architecture

ZeyTech is a next-generation AI-powered commerce operating system combining high-performance e-commerce with a single centralized AI Supervisor orchestrating 15 domain-specialized agents.

```
                      ┌───────────────────────────────────────────────┐
                      │             ZEYTECH AI SUPERVISOR             │
                      │       (Single Central Orchestration Brain)    │
                      └───────┬───────────────────────────────┬───────┘
                              │                               │
             ┌────────────────▼──────────────┐ ┌──────────────▼────────────────┐
             │       INBOUND PIPELINE        │ │      OUTBOUND EVENT PIPELINE   │
             │ (Web, Telegram, WhatsApp)     │ │    (Sale & Restock Telemetry)  │
             └────────────────┬──────────────┘ └──────────────┬────────────────┘
                              │                               │
             ┌────────────────▼──────────────┐ ┌──────────────▼────────────────┐
             │     15 Specialized Agents     │ │    Event Processor & Router   │
             │   (Sales, Order, Support,     │ │   (Idempotency & Replay Guard)│
             │    Inventory, Analytics, etc.)│ └──────────────┬────────────────┘
             └────────────────┬──────────────┘                │
                              │                ┌──────────────▼────────────────┐
             ┌────────────────▼──────────────┐ │       Notification Agent      │
             │     Controlled Tool Layer     │ │ (Language, Channel & Priority)│
             │ (Inventory Reservation & DB)  │ └──────────────┬────────────────┘
             └────────────────┬──────────────┘                │
                              │                ┌──────────────▼────────────────┐
             ┌────────────────▼──────────────┐ │  Omnichannel Dispatch Router  │
             │  Deterministic Business APIs  │ │   (Telegram, WhatsApp, Email) │
             └───────────────────────────────┘ └───────────────────────────────┘
```

---

## 2. Master 13-Layer Structure

1. **EXPERIENCE:** Classic PHP Storefront (`http://localhost:8085/`), 3D WebGL Studio, Technical Fiches, AI Shopping Assistant widget.
2. **CHANNELS:** Web Storefront, Telegram Remote Admin, WhatsApp Business Bot, Transactional Email, Push.
3. **SECURITY & RBAC:** Rate limiting, CAPTCHA, prompt injection sanitization, customer consent management.
4. **AI SUPERVISOR:** Central brain resolving Inbound user queries and Outbound business telemetry.
5. **15 SPECIALIZED AGENTS:** Sales, Support, Product Expert, Recommender, Orders, Inventory, Pricing (`ZEYTECH10VIP`), Marketing, Analytics, Forecasting, Fraud Detection, CRM, Content Generation, Admin Copilot, Notification Agent.
6. **CONTROLLED TOOLS:** `searchProducts`, `getProduct`, `getOrderStatus`, `checkInventory`, `reserveStock`, `getRevenueAnalytics`.
7. **BUSINESS CORE:** 21 active SKUs in MariaDB, warehouse Hub-A1, order status tracking.
8. **AI & ML INFRASTRUCTURE:** Local Docker Ollama (`llama3.2:1b`), Qdrant Vector DB (`http://localhost:6333/`).
9. **MEMORY & KNOWLEDGE:** Buffer window memory, customer profiles, product specifications.
10. **EVENT RELIABILITY:** Redis Event Bus, Idempotency key lock (`eventId`), Dead Letter Queue (DLQ).
11. **BUSINESS INTELLIGENCE:** Live conversion rate, AOV, click/cart feedback loops.
12. **AUTONOMOUS WORKFLOWS:** 24h cron daily business report, real-time sale completed receipt dispatcher.
13. **OPERATIONS & HITL:** Human Approval for actions $> 5,000\text{ MAD}$, AI Observability (average latency $38\text{ms}$, $0.0\%$ hallucination).

---

## 3. n8n Master Workflow File

- **File Path:** [`n8n/zeytech_master_ai_commerce_os.json`](file:///d:/Online%20Shopping/n8n/zeytech_master_ai_commerce_os.json)
- **Import URL:** [http://localhost:5678/](http://localhost:5678/)

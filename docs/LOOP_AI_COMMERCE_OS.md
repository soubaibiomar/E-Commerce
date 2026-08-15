# Loop Engineering — Complete AI Agent Architecture for E-Commerce

## 1. Vision
Loop Engineering is an **AI-powered commerce operating system**, uniting:
- Traditional e-commerce deterministic business operations
- Multi-agent AI with 14 specialized agents
- n8n orchestration hub
- Local LLMs with Ollama (`llama3.2:1b`)
- RAG and vector search with Qdrant (Port `6333`)
- Redis 7 Event Bus (Port `6379`)
- Customer intelligence & CRM 360
- Business analytics & autonomous reporting
- Inventory forecasting & predictive restocking
- Order & support automation
- AI security guardrails & Human-in-the-loop validation
- Multilingual and Moroccan Darija NLP

**Core Principle:**
> One AI Supervisor → Multiple Specialized Agents → Controlled Tools → APIs/Databases → Events → Autonomous Workflows

---

## 2. The 14 Specialized AI Agents

| # | Agent | Primary Responsibility | Controlled Tools |
|---|---|---|---|
| 1 | **Sales Agent** | Product discovery, comparison, sales assistance, margin-aware offers | `searchProducts`, `getProduct`, `compareProducts` |
| 2 | **Customer Support Agent** | FAQs, order assistance, returns, warranty, complaint escalation | `getFAQ`, `createTicket`, `escalateToHuman` |
| 3 | **Product Expert Agent** | Fiche Technique, hardware specifications, 3D WebGL model features | `getProduct`, `get3DModelMetadata`, `getFicheTechnique` |
| 4 | **Recommendation Agent** | Personalized, similar products, next-best-product, cold-start | `searchVectorEmbeddings`, `rerankProducts` |
| 5 | **Order Management Agent** | Order status tracking, cancellation validation, refund workflows | `getOrderStatus`, `cancelOrder`, `trackShipment` |
| 6 | **Inventory Agent** | Stock monitoring, low-stock detection, warehouse hub status | `checkInventory`, `reserveInventory`, `notifyRestock` |
| 7 | **Pricing & Promotion Agent** | Deterministic discounts, coupon validation, margin guardrails | `validateCoupon`, `calculateAllowedOffer` |
| 8 | **Marketing Agent** | Campaign generation, customer segmentation, omnichannel copy | `segmentCustomers`, `generateEmailCopy`, `sendWhatsApp` |
| 9 | **Analytics Agent** | Revenue analysis, conversion rate, AOV, cart abandonment | `getRevenueAnalytics`, `getKPIs`, `getConversionRate` |
| 10 | **Forecasting Agent** | Demand forecasting, seasonal analysis, 7-day stockout prediction | `forecastDemand`, `calculateSalesVelocity` |
| 11 | **Fraud Detection Agent** | Heuristic risk scoring, transaction risk, address anomaly checks | `evaluateFraudRisk`, `verifyIpAddress` |
| 12 | **CRM Agent** | Customer 360 profile, retention analysis, lifetime value | `getCustomerProfile`, `getCustomerOrders` |
| 13 | **Content Generation Agent** | SEO descriptions, technical summaries, multilingual localization | `generateProductDescription`, `localizeContent` |
| 14 | **Admin Copilot** | Executive conversational BI for operations and management | `getExecutiveSummary`, `dispatchBusinessAlert` |

---

## 3. Controlled Tool Router Architecture

```
Agent Request
     ↓
Tool Router
     ↓
Permission & RBAC Check
     ↓
Business Rule & Margin Validation
     ↓
Deterministic Database / API Execution (MariaDB / Qdrant / Redis)
```

---

## 4. Master n8n Autonomous Workflows

The ready-to-import master orchestration file is located at:
[`n8n/zeytech_master_ai_commerce_os.json`](file:///d:/Online%20Shopping/n8n/zeytech_master_ai_commerce_os.json)

### Workflows Included:
1. **AI Supervisor / Intent Router**
2. **Controlled Tool Invocation (MySQL Products & Orders)**
3. **Autonomous Daily AI Business Report** (Runs on a 24h cron, aggregates orders and risk scores, and generates executive recommendations)
4. **Predictive Restocking & Low-Stock Alerts**
5. **Abandoned Cart WhatsApp & Email Recovery**

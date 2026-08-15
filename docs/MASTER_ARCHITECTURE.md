# The Master Architecture: Loop Engineering

## Executive Overview
The **Loop Engineering Platform** is an enterprise-grade, event-driven, multi-agent AI eCommerce ecosystem designed for ultra-fast, modern commerce with photorealistic 3D WebGL inspection, sub-second hybrid search, and local AI reasoning.

---

## 1. System Architecture Diagram

```mermaid
graph TD
    %% Define Styles
    classDef frontend fill:#3b82f6,stroke:#1e3a8a,stroke-width:2px,color:#fff
    classDef gateway fill:#6366f1,stroke:#4338ca,stroke-width:2px,color:#fff
    classDef core fill:#10b981,stroke:#047857,stroke-width:2px,color:#fff
    classDef aiengine fill:#f59e0b,stroke:#b45309,stroke-width:2px,color:#fff
    classDef database fill:#8b5cf6,stroke:#5b21b6,stroke-width:2px,color:#fff
    classDef eventbus fill:#ec4899,stroke:#be185d,stroke-width:2px,color:#fff
    classDef analytics fill:#14b8a6,stroke:#0f766e,stroke-width:2px,color:#fff

    %% Frontend
    subgraph Client [Next.js Client Applications - Port 3000]
        UI_Store[Store / 3D Studio / Chat]:::frontend
        UI_Admin[Admin / AI Generator]:::frontend
    end

    %% Gateway
    Gateway[Next.js App Router API Gateway]:::gateway
    Client <--> Gateway

    %% Core E-Commerce
    subgraph ECommerceCore [E-Commerce Core & Services]
        API_Orders[Orders API]:::core
        API_Inventory[Inventory API]:::core
        API_Payments[Payments & 52 Currencies]:::core
        API_RBAC[Auth & RBAC]:::core
    end
    Gateway <--> ECommerceCore

    %% AI Gateway
    subgraph AILayer [AI Gateway & Multi-Agent System]
        AIGuard[AI Guardrails\n& Fraud Detection]:::aiengine
        AIRouter[AI Supervisor Router]:::aiengine
        
        AgentSales[Sales Agent\nPromotions & Hybrid Search]:::aiengine
        AgentSupport[Support Agent\nOrder Tracking & FAQs]:::aiengine
        AgentInv[Inventory Agent\nForecasting]:::aiengine
        AgentDarija[Moroccan Darija\nNLP Engine]:::aiengine
    end
    Gateway <--> AIGuard
    AIGuard <--> AIRouter
    AIRouter --> AgentSales
    AIRouter --> AgentSupport
    AIRouter --> AgentInv
    AIRouter --> AgentDarija

    %% Databases
    subgraph Data [Data Layer]
        MySQL[(MariaDB Core DB - Port 3308)]:::database
        OllamaLocal[(Docker Ollama LLM - Port 11434)]:::database
    end
    API_Orders <--> MySQL
    API_Inventory <--> MySQL
    API_Payments <--> MySQL
    AgentSales <--> MySQL
    AgentSupport <--> MySQL
    AgentInv <--> MySQL
    AIRouter <--> OllamaLocal

    %% Event Bus
    EventBus{{Event Bus\nAsync Microservice Dispatcher}}:::eventbus
    MySQL -.->|CDC / Hooks| EventBus
    ECommerceCore -.->|Publishes| EventBus

    %% Microservices
    subgraph EventServices [Event-Driven Microservices]
        SvcAnalytics[Business Analytics Engine]:::analytics
        SvcForecast[Inventory Forecasting]:::analytics
        SvcNotify[Notification Center]:::analytics
        SvcPersonal[Personalization Engine]:::analytics
    end
    EventBus --> EventServices
```

---

## 2. The 16 Pillars & Implementation Map

| Pillar | Implementation | Key Files |
| :--- | :--- | :--- |
| **1. Multi-Agent AI System** | AI Supervisor routes to Sales, Support, Inventory | [`loop-app/lib/agents/supervisor.ts`](file:///d:/Online%20Shopping/loop-app/lib/agents/supervisor.ts) |
| **2. Event-Driven Architecture** | Async Event Bus (`ORDER_CREATED`, `AI_QUERY_LOGGED`) | [`loop-app/lib/events/eventBus.ts`](file:///d:/Online%20Shopping/loop-app/lib/events/eventBus.ts) |
| **3. Personalization & Analytics** | Event subscribers log AI query metrics & interest | [`loop-app/lib/events/eventBus.ts`](file:///d:/Online%20Shopping/loop-app/lib/events/eventBus.ts) |
| **4. Hybrid Search** | Keyword + Specs + Category Scoring Engine | [`loop-app/app/api/search/hybrid/route.ts`](file:///d:/Online%20Shopping/loop-app/app/api/search/hybrid/route.ts) |
| **5. Multimodal Search** | 3D WebGL mesh viewer & visual attributes | [`loop-app/components/Product3DStudio.tsx`](file:///d:/Online%20Shopping/loop-app/components/Product3DStudio.tsx) |
| **6. Deterministic Pricing** | Mathematical conversions across 52 currencies | [`loop-app/lib/currency.ts`](file:///d:/Online%20Shopping/loop-app/lib/currency.ts) |
| **7. Moroccan Localization** | Native Darija NLP (`"بغيت شي تلفاز مزيان"`), MAD pricing | [`loop-app/lib/agents/supervisor.ts`](file:///d:/Online%20Shopping/loop-app/lib/agents/supervisor.ts) |
| **8. Enterprise Core & ORM** | Prisma ORM mapping to MariaDB (`localhost:3308`) | [`loop-app/prisma/schema.prisma`](file:///d:/Online%20Shopping/loop-app/prisma/schema.prisma) |
| **9. 3D PBR WebGL Studio** | Three.js orbit, zoom, 5 material finishes, lighting | [`loop-app/components/Product3DStudio.tsx`](file:///d:/Online%20Shopping/loop-app/components/Product3DStudio.tsx) |
| **10. Local Privacy AI (Ollama)**| Docker Ollama container (`llama3.2:1b`, `qwen3:8b`) | [`loop-app/app/api/chat/route.ts`](file:///d:/Online%20Shopping/loop-app/app/api/chat/route.ts) |
| **11. Visual Automation (n8n)** | n8n Webhook Proxy & workflow JSON configs | [`d:/Online Shopping/n8n/`](file:///d:/Online%20Shopping/n8n/) |
| **12. Fiche Technique Engine** | Verified structured hardware parameters table | [`loop-app/components/FicheTechniqueTable.tsx`](file:///d:/Online%20Shopping/loop-app/components/FicheTechniqueTable.tsx) |
| **13. AI Spec Auto-Generator** | Admin AI Suite for instant specification generation | [`loop-app/app/admin/ai-generator/page.tsx`](file:///d:/Online%20Shopping/loop-app/app/admin/ai-generator/page.tsx) |
| **14. SEO & GEO Optimization** | JSON-LD schema (Product, Offer, Org) + Answer-First | [`shopping/includes/seo.php`](file:///d:/Online%20Shopping/shopping/includes/seo.php) |
| **15. Dual-Stack Co-existence** | Next.js on port 3000 & PHP/Docker on port 8085 | Seamlessly sharing same database |
| **16. Security & Guardrails** | SQL injection prevention and input sanitization | Built into Supervisor & Prisma |

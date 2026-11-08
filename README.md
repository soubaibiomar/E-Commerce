# ZeyTech AI Commerce OS

A local commerce automation prototype that connects a PHP storefront to catalog-aware chat, order assistance, and a merchant operations console. It explores how a retailer can route requests through n8n while keeping inventory and approval decisions in the application.

Omar Soubaibi’s repository history records the [ZeyTech implementation](https://github.com/soubaibiomar/E-Commerce/commits/main/) layered on the existing shopping code. See the [license](LICENSE) and component notices for redistribution terms.

## Architecture

- [Shopping application](shopping/) serves the storefront, catalog and order endpoints, webhook handlers, and merchant interfaces.
- [MariaDB schema and demo data](sql/) support catalog and inventory operations.
- The [n8n workflow](n8n/zeytech_master_ai_commerce_os.json) defines routing and escalation. The [Next.js app](loop-app/) is a separate commerce interface.
- The [architecture notes](docs/MASTER_ARCHITECTURE.md) describe the broader design. Treat provider integrations and capacity claims as design targets until tested in the intended environment.

## Run locally

With Docker and Compose, copy `.env.example` to `.env` and replace development defaults with unique local values. Review the published port bindings in [Compose](docker-compose.yml) before starting. Run the stack only on an isolated development machine. From the repository root, run:

```sh
docker compose config --quiet
docker compose up -d --build
docker compose ps
```

Open the [local storefront](http://localhost:8085/index.php). The workflow and external providers require their own configuration. Use only synthetic catalog and customer data.

## Demo and verification

[Storefront screenshot](docs/screenshots/01_storefront_homepage.png) · [Merchant console screenshot](docs/screenshots/06_merchant_operations_console.png)

The [catalog smoke script](scratch/verify_seeded_catalog.js) and [system verification script](scratch/test_deep_suite.js) expect a running, seeded local stack. Read the scripts before running them against an isolated database. From the repository root, run `node scratch/verify_seeded_catalog.js` for the catalog check. To check the separate Next.js interface, enter `loop-app/`, install with `npm ci`, generate the client with `npm run prisma:generate`, and run `npm run build`.

The screenshots show demo data. There is no hosted demo or independently verified production benchmark documented here.

Do not expose the stack with demo accounts or use it for live payments, shipments, or customer data without reviewing authentication, secrets, network exposure, provider behavior, and recovery.

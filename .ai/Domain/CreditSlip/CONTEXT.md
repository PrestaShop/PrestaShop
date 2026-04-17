# CreditSlip Domain

## Purpose

Manages credit slips (return slips) issued to customers after order returns or refunds. Does NOT handle order creation, payment processing, or refund calculation — those belong to the Order domain.

## Layers

| Layer | Path |
|-------|------|
| Core CQRS | `src/Core/Domain/CreditSlip/` — Queries, QueryHandlers, ValueObjects, Exceptions |
| Adapter | `src/Adapter/CreditSlip/` — query handler implementations, `CreditSlipTemplateTypeProvider` |
| Back-office UI | `src/PrestaShopBundle/Controller/Admin/Sell/Order/CreditSlipController.php`, `src/Core/Grid/Definition/Factory/CreditSlipGridDefinitionFactory.php`, `src/PrestaShopBundle/Form/Admin/Sell/Order/CreditSlip/` |

## Non-obvious patterns

- This domain is query-only at the CQRS layer — no Command subdirectory exists. Credit slip creation is triggered from the Order domain, not directly.
- `CreditSlipTemplateTypeProvider` in the adapter provides PDF template types for slip generation.
- The domain has a form for global credit slip options (`CreditSlipOptionsType`) — these are shop-level settings, not per-slip commands.

## Canonical examples

- `src/Core/Domain/CreditSlip/Query/GetCreditSlipIdsByDateRange.php` + `src/Adapter/CreditSlip/QueryHandler/GetCreditSlipIdsByDateRangeHandler.php`

## Related

- [CQRS Component](../../Component/CQRS/CONTEXT.md)
- [Grid Component](../../Component/Grid/CONTEXT.md)

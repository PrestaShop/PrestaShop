# Notification Domain

## Purpose

Provides the back-office notification bell: fetches the latest unread counts and items for orders, customers, and customer messages per employee, and tracks the last-seen element per type. Does not handle email notifications or front-office messaging.

## Layers

| Layer | Path |
|-------|------|
| Core CQRS | `src/Core/Domain/Notification/` — Commands, Queries, QueryResults, ValueObjects, Exceptions |
| Adapter | `src/Adapter/Notification/` — handler implementations (no repository; delegates directly to legacy ObjectModel) |
| Legacy ObjectModel | `classes/Notification.php` (216 lines) — do not add logic here |

## Non-obvious patterns

- The adapter query handler calls `(new Notification())->getActiveLastElements()` directly on the legacy ObjectModel — there is no repository abstraction.
- `ValueObject/Type.php` enumerates exactly three notification types: `order`, `customer`, `customer_message`. Any new notification category requires adding a type here.
- There is no dedicated back-office controller; notification data is consumed by a front-end API endpoint.

## Canonical examples

- `src/Core/Domain/Notification/Query/GetNotificationLastElements.php` + `src/Adapter/Notification/QueryHandler/GetNotificationLastElementsHandler.php`

## Related

- [CQRS Component](../../Component/CQRS/CONTEXT.md)

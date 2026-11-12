# CustomerMessage Domain

## Purpose

Handles sending messages from the back office to customers in the context of an order (via `AddOrderCustomerMessageCommand`). Does NOT manage the customer-service thread lifecycle — that belongs to the CustomerService domain.

## Layers

| Layer | Path |
|-------|------|
| Core CQRS | `src/Core/Domain/CustomerMessage/` — Commands, handler interfaces, Exceptions |
| Adapter | `src/Adapter/CustomerMessage/CommandHandler/` — its service is declared with the CustomerService ones in `services/adapter/customer_service.yml` |
| Legacy ObjectModel | `classes/CustomerMessage.php` (177 lines) — do not add logic here |

## Non-obvious patterns

- This domain is command-only at the CQRS layer — there are no Query or QueryResult subdirectories. Reading messages is done through the CustomerService domain or legacy controllers.
- `CannotSendEmailException` signals that the message was persisted but the email delivery failed — callers must handle this non-fatal case separately from hard persistence failures.
- The single command is `AddOrderCustomerMessageCommand`, meaning every message is scoped to an order; there is no generic "send message" without an order context.

## Canonical examples

- `src/Core/Domain/CustomerMessage/Command/AddOrderCustomerMessageCommand.php` + `src/Adapter/CustomerMessage/CommandHandler/AddOrderCustomerMessageHandler.php`

## Related

- [CQRS Component](../../Component/CQRS/CONTEXT.md)
- [CustomerService Domain](../CustomerService/CONTEXT.md)

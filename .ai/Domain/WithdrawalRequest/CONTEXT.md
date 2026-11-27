# WithdrawalRequest Domain

## Purpose

Implements the EU withdrawal function (Directive 2011/83/EU, article 11a, added by Directive (EU) 2023/2673, applicable since June 19, 2026): a consumer withdraws from an order online with a two-step "Withdraw from contract here" / "Confirm withdrawal" flow, receives an acknowledgement of receipt by email, and the merchant follows up the requests in the back office. It does NOT handle the return of the goods (OrderReturn domain) nor the refund (Order domain).

## Layers

| Layer | Path |
|-------|------|
| Core CQRS | `src/Core/Domain/WithdrawalRequest/` |
| Adapter | `src/Adapter/WithdrawalRequest/` |
| Doctrine entity | `src/PrestaShopBundle/Entity/WithdrawalRequest.php`, `Entity/Repository/WithdrawalRequestRepository.php` |
| Front office | `controllers/front/WithdrawalController.php` (templates live in the theme repositories) |
| Back office | `src/PrestaShopBundle/Controller/Admin/Sell/CustomerService/WithdrawalRequestController.php`, grid `src/Core/Grid/*/WithdrawalRequest*` |
| Services | `services/withdrawal_request/common.yml` (shared with the legacy front office container), `services/adapter/withdrawal_request.yml` (handlers) |

## Non-obvious patterns

- The front office page cannot dispatch commands (the legacy front office container has no command bus): it calls `WithdrawalEligibilityChecker` and `WithdrawalRequestSubmitter` directly, and `SubmitWithdrawalRequestHandler` delegates to the same submitter so both paths behave the same. Anything those services depend on must exist in the legacy container, hence the legacy `Hook::exec` in the submitter.
- Eligibility is computed, never stored: the deadline starts from the first `order_history` row in the `PS_OS_DELIVERED` state, and there is no deadline before delivery. The `delivery` flag of order states is NOT usable: "Processing in progress" and "Shipped" carry it too. `WithdrawalDeadlineCalculator` applies Regulation 1182/71 (day of delivery not counted, end of last day, weekend moved to Monday).
- `orders.delivery_date` is NOT the delivery date (it is rewritten on every state flagged `delivery`), do not use it.
- The request copies the name, email and order reference the consumer declared: it is a legal record, it must not follow later edits of the customer or the order.
- One request per order, enforced by a unique key on `id_order`.
- Withdrawal is a right: the merchant statuses (`pending`, `processed`, `refused`) only track the follow-up, they never gate the withdrawal itself.

## Canonical examples

- `src/Adapter/WithdrawalRequest/WithdrawalEligibilityChecker.php` — single source of the eligibility rules
- `src/Adapter/WithdrawalRequest/WithdrawalRequestSubmitter.php` — statement recording, acknowledgement email, hook

## Related

- [CQRS Component](../../Component/CQRS/CONTEXT.md)
- [OrderReturn Domain](../OrderReturn/CONTEXT.md)
- [CONTAINERS.md](../../CONTAINERS.md)

# Security Domain

## Purpose

Manages customer and employee session lifecycle (deletion, bulk deletion, clearing outdated sessions). It does NOT handle authentication, password management, or permission checks — those live in `src/Core/Security/` (non-domain) and the Profile domain.

## Layers

| Layer | Path |
|-------|------|
| Core CQRS | `src/Core/Domain/Security/` — Commands, ValueObjects (`CustomerSessionId`, `EmployeeSessionId`), Exceptions |
| Adapter | `src/Adapter/Security/` — command handlers, `Access.php` (permission bridge to legacy) |
| Back-office UI | `src/PrestaShopBundle/Controller/Admin/Configure/AdvancedParameters/SecurityController.php` |

## Non-obvious patterns

- `src/Adapter/Security/Access.php` is NOT a command handler — it implements `AccessCheckerInterface` and bridges calls to the legacy `Access` class for employee permission checks. It is a standalone service, not part of the CQRS flow.
- Session value objects (`CustomerSessionId`, `EmployeeSessionId`) are the aggregate identifiers; sessions are deleted via commands with no corresponding Query — session state is not read through CQRS in this domain.
- There is a second `SecurityController` at `src/PrestaShopBundle/Controller/Admin/SecurityController.php` (likely a legacy redirect shim); the canonical one is under `Configure/AdvancedParameters/`.

## Canonical examples

- `src/Core/Domain/Security/Command/DeleteCustomerSessionCommand.php` + `src/Adapter/Security/CommandHandler/DeleteCustomerSessionHandler.php`

## Related

- [CQRS Component](../../Component/CQRS/CONTEXT.md)
- [Profile Domain](../Profile/CONTEXT.md) — profile-level permission management
- `tests/Integration/Behaviour/Features/Scenario/Security/` — Behat behavior scenarios

# Contact Domain

## Purpose

Manages store contact entries (customer service addresses shown at checkout and in emails) including creation and editing. Does not handle the contact form submission flow — that is a front-office concern.

## Layers

| Layer | Path |
|-------|------|
| Core CQRS | `src/Core/Domain/Contact/` — Commands, Queries, QueryResults, ValueObjects, Exceptions |
| Adapter | `src/Adapter/Contact/` — handler implementations |
| Legacy ObjectModel | `classes/Contact.php` (105 lines) — do not add logic here |
| Back-office UI | `src/PrestaShopBundle/Controller/Admin/Configure/ShopParameters/ContactsController.php`, `src/Core/Grid/Definition/Factory/ContactGridDefinitionFactory.php` |

## Non-obvious patterns

- The back-office controller is named `ContactsController` (plural), while the domain is `Contact` (singular).

## Canonical examples

- `src/Core/Domain/Contact/Command/AddContactCommand.php` + `src/Adapter/Contact/CommandHandler/AddContactHandler.php`

## Related

- [CQRS Component](../../Component/CQRS/CONTEXT.md)
- [Forms Component](../../Component/Forms/CONTEXT.md)
- [Grid Component](../../Component/Grid/CONTEXT.md)
- `tests/Integration/Behaviour/Features/Scenario/Contact/` — Behat behavior scenarios

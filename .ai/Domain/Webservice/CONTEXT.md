# Webservice Domain

## Purpose

Manages REST API access keys (Webservice keys) and their per-resource HTTP-method permissions. Does NOT implement the REST API itself — that lives in the legacy `WebserviceRequest` layer outside this domain.

## Layers

| Layer | Path |
|-------|------|
| Core CQRS | `src/Core/Domain/Webservice/` — Commands, Queries, QueryResults, ValueObjects, Exceptions |
| Adapter | `src/Adapter/Webservice/` — handler implementations, `WebserviceConfiguration`, `WebserviceKeyEraser`, `WebserviceKeyStatusModifier` |
| Back-office UI | `src/PrestaShopBundle/Controller/Admin/Configure/AdvancedParameters/WebserviceController.php`, `src/Core/Grid/Definition/Factory/WebserviceKeyDefinitionFactory.php` |

## Non-obvious patterns

- The `Key` value object enforces a fixed 32-character alphanumeric length at construction time — key format validation is domain-level, not form-level.
- `Permission` value object maps named constants to HTTP verbs (`GET`, `HEAD`, `PUT`, `PATCH`, `POST`, `DELETE`) — permissions are stored and compared as HTTP method strings, not bitmasks.
- Adapter contains standalone helpers (`WebserviceKeyEraser`, `WebserviceKeyStatusModifier`) outside the standard `CommandHandler/` sub-folder, used for batch operations.
- There is no toggle-status command — enable/disable is embedded in `EditWebserviceKeyCommand`.

## Canonical examples

- `src/Core/Domain/Webservice/Command/AddWebserviceKeyCommand.php` + `src/Adapter/Webservice/CommandHandler/AddWebserviceKeyHandler.php`

## Related

- [CQRS Component](../../Component/CQRS/CONTEXT.md)
- [Forms Component](../../Component/Forms/CONTEXT.md)
- [Grid Component](../../Component/Grid/CONTEXT.md)
- `tests/Integration/Behaviour/Features/Scenario/Webservice/` — Behat behavior scenarios

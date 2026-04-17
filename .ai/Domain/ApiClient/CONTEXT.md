# ApiClient Domain

## Purpose

Manages OAuth API clients used to authenticate against the PrestaShop Admin API. Does not handle authentication flows or token issuance — those are handled by the API Platform / League OAuth2 layer.

## Layers

| Layer | Path |
|-------|------|
| Core CQRS | `src/Core/Domain/ApiClient/` — Commands, Queries, QueryResults, ValueObjects, Exceptions, Settings |
| Adapter | `src/Adapter/ApiClient/` — handler implementations |
| Doctrine Entity | `src/PrestaShopBundle/Entity/ApiClient.php` — no legacy ObjectModel exists for this domain |
| API Platform resource | `src/PrestaShopBundle/ApiPlatform/Resources/ApiClient.php` — exposes `/api-clients/infos` via CQRS |
| Back-office UI | `src/PrestaShopBundle/Controller/Admin/Configure/AdvancedParameters/AdminAPIController.php` — accessible via Advanced Parameters > Admin API. Contains both API client CRUD pages and API configuration form (confusing naming) |

## Non-obvious patterns

- This domain has **no legacy ObjectModel** (`classes/ApiClient.php` does not exist). The persistence layer uses a Doctrine entity exclusively — this is the modern pattern to follow.
- There is a `GenerateApiClientSecretCommand` — secret regeneration is a separate command, not part of edit.
- `src/Core/Domain/ApiClient/ApiClientSettings.php` defines field length constants; reference it before adding new string fields.
- The API Platform resource uses a custom `CQRSGet` metadata attribute that maps `_context[apiClientId]` from the request context into the query — not a standard REST pattern.

## Canonical examples

- `src/Core/Domain/ApiClient/Command/AddApiClientCommand.php` + `src/Adapter/ApiClient/CommandHandler/AddApiClientCommandHandler.php`

## Related

- [CQRS Component](../../Component/CQRS/CONTEXT.md)
- [Forms Component](../../Component/Forms/CONTEXT.md)
- `tests/Integration/Behaviour/Features/Scenario/ApiClient/` — Behat behavior scenarios

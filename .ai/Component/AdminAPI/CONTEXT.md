# AdminAPI Component

## Purpose

The Admin API exposes PrestaShop's back-office over REST, built on API Platform 3 with OAuth2 authorization (server inside the bundle, JWT-based) and per-route scopes. Endpoints don't define their own business logic — they delegate to the existing CQRS layer. Most resource definitions ship in a separate module, `ps_apiresources`, rather than in core.

For endpoint authoring conventions (URI shape, scopes, mappings, do/don't), the authoritative source is the module's own context: [`modules/ps_apiresources/CONTEXT.md`](../../../modules/ps_apiresources/CONTEXT.md). This file is intentionally light on those details and focuses on what's specific to the **core** side of the Admin API.

## Layers

| Layer | Path |
|-------|------|
| API Platform integration | `src/PrestaShopBundle/ApiPlatform/` (Provider/, Processor/, Normalizer/, Serializer/, Validator/, OpenApi/, Scopes/, Metadata/, Pagination/, Encoder/) |
| Core-side API resources (always-available, even without ps_apiresources) | `src/PrestaShopBundle/ApiPlatform/Resources/` (currently `ApiClient.php`, `Language.php`) |
| Legacy API controllers (pre-API-Platform) | `src/PrestaShopBundle/Controller/Api/` |
| OAuth2 authorization server | `src/PrestaShopBundle/Security/OAuth2/` (Repository/, Provider/, GrantType/, Entity/) |
| OAuth2 core interfaces + JWT | `src/Core/Security/OAuth2/` |
| ApiClient domain (CQRS) | `src/Core/Domain/ApiClient/` (Command/, Query/, Exception/, ValueObject/, QueryResult/) |
| API routing (Symfony) | `src/PrestaShopBundle/Resources/config/routing/api.yml`, `admin-api.yml`, `routing/api/{domain}.yml` |
| Service wiring | `src/PrestaShopBundle/Resources/config/services/bundle/api_platform.yml`, `oauth.yml`, `open_api.yml` |
| Module-provided API resources | `modules/ps_apiresources/src/ApiPlatform/Resources/` |
| Integration tests (core) | `tests/Integration/ApiPlatform/`, `tests/Integration/PrestaShopBundle/ApiPlatform/`, `tests/Integration/PrestaShopBundle/Controller/Api/`, `tests/Resources/ApiPlatform/Resources/` |
| Integration tests (module) | `modules/ps_apiresources/tests/Integration/` (uses `phpunit-local.xml` / `phpunit-ci.xml`) |

## Non-obvious patterns

- API Platform resources can live OUTSIDE core, inside installed modules — `ps_apiresources` is the canonical example. The scopes extractor scans installed modules to discover the available scope tree.
- A handful of resources stay in **core** (`src/PrestaShopBundle/ApiPlatform/Resources/`) for endpoints that must work even when `ps_apiresources` is uninstalled — typically when the core itself depends on them. Current examples: `Language` (`/languages` — every other endpoint relies on the lang ID↔ISO mapping), `ApiClient` (`/api-clients/infos` — lets a token introspect itself; declares `scopes: []` and reads identity from `[_context][apiClientId]`).
- Endpoints reuse CQRS by indirection: `QueryProvider` / `QueryListProvider` dispatch read operations through the query bus, `CommandProcessor` dispatches write operations through the command bus. There are no API-specific business handlers — all logic comes from the existing CQRS layer.
- `ExperimentalOperationsMetadataCollectionFactoryDecorator` filters operations marked experimental when not in debug mode, so half-baked endpoints don't leak into production OpenAPI docs.
- `CQRSNotFoundMetadataCollectionFactoryDecorator` filters out resources whose backing CQRS messages don't exist on the current branch — this matters when a module ships a resource for a domain class that's been renamed or removed.
- Normalizer chain handles PrestaShop value objects: `CQRSApiNormalizer` (priority -1, runs last), `ValueObjectNormalizer`, `DecimalNumberNormalizer`, `ShopConstraintNormalizer`, `DateTimeImmutableNormalizer`, `UploadedFileNormalizer`, `LocalizedValueUpdater` for multilingual fields.
- File uploads use a custom `MultipartDecoder` in `src/PrestaShopBundle/ApiPlatform/Encoder/`.
- Position updates use a dedicated `UpdatePositionProcessor` (`api_platform.state_processor`) backed by `PositionCollectionUpdater`.
- OAuth2 supports a custom `client_credentials` grant via `CustomClientCredentialsGrant`. Clients are persisted via the `ApiClient` domain (CRUD lives in the back-office UI).
- Scopes are derived from API Resource metadata, cached via `CachedApiResourceScopesExtractor`.

## Canonical examples

- `src/PrestaShopBundle/ApiPlatform/Provider/QueryProvider.php` — query dispatch via the query bus
- `src/PrestaShopBundle/ApiPlatform/Processor/CommandProcessor.php` — command dispatch via the command bus
- `src/PrestaShopBundle/ApiPlatform/Normalizer/CQRSApiNormalizer.php` — main normalizer (priority -1, runs last)
- `src/PrestaShopBundle/Security/OAuth2/PrestashopAuthorisationServer.php` — OAuth2 server
- `modules/ps_apiresources/src/ApiPlatform/Resources/` — example resource definitions

## Conventions

- **Where new resources live** — default to `modules/ps_apiresources/src/ApiPlatform/Resources/`. Only put a resource in `src/PrestaShopBundle/ApiPlatform/Resources/` when the core itself depends on the endpoint (e.g. lang ID↔ISO mapping, self-introspection of the current API client). Endpoint authoring details (URI, scopes, mappings, do/don't, testing) live in [`modules/ps_apiresources/CONTEXT.md`](../../../modules/ps_apiresources/CONTEXT.md) — that's the source of truth, don't duplicate it here.
- **Composer scripts** (core-level, not in the module's CONTEXT.md):
  - `composer check-test-db` — verify the test DB is provisioned (dump files + DB accessible). Use this to skip needless re-creation.
  - `composer api-module-tests-local` — full setup (slow): creates the test DB, dumps tables, dumps the module autoload, runs the entire module suite.
  - `composer run-api-module-tests-local` — fast re-run of the suite (DB already provisioned).
  - `_PS_ROOT_DIR_=$(pwd) php -d date.timezone=UTC ./vendor/phpunit/phpunit/phpunit -c modules/ps_apiresources/tests/Integration/phpunit-local.xml --filter=ClassName` — single test class. `_PS_ROOT_DIR_` MUST be set per-command — PHPUnit can't auto-detect the PrestaShop root when the module is symlinked.

## Skills

| Skill | Owner | Trigger |
|-------|-------|---------|
| [`setup-apiresources-dev`](skills/setup-apiresources-dev/SKILL.md) | core (this component) | "set up ps_apiresources for development", "fix the API integration tests" |
| [`link-apiresources-fork`](skills/link-apiresources-fork/SKILL.md) | core (this component) | "pair core with ps_apiresources branch", "use my ps_apiresources fork in the core PR", "revert ps_apiresources to dev-dev" |
| [`ps-api-endpoint`](skills/ps-api-endpoint/SKILL.md) | `ps_apiresources` module (chained symlink) | "add a new admin API endpoint", "expose {Entity} on the API" |

`ps-api-endpoint` is module-owned — the link is a chained symlink into `modules/ps_apiresources/.claude/skills/`. See [`STRUCTURE.md` → "Adding a module-owned skill"](../../STRUCTURE.md#adding-a-module-owned-skill) for the discovery pattern.

## Related

- [`modules/ps_apiresources/CONTEXT.md`](../../../modules/ps_apiresources/CONTEXT.md) — endpoint authoring conventions (URI, scopes, mappings, do/don't, testing)
- `Component/CQRS` — endpoints delegate to commands and queries
- `Domain/ApiClient` — back-office CRUD for API clients
- External: <https://devdocs.prestashop-project.org/9/admin-api/> — 8 chapters: Context, OAuth2, Resource Server, Authorization Server, How to Use, Contribute to Core API, Setup Development Environment, Swagger Documentation

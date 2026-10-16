# AdminAPI Component

## Purpose

The Admin API exposes PrestaShop's back-office over REST, built on API Platform 3 with OAuth2 authorization (server inside the bundle, JWT-based) and per-route scopes. Endpoints don't define their own business logic — they delegate to the existing CQRS layer. Most resource definitions ship in a separate module, `ps_apiresources`, rather than in core.

For endpoint authoring conventions (URI shape, scopes, mappings, DTOs, errors, tests), the authoritative source is the module's own context: [`modules/ps_apiresources/.ai/CONTEXT.md`](../../../modules/ps_apiresources/.ai/CONTEXT.md); the review checklist is [`modules/ps_apiresources/.ai/REVIEW.md`](../../../modules/ps_apiresources/.ai/REVIEW.md). This file focuses on what's specific to the **core** side of the Admin API: the machinery the resources rely on, what changed per core version, and how to test it. The module folder is gitignored and may be a symlink to a separate clone: read those files explicitly, they are not loaded automatically (see "Cross-repository work" in the root `CONTEXT.md`).

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
| Module-provided API resources | `modules/ps_apiresources/src/ApiPlatform/Resources/{Domain}/` (sub objects in `{Domain}/Dto/`), index in [`generated/api-resources.md`](../../generated/api-resources.md) |
| Integration tests (core) | `tests/Integration/ApiPlatform/` (run with `tests/Integration/phpunit-api.xml`), `tests/Integration/PrestaShopBundle/ApiPlatform/`, `tests/Integration/PrestaShopBundle/Controller/Api/`, fixtures in `tests/Resources/ApiPlatform/Resources/` |
| Unit tests (core) | `tests/Unit/PrestaShopBundle/ApiPlatform/` (OpenAPI factory and adapters, serializer) |
| Integration tests (module) | `modules/ps_apiresources/tests/Integration/` (uses `phpunit-local.xml` / `phpunit-ci.xml`) |
| UI tests (API campaigns) | `tests/UI/campaigns/functional/API/` (Playwright, assert the exact response keys and request the endpoint scopes) |

## Non-obvious patterns

- API Platform resources can live OUTSIDE core, inside installed modules — `ps_apiresources` is the canonical example. The scopes extractor scans installed modules to discover the available scope tree.
- A handful of resources stay in **core** (`src/PrestaShopBundle/ApiPlatform/Resources/`) for endpoints that must work even when `ps_apiresources` is uninstalled — typically when the core itself depends on them. Current examples: `Language` (`/languages` — every other endpoint relies on the lang ID↔ISO mapping), `ApiClient` (`/api-clients/infos` — lets a token introspect itself; declares `scopes: []` and reads identity from `[_context][apiClientId]`).
- Endpoints reuse CQRS by indirection: `QueryProvider` / `QueryListProvider` dispatch read operations through the query bus, `CommandProcessor` dispatches write operations through the command bus. There are no API-specific business handlers — all logic comes from the existing CQRS layer.
- **Operation attributes** (`Metadata/`): `CQRSGet`, `CQRSCreate`, `CQRSPartialUpdate`, `CQRSUpdate`, `CQRSDelete`, `CQRSGetCollection`, `PaginatedList` (grid data factory), `CQRSPaginate` (CQRS list query), `UpdatePosition`. Their named arguments (`scopes`, `CQRSQuery`, `CQRSCommand`, `*Mapping`, `experimentalOperation`, `minVersion`, `maxVersion`, `defaultValues`, `allowEmptyBody`…) are copied into `extraProperties` by `AbstractScopedOperation` / `AbstractCQRSOperation`, and the providers read `extraProperties` only. A core that doesn't know an argument throws at metadata build time, which is why the module passes the recent ones through `extraProperties: [...]` (it supports cores from 9.0.3).
- **Query results → resource**: `QueryResultSerializerTrait` normalizes the query result, applies `CQRSQueryMapping` (`NormalizationMapper`, `@index` placeholders), then denormalizes into the resource with `CQRSApiSerializer::CAST_BOOL` (tiny ints of the database become booleans; at every level, nested DTOs and `@var Foo[]` lists included, since 9.2.1). A scalar list result is wrapped under `_queryResult` for non-collection operations; `collectionAsList` (9.2.1) re-indexes an id-indexed collection result as a JSON list.
- **Request body → command**: `CQRSApiValidator` runs the resource constraints with the operation `validationContext` groups, then `CQRSApiNormalizer` builds the command from the constructor parameters and the setters (a multi-parameter setter such as `setMinimumAmount(?DecimalNumber $amount, int $currencyId, ...)` is fed by a sub object `{amount, currencyId, ...}`; a `null` sub object calls the setter with null when its first parameter is nullable; a missing parameter inside the object is a 500 today). `defaultValues` (9.2.0, `DefaultValuesTrait`) are injected into the payload before validation; `[_context]` mappings and `[_context]` default values resolve from `ContextParametersProvider` (shop constraint, language, api client).
- **Errors**: `exceptionToStatus` (resource level + operation level, merged) is evaluated by API Platform's `ErrorListener` in declaration order with `instanceof`, the first matching class wins — a subclass mapped to 404 must be listed before its parent mapped to 422. Exceptions raised while denormalizing value objects (`ShopException`, `CurrencyConstraintException`…) go through the same map; anything unmapped is a 500.
- **Paginated lists** (`QueryListProvider`): envelope `{totalItems, orderBy, sortOrder, limit, offset, filters, items}`, `offset` always an integer (9.2.1), `items` always a JSON list. `filtersClass` provides the defaults (`orderBy`, `limit`), `filtersMapping` translates API field names to grid field names for `orderBy` and `filters[...]`; a grid-based list without them answers 500 on `orderBy` and ignores the filters. An unknown `orderBy` column still answers 500 (known gap).
- **OpenAPI** (`OpenApi/`): `CQRSOpenApiFactory` and its adapters document the request bodies from the command constructor and setters (`ConstructorExtractor` / `ReflectionExtractor`, registered explicitly in `open_api.yml` — the serializer relies on them too), the resource schemas from the property types (`@var Foo[]` item types from 9.2.1, `DefaultValuesAdapter`, `MultiParameterSetterAdapter`, the resource `openapiContext` merged on the command schemas), the collection parameters and the paginated envelope (9.2.1). Class PHPDoc is **not** used as a description (the PHPDoc factory was removed in 9.2.1): texts come from `description:` / `openapi: new OpenApiOperation(summary:, description:)` and `#[ApiProperty(description:)]`. Export with `bin/console api:openapi:export --yaml` (add `-o file`) and diff before/after a change.
- `ExperimentalOperationsMetadataCollectionFactoryDecorator` filters operations marked experimental when not in debug mode, so half-baked endpoints don't leak into production OpenAPI docs.
- `CoreVersionCompatibilityMetadataCollectionFactoryDecorator` (9.2.0) drops the operations whose `minVersion` / `maxVersion` don't match `Version::VERSION`; the module ships a twin decorator for older cores.
- `CQRSNotFoundMetadataCollectionFactoryDecorator` filters out resources whose backing CQRS messages don't exist on the current branch — this matters when a module ships a resource for a domain class that's been renamed or removed.
- Normalizer chain handles PrestaShop value objects: `CQRSApiNormalizer` (priority -1, runs last), `ValueObjectNormalizer`, `DecimalNumberNormalizer`, `ShopConstraintNormalizer`, `DateTimeImmutableNormalizer`, `UploadedFileNormalizer`, `LocalizedValueUpdater` for multilingual fields. `DecimalNumberNormalizer` raises the serializer's `NotNormalizableValueException` on a non-numeric value (a 400, and the next member of a union type gets its turn), not the decimal library's exception. Symfony comparison constraints (`Range`, `GreaterThan`…) don't work on `DecimalNumber` properties: only a core `PositiveOrZero` variant is DecimalNumber-aware, a range check belongs in the command.
- **Union types mixing scalars and a class** (e.g. `int|string|bool|DecimalNumber|null` for a typed default value): PHP reflects the class member first and the serializer tries members in that order, so `DecimalNumber` would swallow every scalar. `ScalarFirstUnionTypeExtractor` (a `property_info.type_extractor`, priority 20, `src/PrestaShopBundle/ApiPlatform/Serializer/`) answers such unions with the scalars first and stays silent for every other property. It sits in `property_info`, so it applies to API Platform's property metadata, Symfony's `ObjectNormalizer` and `CQRSApiNormalizer` alike — the resource DTOs are not denormalized by `CQRSApiNormalizer` (only domain objects are), which is why a decorator on that normalizer alone would not work.
- File uploads use a custom `MultipartDecoder` in `src/PrestaShopBundle/ApiPlatform/Encoder/`.
- Position updates use a dedicated `UpdatePositionProcessor` (`api_platform.state_processor`) backed by `PositionCollectionUpdater`.
- OAuth2 supports a custom `client_credentials` grant via `CustomClientCredentialsGrant`. Clients are persisted via the `ApiClient` domain (CRUD lives in the back-office UI).
- Scopes are derived from API Resource metadata, cached via `CachedApiResourceScopesExtractor`.

## What changed per core version

The module supports 9.0.3, 9.1.5, 9.2.x and develop (its CI matrix). A resource relying on one of these features either declares `minVersion` on the operation or keeps a behaviour that works everywhere.

| Core | Feature |
|------|---------|
| 9.0.2 | `@index` placeholders in mappings; `CQRSDelete` with a `CQRSCommand` and `allowEmptyBody` |
| 9.0.3 | `CQRSPaginate` (paginated list from a CQRS query, `itemsField` / `countField`) |
| 9.2.0 | `minVersion` / `maxVersion` filtering; `defaultValues` (context paths allowed, typed end to end, exposed as `default` in the schema); `ScalarFirstUnionTypeExtractor`; `_queryResult` wrapping of scalar lists; request bodies documented from the commands; multistore context parameters documented |
| 9.2.1 (PrestaShop#43116, #43237) | Booleans of every query result cast at every level (nested DTOs, `@var Foo[]` lists); `@var Foo[]` item types read from the PHPDoc; `collectionAsList`; paginated envelope with an integer `offset` and documented `limit` / `offset` / `orderBy` / `sortOrder` / `filters[...]` parameters; sub object schemas adapted (`DecimalNumber` as number), unused schemas removed, class PHPDoc no longer published; `DecimalNumber` parameters of multi-parameter setters documented as numbers; API test environment without employee (`phpunit-api.xml`) |

## Testing the API

- **Run the API in test like in production**: `tests/Integration/phpunit-api.xml` bootstraps like `admin-api/index.php` — no `_PS_ADMIN_DIR_`, so no employee in the legacy context. The module's `ApiTestCase::requestApi()` also resets the legacy static caches and clears the employee / cart of the context between requests. An employee in the context hides real bugs (e.g. `Product::getPriceStatic()` without a cart), so a test that passes only with one is wrong.
- Core API suite: `php -d date.timezone=UTC vendor/bin/phpunit -c tests/Integration/phpunit-api.xml`. Module suite: `composer api-module-tests-local` (full setup) then `composer run-api-module-tests-local`, or one class with `_PS_ROOT_DIR_=$(pwd) php -d date.timezone=UTC ./vendor/phpunit/phpunit/phpunit -c modules/ps_apiresources/tests/Integration/phpunit-local.xml --filter=ClassName` (`_PS_ROOT_DIR_` MUST be set per command, PHPUnit can't detect the root when the module is symlinked). `composer check-test-db` tells whether the test DB is provisioned.
- Both suites share `var/cache/test`: clear it when switching from one to the other in the same checkout (CI runs them in separate workflows).
- A core change to the API infrastructure comes with a unit test (`tests/Unit/PrestaShopBundle/ApiPlatform/`) or an integration test on the fixture resources (`tests/Resources/ApiPlatform/Resources/`, `tests/Integration/PrestaShopBundle/ApiPlatform/`), and the module suite is run against it (link the module PR with the `link-apiresources-fork` skill: the core PR pins the module branch in `composer.json`, its CI must be green while the module PR's CI stays red until the core PR is merged).
- Contract changes are checked with the OpenAPI export (`bin/console api:openapi:export --yaml`) diffed before/after, and with real calls; PR descriptions show before/after extracts (see PrestaShop#43116 and #43237).

## Canonical examples

- `src/PrestaShopBundle/ApiPlatform/Provider/QueryProvider.php` — query dispatch via the query bus
- `src/PrestaShopBundle/ApiPlatform/Provider/QueryListProvider.php` — paginated lists (grid or CQRS query), envelope and filters
- `src/PrestaShopBundle/ApiPlatform/Processor/CommandProcessor.php` — command dispatch via the command bus
- `src/PrestaShopBundle/ApiPlatform/Normalizer/CQRSApiNormalizer.php` — main normalizer (priority -1, runs last)
- `src/PrestaShopBundle/ApiPlatform/OpenApi/Factory/CQRSOpenApiFactory.php` — OpenAPI generation entry point, adapters in `OpenApi/Adapter/`
- `src/PrestaShopBundle/Security/OAuth2/PrestashopAuthorisationServer.php` — OAuth2 server
- `modules/ps_apiresources/src/ApiPlatform/Resources/` — example resource definitions

## Conventions

- **Where new resources live** — default to `modules/ps_apiresources/src/ApiPlatform/Resources/`. Only put a resource in `src/PrestaShopBundle/ApiPlatform/Resources/` when the core itself depends on the endpoint (e.g. lang ID↔ISO mapping, self-introspection of the current API client). Endpoint authoring details (URI, scopes, mappings, DTOs, errors, testing) live in [`modules/ps_apiresources/.ai/CONTEXT.md`](../../../modules/ps_apiresources/.ai/CONTEXT.md) — that's the source of truth, don't duplicate it here.
- **Reviewing an endpoint** (new or existing) follows [`modules/ps_apiresources/.ai/REVIEW.md`](../../../modules/ps_apiresources/.ai/REVIEW.md): the resource is compared with the core CQRS classes it names (constructor and setters of the command, query result getters, exceptions thrown by the handler and the value objects) and the OpenAPI output is compared with the real payloads.
- **Fix the core, don't work around it in the module**: a query result that returns raw database rows, a missing `NotFoundException`, a command without a setter for an editable field, are core changes on the lowest applicable branch, paired with the module PR.

## Skills

| Skill | Owner | Trigger |
|-------|-------|---------|
| [`setup-apiresources-dev`](skills/setup-apiresources-dev/SKILL.md) | core (this component) | "set up ps_apiresources for development", "fix the API integration tests" |
| [`link-apiresources-fork`](skills/link-apiresources-fork/SKILL.md) | core (this component) | "pair core with ps_apiresources branch", "use my ps_apiresources fork in the core PR", "revert ps_apiresources to dev-dev" |
| [`ps-api-endpoint`](skills/ps-api-endpoint/SKILL.md) | `ps_apiresources` module (chained symlink) | "add a new admin API endpoint", "expose {Entity} on the API", "fix the {Entity} endpoint" |
| [`review-api-pr`](skills/review-api-pr/SKILL.md) | `ps_apiresources` module (chained symlink) | "review this ps_apiresources PR", "audit the {Entity} endpoint" |

`ps-api-endpoint` and `review-api-pr` are module-owned — the links are chained symlinks into `modules/ps_apiresources/.ai/skills/`. See [`STRUCTURE.md` → "Adding a module-owned skill"](../../STRUCTURE.md#adding-a-module-owned-skill) for the discovery pattern.

## Related

- [`modules/ps_apiresources/.ai/CONTEXT.md`](../../../modules/ps_apiresources/.ai/CONTEXT.md) — endpoint authoring conventions (URI, scopes, mappings, DTOs, errors, testing)
- [`modules/ps_apiresources/.ai/REVIEW.md`](../../../modules/ps_apiresources/.ai/REVIEW.md) — review checklist (also the CodeRabbit guideline of the module)
- [`generated/api-resources.md`](../../generated/api-resources.md) — endpoint inventory per domain
- `Component/CQRS` — endpoints delegate to commands and queries
- `Domain/ApiClient` — back-office CRUD for API clients
- External: <https://devdocs.prestashop-project.org/9/admin-api/> — 8 chapters: Context, OAuth2, Resource Server, Authorization Server, How to Use, Contribute to Core API, Setup Development Environment, Swagger Documentation

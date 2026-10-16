# AdminAPI Component

## Purpose

The Admin API exposes PrestaShop's back-office over REST, built on API Platform 3 with OAuth2 authorization (server inside the bundle, JWT-based) and per-route scopes. Endpoints don't define their own business logic — they delegate to the existing CQRS layer. Most resource definitions ship in a separate module, `ps_apiresources`, rather than in core.

For endpoint authoring conventions (URI shape, scopes, mappings, DTOs, errors, tests), the authoritative source is the module's own context: [`modules/ps_apiresources/.ai/CONTEXT.md`](../../../modules/ps_apiresources/.ai/CONTEXT.md); the review checklist is [`modules/ps_apiresources/.ai/REVIEW.md`](../../../modules/ps_apiresources/.ai/REVIEW.md). This file focuses on what's specific to the **core** side of the Admin API: the machinery the resources rely on, what each core version adds to it, and how to test it. The module folder is gitignored and may be a symlink to a separate clone: read those files explicitly, they are not loaded automatically (see "Cross-repository work" in the root `CONTEXT.md`).

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
- **Operation attributes** (`Metadata/`): `CQRSGet`, `CQRSCreate`, `CQRSPartialUpdate`, `CQRSUpdate`, `CQRSDelete`, `CQRSGetCollection`, `PaginatedList` (grid data factory), `CQRSPaginate` (CQRS list query), `UpdatePosition`. Their named arguments (`scopes`, `CQRSQuery`, `CQRSCommand`, `*Mapping`, `experimentalOperation`, `minVersion`, `maxVersion`, `defaultValues`, `allowEmptyBody`…) are copied into `extraProperties` by `AbstractScopedOperation` / `AbstractCQRSOperation`, and the providers read `extraProperties` only. A core that doesn't know an argument throws at metadata build time (how the module copes with the older cores: [`VERSIONS.md`](../../../modules/ps_apiresources/.ai/VERSIONS.md)).
- **Query results → resource**: `QueryResultSerializerTrait` normalizes the query result, applies `CQRSQueryMapping` (`NormalizationMapper`, `@index` placeholders), then denormalizes into the resource with `CQRSApiSerializer::CAST_BOOL` (tiny ints of the database become booleans; at every level, nested DTOs and `@var Foo[]` lists included, since 9.2.1). A scalar list result is wrapped under `_queryResult` for non-collection operations; the `collectionAsList` extra property (9.2.1, no operation argument) re-indexes an id-indexed collection result as a JSON list.
- **Request body → command**: `CQRSApiValidator` runs the resource constraints with the operation `validationContext` groups, then `CQRSApiNormalizer` builds the command from the constructor parameters and the setters (a multi-parameter setter such as `setMinimumAmount(?DecimalNumber $amount, int $currencyId, ...)` is fed by a sub object `{amount, currencyId, ...}`; a `null` sub object calls the setter with null when its first parameter is nullable; a missing parameter inside the object is a 500 today). `defaultValues` (9.2.0, `DefaultValuesTrait`) are injected into the payload before validation; `[_context]` mappings and `[_context]` default values resolve from `ContextParametersProvider` (shop constraint, language, api client).
- **Request context**: the listeners of `EventListener/API/Context/` build the shop context (default `PS_SHOP_DEFAULT`; its request parameters: [`MULTISHOP.md`](../../../modules/ps_apiresources/.ai/MULTISHOP.md)), the language (`langId`, default `PS_LANG_DEFAULT`, the language of the single-language values of the lists) and the currency (`currencyId`, default `PS_CURRENCY_DEFAULT`) before the operation runs; `ContextParametersProvider` exposes them to the `[_context]` mappings and default values.
- **Errors**: `exceptionToStatus` (resource level + operation level, merged) is evaluated by API Platform's `ErrorListener` with `instanceof`, in declaration order; the exceptions raised while denormalizing value objects go through the same map, anything unmapped is a 500. The mapping rules of the resources: [`ERRORS.md`](../../../modules/ps_apiresources/.ai/ERRORS.md).
- **Paginated lists**: `QueryListProvider` builds the envelope of `PaginatedList` and `CQRSPaginate` from the `filtersClass` and `filtersMapping` of the operation; an unknown `orderBy` column still answers 500 (known gap). The envelope and the rules of a list resource: [`LISTS.md`](../../../modules/ps_apiresources/.ai/LISTS.md).
- **OpenAPI** (`OpenApi/`): `CQRSOpenApiFactory` and its adapters document the request bodies from the command constructor and setters (`ConstructorExtractor` / `ReflectionExtractor`, registered explicitly in `open_api.yml` — the serializer relies on them too), the resource schemas from the property types (`@var Foo[]` item types from 9.2.1, `DefaultValuesAdapter`, `MultiParameterSetterAdapter`, the resource `openapiContext` merged on the command schemas), the collection parameters and the paginated envelope (9.2.1). What a resource documents, and the generator limits: [`OPENAPI.md`](../../../modules/ps_apiresources/.ai/OPENAPI.md).
- `ExperimentalOperationsMetadataCollectionFactoryDecorator` drops the operations marked `experimentalOperation` unless the kernel runs in debug mode or the experimental endpoints feature flag (`admin_api_experimental_endpoints`) is on, so half-baked endpoints don't leak into production OpenAPI docs.
- `CoreVersionCompatibilityMetadataCollectionFactoryDecorator` (9.2.0) drops the operations whose `minVersion` / `maxVersion` don't match `Version::VERSION` (older cores: [`VERSIONS.md`](../../../modules/ps_apiresources/.ai/VERSIONS.md)).
- `CQRSNotFoundMetadataCollectionFactoryDecorator` drops, operation by operation, the operations whose `CQRSQuery` / `CQRSCommand` class or `gridDataFactory` service doesn't exist on the running core; the other operations of the resource stay. This matters when a module ships a resource for a domain class that's been renamed, removed or added later. Neither this filter nor the `minVersion` one applies when the experimental endpoints feature flag is on.
- Normalizer chain handles PrestaShop value objects: `CQRSApiNormalizer` (priority -1, runs last), `ValueObjectNormalizer`, `DecimalNumberNormalizer`, `ShopConstraintNormalizer`, `DateTimeImmutableNormalizer`, `UploadedFileNormalizer`, `LocalizedValueUpdater` for multilingual fields. `DecimalNumberNormalizer` raises the serializer's `NotNormalizableValueException` on a non-numeric value (a 400, and the next member of a union type gets its turn), not the decimal library's exception.
- **Union types mixing scalars and a class** (e.g. `int|string|bool|DecimalNumber|null` for a typed default value): PHP reflects the class member first and the serializer tries members in that order, so `DecimalNumber` would swallow every scalar. `ScalarFirstUnionTypeExtractor` (a `property_info.type_extractor`, priority 20, `src/PrestaShopBundle/ApiPlatform/Serializer/`) answers such unions with the scalars first and stays silent for every other property. It sits in `property_info`, so it applies to API Platform's property metadata, Symfony's `ObjectNormalizer` and `CQRSApiNormalizer` alike — the resource DTOs are not denormalized by `CQRSApiNormalizer` (only domain objects are), which is why a decorator on that normalizer alone would not work.
- File uploads use a custom `MultipartDecoder` in `src/PrestaShopBundle/ApiPlatform/Encoder/`.
- Position updates use a dedicated `UpdatePositionProcessor` (`api_platform.state_processor`) backed by `PositionCollectionUpdater`.
- OAuth2 supports a custom `client_credentials` grant via `CustomClientCredentialsGrant`. Clients are persisted via the `ApiClient` domain (CRUD lives in the back-office UI).
- Scopes are derived from API Resource metadata, cached via `CachedApiResourceScopesExtractor`.

## What changed per core version

The features of the core API layer the resources rely on. A core PR adding one adds its row here; the module gates the operations that need it with `minVersion` ([`VERSIONS.md`](../../../modules/ps_apiresources/.ai/VERSIONS.md)).

| Core | Feature |
|------|---------|
| 9.0.2 | `@index` placeholders in mappings; `CQRSDelete` with a `CQRSCommand` and `allowEmptyBody` |
| 9.0.3 | `CQRSPaginate` (paginated list from a CQRS query, `itemsField` / `countField`) |
| 9.1.2 | 207 responses of the bulk commands (`AbstractBulkCommandHandler`, `BulkCommandExceptionNormalizer`) |
| 9.2.0 | `minVersion` / `maxVersion` filtering in the core; `defaultValues` (context paths allowed, typed end to end, exposed as `default` in the schema); `ScalarFirstUnionTypeExtractor`; `_queryResult` wrapping of scalar lists; request bodies documented from the commands; multistore context parameters documented; `File` properties documented as binary parts of the multipart body; multipart parts holding a JSON array or object decoded ([`UPLOAD.md`](../../../modules/ps_apiresources/.ai/UPLOAD.md)) |
| 9.2.1 (PrestaShop#43116, #43237, #43264, #43252) | Booleans of every query result cast at every level (nested DTOs, `@var Foo[]` lists); `@var Foo[]` / `@var int[]` item types read from the PHPDoc, request bodies included; `collectionAsList`; paginated envelope with an integer `offset` and documented list parameters; sub object schemas adapted (`DecimalNumber` as number), unused schemas removed, class PHPDoc no longer published; `DecimalNumber` parameters of multi-parameter setters documented as numbers, with their required parameters and a nullable object when the setter accepts `null` alone; API test environment without employee (`phpunit-api.xml`) |

## Testing the API

- **Run the API in test like in production**: `tests/Integration/phpunit-api.xml` bootstraps like `admin-api/index.php` — no `_PS_ADMIN_DIR_`, so no employee in the legacy context. An employee in the context hides real bugs (e.g. `Product::getPriceStatic()` without a cart), so a test that passes only with one is wrong; the module suite runs the same way ([`TESTING.md`](../../../modules/ps_apiresources/.ai/TESTING.md)).
- Core API suite: `php -d date.timezone=UTC vendor/bin/phpunit -c tests/Integration/phpunit-api.xml`; `composer check-test-db` tells whether the test DB is provisioned. The module suite, run from this checkout: [`TESTING.md` → "Running the suite"](../../../modules/ps_apiresources/.ai/TESTING.md#running-the-suite).
- A core change to the API infrastructure comes with a unit test (`tests/Unit/PrestaShopBundle/ApiPlatform/`) or an integration test on the fixture resources (`tests/Resources/ApiPlatform/Resources/`, `tests/Integration/PrestaShopBundle/ApiPlatform/`), and the module suite is run against it: the core PR pins the module branch with the `link-apiresources-fork` skill (CI expectations and merge order: [`CONTEXT.md` → "Working with the core"](../../../modules/ps_apiresources/.ai/CONTEXT.md#working-with-the-core)). The version branch keeps that pin after the merges, since it already holds the merged module content: the next pairing replaces it, a switch to `dev-dev` brings newer module changes when they are needed, and the core release points it to the released module version. A PR switching to `dev-dev` right after the merge, for the same content, is not needed.
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
- **Reviewing an endpoint** (new or existing) follows [`modules/ps_apiresources/.ai/REVIEW.md`](../../../modules/ps_apiresources/.ai/REVIEW.md).
- **Fix the core, don't work around it in the module**: [`CONTEXT.md` → "Working with the core"](../../../modules/ps_apiresources/.ai/CONTEXT.md#working-with-the-core).

## Skills

| Skill | Owner | Trigger |
|-------|-------|---------|
| [`setup-apiresources-dev`](skills/setup-apiresources-dev/SKILL.md) | core (this component) | "set up ps_apiresources for development", "fix the API integration tests" |
| [`link-apiresources-fork`](skills/link-apiresources-fork/SKILL.md) | core (this component) | "pair core with ps_apiresources branch", "use my ps_apiresources fork in the core PR", "point ps_apiresources to its release" |
| [`ps-api-endpoint`](skills/ps-api-endpoint/SKILL.md) | `ps_apiresources` module (chained symlink) | "add a new admin API endpoint", "expose {Entity} on the API", "fix the {Entity} endpoint" |
| [`review-api-pr`](skills/review-api-pr/SKILL.md) | `ps_apiresources` module (chained symlink) | "review this ps_apiresources PR", "audit the {Entity} endpoint" |

`ps-api-endpoint` and `review-api-pr` are module-owned — the links are chained symlinks into `modules/ps_apiresources/.ai/skills/`. See [`STRUCTURE.md` → "Adding a module-owned skill"](../../STRUCTURE.md#adding-a-module-owned-skill) for the discovery pattern.

## Related

- [`modules/ps_apiresources/.ai/CONTEXT.md`](../../../modules/ps_apiresources/.ai/CONTEXT.md) — endpoint authoring conventions (URI, scopes, mappings, DTOs, errors, testing)
- [`modules/ps_apiresources/.ai/REVIEW.md`](../../../modules/ps_apiresources/.ai/REVIEW.md) — review checklist (also the CodeRabbit guideline of the module)
- [`generated/api-resources.md`](../../generated/api-resources.md) — endpoint inventory per domain
- `Component/CQRS` — endpoints delegate to commands and queries
- `Component/ExtraProperty` — module-declared extra fields on core entities, exposed on the entity endpoints under `extraProperties` (`ExtraPropertyApiSubscriber`, validator and OpenAPI adapter in `ApiPlatform/`) and managed through `/extra-property-definitions` (module, core 9.2.0): the answer to third-party extensibility of the core resources
- `Domain/ApiClient` — back-office CRUD for API clients
- External: [ADR 0023 — CQRS API guidelines](https://github.com/PrestaShop/ADR/blob/master/0023-cqrs-api-guidelines.md) — the decision record of the contract (URIs, methods, bulk operations, scopes, resource properties); the module context cites it per rule and lists the shipped deviations
- External: <https://devdocs.prestashop-project.org/9/admin-api/> — 8 chapters: Context, OAuth2, Resource Server, Authorization Server, How to Use, Contribute to Core API, Setup Development Environment, Swagger Documentation

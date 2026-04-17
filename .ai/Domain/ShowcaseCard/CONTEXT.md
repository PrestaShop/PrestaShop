# ShowcaseCard Domain

## Purpose

Manages the open/closed display state of informational "showcase" cards shown in the back-office (e.g. categories, customers, SEO URLs). It does not manage any catalog or user data — only UI visibility preferences stored in `configuration` table.

## Layers

| Layer | Path |
|-------|------|
| Core CQRS | `src/Core/Domain/ShowcaseCard/` — Commands, Queries, ValueObjects, Exceptions |
| Adapter | `src/Core/Domain/ShowcaseCard/CommandHandler/`, `src/Core/Domain/ShowcaseCard/QueryHandler/` — handlers live inside Core, no `src/Adapter/ShowcaseCard/` |
| Back-office UI | `src/PrestaShopBundle/Controller/Admin/Configure/ShowcaseCardController.php` |

## Non-obvious patterns

- Handlers are located inside `src/Core/Domain/ShowcaseCard/CommandHandler/` and `QueryHandler/` (not under `src/Adapter/`) — an unusual placement in this codebase.
- Card-to-configuration-key mapping is centralised in `ConfigurationMap.php`, using the template `PS_SHOWCASECARD_%s_CLOSED`; each card name maps to a `configuration` row.
- `ValueObject/ShowcaseCard.php` holds string constants for every supported card name (e.g. `SEO_URLS_CARD`, `CATEGORIES_CARD`).

## Canonical examples

- `src/Core/Domain/ShowcaseCard/Command/CloseShowcaseCardCommand.php` + `src/Core/Domain/ShowcaseCard/CommandHandler/CloseShowcaseCardHandler.php`

## Related

- [CQRS Component](../../Component/CQRS/CONTEXT.md)

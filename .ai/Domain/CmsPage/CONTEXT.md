# CmsPage Domain

## Purpose

Manages CMS static pages (content, status, category assignment, meta). Does not manage CMS categories — those are a separate domain (`CmsPageCategory`).

## Layers

| Layer | Path |
|-------|------|
| Core CQRS | `src/Core/Domain/CmsPage/` — Commands, Queries, QueryResults, ValueObjects, Exceptions |
| Adapter | `src/Adapter/CMS/Page/` — handler implementations |
| Back-office UI | `src/PrestaShopBundle/Controller/Admin/Improve/Design/CmsPageController.php`, `src/Core/Grid/Definition/Factory/CmsPageDefinitionFactory.php` |

## Non-obvious patterns

- The adapter lives under `src/Adapter/CMS/Page/` (not `src/Adapter/CmsPage/`); both CmsPage and CmsPageCategory adapters share the `src/Adapter/CMS/` parent with a `CMSDataProvider.php` at that level.
- A `GetCmsCategoryIdForRedirection` query lives in the CmsPage domain (not CmsPageCategory) because it serves the page-edit redirect flow.

## Canonical examples

- `src/Core/Domain/CmsPage/Command/AddCmsPageCommand.php` + `src/Adapter/CMS/Page/CommandHandler/AddCmsPageHandler.php`

## Related

- [CmsPageCategory Domain](../CmsPageCategory/CONTEXT.md)
- [CQRS Component](../../Component/CQRS/CONTEXT.md)
- [Forms Component](../../Component/Forms/CONTEXT.md)
- [Grid Component](../../Component/Grid/CONTEXT.md)
- `tests/Integration/Behaviour/Features/Scenario/CmsPage/` — Behat behavior scenarios
- [GOTCHAS.md](../../GOTCHAS.md) — adapter path exception (`Adapter/CMS/Page/` not `Adapter/CmsPage/`)

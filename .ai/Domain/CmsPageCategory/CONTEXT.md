# CmsPageCategory Domain

## Purpose

Manages the hierarchical tree of CMS page categories including creation, editing, bulk status changes, and breadcrumb resolution. Does not manage CMS page content — that is handled by the `CmsPage` domain.

## Layers

| Layer | Path |
|-------|------|
| Core CQRS | `src/Core/Domain/CmsPageCategory/` — Commands, Queries, QueryResults, ValueObjects, Exceptions |
| Adapter | `src/Adapter/CMS/PageCategory/` — handler implementations |
| Back-office UI | `src/PrestaShopBundle/Controller/Admin/Improve/Design/CmsPageController.php` (shared controller), `src/Core/Grid/Definition/Factory/CmsPageCategoryDefinitionFactory.php` |

## Non-obvious patterns

- The adapter lives under `src/Adapter/CMS/PageCategory/` (not `src/Adapter/CmsPageCategory/`); it shares the `src/Adapter/CMS/` parent namespace with the CmsPage adapter.
- The back-office controller is `CmsPageController`, which handles both CMS pages and CMS page categories in a single controller.
- Abstract base command classes (`AbstractCmsPageCategoryCommand`, `AbstractBulkCmsPageCategoryCommand`) are used to share common validation across add/edit/bulk operations.

## Canonical examples

- `src/Core/Domain/CmsPageCategory/Command/AddCmsPageCategoryCommand.php` + `src/Adapter/CMS/PageCategory/CommandHandler/AddCmsPageCategoryHandler.php`

## Related

- [CmsPage Domain](../CmsPage/CONTEXT.md)
- [CQRS Component](../../Component/CQRS/CONTEXT.md)
- [Forms Component](../../Component/Forms/CONTEXT.md)
- [Grid Component](../../Component/Grid/CONTEXT.md)

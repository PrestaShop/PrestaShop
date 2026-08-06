# Attachment Domain

## Purpose

Manages file attachments that can be linked to products (e.g., manuals, datasheets). Does not handle product images — those belong to the Product domain.

## Layers

| Layer | Path |
|-------|------|
| Core CQRS | `src/Core/Domain/Attachment/` — Commands, Queries, QueryResults, ValueObjects, Exceptions, Configuration |
| Adapter | `src/Adapter/Attachment/` — handler implementations |
| Legacy ObjectModel | `classes/Attachment.php` (339 lines) — do not add logic here |
| Back-office UI | `src/PrestaShopBundle/Controller/Admin/Sell/Catalog/AttachmentController.php` |

## Non-obvious patterns

- `Configuration/AttachmentConstraint.php` holds validation constraint constants (file size, allowed MIME types, etc.) — check here before adding upload validation logic.

## Canonical examples

- `src/Core/Domain/Attachment/Command/AddAttachmentCommand.php` + `src/Adapter/Attachment/CommandHandler/AddAttachmentCommandHandler.php`

## Related

- [CQRS Component](../../Component/CQRS/CONTEXT.md)
- [Forms Component](../../Component/Forms/CONTEXT.md)
- [Grid Component](../../Component/Grid/CONTEXT.md)
- `tests/Integration/Behaviour/Features/Scenario/Attachment/` — Behat behavior scenarios

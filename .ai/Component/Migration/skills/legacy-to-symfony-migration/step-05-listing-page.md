---
step: 5
title: "Listing page (vertical slice)"
previous: step-04-behat-tests.md
next: step-06-form-page.md
deliverable: "A working listing page: grid + controller listing actions + listing routes + index template + listing JS, all reachable via the feature flag"
---

# Step 5 — Listing page (vertical slice)

This step produces a complete, demoable listing page in one go. It is a **vertical slice**: every layer needed for the listing — grid, controller, routing, template, JS — is built and wired in this single step. The form (step 6) is a separate slice with its own layers.

Read `@.ai/Component/Grid/CONTEXT.md`, `@.ai/Component/Controller/CONTEXT.md`, `@.ai/Component/Twig/CONTEXT.md`, `@.ai/Component/Javascript/CONTEXT.md` for the conventions of each layer.

## Why a vertical slice

A horizontal "controller-class step" followed by a "routing step" followed by a "templates step" produces awkward intermediate states (empty controller, dead routes, JS that has no template to attach to). A vertical slice produces a usable page after one round of work — the page can be opened, the grid reads from the manifest, bulk actions and filters work end-to-end.

## Slice ordering: listing vs form

Listing-first is the conventional default (unblocks bulk operations earlier and is usually simpler than the form). Form-first is valid when listing is already migrated or out of scope. **The first slice to run creates the controller class and routing file; the second extends them.**

## Skills to invoke

In suggested order:

| Skill | Produces |
|---|---|
| `create-grid-definition` | `GridDefinitionFactory`, `{Domain}Filters` class, DI registration. Use `buildDeleteAction` helper for the delete row action when applicable. |
| `create-grid-query-builder` | DBAL `QueryBuilder` for grid rows; column aliases must match column IDs in the definition |
| `create-position-column` | PositionColumn + dedicated `update-position` route + position handling in the repository — **conditional, only if the manifest lists a `position` field** |
| `create-controller-listing` | `indexAction({Domain}Filters $filters)`, `deleteAction`, `toggleStatusAction`, bulk actions. Creates the controller class on first slice, extends it on second. |
| `create-admin-routing` | Listing routes (`index`, `search`, `delete`, `toggle_status`, `bulk_*`, `update_position` if applicable). Carries `_legacy_feature_flag: {domain}` on each route. Creates the YAML on first slice, extends it on second. |
| `create-twig-index-template` | `index.html.twig` rendering the grid panel and the toolbar |
| `create-ts-entry-point` | `js/pages/{domain}/index.ts` + webpack entry for the listing |
| `init-grid-extensions` | Grid JS extensions (sortable, bulk, filters, position drag-and-drop if applicable) |

## Orchestration notes

- The `{Domain}Filters` class declared in `create-grid-definition` is the argument resolved automatically into `indexAction` — prefer it over manual `$request->query` parsing.
- Filter reset is handled by Symfony's `CommonController::resetSearchAction` — do not reimplement it in the controller.
- The grid factory service ID, the `{Domain}Filters` class, and the JS Grid constructor ID must use the same `GRID_ID` string (see `Grid/CONTEXT.md`).
- Routes need `_legacy_feature_flag: {domain}` so the toggle works. The flag entry exists from step 1.

## Gate to next step

- [ ] `php bin/console debug:router | grep {domain}` lists every listing route
- [ ] With flag enabled, the listing page loads, columns render, filters apply, sort works
- [ ] Delete and toggle work and emit a flash message
- [ ] Bulk actions (per the manifest) work
- [ ] Position drag-and-drop works (if applicable)
- [ ] With flag disabled, the legacy listing still loads (no regression)

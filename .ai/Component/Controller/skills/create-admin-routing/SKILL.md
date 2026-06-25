---
name: create-admin-routing
description: >
  Create the Symfony routing YAML file declaring all admin routes for the
  domain. Trigger: "create routing for {Domain}".
needs: [create-controller-listing, create-controller-form-actions]
produces: "YAML routing file with all admin routes carrying _legacy_feature_flag and _legacy_controller"
---

# create-admin-routing

## Instructions

1. Create `src/PrestaShopBundle/Resources/config/routing/admin/{domain}.yml`.
2. For each action in the controller (listing + form actions), declare a route. Always include `_legacy_controller` (permanent — see below). **While the page is under migration**, also include `_legacy_feature_flag` so requests are dispatched to the new or legacy controller depending on the flag state:
   ```yaml
   admin_{domain}s_index:
     path: /{domain}s
     methods: [GET]
     defaults:
       _controller: 'PrestaShopBundle:Admin/{Section}/{Domain}:index'
       _legacy_controller: Admin{Domain}s
       _legacy_feature_flag: {domain}
   ```
3. Include routes: index (GET), create (GET+POST), edit (GET+POST with `{id}` parameter), delete (POST with `{id}`), toggle status (POST+JSON), bulk delete/enable/disable (POST).
4. Import this file from the main admin routing file.
5. CRITICAL: `_legacy_feature_flag` value must exactly match the `name` of the feature flag in feature_flag.xml.
6. Verify with `php bin/console debug:router | grep {domain}`.

### `_legacy_controller` is permanent

Keep `_legacy_controller` on every route — it stays for the lifetime of the page, even after full migration. Two consumers depend on it:

- **Permission checks:** admin permission rules are keyed by the legacy controller name. Action security expressions like `is_granted('read', request.get('_legacy_controller'))` rely on this attribute being present
- **Automatic legacy link conversion:** third-party modules and old code paths still call `Link::getAdminLink('Admin{Domain}s')`; the routing layer uses `_legacy_controller` to resolve those calls to the Symfony route

Removing `_legacy_controller` after release would silently break permissions and any legacy `Link::getAdminLink()` callers — never strip it.

### `_legacy_feature_flag` is migration-only

`_legacy_feature_flag`, in contrast, only exists while the new page coexists with the legacy one. Once the feature flag has been promoted to stable (GA) and the legacy controller is no longer used as a fallback, **remove `_legacy_feature_flag`** from the route — but keep `_legacy_controller`. Routes for fully-released pages have `_legacy_controller` and no `_legacy_feature_flag`.

This skill scaffolds routes for pages currently under migration. For routes added or refactored on already-released pages, omit `_legacy_feature_flag` and keep `_legacy_controller`.

## Rules

Conventions (atomic commit, case-sensitive flag matching, toggle returning JSON, `_legacy_controller` permanence, post-release flag removal) are in [Controller/CONTEXT.md](../../CONTEXT.md). Skill-specific reminders:

- Edit and delete routes must include `{id}` path parameter
- Toggle status route must allow POST method
- Never strip `_legacy_controller` — it remains required for permissions and legacy link conversion long after migration is over

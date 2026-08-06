---
name: create-removal-issue
description: >
  Create a GitHub issue that targets the actual removal of the legacy controller
  in the next major version. The issue tracks readiness prerequisites and
  assigns a major release milestone.
produces: "GitHub issue tracking the actual removal of Admin{Domain}sController in the next major version"
---

# create-removal-issue

## Prerequisites (implicit)

- The migrated Symfony page is GA (`stability="stable"`, `state=1`) and has been stable for at least one minor release with no P1 regressions.

## Instructions

1. Create a GitHub issue with title: `Remove deprecated Admin{Domain}sController`.
2. Body:
   ```markdown
   **Target:** PS X.0

   **Context:** The new `{Domain}Controller` (Symfony) is GA since PS W.V (PR #YYYYY).

   **Prerequisites:**
   - [ ] GA page has been stable for ≥1 minor release with no P1 regressions
   - [ ] Migration guide published (release notes / dev blog)
   - [ ] All known modules updated (verify with ecosystem team)

   **Files to delete:**
   - `controllers/admin/Admin{Domain}sController.php`
   - Legacy-only service registrations referencing this controller

   **Files to update:**
   - Remove `_legacy_feature_flag: {domain}` from routing YAML once the flag entry itself is dropped
   - Clean up `feature_flag.xml` entry if no longer needed
   ```
3. Assign to the team's major release milestone.
4. Tag with: `major-release`, `legacy-cleanup`.

## Rules

Conventions (timing, reference GA PR) are in [Migration/CONTEXT.md](../../CONTEXT.md#removal-next-major-version). Skill-specific reminders:

- The `_legacy_controller: Admin{Domain}s` attribute on routes is **kept** (preserves URL compatibility for legacy admin links) — do not list it as a "file to update"
- Never create a PR for removal until all prerequisites in the issue are checked

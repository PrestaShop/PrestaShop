---
step: 9
title: "Removal (next major)"
previous: step-08-general-availability.md
next: null
deliverable: "GitHub issue tracking the legacy controller removal in the next major version"
---

# Step 9 — Removal

The legacy controller is removed in the **next major version** after the migrated page reaches GA. This step does not delete code — it opens the tracking issue. The actual removal PR is opened against the major-version branch when the team plans the major release.

## When to enter

- The migrated page is GA (step 8 merged) and stable for ≥1 minor release with no P1 regressions
- A major release is in planning

There is no fixed delay (e.g. "6 months"). Timing is per-release planning. Common practice is to wait 1–2 minor releases after GA so module developers have time to adapt.

## Skill to invoke

| Skill | Produces |
|---|---|
| `create-removal-issue` | GitHub issue titled "Remove deprecated `Admin{Domain}sController`" assigned to the major-release milestone, listing prerequisites (module readiness, migration guide) and the files to delete |

## Orchestration notes

- The deprecation banner inside the legacy controller (`$this->warnings[]`) is **not** part of the standard flow — it has been done once for Carrier and is not a default step. Do not invoke it here.
- The release notes / dev blog announcement of the removal target lives outside this repository (PrestaShop release notes), not in CHANGELOG.md.
- The `_legacy_controller: Admin{Domain}s` attribute on routes is **kept** even after removal — it preserves URL compatibility for legacy admin links.
- The `_legacy_feature_flag: {domain}` attribute is removed only when the flag entry itself is dropped from `feature_flag.xml` — done in the same major-release cleanup as the controller deletion.

## Gate

This is the final step of the migration lifecycle. Subsequent work happens in a separate "remove deprecated controller" PR against the major-version branch, gated on the prerequisites listed in the tracking issue.

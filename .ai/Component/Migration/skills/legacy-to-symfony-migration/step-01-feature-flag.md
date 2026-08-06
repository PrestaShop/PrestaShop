---
step: 1
title: "Feature Flag Registration"
previous: step-00-audit.md
next: step-02-domain-layer.md
deliverable: "feature_flag.xml entry with stability='beta' and state='0', registered in install fixtures"
---

# Step 1 — Feature Flag Registration

Register the feature flag **before** any other code is written. The flag is the routing toggle that decides — at request time — whether a route resolves to the new Symfony controller or the legacy controller. Several later steps may also gate **conditional CQRS code in legacy paths** behind this flag, so it must exist as soon as those handlers may be touched. The latest acceptable moment is controller wiring (steps 5/6); doing it now is the safe default.

## Why this step

- Conditional behavior in CQRS handlers (e.g. dispatch a new event only when the new page is enabled) needs a registered flag to read.
- Routes carrying `_legacy_feature_flag: {domain}` (added in steps 5/6) cause a 500 if the referenced flag entry does not exist.
- The flag is the rollback lever: a critical post-GA bug is fixed by toggling state to 0, no redeploy needed.

## Skill to invoke

| Skill | Produces |
|---|---|
| `register-feature-flag` | `feature_flag.xml` entry with `id={domain}`, `stability=beta`, `state=0`, plus admin label/description |

## Orchestration notes

- The flag name must be lowercase, snake_case, and exactly match what later routing YAML uses in `_legacy_feature_flag: {domain}`.
- After adding the XML entry, populate `ps_feature_flag` locally (installer fixtures or one-off SQL) so the flag is toggleable from the admin panel.
- Toggle the flag on locally — both sides (Symfony page once it exists, legacy page already) must remain reachable as the flag flips.
- Promotion to stable is a separate, dedicated PR (step 8). Do not change `stability` or `state` here.

## Gate to next step

- [ ] `feature_flag.xml` entry committed
- [ ] Local DB has the row (via installer or SQL)
- [ ] Flag is toggleable from **Advanced Parameters → New & Experimental Features**
- [ ] Legacy controller still reachable with flag disabled (no regressions introduced by the XML entry)

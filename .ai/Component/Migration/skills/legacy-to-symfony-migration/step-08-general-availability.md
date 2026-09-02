---
step: 8
title: "General Availability"
previous: step-07-playwright-tests.md
next: step-09-removal.md
deliverable: "feature_flag.xml updated to stability='stable' and state='1'; Playwright campaigns no longer require the flag enable; upgrade SQL handed off to autoupgrade if a DB change is needed"
---

# Step 8 — General Availability

GA is a dedicated PR. It contains no new features — only the flag promotion and the resulting Playwright cleanup. Mixing feature changes with the GA PR makes the promotion harder to revert if a critical bug is found.

## When to enter

- The page has been stable in beta for **at least one minor release** with no P1 regressions
- All Behat (step 4) and Playwright (step 7) campaigns are green
- QA has signed off

Timing is per-page judgment, not a fixed duration. Some pages reach GA quickly; complex ones take several releases.

## Skills to invoke

| Skill | Produces |
|---|---|
| `promote-feature-flag-to-stable` | `feature_flag.xml` updated to `stability=stable` / `state=1`; Playwright campaigns updated to no longer call `enableFeatureFlag` |
| `write-upgrade-sql-script` | An idempotent SQL script for shops upgrading to this version — **conditional**, only if a DB-side change is needed (flag state transition, schema change, fixture row). The script lands in the `autoupgrade` module repo, not in core. |

## Orchestration notes

- The GA PR contains **only** the flag promotion and the Playwright cleanup. Any incidental fix should go in a separate PR.
- The legacy controller must remain reachable with the flag manually disabled — verify this explicitly before merging.
- Upgrade SQL is a cross-repo handoff: open it (or hand it off) against `https://github.com/PrestaShop/autoupgrade`, not in this repo.

## Gate to next step

- [ ] `feature_flag.xml` has `stability=stable` and `state=1`
- [ ] Full Playwright suite passes without any `enableFeatureFlag` call
- [ ] Legacy controller still loads with `state=0` set manually
- [ ] PR description documents QA sign-off and rollback procedure
- [ ] Upgrade SQL handed off to `autoupgrade` (if needed)

---
name: promote-feature-flag-to-stable
description: >
  Promote the domain's feature flag from beta to stable (GA) and update Playwright
  tests to no longer require the flag. This is done in a dedicated GA PR with minimal
  changes. Trigger: "promote {Domain} to GA", "make {Domain} stable".
needs: [write-playwright-campaigns]
produces: "feature_flag.xml updated to stable/state=1 + Playwright tests updated"
---

# promote-feature-flag-to-stable

## Prerequisite

The feature flag must already exist (registered earlier as `beta`). This skill only promotes it; it does not create it.

## 1. Promote the feature flag

1. Verify GA prerequisites: page is stable, no P1 regressions in the last release.
2. Open `install-dev/data/xml/feature_flag.xml`
3. Find the `{domain}` entry
4. Change `<stability>beta</stability>` to `<stability>stable</stability>`
5. Change `<state>0</state>` to `<state>1</state>`
6. After merging: verify the flag is `stable/state=1` in a fresh install

## 2. Migrate Playwright tests to default

In the same PR (or immediately after):

1. For each Playwright campaign file:
   - Remove `enableFeatureFlag('{domain}')` calls from `before()` / `beforeAll`
   - Replace any legacy `index.php?controller=Admin{Domain}s` URLs with Symfony route paths
   - Update page object imports if HTML structure changed
2. Run the full campaign suite without the flag enable — all must pass
3. Remove the `enableFeatureFlag` call completely — do not leave it commented out

## Rules

- GA PR contains ONLY the flag promotion + test migration — zero feature changes
- Promote in a dedicated PR so it can be reverted instantly if a critical bug is found
- `stability=stable` + `state=1`: stable = shown in admin panel, state=1 = enabled by default
- Do not change any other flag entry in the file
- Playwright test suite must remain green after removing the flag enable

#!/usr/bin/env bash
#
# Release Healthcheck — Dependencies: modules & themes (env tier: composer + gh).
#
# Verifies that bundled PrestaShop packages are pinned to released tags (no dev
# branches), are up to date, and that the lock file is coherent.
# Provenance: section B (B1, B2, B7, B8) in issue #41804. B4/B5/B6 dropped (tautological/redundant).

# HC_GROUP is read by emit() in lib.sh (sourced separately) — silence cross-file SC2034.
# shellcheck disable=SC2034
HC_GROUP="Dependencies — modules & themes"
#
# NB: B4/B5 (theme "pinned & released") and B6 (api "pinned & released") were removed —
# "released"/"tag exists" is tautological (a release publishes the tag, so a lock pin implies
# it shipped), and "outdated"/"no dev pin" are already covered by B1/B2.

# spec ref: B1 — native modules up to date (excluding the blocked psgdpr V2)
check_native_modules_up_to_date() {
  local out names
  HC_LINK="https://github.com/$REPO/blob/$REF/composer.json"
  out=$(composer outdated -D -f json "prestashop/*" 2>/dev/null)
  names=$(printf '%s' "$out" | jq -r '.installed[]? | select(.name != "prestashop/psgdpr") | "\(.name) \(.version)→\(.latest)"' 2>/dev/null)
  if [ -z "$names" ]; then
    emit "deps/native-modules-up-to-date" "Native modules up to date" "$HC_PASS" "$HC_SEV_FAIL" "no outdated prestashop/* packages (psgdpr excluded)"
  else
    emit "deps/native-modules-up-to-date" "Native modules up to date" "$HC_FAIL" "$HC_SEV_FAIL" "outdated: $(printf '%s' "$names" | paste -sd'; ' -)"
  fi
}

# spec ref: B2 — no prestashop/* package pinned to a dev branch in the lock
check_no_dev_branch_pins() {
  local pins
  HC_LINK="https://github.com/$REPO/blob/$REF/composer.lock"
  pins=$(jq -r '(.packages + .["packages-dev"])[]? | select(.name|startswith("prestashop/")) | select((.version|test("^dev-")) or (.version|test("@dev$")) or (.version|test("-dev$"))) | "\(.name) (\(.version))"' composer.lock 2>/dev/null)
  if [ -z "$pins" ]; then
    emit "deps/no-dev-branch-pins" "No dev branches pinned" "$HC_PASS" "$HC_SEV_FAIL" "all prestashop/* packages pinned to released tags"
  else
    emit "deps/no-dev-branch-pins" "No dev branches pinned" "$HC_FAIL" "$HC_SEV_FAIL" "dev pins: $(printf '%s' "$pins" | paste -sd'; ' -)"
  fi
}

# spec ref: B7 — composer audit (security vulnerabilities). NB: the psgdpr exclusion applies
# only to module *updates* (B1), NOT to security audit — every advisory counts here.
check_composer_audit() {
  local out count
  out=$(composer audit --no-interaction 2>&1)
  if printf '%s' "$out" | grep -qiE 'No security vulnerability advisories found'; then
    emit "deps/composer-audit" "Composer audit" "$HC_PASS" "$HC_SEV_WARN" "no security advisories"
    return
  fi
  # Parse the canonical "Found N security vulnerability advisories affecting M packages" summary.
  count=$(printf '%s' "$out" | grep -oiE 'Found [0-9]+ security vulnerabilit' | grep -oE '[0-9]+' | head -1)
  if [ -n "$count" ]; then
    local pkgs; pkgs=$(printf '%s' "$out" | grep -oiE 'affecting [0-9]+ package' | grep -oE '[0-9]+' | head -1)
    emit "deps/composer-audit" "Composer audit" "$HC_FAIL" "$HC_SEV_WARN" "$count security advisory/ies affecting ${pkgs:-?} package(s)"
  else
    emit "deps/composer-audit" "Composer audit" "$HC_FAIL" "$HC_SEV_WARN" "could not parse composer audit output"
  fi
}

# spec ref: B8 — composer.lock coherent with composer.json
check_lock_json_in_sync() {
  local out rc
  HC_LINK="https://github.com/$REPO/blob/$REF/composer.json"
  out=$(composer validate --no-check-publish --no-interaction 2>&1); rc=$?
  if [ "$rc" -eq 0 ]; then
    emit "deps/lock-json-in-sync" "Lock ↔ json in sync" "$HC_PASS" "$HC_SEV_FAIL" "composer validate passed"
  else
    emit "deps/lock-json-in-sync" "Lock ↔ json in sync" "$HC_FAIL" "$HC_SEV_FAIL" "composer validate failed: $(printf '%s' "$out" | grep -iE 'lock|invalid|error' | head -n1)"
  fi
}

run_dependencies_checks() {
  HC_GROUP="Dependencies — modules & themes"
  guard check_native_modules_up_to_date
  guard check_no_dev_branch_pins
  guard check_composer_audit
  guard check_lock_json_in_sync
}

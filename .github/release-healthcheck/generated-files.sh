#!/usr/bin/env bash
#
# Release Healthcheck — Generated files in sync (env tiers: app + composer + translation).
#
# Pattern: regenerate a tracked, generated file and expect NO git diff. A diff means
# the committed artifact is stale. The working tree is restored afterwards (read-only contract).
# Provenance: these were section C (C1–C6) in issue #41804.
#
# Tier split (callers pick the matching run_* function):
#   app          : C1 hooks listing, C2 JS routing  (need a booted PrestaShop: PHP+MySQL+assets)
#   composer     : C3 translation fixtures, C4 license headers, C5 linters (no DB)
#   translation  : C6 default catalogue extraction (checks out the external TranslationTool)
#   static       : C7 localization packs sync with LocalizationFiles (git + public clone only)

# HC_GROUP is read by emit() in lib.sh (sourced separately) — silence cross-file SC2034.
# shellcheck disable=SC2034
HC_GROUP="Generated files in sync"

ge_9_1() { [ "$MAJOR" -gt 9 ] || { [ "$MAJOR" -eq 9 ] && [ "$MINOR" -ge 1 ]; }; }
ge_9_0() { [ "$MAJOR" -ge 9 ]; }

# _regen_no_diff <id> <title> <base_sev> <regen-command> [path-spec]
# Runs the regeneration command then asserts git is clean (over [path-spec], or whole tree).
_regen_no_diff() {
  local id="$1" title="$2" sev="$3" cmd="$4" paths="${5:-}" cmd_out
  if ! cmd_out="$(eval "$cmd" 2>&1)"; then
    emit "$id" "$title" "$HC_FAIL" "$sev" "regeneration command failed: $(printf '%s' "$cmd_out" | tail -n1)"
    return
  fi
  # shellcheck disable=SC2086  # paths is an intentional word-split path spec (may be empty)
  if git diff --quiet -- $paths 2>/dev/null; then
    emit "$id" "$title" "$HC_PASS" "$sev" "no diff after regeneration${paths:+ ($paths)}"
  else
    # shellcheck disable=SC2086
    local changed; changed="$(git diff --name-only -- $paths 2>/dev/null | paste -sd', ' -)"
    emit "$id" "$title" "$HC_FAIL" "$sev" "regeneration produced a diff: $changed"
    # shellcheck disable=SC2086
    git checkout -- $paths 2>/dev/null || true
  fi
}

# spec ref: C1 — hooks listing (hook.xml) regenerates clean
check_hooks_listing_in_sync() {
  _regen_no_diff "generated/hooks-listing" "Hooks listing (hook.xml)" "$HC_SEV_FAIL" \
    "php bin/console prestashop:update:configuration-file-hooks-listing"
}

# spec ref: C2 — FOS JS routing dump regenerates clean
check_js_routing_in_sync() {
  HC_LINK="https://github.com/$REPO/blob/$REF/admin-dev/themes/new-theme/js/fos_js_routes.json"
  _regen_no_diff "generated/js-routing" "FOS JS routing" "$HC_SEV_FAIL" \
    "php bin/console fos:js-routing:dump --format=json --target=admin-dev/themes/new-theme/js/fos_js_routes.json" \
    "admin-dev/themes/new-theme/js/fos_js_routes.json"
}

# spec ref: C3 — translation fixtures regenerate clean
check_translation_fixtures_in_sync() {
  _regen_no_diff "generated/translation-fixtures" "Translation fixtures" "$HC_SEV_FAIL" \
    "php bin/console prestashop:translation:check-fixtures"
}

# spec ref: C4 — license headers stamped (≥9.1 via header-stamp, else legacy command)
check_license_headers_in_sync() {
  if ge_9_1; then
    _regen_no_diff "generated/license-headers" "License headers" "$HC_SEV_WARN" "composer header-stamp-fix"
  else
    _regen_no_diff "generated/license-headers" "License headers" "$HC_SEV_WARN" "php bin/console prestashop:licenses:update"
  fi
}

# spec ref: C5 — security-attribute & legacy-link linters pass
check_linters_pass() {
  local sev; if ge_9_0; then sev="$HC_SEV_WARN"; else sev="$HC_SEV_FAIL"; fi
  local out rc
  out=$(php bin/console prestashop:linter:security-attribute find-missing 2>&1 && php bin/console prestashop:linter:legacy-link 2>&1); rc=$?
  if [ "$rc" -eq 0 ]; then
    emit "generated/linters" "Security & legacy-link linters" "$HC_PASS" "$sev" "both linters passed"
  else
    emit "generated/linters" "Security & legacy-link linters" "$HC_FAIL" "$sev" "linter reported issues: $(printf '%s' "$out" | tail -n1)"
  fi
}

# spec ref: C6 — default translation catalogue fully extracted (via external TranslationTool)
# Highest-risk check: clones PrestaShop/TranslationTool and replays extract+export. Any tooling
# failure degrades to a WARN ("could not verify") rather than crashing or hard-failing.
check_default_catalogue_extracted() {
  local id="generated/default-catalogue" title="Default catalogue fully extracted"
  local core_dir tool_dir latest_tag
  HC_LINK="https://github.com/$REPO/tree/$REF/translations/default"
  core_dir="$(pwd)"
  tool_dir="$(mktemp -d 2>/dev/null)" || { emit "$id" "$title" "$HC_FAIL" "$HC_SEV_WARN" "could not verify (no temp dir)"; return; }

  # PrestaShopCorp/TranslationTool is private — clone with the token (works upstream with
  # JARVIS_TOKEN; on forks/PRs without org access this fails and we degrade to ⚠️).
  local clone_url="https://github.com/PrestaShopCorp/TranslationTool"
  if [ -n "${GH_TOKEN:-}" ]; then
    clone_url="https://x-access-token:${GH_TOKEN}@github.com/PrestaShopCorp/TranslationTool"
  fi
  if ! git clone --quiet "$clone_url" "$tool_dir" 2>/dev/null; then
    emit "$id" "$title" "$HC_FAIL" "$HC_SEV_WARN" "could not verify (TranslationTool clone failed — needs PrestaShopCorp access)"; return
  fi
  # Same tag selection as the canonical "Create default catalog PR" workflow
  # (most recently created tag, not highest version-sorted name) — keep in sync.
  latest_tag="$(git -C "$tool_dir" describe --tags "$(git -C "$tool_dir" rev-list --tags --max-count=1 2>/dev/null)" 2>/dev/null)"
  [ -n "$latest_tag" ] && git -C "$tool_dir" checkout --quiet "$latest_tag" 2>/dev/null

  if ! ( cd "$tool_dir" && COMPOSER_PROCESS_TIMEOUT=600 composer install --no-dev --ansi --no-interaction --no-progress ) >/dev/null 2>&1; then
    emit "$id" "$title" "$HC_FAIL" "$HC_SEV_WARN" "could not verify (TranslationTool composer install failed)"; return
  fi

  if ! ( cd "$tool_dir" \
        && php bin/console prestashop:translation:extract "$core_dir/.t9n.yml" \
        && php bin/console prestashop:translation:export ) >/dev/null 2>&1; then
    emit "$id" "$title" "$HC_FAIL" "$HC_SEV_WARN" "could not verify (extract/export failed — tool ${latest_tag:-default})"; return
  fi

  local dump="$tool_dir/app/dumps/translatables/default"
  if [ ! -d "$dump" ]; then
    emit "$id" "$title" "$HC_FAIL" "$HC_SEV_WARN" "could not verify (no dump produced at app/dumps/translatables/default)"; return
  fi

  # Replace the catalogue with the freshly extracted one. Use the `src/.` → `dest/` idiom
  # (after a clean rm + mkdir) so we copy CONTENTS, never nesting default/default — this is
  # portable across GNU cp (Linux CI) and BSD cp (macOS), regardless of dest pre-existence.
  rm -rf "$core_dir/translations/default"
  mkdir -p "$core_dir/translations/default"
  cp -R "$dump/." "$core_dir/translations/default/"
  # `git status --porcelain` (not `git diff`) so brand-new catalogue files — untracked
  # after the rm+cp — count as drift too, exactly like the canonical workflow's `git add`.
  local drift
  drift="$(git -C "$core_dir" status --porcelain -- translations/ 2>/dev/null)"
  if [ -z "$drift" ]; then
    emit "$id" "$title" "$HC_PASS" "$HC_SEV_FAIL" "catalogue fully extracted (no diff under translations/)"
  else
    local n names
    n="$(printf '%s\n' "$drift" | wc -l | tr -d ' ')"
    names="$(printf '%s\n' "$drift" | awk '{print $NF}' | head -n 10 | paste -sd, - | sed 's/,/, /g')"
    [ "$n" -gt 10 ] && names="$names, …"
    emit "$id" "$title" "$HC_FAIL" "$HC_SEV_FAIL" "catalogue not fully extracted: $n file(s) differ — $names"
    # Full drift goes to the job log (stderr) so the mismatch is diagnosable.
    log "default-catalogue drift (git status --porcelain -- translations/):"
    printf '%s\n' "$drift" >&2
    git -C "$core_dir" diff --stat -- translations/ >&2 2>/dev/null || true
    git -C "$core_dir" diff -- translations/ 2>/dev/null | head -n 200 >&2 || true
  fi
  git -C "$core_dir" checkout -- translations/ 2>/dev/null || true
  git -C "$core_dir" clean -qfd translations/ 2>/dev/null || true
}

# spec ref: C7 — bundled localization packs in sync with PrestaShop/LocalizationFiles.
# Mirrors sync_localization_files.yml: copy every top-level *.xml pack from the source
# repo (copy-only — core-only packs and CLDR/.htaccess are never touched) and expect no
# git diff. A diff means the packs are stale: run the sync workflow on this branch.
check_localization_packs_in_sync() {
  local id="generated/localization-packs" title="Localization packs in sync"
  HC_LINK="https://github.com/$REPO/tree/$REF/localization"
  if ! ge_9_1; then
    emit "$id" "$title" "$HC_NA" "$HC_SEV_FAIL" "sync with LocalizationFiles only covers >= 9.1" ">= 9.1"
    return
  fi

  local src_dir
  src_dir="$(mktemp -d 2>/dev/null)" || { emit "$id" "$title" "$HC_FAIL" "$HC_SEV_WARN" "could not verify (no temp dir)"; return; }
  if ! git clone --quiet --depth 1 https://github.com/PrestaShop/LocalizationFiles "$src_dir" 2>/dev/null; then
    emit "$id" "$title" "$HC_FAIL" "$HC_SEV_WARN" "could not verify (LocalizationFiles clone failed)"; return
  fi

  find "$src_dir" -maxdepth 1 -name '*.xml' -exec cp {} localization/ \;
  if git diff --quiet -- localization/ 2>/dev/null; then
    emit "$id" "$title" "$HC_PASS" "$HC_SEV_FAIL" "no diff after copying packs from LocalizationFiles master"
  else
    local changed; changed="$(git diff --name-only -- localization/ 2>/dev/null | wc -l | tr -d ' ')"
    emit "$id" "$title" "$HC_FAIL" "$HC_SEV_FAIL" "$changed stale pack(s) — run the sync-localization-files workflow on $REF"
    git checkout -- localization/ 2>/dev/null || true
  fi
}

run_generated_files_app_checks() {
  HC_GROUP="Generated files in sync"
  guard check_hooks_listing_in_sync
  guard check_js_routing_in_sync
}

run_generated_files_deps_checks() {
  HC_GROUP="Generated files in sync"
  guard check_translation_fixtures_in_sync
  guard check_license_headers_in_sync
  guard check_linters_pass
}

run_generated_files_translation_checks() {
  HC_GROUP="Generated files in sync"
  guard check_default_catalogue_extracted
}

run_generated_files_static_checks() {
  HC_GROUP="Generated files in sync"
  guard check_localization_packs_in_sync
}

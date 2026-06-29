#!/usr/bin/env bash
#
# Release Healthcheck — Cross-repo / external readiness (env tier: gh/http).
#
# Read-only reachability of the satellite repos & PRs a release depends on. Everything
# here is report-only (WARN): we cannot block a build on another repo's state.
# Provenance: these were section F (F1–F5) in issue #41804.

# HC_GROUP is read by emit() in lib.sh (sourced separately) — silence cross-file SC2034.
# shellcheck disable=SC2034
HC_GROUP="Cross-repo / external readiness"

# spec ref: F1 — the max upgrade version for core_version is set in distribution-api's
# canonical file public/json/autoupgrade.json. PR title/body text is unreliable, so assert the
# FILE CONTENT: either the merged file already lists the version as a prestashop_max, or an
# OPEN PR's diff adds it. Never relies on the PR title.
check_autoupgrade_max_version_pr() {
  local id="cross-repo/autoupgrade-max-version-pr" title="Autoupgrade max-version" af="public/json/autoupgrade.json" maxes n patch
  # Default verify link: the canonical file (used by the merged + not-found outcomes).
  HC_LINK="https://github.com/PrestaShop/distribution-api/blob/HEAD/$af"
  # 1. Merged: the live file lists the version as a max upgrade target.
  maxes=$(gh api "repos/PrestaShop/distribution-api/contents/$af" -q '.content' 2>/dev/null | base64 -d 2>/dev/null | jq -r '.prestashop[]?.prestashop_max' 2>/dev/null)
  if printf '%s\n' "$maxes" | grep -qx "$CORE_VERSION"; then
    emit "$id" "$title" "$HC_PASS" "$HC_SEV_WARN" "$af lists $CORE_VERSION as a max upgrade target"
    return
  fi
  # 2. In progress: an open PR's diff to that file adds the version.
  for n in $(gh pr list --repo PrestaShop/distribution-api --state open --json number -q '.[].number' 2>/dev/null); do
    patch=$(gh api "repos/PrestaShop/distribution-api/pulls/$n/files" -q ".[] | select(.filename==\"$af\") | .patch" 2>/dev/null)
    if printf '%s' "$patch" | grep -E '^\+' | grep -qF "\"$CORE_VERSION\""; then
      HC_LINK="https://github.com/PrestaShop/distribution-api/pull/$n"
      emit "$id" "$title" "$HC_PASS" "$HC_SEV_WARN" "open PR #$n adds $CORE_VERSION to $af (pending merge)"
      return
    fi
  done
  emit "$id" "$title" "$HC_FAIL" "$HC_SEV_WARN" "no distribution-api change adds $CORE_VERSION to $af (critical for security releases)"
}

# spec ref: F2 — autoupgrade ships hook SQL for core_version. The module carries a per-version
# file upgrade/sql/<version>.sql ONLY when schema/hooks changed; inspect its content for a
# hook INSERT rather than guessing from PR text. Absence ⇒ no DB changes for this version.
check_autoupgrade_sql_hooks() {
  local id="cross-repo/autoupgrade-sql-hooks" title="Autoupgrade SQL hooks" sql
  sql=$(gh api "repos/PrestaShop/autoupgrade/contents/upgrade/sql/${CORE_VERSION}.sql" -q '.content' 2>/dev/null | base64 -d 2>/dev/null)
  if [ -z "$sql" ]; then
    HC_LINK="https://github.com/PrestaShop/autoupgrade/tree/dev/upgrade/sql"
    emit "$id" "$title" "$HC_NA" "$HC_SEV_WARN" "no upgrade/sql/${CORE_VERSION}.sql in autoupgrade — no schema/hook changes for this version"
    return
  fi
  HC_LINK="https://github.com/PrestaShop/autoupgrade/blob/dev/upgrade/sql/${CORE_VERSION}.sql"
  # The backticks below are literal SQL identifiers, intentionally inside single quotes.
  # shellcheck disable=SC2016
  if printf '%s' "$sql" | grep -qiE 'INSERT INTO `?PREFIX_hook`?'; then
    emit "$id" "$title" "$HC_PASS" "$HC_SEV_WARN" "autoupgrade upgrade/sql/${CORE_VERSION}.sql includes hook INSERTs"
  else
    emit "$id" "$title" "$HC_PASS" "$HC_SEV_WARN" "autoupgrade upgrade/sql/${CORE_VERSION}.sql present (no hook INSERTs)"
  fi
}

# NB: F3 (theme releases published) and F4 (api release published) were removed — both are
# tautological: a GitHub release publishes the tag, so once a version is pinned in composer.lock
# it has necessarily already shipped. The meaningful theme/api invariants live in B1/B2/B6.

# spec ref: F5 — docs major branch exists (major releases only)
check_devdoc_major_branch() {
  if [ "$RELEASE_TYPE" != "major" ]; then
    emit "cross-repo/devdoc-major-branch" "Devdoc branch (major)" "$HC_NA" "$HC_SEV_WARN" "only applies to major releases (this is $RELEASE_TYPE)"
    return
  fi
  HC_LINK="https://github.com/PrestaShop/docs/tree/$MAJOR"
  if gh_json "repos/PrestaShop/docs/branches/${MAJOR}" '.name' | grep -q "$MAJOR" \
     || gh_json "repos/PrestaShop/docs/branches/${MAJOR}.x" '.name' | grep -q "$MAJOR"; then
    emit "cross-repo/devdoc-major-branch" "Devdoc branch (major)" "$HC_PASS" "$HC_SEV_WARN" "PrestaShop/docs has a branch for major $MAJOR"
  else
    emit "cross-repo/devdoc-major-branch" "Devdoc branch (major)" "$HC_FAIL" "$HC_SEV_WARN" "no PrestaShop/docs branch for major $MAJOR"
  fi
}

run_cross_repo_readiness_checks() {
  HC_GROUP="Cross-repo / external readiness"
  guard check_autoupgrade_max_version_pr
  guard check_autoupgrade_sql_hooks
  guard check_devdoc_major_branch
}

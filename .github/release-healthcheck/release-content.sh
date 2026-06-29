#!/usr/bin/env bash
#
# Release Healthcheck — Release content artifacts (env tier: static).
#
# Verifies the human-authored release artifacts: changelog and contributors.
# Provenance: these were section D (D1–D3) in issue #41804.

# HC_GROUP is read by emit() in lib.sh (sourced separately) — silence cross-file SC2034.
# shellcheck disable=SC2034
HC_GROUP="Release content artifacts"

CHANGELOG_FILE="docs/CHANGELOG.txt"   # NB: under docs/, not repo root

# spec ref: D1 — CHANGELOG.txt has an entry for core_version (header e.g. "#   v9.1.4 - (2026-06-03)")
check_changelog_updated() {
  HC_LINK="https://github.com/$REPO/blob/$REF/$CHANGELOG_FILE"
  if [ ! -f "$CHANGELOG_FILE" ]; then
    emit "release/changelog-updated" "Changelog updated" "$HC_FAIL" "$HC_SEV_FAIL" "$CHANGELOG_FILE not found"
    return
  fi
  if grep -qF "v$CORE_VERSION" "$CHANGELOG_FILE"; then
    emit "release/changelog-updated" "Changelog updated" "$HC_PASS" "$HC_SEV_FAIL" "$CHANGELOG_FILE has an entry for v$CORE_VERSION"
  else
    emit "release/changelog-updated" "Changelog updated" "$HC_FAIL" "$HC_SEV_FAIL" "no v$CORE_VERSION entry in $CHANGELOG_FILE"
  fi
}

# NB: D2 (contributors updated) was removed — it only checked CONTRIBUTORS.md exists (always
# true), and a release can legitimately add no new contributors, so it carried no signal.

# spec ref: D3 — the changelog commit is HEAD (the tag should point at it)
check_changelog_is_last_commit() {
  local subject sha
  sha="$(git rev-parse HEAD 2>/dev/null)"
  HC_LINK="https://github.com/$REPO/commit/$sha"
  subject="$(git log -1 --pretty=%s 2>/dev/null)"
  if printf '%s' "$subject" | grep -qiE 'changelog'; then
    emit "release/changelog-last-commit" "Changelog is last commit" "$HC_PASS" "$HC_SEV_WARN" "HEAD subject: $subject"
  else
    emit "release/changelog-last-commit" "Changelog is last commit" "$HC_FAIL" "$HC_SEV_WARN" "HEAD subject is not a changelog commit: $subject"
  fi
}

run_release_content_checks() {
  HC_GROUP="Release content artifacts"
  guard check_changelog_updated
  guard check_changelog_is_last_commit
}

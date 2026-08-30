#!/usr/bin/env bash
#
# Release Healthcheck — Quality gates / CI (env tier: gh).
#
# Verifies CI is green on the inspected HEAD commit.
# Provenance: this was section E (E1) in issue #41804.
#
# No nightly/UI check here on purpose: nightly runs only on version branches (not
# build branches) and UI tests are run manually per PR.

# HC_GROUP is read by emit() in lib.sh (sourced separately) — silence cross-file SC2034.
# shellcheck disable=SC2034
HC_GROUP="Quality gates / CI"
# REPO is the core repo under inspection — the one running the workflow (github.repository),
# which on a private security mirror is NOT the canonical upstream.
REPO="${REPO:-PrestaShop/PrestaShop}"

# spec ref: E1 — CI green on HEAD
# Judge from check-runs (the mechanism PrestaShop CI actually uses). The legacy combined
# Status API returns "pending" when nothing posts commit statuses, so it is NOT treated as
# authoritative — only an explicit "failure" there counts against the commit.
check_ci_green_on_head() {
  local sha runs failing pending state
  sha="$(git rev-parse HEAD 2>/dev/null)"
  if [ -z "$sha" ]; then
    emit "ci/green-on-head" "CI green on HEAD" "$HC_FAIL" "$HC_SEV_FAIL" "could not resolve HEAD sha"
    return
  fi
  HC_LINK="https://github.com/$REPO/commit/$sha/checks"

  runs=$(gh api "repos/$REPO/commits/$sha/check-runs?per_page=100" 2>/dev/null)
  failing=$(printf '%s' "$runs" | jq '[.check_runs[]? | select(.conclusion=="failure" or .conclusion=="cancelled" or .conclusion=="timed_out")] | length' 2>/dev/null)
  pending=$(printf '%s' "$runs" | jq '[.check_runs[]? | select(.status!="completed")] | length' 2>/dev/null)
  state=$(gh_json "repos/$REPO/commits/$sha/status" '.state')
  failing="${failing:-0}"; pending="${pending:-0}"; state="${state:-unknown}"

  if [ "$failing" -gt 0 ] || [ "$pending" -gt 0 ] || [ "$state" = "failure" ]; then
    emit "ci/green-on-head" "CI green on HEAD" "$HC_FAIL" "$HC_SEV_FAIL" "HEAD ${sha:0:8}: $failing failing, $pending pending check-run(s), status=$state"
  else
    emit "ci/green-on-head" "CI green on HEAD" "$HC_PASS" "$HC_SEV_FAIL" "HEAD ${sha:0:8}: no failing/pending check-runs (status=$state)"
  fi
}

run_ci_status_checks() {
  HC_GROUP="Quality gates / CI"
  guard check_ci_green_on_head
}

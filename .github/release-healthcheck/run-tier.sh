#!/usr/bin/env bash
#
# Release Healthcheck — run-tier.sh
#
# Entry point for one environment tier. Sources the relevant section scripts and
# runs their checks, appending JSON-line results to results-<tier>.json.
#
# Usage: run-tier.sh <static|deps|app|translation|gh>
#
# Expects the derived values in the environment (set by the workflow from the
# `derive` job outputs):
#   REF TARGET_VERSION CORE_VERSION MAJOR MINOR PATCH VERSION_BRANCH
#   RELEASE_TYPE BRANCH_KIND BUILD_BRANCH RELEASE_PUBLISHED CLASSIC_RELEASED
#   REPO (inspected repo) · UPSTREAM_REPO (public release target)
#
# This script is read-only with respect to the repository and all remotes.

# NB: no `pipefail` — checks use `cmd | grep -q` extensively, and pipefail turns the
# SIGPIPE that grep -q sends upstream into a false-negative for the whole pipeline.
set -u

TIER="${1:?usage: run-tier.sh <static|deps|app|translation|gh>}"
SELF_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

# Results sink for this tier (consumed later by render.sh).
export HC_RESULTS_FILE="${HC_RESULTS_FILE:-results-${TIER}.json}"
: > "$HC_RESULTS_FILE"

# shellcheck source=lib.sh
. "$SELF_DIR/lib.sh"

# Validate the derived environment contract up front.
: "${REF:?REF is required}"
: "${TARGET_VERSION:?TARGET_VERSION is required}"
: "${CORE_VERSION:?CORE_VERSION is required}"
: "${MAJOR:?}" "${MINOR:?}" "${PATCH:?}"
: "${VERSION_BRANCH:?}" "${RELEASE_TYPE:?}" "${BRANCH_KIND:?}" "${BUILD_BRANCH:?}"
: "${RELEASE_PUBLISHED:?}" "${CLASSIC_RELEASED:?}"
# REPO = inspected repo (github.repository); UPSTREAM_REPO = public release target.
export REPO="${REPO:-PrestaShop/PrestaShop}"
export UPSTREAM_REPO="${UPSTREAM_REPO:-PrestaShop/PrestaShop}"

log "Running tier '$TIER' for $CORE_VERSION on $REF ($BRANCH_KIND) → $HC_RESULTS_FILE"

case "$TIER" in
  static)
    . "$SELF_DIR/version-integrity.sh"
    . "$SELF_DIR/release-content.sh"
    run_version_integrity_checks
    run_release_content_checks
    ;;
  deps)
    . "$SELF_DIR/dependencies.sh"
    . "$SELF_DIR/generated-files.sh"
    run_dependencies_checks
    run_generated_files_deps_checks
    ;;
  app)
    . "$SELF_DIR/generated-files.sh"
    run_generated_files_app_checks
    ;;
  translation)
    . "$SELF_DIR/generated-files.sh"
    run_generated_files_translation_checks
    ;;
  gh)
    . "$SELF_DIR/ci-status.sh"
    . "$SELF_DIR/cross-repo-readiness.sh"
    . "$SELF_DIR/post-release.sh"
    run_ci_status_checks
    run_cross_repo_readiness_checks
    run_post_release_checks
    ;;
  *)
    log "FATAL: unknown tier '$TIER'"
    exit 2
    ;;
esac

log "Tier '$TIER' produced $(wc -l < "$HC_RESULTS_FILE" | tr -d ' ') result(s)"

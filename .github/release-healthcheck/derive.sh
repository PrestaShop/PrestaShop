#!/usr/bin/env bash
#
# Release Healthcheck — derive.sh
#
# Computes every derived value and runtime flag from the two real inputs
# (branch, target_version) and writes them as `key=value` lines to stdout AND,
# when running inside GitHub Actions, to $GITHUB_OUTPUT so downstream jobs can
# consume them via `needs.derive.outputs.*`.
#
# Inputs (environment):
#   HC_REF             the ref being inspected — a branch OR a tag (workflow input `ref`)
#   HC_TARGET_VERSION  the anticipated/target full version (workflow input `target_version`)
#
# Runtime flags (release_published, classic_released) require a checkout with
# tags/refs available and `gh` authenticated; they degrade to false if unknown.

# No `pipefail`: classic_released greps a piped release list with grep -q, whose early
# SIGPIPE would otherwise fail the pipeline (false negative).
set -u

SELF_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
# shellcheck source=lib.sh
. "$SELF_DIR/lib.sh"

REF="${HC_REF:?HC_REF is required}"
TARGET_VERSION="${HC_TARGET_VERSION:?HC_TARGET_VERSION is required}"
# UPSTREAM_REPO = the PUBLIC release target where the GitHub Release lands and from which
# the public ecosystem propagates. Distinct from the inspected repo (github.repository),
# because a private security mirror still publishes its release to the public org.
UPSTREAM_REPO="${UPSTREAM_REPO:-PrestaShop/PrestaShop}"

out() {
  echo "$1=$2"
  if [ -n "${GITHUB_OUTPUT:-}" ]; then echo "$1=$2" >> "$GITHUB_OUTPUT"; fi
}

# --- core_version + components -------------------------------------------------
if ! parse_semver "$TARGET_VERSION"; then
  log "FATAL: target_version '$TARGET_VERSION' is not a valid version"
  exit 1
fi
CORE_VERSION="$SV_CORE"
MAJOR="$SV_MAJOR"; MINOR="$SV_MINOR"; PATCH="$SV_PATCH"
VERSION_BRANCH="${MAJOR}.${MINOR}.x"

# --- release_type --------------------------------------------------------------
# pre-release suffix wins; else patch>0 ⇒ patch; else minor>0 ⇒ minor; else major.
if [ -n "${SV_PRERELEASE_LABEL:-}" ]; then
  RELEASE_TYPE="pre-release"
elif [ "$PATCH" -gt 0 ]; then
  RELEASE_TYPE="patch"
elif [ "$MINOR" -gt 0 ]; then
  RELEASE_TYPE="minor"
else
  RELEASE_TYPE="major"
fi

# --- branch_kind + canonical build branch name --------------------------------
# Build branch convention (see .github/workflows/create-build-branch.yml):
#   build-<major><minor><patch>           e.g. build-915
#   build-<major><minor><patch>-<label><n> e.g. build-920-beta1
if [ -n "${SV_PRERELEASE_LABEL:-}" ]; then
  BUILD_BRANCH="build-${MAJOR}${MINOR}${PATCH}-${SV_PRERELEASE_LABEL}${SV_PRERELEASE_NUM}"
else
  BUILD_BRANCH="build-${MAJOR}${MINOR}${PATCH}"
fi

# Classify the ref. `tag` (a full-semver ref, i.e. a released/RC tag) grades strictly like
# `build`; only `dev` (version branch / develop) downgrades FAILs to warnings.
if [[ "$REF" =~ ^build-[0-9]+(-[a-z]+[0-9]+)?$ ]]; then
  BRANCH_KIND="build"
elif [[ "$REF" =~ ^[0-9]+\.[0-9]+\.x$ ]] || [ "$REF" = "develop" ]; then
  BRANCH_KIND="dev"
elif [[ "$REF" =~ ^[0-9]+\.[0-9]+\.[0-9]+(-(alpha|beta|rc)\.[0-9]+)?$ ]]; then
  BRANCH_KIND="tag"
else
  # Unknown shape (a feature branch, a SHA, …). Treat as build so gates stay strict.
  BRANCH_KIND="build"
fi

# --- runtime flags -------------------------------------------------------------
# release_published: a non-draft GH release for CORE_VERSION exists on the public target.
RELEASE_PUBLISHED="false"
if gh_release_published "$UPSTREAM_REPO" "$CORE_VERSION"; then
  RELEASE_PUBLISHED="true"
fi

# classic_released: a non-draft PrestaShopCorp/prestashop-classic release exists for the version.
# Classic appends its own scope version to the tag (e.g. 9.1.4 ships as "9.1.4-5.0"), so match
# the core version as a prefix. Best-effort: needs token access to the private repo.
CLASSIC_RELEASED="false"
if gh release list --repo PrestaShopCorp/prestashop-classic --limit 100 --json tagName,isDraft \
     -q '.[] | select(.isDraft==false) | .tagName' 2>/dev/null | grep -qE "^${CORE_VERSION}(-|\$)"; then
  CLASSIC_RELEASED="true"
fi

out core_version       "$CORE_VERSION"
out major              "$MAJOR"
out minor              "$MINOR"
out patch              "$PATCH"
out version_branch     "$VERSION_BRANCH"
out release_type       "$RELEASE_TYPE"
out branch_kind        "$BRANCH_KIND"
out build_branch       "$BUILD_BRANCH"
out release_published  "$RELEASE_PUBLISHED"
out classic_released   "$CLASSIC_RELEASED"

log "Derived: core=$CORE_VERSION type=$RELEASE_TYPE branch_kind=$BRANCH_KIND build_branch=$BUILD_BRANCH published=$RELEASE_PUBLISHED classic=$CLASSIC_RELEASED"

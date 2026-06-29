#!/usr/bin/env bash
#
# Release Healthcheck — shared library.
#
# Sourced by every check script and by run-tier.sh / derive.sh.
# Provides:
#   - emit()            : append one structured result (JSON line) to $HC_RESULTS_FILE
#   - the PASS/FAIL/NA/PENDING raw states and FAIL/WARN base severities
#   - parse_semver()    : split a version string into its components
#   - gh_*/http_* helpers (read-only, never fatal)
#   - guard()           : run a check function, converting an unexpected crash into a WARN
#
# This library performs NO mutation of the repository or any remote — it only reads.

# The HC_*/SV_* constants and parse_semver outputs below are part of this library's
# API: they are consumed by the section check scripts that source this file. shellcheck
# cannot follow that cross-file usage, so SC2034 ("appears unused") is silenced here.
# shellcheck disable=SC2034

# Raw result states a check may report.
readonly HC_PASS="PASS"       # the thing is in the expected state
readonly HC_FAIL="FAIL"       # the thing is NOT in the expected state
readonly HC_NA="NA"           # the check does not apply to this run
readonly HC_PENDING="PENDING" # the check is publication-gated and not yet gradable

# Base severities. The effective severity (and whether a FAIL gates the run) is
# decided at render time, where the dev-branch downgrade is applied in one place.
readonly HC_SEV_FAIL="FAIL"   # release-blocking gate on a build branch
readonly HC_SEV_WARN="WARN"   # report-only

# Per-script section title. Each check script sets this before defining its checks.
HC_GROUP="${HC_GROUP:-Uncategorised}"

# Destination for emitted results (one compact JSON object per line).
HC_RESULTS_FILE="${HC_RESULTS_FILE:-results.json}"

log() { echo "[healthcheck] $*" >&2; }

# emit <id> <title> <raw> <base_sev> <detail> [applies]
#
# <id>       semantic slug, e.g. "version/core-constants"
# <title>    human label shown in the checklist
# <raw>      one of HC_PASS|HC_FAIL|HC_NA|HC_PENDING
# <base_sev> one of HC_SEV_FAIL|HC_SEV_WARN
# <detail>   one-line explanation (actual vs expected, etc.)
# <applies>  optional free text describing scope (default "All")
#
# Optional: set HC_LINK to a URL before calling emit to attach a manual-verify link
# (rendered in the report's "Verify" column). It is consumed and reset on every call.
emit() {
  local id="$1" title="$2" raw="$3" sev="$4" detail="$5" applies="${6:-All}" link="${HC_LINK:-}"
  jq -nc \
    --arg id "$id" \
    --arg group "$HC_GROUP" \
    --arg title "$title" \
    --arg raw "$raw" \
    --arg sev "$sev" \
    --arg detail "$detail" \
    --arg applies "$applies" \
    --arg link "$link" \
    '{id:$id, group:$group, title:$title, raw:$raw, base_sev:$sev, detail:$detail, applies:$applies, link:$link}' \
    >> "$HC_RESULTS_FILE"
  log "$id -> $raw ($sev): $detail"
  HC_LINK=""
}

# guard <check_function>
#
# Runs a check in a subshell so a crash (set -e abort, unexpected non-zero) cannot
# kill the whole tier. If the function exits non-zero WITHOUT having emitted a line,
# we record a "could not verify" WARN so the slot is never silently dropped.
guard() {
  local fn="$1"
  local before after
  before="$(wc -l < "$HC_RESULTS_FILE" 2>/dev/null || echo 0)"
  ( set +e; "$fn" ) || true
  after="$(wc -l < "$HC_RESULTS_FILE" 2>/dev/null || echo 0)"
  if [ "$after" -le "$before" ]; then
    emit "internal/${fn}" "$fn" "$HC_FAIL" "$HC_SEV_WARN" "check crashed before emitting a result"
  fi
}

# parse_semver <version>
# Sets globals: SV_MAJOR SV_MINOR SV_PATCH SV_PRERELEASE_LABEL SV_PRERELEASE_NUM SV_CORE
# Accepts "9.1.5", "9.2.0-beta.1", "9.2.0-rc.2", "9.2.0-alpha.1". Returns non-zero on garbage.
parse_semver() {
  local v="$1"
  if [[ "$v" =~ ^([0-9]+)\.([0-9]+)\.([0-9]+)(-(alpha|beta|rc)\.([0-9]+))?$ ]]; then
    SV_MAJOR="${BASH_REMATCH[1]}"
    SV_MINOR="${BASH_REMATCH[2]}"
    SV_PATCH="${BASH_REMATCH[3]}"
    SV_PRERELEASE_LABEL="${BASH_REMATCH[5]}"
    SV_PRERELEASE_NUM="${BASH_REMATCH[6]}"
    SV_CORE="${SV_MAJOR}.${SV_MINOR}.${SV_PATCH}"
    return 0
  fi
  return 1
}

# php_const_string <file> <CONST>  -> echoes the single-quoted string value of `const CONST = '...'`
php_const_string() {
  sed -nE "s/.*${2} = '([^']*)';.*/\1/p" "$1" | head -n1
}

# php_const_int <file> <CONST>  -> echoes the integer value of `const CONST = N;`
php_const_int() {
  sed -nE "s/.*${2} = ([0-9]+);.*/\1/p" "$1" | head -n1
}

# php_define_string <file> <NAME>  -> echoes value of define('NAME', '...')
php_define_string() {
  sed -nE "s/.*define\('${2}', '([^']*)'\).*/\1/p" "$1" | head -n1
}

# --- GitHub / HTTP read-only helpers (never fatal) -----------------------------

# gh_json <api-path> [jq-filter]  -> echoes filtered JSON, or empty on any error.
gh_json() {
  local path="$1" filter="${2:-.}"
  gh api "$path" 2>/dev/null | jq -r "$filter" 2>/dev/null || true
}

# gh_tag_exists <owner/repo> <tag>  -> returns 0 if the tag ref exists (tries with and without a leading "v").
gh_tag_exists() {
  local repo="$1" tag="$2"
  if gh api "repos/${repo}/git/refs/tags/${tag}" >/dev/null 2>&1; then return 0; fi
  if gh api "repos/${repo}/git/refs/tags/v${tag}" >/dev/null 2>&1; then return 0; fi
  return 1
}

# gh_release_published <owner/repo> <tag>  -> returns 0 if a NON-draft release exists for the tag (tries "v" too).
gh_release_published() {
  local repo="$1" tag="$2" draft
  draft="$(gh release view "$tag" --repo "$repo" --json isDraft -q '.isDraft' 2>/dev/null)"
  if [ "$draft" = "false" ]; then return 0; fi
  draft="$(gh release view "v$tag" --repo "$repo" --json isDraft -q '.isDraft' 2>/dev/null)"
  if [ "$draft" = "false" ]; then return 0; fi
  return 1
}

# http_get <url>  -> echoes the body on HTTP 2xx, otherwise empty (non-fatal).
http_get() {
  curl -fsSL --max-time 30 "$1" 2>/dev/null || true
}

# http_body_contains <url> <needle>  -> returns 0 if a successful fetch contains <needle>.
# Uses a here-string (not a pipe) so grep -q's early exit can't SIGPIPE-fail curl.
http_body_contains() {
  grep -qF -- "$2" <<<"$(http_get "$1")"
}

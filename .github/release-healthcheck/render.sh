#!/usr/bin/env bash
#
# Release Healthcheck — render.sh
#
# Aggregates the per-tier results (JSON lines) into a single grouped Markdown
# checklist, applies the dev-branch severity downgrade, and prints the report to
# stdout (the workflow appends it to $GITHUB_STEP_SUMMARY).
#
# Usage: render.sh [results-file ...]   (defaults to results-*.json in CWD)
#
# Effective emoji per result:
#   PASS                                   -> ✅
#   NA | PENDING                           -> ➖
#   FAIL & base_sev=FAIL & branch_kind!=dev -> ❌ (release-blocking, shown in the verdict)
#   FAIL (any other case)                  -> ⚠️ (report-only)
#
# The full report is printed to stdout FIRST, then the script exits 1 if any ❌ is present
# (else 0). Because the report is emitted before the exit, it is always displayed (and the
# summary job runs with `if: always()`), yet the run is only green when everything is green.

set -uo pipefail

SELF_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
# shellcheck source=lib.sh
. "$SELF_DIR/lib.sh"

BRANCH_KIND="${BRANCH_KIND:-build}"

# Collect input files.
if [ "$#" -gt 0 ]; then
  FILES=("$@")
else
  # shellcheck disable=SC2206
  FILES=(results-*.json)
fi

COMBINED="$(mktemp 2>/dev/null || echo combined-results.json)"
: > "$COMBINED"
for f in "${FILES[@]}"; do
  [ -f "$f" ] && cat "$f" >> "$COMBINED"
done

if [ ! -s "$COMBINED" ]; then
  echo "No healthcheck results found (looked in: ${FILES[*]})."
  exit 1
fi

# jq snippet computing the effective emoji for one result object given $bk.
# $bk is a jq parameter, not a shell variable — single quotes are intentional.
# shellcheck disable=SC2016
EMOJI_DEF='def emoji($bk):
  if .raw=="PASS" then "✅"
  elif .raw=="NA" or .raw=="PENDING" then "➖"
  elif .raw=="FAIL" then (if (.base_sev=="FAIL" and $bk!="dev") then "❌" else "⚠️" end)
  else "❓" end;'

# Tallies.
count() { jq -s "$EMOJI_DEF"' [.[] | emoji($bk)] | map(select(. == $e)) | length' --arg bk "$BRANCH_KIND" --arg e "$1" "$COMBINED"; }
N_PASS=$(count "✅"); N_FAIL=$(count "❌"); N_WARN=$(count "⚠️"); N_NA=$(count "➖")

# The full report is printed first (below), THEN the script exits non-zero when any ❌ is
# present — so the report is always displayed, but the run is only green when everything is.
if [ "${N_FAIL:-0}" -gt 0 ]; then
  VERDICT="❌ FAIL"
  EXIT_CODE=1
else
  VERDICT="✅ PASS"
  EXIT_CODE=0
fi

case "$BRANCH_KIND" in
  dev) MODE="dev (anticipatory — failures shown as ⚠️)" ;;
  tag) MODE="tag (released ref)" ;;
  *)   MODE="build" ;;
esac

# --- Markdown report -----------------------------------------------------------
{
  echo "## 🩺 Release Healthcheck — ${CORE_VERSION:-?} on \`${REF:-?}\`"
  echo
  echo "**Target:** \`${TARGET_VERSION:-?}\` · **Type:** ${RELEASE_TYPE:-?} · **Mode:** ${MODE}"
  echo "**Release published:** ${RELEASE_PUBLISHED:-?} · **Classic released:** ${CLASSIC_RELEASED:-?}"
  echo
  echo "**Result: ${VERDICT}** — ✅ ${N_PASS} · ❌ ${N_FAIL} · ⚠️ ${N_WARN} · ➖ ${N_NA}"
  echo

  # Fixed section order (A → G). NB: do NOT name this `GROUPS` — that is a bash
  # special variable (the caller's Unix group IDs) and cannot be reassigned.
  SECTION_ORDER=(
    "Version & branch integrity"
    "Dependencies — modules & themes"
    "Generated files in sync"
    "Release content artifacts"
    "Quality gates / CI"
    "Cross-repo / external readiness"
    "Post-release verification"
  )
  for g in "${SECTION_ORDER[@]}"; do
    local_rows=$(jq -rs "$EMOJI_DEF"'
      map(select(.group==$g))
      | sort_by(.id)
      | .[]
      | "| \(emoji($bk)) | \(.title) | \(.detail) | \(if (.link // "") != "" then "[🔗](\(.link))" else "" end) |"' \
      --arg bk "$BRANCH_KIND" --arg g "$g" "$COMBINED")
    [ -z "$local_rows" ] && continue
    echo "### ${g}"
    echo "| | Check | Detail | Verify |"
    echo "|---|---|---|---|"
    echo "$local_rows"
    echo
  done

  # Any result whose group is not in the fixed list (defensive: internal crashes, new sections).
  other=$(jq -rs "$EMOJI_DEF"'
    map(select(.group as $g | ["Version & branch integrity","Dependencies — modules & themes","Generated files in sync","Release content artifacts","Quality gates / CI","Cross-repo / external readiness","Post-release verification"] | index($g) | not))
    | sort_by(.id) | .[]
    | "| \(emoji($bk)) | \(.title) | \(.detail) | \(if (.link // "") != "" then "[🔗](\(.link))" else "" end) |"' \
    --arg bk "$BRANCH_KIND" "$COMBINED")
  if [ -n "$other" ]; then
    echo "### Other"
    echo "| | Check | Detail | Verify |"
    echo "|---|---|---|---|"
    echo "$other"
    echo
  fi
}

log "Verdict: $VERDICT (exit $EXIT_CODE) — pass=$N_PASS fail=$N_FAIL warn=$N_WARN na=$N_NA"
exit "$EXIT_CODE"

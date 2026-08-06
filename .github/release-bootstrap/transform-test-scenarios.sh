#!/usr/bin/env bash
#
# PrestaShop/test-scenarios version-branch bootstrap transform.
# Adds the new version branch as a "core" entry in config.json and a matching
# checkout step in gh-pages.yml. ADD-only and idempotent. cwd = checked-out repo.
#
set -euo pipefail
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
# shellcheck source=lib.sh
source "${SCRIPT_DIR}/lib.sh"

SLUG="prestashop_${NEW//./}"   # 9.2.x -> prestashop_92x

# 1) config.json: insert a core item for NEW right after the 'develop' entry
#    (always present), so the script keeps working once 9.1.x is removed.
cfg="config.json"
if [ -f "${cfg}" ] && [ "$(jq --arg b "${NEW}" 'any(.items[]; .branch == $b)' "${cfg}")" != "true" ]; then
  obj="$(jq -n --arg p "./${SLUG}/" --arg b "${NEW}" \
    '{repository:"PrestaShop/PrestaShop", path:$p, branch:$b, title:$b, type:"core"}')"
  jq --argjson obj "${obj}" '
    .items as $a
    | (.items | map(.branch == "develop") | index(true)) as $d
    | .items = (if $d != null then $a[0:($d+1)] + [$obj] + $a[($d+1):] else [$obj] + $a end)
  ' "${cfg}" > "${cfg}.tmp" && mv "${cfg}.tmp" "${cfg}"
  if [ "$(jq --arg b "${NEW}" 'any(.items[]; .branch == $b)' "${cfg}")" != "true" ]; then
    echo "::error::test-scenarios: failed to insert ${NEW} into config.json items" >&2
    exit 1
  fi
  log "config.json: inserted core item ${NEW}"
fi

# 2) gh-pages.yml: insert a Checkout step for NEW before the first 9.x checkout
ghp=".github/workflows/gh-pages.yml"
if [ -f "${ghp}" ] && ! grep -qF -- "path: ${SLUG}" "${ghp}"; then
  block="$(mktemp)"
  printf '      - name: Checkout PrestaShop repository (%s)\n        uses: actions/checkout@v4\n        with:\n          repository: PrestaShop/PrestaShop\n          fetch-depth: 1\n          path: %s\n          ref: %s\n\n' \
    "${NEW}" "${SLUG}" "${NEW}" > "${block}"
  awk -v f="${block}" '
    !done && /^      - name: Checkout PrestaShop repository \(9\.[0-9]+\.x\)/ {
      while ((getline line < f) > 0) print line
      done = 1
    }
    { print }
  ' "${ghp}" > "${ghp}.tmp" && mv "${ghp}.tmp" "${ghp}"
  rm -f "${block}"
  if ! grep -qF -- "path: ${SLUG}" "${ghp}"; then
    echo "::error::test-scenarios: gh-pages.yml anchor (9.x checkout step) not found, nothing inserted" >&2
    exit 1
  fi
  log "gh-pages.yml: inserted checkout step ${NEW}"
fi

log "test-scenarios transform done for ${NEW}"

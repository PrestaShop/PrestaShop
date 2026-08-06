#!/usr/bin/env bash
#
# PrestaShop/ps_apiresources version-branch bootstrap transform.
# Adds the new version branch to the test matrices in php.yml and integration.yml.
# Both list versions as an INLINE flow array (e.g. ['9.0.3', '9.1.x', 'develop']);
# we insert NEW right after the stable 'develop' entry, scoped to the matrix line
# only (integration.yml also references 'develop' in `if:` conditions, which must
# not be touched). ADD-only, idempotent. cwd = checked-out repo.
#
set -euo pipefail
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
# shellcheck source=lib.sh
source "${SCRIPT_DIR}/lib.sh"

WF=".github/workflows"

# add_to_inline_matrix FILE VERSION_KEY_REGEX
#   Insert 'NEW' right after 'develop' in the inline array on the matrix line
#   identified by "<key>: [" (only that line). Idempotent + fails loudly.
add_to_inline_matrix() {
  local file="$1" key="$2"
  [ -f "${file}" ] || { log "skip (missing): ${file}"; return 0; }
  if line_has "${file}" "${key}: \\[" "'${NEW}'"; then return 0; fi
  sed -i.bak -E "/${key}: \\[/ s/'develop'/'develop', '${NEW}'/" "${file}"
  rm -f "${file}.bak"
  if ! line_has "${file}" "${key}: \\[" "'${NEW}'"; then
    echo "::error::ps_apiresources: failed to add ${NEW} to ${file} (${key})" >&2
    return 1
  fi
  log "${file}: ${key} += ${NEW}"
}

# php.yml uses the key `presta_version`, integration.yml uses `prestashop_version`.
add_to_inline_matrix "${WF}/php.yml" "presta_version"
add_to_inline_matrix "${WF}/integration.yml" "prestashop_version"

log "ps_apiresources transform done for ${NEW}"

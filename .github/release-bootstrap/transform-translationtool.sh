#!/usr/bin/env bash
#
# PrestaShopCorp/TranslationTool version-branch bootstrap transform.
# Adds the new version branch to the package-update version lists (keeps the
# previous stable — cohabitation). The two catalog workflows take a free-form
# branch input and need no change. ADD-only, idempotent. cwd = checked-out repo.
#
set -euo pipefail
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
# shellcheck source=lib.sh
source "${SCRIPT_DIR}/lib.sh"

WF=".github/workflows"

# These lists have no 'develop' entry, so anchor on the stable list key and add
# NEW as the first item — keeps working once 9.1.x is removed.

# manual_update_translation_files_packages.yml: prestashop_version is the first
# choice input, so the first 'options:' is its option list.
insert_block_after "${WF}/manual_update_translation_files_packages.yml" "options:" "'${NEW}'" <<EOF
                    - '${NEW}'
EOF

# automatic_*_{preproduction,integration,production}.yml: matrix.prestashop_version
for env in preproduction integration production; do
  insert_block_after "${WF}/automatic_update_translation_files_packages_${env}.yml" "prestashop_version:" "'${NEW}'" <<EOF
                    - '${NEW}'
EOF
done

log "translationtool transform done for ${NEW}"

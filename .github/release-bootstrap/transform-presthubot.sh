#!/usr/bin/env bash
#
# PrestaShop/presthubot version-branch bootstrap transform.
# Adds the new version branch to the SlackNotifierCommand support lists,
# anchored on the always-present 'develop' entry (so the script keeps working
# once 9.1.x is removed). ADD-only and idempotent. cwd = checked-out repo.
#
set -euo pipefail
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
# shellcheck source=lib.sh
source "${SCRIPT_DIR}/lib.sh"

f="src/App/Command/SlackNotifierCommand.php"
[ -f "${f}" ] || { log "skip (missing): ${f}"; exit 0; }

# 1) BRANCH_SUPPORT: add NEW just before the 'develop' entry.
insert_block_before "${f}" "'develop'," "'${NEW}'," <<EOF
        '${NEW}',
EOF

# 2) CAMPAIGN_SUPPORT['functional']: add NEW (mysql + mariadb) before develop's.
insert_block_before "${f}" "'develop' => " "'${NEW}' => ['mysql', 'mariadb']," <<EOF
            '${NEW}' => ['mysql', 'mariadb'],
EOF

log "presthubot transform done for ${NEW}"

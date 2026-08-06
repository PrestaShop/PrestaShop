#!/usr/bin/env bash
#
# PrestaShop/ga.tests.ui.pr version-branch bootstrap transform (QA-team owned).
# Adds the new version branch to the base_branch dropdowns and mirrors the
# previous 9.x "modules" long-campaign exclusion. The other workflows derive the
# branch at runtime (startsWith('9.') / 9.${PS_MINOR}.x) and need no change.
# ADD-only, idempotent. cwd = checked-out repo.
#
set -euo pipefail
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
# shellcheck source=lib.sh
source "${SCRIPT_DIR}/lib.sh"

WF=".github/workflows"

# pr_test.yml: add NEW to the base_branch choice options ...
insert_block_after "${WF}/pr_test.yml" "- 'develop'" "'${NEW}'" <<EOF
          - '${NEW}'
EOF
# ... and mirror the 9.1.x "modules" exclusion in test-long-campaigns, placed
# just before the develop excludes (anchored on "## develop") with a "## NEW"
# comment header like the other branches.
insert_block_before "${WF}/pr_test.yml" "## develop" "BASE_BRANCH: ${NEW}" <<EOF
          ## ${NEW}
          - BASE_BRANCH: ${NEW}
            TEST_COMMAND: 'modules'
EOF

# pr_test_single_campaign.yml: add NEW to the base_branch choice options
insert_block_after "${WF}/pr_test_single_campaign.yml" "- 'develop'" "'${NEW}'" <<EOF
          - '${NEW}'
EOF

log "ga.tests.ui.pr transform done for ${NEW}"

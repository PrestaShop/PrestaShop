#!/usr/bin/env bash
#
# PrestaShop/kanbanbot version-branch bootstrap transform.
# Adds the new version branch to the enumerated target-branch lists so kanbanbot
# accepts PRs targeting it (needed for every release, minor included). The
# security-window constants (CheckSecurityBranchCommandHandler) are version_compare
# based and only move on a MAJOR — flagged for manual review, never auto-edited.
# Test fixtures are likewise flagged for the maintainer. ADD-only, idempotent.
# cwd = checked-out repo.
#
set -euo pipefail
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
# shellcheck source=lib.sh
source "${SCRIPT_DIR}/lib.sh"

DESC="src/PullRequest/Domain/Aggregate/PullRequest/PullRequestDescription.php"
PR="src/PullRequest/Domain/Aggregate/PullRequest/PullRequest.php"

# 1) TARGET_BRANCH_AVAILABLE (single-line array): add NEW right after 'develop'
#    (always present) so the script keeps working once 9.1.x is removed.
if [ -f "${DESC}" ] && ! line_has "${DESC}" 'TARGET_BRANCH_AVAILABLE' "'${NEW}'"; then
  sed -i.bak -E "s/(TARGET_BRANCH_AVAILABLE = \\['develop')/\\1, '${NEW}'/" "${DESC}"
  rm -f "${DESC}.bak"
  line_has "${DESC}" 'TARGET_BRANCH_AVAILABLE' "'${NEW}'" \
    || { echo "::error::kanbanbot: failed to add ${NEW} to TARGET_BRANCH_AVAILABLE" >&2; exit 1; }
  log "TARGET_BRANCH_AVAILABLE += ${NEW}"
fi

# 2) PullRequest.php array_diff stale-label list (multi-line): add NEW after
#    'develop' (always present) rather than the transient 9.1.x.
insert_block_after "${PR}" "'develop'," "'${NEW}'," <<EOF
            '${NEW}',
EOF

# 3) Manual follow-ups (no auto-edit): summary notes for the reviewer.
summary="${GITHUB_STEP_SUMMARY:-/dev/stderr}"
{
  echo "### kanbanbot — manual follow-ups for ${NEW}"
  echo "- Add a \`${NEW}\` case to \`TranslationsCatalogProviderTest\` and \`CheckTableDescriptionCommandHandlerTest\` (test coverage — not auto-generated)."
  if [ "${RELEASE_TYPE}" = "major" ]; then
    echo "- MAJOR release: review \`CheckSecurityBranchCommandHandler::MIN_MAINTAINED_VERSION\` / \`MIN_SECURITY_VERSION\` for the new maintenance window."
  fi
} >> "${summary}"

log "kanbanbot transform done for ${NEW}"

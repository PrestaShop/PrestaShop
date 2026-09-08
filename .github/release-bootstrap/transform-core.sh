#!/usr/bin/env bash
#
# Core repo (PrestaShop/PrestaShop) version-branch bootstrap transform.
# Adds the new version branch to the CI matrices / branch lists. ADD-only and
# idempotent: nothing is removed, re-running once NEW is present is a no-op.
# Run with cwd = the checked-out core repo.
#
set -euo pipefail
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
# shellcheck source=lib.sh
source "${SCRIPT_DIR}/lib.sh"

WF=".github/workflows"

# 1 & 2) cron_js_routing.yml + cron_nightly_build.yml:
#        add NEW right after the develop entry in matrix.BRANCH and in include
#        (anchored on develop — the stable entry — so order is develop, NEW, …).
for f in cron_js_routing.yml cron_nightly_build.yml; do
  insert_block_after "${WF}/${f}" "- develop" "- ${NEW}" <<EOF
          - ${NEW}
EOF
  insert_block_after_entry "${WF}/${f}" "- BRANCH: develop" 1 "BRANCH: ${NEW}" <<EOF
          - BRANCH: ${NEW}
            node: 20
EOF
done

# 3) cron_php_update_modules.yml: add NEW, keep the previous stable (cohabitation)
insert_block_after "${WF}/cron_php_update_modules.yml" "- develop" "- ${NEW}" <<EOF
          - ${NEW}
EOF

# 4) Create the per-version nightly caller files from the template.
#    PHP 8.2 / Node 20 inherited from develop (9.1.x used PHP 8.1).
#    nightly_tests_follow_up.yml derives <branch>/<db> from these file names.
#    (cron_nightly_tests_reusable.yml itself needs no change: it resolves the
#    campaigns from the branch's tests/UI/package.json.)
tmpl="${SCRIPT_DIR}/templates/cron_nightly_tests_DB.yml.tmpl"
PHP_VERSION="8.2"
NODE_VERSION="20"
for db in mysql mariadb; do
  out="${WF}/cron_nightly_tests_${NEW}_${db}.yml"
  if [ ! -f "${out}" ]; then
    sed -e "s/@BRANCH@/${NEW}/g" \
        -e "s/@DB@/${db}/g" \
        -e "s/@JOB@/${NEW_JOB}/g" \
        -e "s/@PHP@/${PHP_VERSION}/g" \
        -e "s/@NODE@/${NODE_VERSION}/g" \
        "${tmpl}" > "${out}"
    log "created ${out}"
  fi
done

# 4b) nightly_tests_follow_up.yml (auto-retry + report import): watch the NEW
#     branch nightly test workflows, placed right after the develop entries
#     (anchored on the develop mariadb entry — develop is always present).
insert_block_after "${WF}/nightly_tests_follow_up.yml" \
  "- 'Nightly tests and report - develop (mariadb)'" \
  "Nightly tests and report - ${NEW} (mysql)" <<EOF
      - 'Nightly tests and report - ${NEW} (mysql)'
      - 'Nightly tests and report - ${NEW} (mariadb)'
EOF

# 5) cron_create_merge_prs.yml: insert NEW into the merge-up CHAIN. Each stable
#    branch merges into the one just above it, not straight into develop:
#    the pair that currently targets develop is retargeted to NEW, then a new
#    NEW -> develop pair is added. e.g. {9.1.x -> develop} becomes
#    {9.1.x -> 9.2.x} + {9.2.x -> develop}. Generalises for later releases.
#    Guard/anchor on the matrix line ("- source: NEW", anchored) so neither the
#    retarget nor the guard is fooled by the "{source:…, target: develop}"
#    example in the file's header comment.
merge="${WF}/cron_create_merge_prs.yml"
if [ -f "${merge}" ] && ! grep -qE "^[[:space:]]*- source: ${NEW//./\\.}\$" "${merge}"; then
  # Retarget the single matrix "target: develop" line (anchored; the comment's
  # "target: develop})" does not match) to point at NEW.
  sed -i.bak -E "s/^([[:space:]]*)target: develop\$/\1target: ${NEW}/" "${merge}"
  rm -f "${merge}.bak"
  # Add the new NEW -> develop pair.
  insert_block_after "${merge}" "pair:" "- source: ${NEW}" <<EOF
          - source: ${NEW}
            target: develop
EOF
  log "merge-up chain: retargeted -> develop to -> ${NEW}, added ${NEW} -> develop"
fi

# 6) PULL_REQUEST_TEMPLATE.md: add NEW to the "| Branch?" choice line
prt=".github/PULL_REQUEST_TEMPLATE.md"
if [ -f "${prt}" ] && ! line_has "${prt}" '^\| Branch\?' "${NEW}"; then
  sed -i.bak -E "/Branch\?/ s#(develop / )#\\1${NEW} / #" "${prt}"
  rm -f "${prt}.bak"
  line_has "${prt}" '^\| Branch\?' "${NEW}" || { echo "::error::core: failed to add ${NEW} to PR template" >&2; exit 1; }
  log "added ${NEW} to ${prt} Branch line"
fi

log "core transform done for ${NEW}"

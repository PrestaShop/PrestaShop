#!/usr/bin/env bash
#
# PrestaShop/docker version-branch bootstrap transform.
# Registers the new version branch in versions.py — its PHP list is copied from
# the always-present 'nightly' entry (nightly == develop, which NEW was just cut
# from) and the entry is inserted right before 'nightly', matching the file's
# indentation. The images/<NEW>/ Dockerfiles are then produced by the repo's own
# generator (`prestashop_docker.py generate`), never written by hand — this
# mirrors docker's sync-releases.yml workflow minus its backlog step. The
# generator rewrites EVERY version's Dockerfile, so anything outside versions.py
# and images/<NEW>/ is reverted afterwards: that churn belongs to the daily
# sync-releases run, not to this PR. ADD-only and idempotent (guard on the
# versions.py entry; regenerating already-committed files is byte-identical).
# cwd = checked-out repo. Python 3.9 + requirements.txt are provisioned by the
# composite action (steps gated on transform_id == 'docker').
#
set -euo pipefail
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
# shellcheck source=lib.sh
source "${SCRIPT_DIR}/lib.sh"

f="versions.py"
[ -f "${f}" ] || { log "skip (missing): ${f}"; exit 0; }

# 1) versions.py: add the NEW entry just before 'nightly', PHP list copied
#    verbatim from nightly's block (keeps its 8-space item indentation).
php_list="$(awk "/'nightly': \(/{f=1;next} f && /\),/{exit} f" "${f}")"
if [ -z "${php_list}" ]; then
  echo "::error::could not extract the 'nightly' PHP list from ${f}" >&2
  exit 1
fi
insert_block_before "${f}" "'nightly': (" "'${NEW}': (" <<EOF
    '${NEW}': (
${php_list}
    ),
EOF

# 2) Let the generator create images/<NEW>/<php>-<apache|fpm>/Dockerfile from
#    the updated versions.py.
python3 prestashop_docker.py generate

# 3) Scope the diff: revert modified files and delete generated ones outside
#    versions.py + images/<NEW>/.
git diff --name-only | while read -r p; do
  case "${p}" in
    versions.py | "images/${NEW}/"*) ;;
    *) git checkout -q -- "${p}"; log "reverted out-of-scope change: ${p}" ;;
  esac
done
git ls-files --others --exclude-standard | while read -r p; do
  case "${p}" in
    "images/${NEW}/"*) ;;
    *) rm -f "${p}"; log "removed out-of-scope generated file: ${p}" ;;
  esac
done

log "docker transform done for ${NEW}"

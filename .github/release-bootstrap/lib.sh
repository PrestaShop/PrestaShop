#!/usr/bin/env bash
#
# Shared helpers for the version-branch bootstrap transforms.
#
# Each transform-<repo>.sh sources this file and edits files IN PLACE in the
# current working directory (the checked-out target repo). Every edit MUST be
# idempotent: running it again once the new branch is already present must
# leave the tree unchanged (empty `git diff`). That empty-diff property is what
# lets the workflow decide "modification already done -> no PR".
#
# YAML list edits use surgical text insertion (awk, literal-substring anchors)
# rather than `yq`: this preserves the file byte-for-byte except the inserted
# lines (reviewer-friendly diffs) and avoids `yq -i` re-serializing GitHub
# Actions workflows (which can rewrite the `on:` key or reflow comments).
#
set -euo pipefail

: "${VERSION_BRANCH:?VERSION_BRANCH must be set (e.g. 9.2.x)}"

# --- Derivations from the version branch (e.g. 9.2.x) ------------------------
NEW="${VERSION_BRANCH}"          # 9.2.x
NEW_JOB="${NEW//./_}"            # 9_2_x   (workflow job ids / generated file names)
NEW_MAJOR="${NEW%%.*}"           # 9
_tail="${NEW#*.}"
NEW_MINOR="${_tail%%.*}"         # 2
BASE="${BASE:-develop}"          # branch the bootstrap PRs target / merge-up target
RELEASE_TYPE="${RELEASE_TYPE:-minor}"
export NEW NEW_JOB NEW_MAJOR NEW_MINOR BASE RELEASE_TYPE

log() { printf '  %s\n' "$*" >&2; }

# present FILE LITERAL  -> true when LITERAL appears anywhere in FILE
#   `--` ends option parsing so a LITERAL starting with '-' (e.g. "- 9.2.x")
#   is treated as a pattern, not as grep flags.
present() { grep -qF -- "$2" "$1" 2>/dev/null; }

# line_has FILE LINE_REGEX LITERAL -> true when a line matching LINE_REGEX
#   (grep -E) already contains the literal LITERAL.
line_has() { grep -E -- "$2" "$1" 2>/dev/null | grep -qF -- "$3"; }

# insert_block_after FILE ANCHOR_LITERAL GUARD_LITERAL   (block read from stdin)
#   Insert the multi-line block from stdin immediately after the FIRST line that
#   CONTAINS the literal substring ANCHOR_LITERAL, unless GUARD_LITERAL already
#   appears in the file. Literal (awk index) matching avoids regex-escaping
#   pitfalls. No-op when GUARD is present (idempotent). Fails loudly if the
#   anchor is not found or the guard is still absent afterwards.
insert_block_after() {
  local file="$1" anchor="$2" guard="$3"
  local blk; blk="$(mktemp)"; cat > "${blk}"
  if [ ! -f "${file}" ]; then rm -f "${blk}"; log "skip (missing): ${file}"; return 0; fi
  if present "${file}" "${guard}"; then rm -f "${blk}"; return 0; fi
  if awk -v bf="${blk}" -v anchor="${anchor}" '
        { print }
        !ins && index($0, anchor) { while ((getline l < bf) > 0) print l; close(bf); ins = 1 }
        END { if (!ins) exit 3 }
      ' "${file}" > "${file}.tmp"; then
    mv "${file}.tmp" "${file}"; rm -f "${blk}"
  else
    rm -f "${file}.tmp" "${blk}"
    echo "::error::insert anchor '${anchor}' not found in ${file}" >&2
    return 1
  fi
  if ! present "${file}" "${guard}"; then
    echo "::error::guard '${guard}' still absent after insert in ${file}" >&2
    return 1
  fi
  log "inserted after '${anchor}' in ${file}"
}

# insert_block_before FILE ANCHOR_LITERAL GUARD_LITERAL   (block read from stdin)
#   Like insert_block_after, but inserts the block immediately BEFORE the first
#   line containing ANCHOR_LITERAL. Used to anchor on a stable, always-present
#   line (e.g. develop / the previous stable) and keep the new entry ordered
#   ahead of it.
insert_block_before() {
  local file="$1" anchor="$2" guard="$3"
  local blk; blk="$(mktemp)"; cat > "${blk}"
  if [ ! -f "${file}" ]; then rm -f "${blk}"; log "skip (missing): ${file}"; return 0; fi
  if present "${file}" "${guard}"; then rm -f "${blk}"; return 0; fi
  if awk -v bf="${blk}" -v anchor="${anchor}" '
        !ins && index($0, anchor) { while ((getline l < bf) > 0) print l; close(bf); ins = 1 }
        { print }
        END { if (!ins) exit 3 }
      ' "${file}" > "${file}.tmp"; then
    mv "${file}.tmp" "${file}"; rm -f "${blk}"
  else
    rm -f "${file}.tmp" "${blk}"
    echo "::error::insert anchor '${anchor}' not found in ${file}" >&2
    return 1
  fi
  if ! present "${file}" "${guard}"; then
    echo "::error::guard '${guard}' still absent after insert in ${file}" >&2
    return 1
  fi
  log "inserted before '${anchor}' in ${file}"
}

# insert_block_after_entry FILE ANCHOR_LITERAL TRAILING GUARD   (block from stdin)
#   Insert the block immediately AFTER a multi-line entry: the first line
#   containing ANCHOR_LITERAL plus its next TRAILING lines (e.g. a "- BRANCH:
#   develop" / "node: 20" pair → TRAILING=1). Lets us anchor on the stable
#   develop entry and drop the new entry right after it. No-op when GUARD is
#   present; fails loudly if the anchor is not found.
insert_block_after_entry() {
  local file="$1" anchor="$2" trailing="$3" guard="$4"
  local blk; blk="$(mktemp)"; cat > "${blk}"
  if [ ! -f "${file}" ]; then rm -f "${blk}"; log "skip (missing): ${file}"; return 0; fi
  if present "${file}" "${guard}"; then rm -f "${blk}"; return 0; fi
  if awk -v bf="${blk}" -v anchor="${anchor}" -v trailing="${trailing}" '
        !ins && index($0, anchor) {
          print
          for (i = 0; i < trailing; i++) { if ((getline nl) > 0) print nl }
          while ((getline l < bf) > 0) print l
          close(bf); ins = 1; next
        }
        { print }
        END { if (!ins) exit 3 }
      ' "${file}" > "${file}.tmp"; then
    mv "${file}.tmp" "${file}"; rm -f "${blk}"
  else
    rm -f "${file}.tmp" "${blk}"
    echo "::error::insert anchor '${anchor}' not found in ${file}" >&2
    return 1
  fi
  if ! present "${file}" "${guard}"; then
    echo "::error::guard '${guard}' still absent after insert in ${file}" >&2
    return 1
  fi
  log "inserted after entry '${anchor}' (+${trailing}) in ${file}"
}

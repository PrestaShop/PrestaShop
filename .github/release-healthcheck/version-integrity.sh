#!/usr/bin/env bash
#
# Release Healthcheck — Version & branch integrity (env tier: static).
#
# Verifies the version constants and the build branch's lineage / naming / tag state.
# Provenance: these were section A (A1–A6) in issue #41804.

# HC_GROUP is read by emit() in lib.sh (sourced separately) — silence cross-file SC2034.
# shellcheck disable=SC2034
HC_GROUP="Version & branch integrity"
REPO="${REPO:-PrestaShop/PrestaShop}"

# spec ref: A1 — core version constants in src/Core/Version.php
check_core_version_constants() {
  local f="src/Core/Version.php" v mvs mv miv rv detail
  HC_LINK="https://github.com/$REPO/blob/$REF/$f"
  if [ ! -f "$f" ]; then
    emit "version/core-constants" "Core version constants" "$HC_FAIL" "$HC_SEV_FAIL" "$f not found"
    return
  fi
  v=$(php_const_string "$f" "VERSION")
  mvs=$(php_const_string "$f" "MAJOR_VERSION_STRING")
  mv=$(php_const_int "$f" "MAJOR_VERSION")
  miv=$(php_const_int "$f" "MINOR_VERSION")
  rv=$(php_const_int "$f" "RELEASE_VERSION")
  detail="VERSION=$v, MAJOR_VERSION_STRING=$mvs, MAJOR=$mv, MINOR=$miv, RELEASE=$rv (expected $CORE_VERSION / $MAJOR.$MINOR.$PATCH)"
  if [ "$v" = "$CORE_VERSION" ] && [ "$mvs" = "$MAJOR" ] && [ "$mv" = "$MAJOR" ] && [ "$miv" = "$MINOR" ] && [ "$rv" = "$PATCH" ]; then
    emit "version/core-constants" "Core version constants" "$HC_PASS" "$HC_SEV_FAIL" "$detail"
  else
    emit "version/core-constants" "Core version constants" "$HC_FAIL" "$HC_SEV_FAIL" "$detail"
  fi
}

# spec ref: A2 — installer version in install-dev/install_version.php
check_installer_version_constant() {
  local f="install-dev/install_version.php" iv detail
  HC_LINK="https://github.com/$REPO/blob/$REF/$f"
  if [ ! -f "$f" ]; then
    emit "version/installer-constant" "Installer version constant" "$HC_FAIL" "$HC_SEV_FAIL" "$f not found"
    return
  fi
  iv=$(php_define_string "$f" "_PS_INSTALL_VERSION_")
  detail="_PS_INSTALL_VERSION_=$iv (expected $CORE_VERSION)"
  if [ "$iv" = "$CORE_VERSION" ] \
     && grep -q "_PS_INSTALL_MINIMUM_PHP_VERSION_ID_" "$f" \
     && grep -q "_PS_INSTALL_MAXIMUM_PHP_VERSION_ID_" "$f"; then
    emit "version/installer-constant" "Installer version constant" "$HC_PASS" "$HC_SEV_FAIL" "$detail; min/max PHP id present"
  else
    emit "version/installer-constant" "Installer version constant" "$HC_FAIL" "$HC_SEV_FAIL" "$detail"
  fi
}

# spec ref: A3 — Version.php VERSION === install_version.php === core_version
check_version_consistency() {
  local v iv
  HC_LINK="https://github.com/$REPO/blob/$REF/src/Core/Version.php"
  v=$(php_const_string "src/Core/Version.php" "VERSION")
  iv=$(php_define_string "install-dev/install_version.php" "_PS_INSTALL_VERSION_")
  if [ "$v" = "$CORE_VERSION" ] && [ "$iv" = "$CORE_VERSION" ]; then
    emit "version/consistency" "Version consistency" "$HC_PASS" "$HC_SEV_FAIL" "Version.php=$v, installer=$iv, target=$CORE_VERSION"
  else
    emit "version/consistency" "Version consistency" "$HC_FAIL" "$HC_SEV_FAIL" "Version.php=$v, installer=$iv, target=$CORE_VERSION"
  fi
}

# spec ref: A4 — build branch descends from the version branch OR the previous patch tag
check_build_branch_base() {
  if [ "$BRANCH_KIND" != "build" ]; then
    emit "version/build-branch-base" "Build branch base" "$HC_NA" "$HC_SEV_WARN" "ref kind '$BRANCH_KIND' — build-branch lineage check N/A"
    return
  fi
  HC_LINK="https://github.com/$REPO/compare/$VERSION_BRANCH...$REF"
  local detail=""
  if git fetch --quiet origin "$VERSION_BRANCH" 2>/dev/null && git merge-base --is-ancestor FETCH_HEAD HEAD 2>/dev/null; then
    emit "version/build-branch-base" "Build branch base" "$HC_PASS" "$HC_SEV_WARN" "HEAD descends from origin/$VERSION_BRANCH"
    return
  fi
  if [ "$PATCH" -gt 0 ]; then
    local prev="$MAJOR.$MINOR.$((PATCH - 1))"
    if git fetch --quiet origin "refs/tags/$prev:refs/tags/$prev" 2>/dev/null && git merge-base --is-ancestor "$prev" HEAD 2>/dev/null; then
      emit "version/build-branch-base" "Build branch base" "$HC_PASS" "$HC_SEV_WARN" "HEAD descends from previous patch tag $prev"
      return
    fi
    detail=" (also not a descendant of tag $prev)"
  fi
  emit "version/build-branch-base" "Build branch base" "$HC_FAIL" "$HC_SEV_WARN" "HEAD does not descend from origin/$VERSION_BRANCH$detail"
}

# spec ref: A5 — branch follows the build-branch naming convention for target_version
check_branch_naming() {
  if [ "$BRANCH_KIND" != "build" ]; then
    emit "version/branch-naming" "Branch naming" "$HC_NA" "$HC_SEV_WARN" "ref kind '$BRANCH_KIND' — build-branch naming check N/A"
    return
  fi
  if [ "$REF" = "$BUILD_BRANCH" ]; then
    emit "version/branch-naming" "Branch naming" "$HC_PASS" "$HC_SEV_WARN" "branch '$REF' matches convention"
  else
    emit "version/branch-naming" "Branch naming" "$HC_FAIL" "$HC_SEV_WARN" "branch '$REF', expected '$BUILD_BRANCH'"
  fi
}

# spec ref: A6 — tag presence is the inverse of publication state
check_tag_state_vs_publication() {
  local tag_present=0
  HC_LINK="https://github.com/$REPO/tags"
  gh_tag_exists "$REPO" "$CORE_VERSION" && tag_present=1
  if [ "$RELEASE_PUBLISHED" = "true" ]; then
    if [ "$tag_present" = 1 ]; then
      emit "version/tag-state" "Tag state vs publication" "$HC_PASS" "$HC_SEV_WARN" "tag $CORE_VERSION present (release published)"
    else
      emit "version/tag-state" "Tag state vs publication" "$HC_FAIL" "$HC_SEV_WARN" "release published but tag $CORE_VERSION missing"
    fi
  else
    if [ "$tag_present" = 0 ]; then
      emit "version/tag-state" "Tag state vs publication" "$HC_PASS" "$HC_SEV_WARN" "tag $CORE_VERSION absent (not yet published)"
    else
      emit "version/tag-state" "Tag state vs publication" "$HC_FAIL" "$HC_SEV_WARN" "tag $CORE_VERSION already exists before publication"
    fi
  fi
}

run_version_integrity_checks() {
  HC_GROUP="Version & branch integrity"
  guard check_core_version_constants
  guard check_installer_version_constant
  guard check_version_consistency
  guard check_build_branch_base
  guard check_branch_naming
  guard check_tag_state_vs_publication
}

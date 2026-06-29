#!/usr/bin/env bash
#
# Release Healthcheck — Post-release verification (env tier: gh/http/git).
#
# These run every time but are only graded once the release is published; before that
# each reports PENDING (➖) and never gates. G3/G5 additionally wait for the Classic release.
# Provenance: these were section G (G1–G7) in issue #41804.

# HC_GROUP is read by emit() in lib.sh (sourced separately) — silence cross-file SC2034.
# shellcheck disable=SC2034
HC_GROUP="Post-release verification"
REPO="${REPO:-PrestaShop/PrestaShop}"

# Emit PENDING and signal "skip" (return 1) when the release is not yet published.
_gate_published() {
  if [ "$RELEASE_PUBLISHED" != "true" ]; then
    emit "$1" "$2" "$HC_PENDING" "$3" "pending — release not yet published"
    return 1
  fi
  return 0
}

# spec ref: G1 — build branch merged back into the version branch
check_build_branch_merged_back() {
  local id="post-release/build-branch-merged-back" title="Build branch merged back"
  # A build branch is cut for every release — including pre-releases (e.g. build-920-beta1) —
  # and merged back, so this applies to all release types.
  HC_LINK="https://github.com/$REPO/compare/$VERSION_BRANCH...$BUILD_BRANCH"
  _gate_published "$id" "$title" "$HC_SEV_FAIL" || return
  # A merged build branch is typically deleted afterwards — absence ⇒ merged & cleaned up.
  if ! git ls-remote --exit-code --heads origin "$BUILD_BRANCH" >/dev/null 2>&1; then
    emit "$id" "$title" "$HC_PASS" "$HC_SEV_FAIL" "$BUILD_BRANCH absent on origin — merged and cleaned up"
    return
  fi
  git fetch --quiet origin "$BUILD_BRANCH" "$VERSION_BRANCH" 2>/dev/null || true
  if git merge-base --is-ancestor "origin/$BUILD_BRANCH" "origin/$VERSION_BRANCH" 2>/dev/null; then
    emit "$id" "$title" "$HC_PASS" "$HC_SEV_FAIL" "$BUILD_BRANCH merged into $VERSION_BRANCH"
  else
    emit "$id" "$title" "$HC_FAIL" "$HC_SEV_FAIL" "$BUILD_BRANCH not yet merged into $VERSION_BRANCH"
  fi
}

# spec ref: G2 — tag + non-draft GitHub release published (== release_published)
check_tag_and_release_published() {
  local id="post-release/tag-and-release-published" title="Tag & GitHub release published"
  HC_LINK="https://github.com/$UPSTREAM_REPO/releases/tag/$CORE_VERSION"
  if [ "$RELEASE_PUBLISHED" = "true" ]; then
    emit "$id" "$title" "$HC_PASS" "$HC_SEV_FAIL" "tag $CORE_VERSION published with a non-draft release"
  else
    emit "$id" "$title" "$HC_PENDING" "$HC_SEV_FAIL" "pending — no published release for $CORE_VERSION yet"
  fi
}

# spec ref: G3 — distribution API lists core_version (independent of the Classic release)
check_distribution_api_fresh_install() {
  local id="post-release/distribution-api" title="Distribution API — fresh install"
  HC_LINK="https://api.prestashop-project.org/prestashop"
  _gate_published "$id" "$title" "$HC_SEV_WARN" || return
  if http_body_contains "https://api.prestashop-project.org/prestashop" "\"version\":\"$CORE_VERSION\""; then
    emit "$id" "$title" "$HC_PASS" "$HC_SEV_WARN" "distribution API lists $CORE_VERSION"
  else
    emit "$id" "$title" "$HC_FAIL" "$HC_SEV_WARN" "distribution API does not list $CORE_VERSION yet"
  fi
}

# spec ref: G4 — Docker images published (auto-PR merged + publish ran)
check_docker_images_published() {
  local id="post-release/docker-images" title="Docker images published"
  HC_LINK="https://hub.docker.com/r/prestashop/prestashop/tags?name=$CORE_VERSION"
  _gate_published "$id" "$title" "$HC_SEV_WARN" || return
  # Docker PRs have generic titles ("Sync backlog …") — verify the published image tag instead.
  local name
  name=$(http_get "https://hub.docker.com/v2/repositories/prestashop/prestashop/tags/$CORE_VERSION" | jq -r '.name // empty' 2>/dev/null)
  if [ -n "$name" ]; then
    emit "$id" "$title" "$HC_PASS" "$HC_SEV_WARN" "Docker Hub image prestashop/prestashop:$CORE_VERSION published"
  else
    emit "$id" "$title" "$HC_FAIL" "$HC_SEV_WARN" "no prestashop/prestashop:$CORE_VERSION image on Docker Hub yet"
  fi
}

# spec ref: G5 — classic feeds updated (independent of the Classic-release flag)
check_classic_feeds_updated() {
  local id="post-release/classic-feeds" title="Classic feeds updated"
  local base="https://assets.prestashop3.com/dst/edition/corporate"
  HC_LINK="$base/edition_versions.js"
  _gate_published "$id" "$title" "$HC_SEV_WARN" || return
  # BOTH feeds must reference the version — one alone is an incomplete update.
  local ev="no" lc="no"
  http_body_contains "$base/edition_versions.js" "$CORE_VERSION" && ev="yes"
  http_body_contains "$base/latest-classic.js"   "$CORE_VERSION" && lc="yes"
  if [ "$ev" = "yes" ] && [ "$lc" = "yes" ]; then
    emit "$id" "$title" "$HC_PASS" "$HC_SEV_WARN" "both classic feeds reference $CORE_VERSION"
  else
    emit "$id" "$title" "$HC_FAIL" "$HC_SEV_WARN" "classic feeds incomplete: edition_versions=$ev, latest-classic=$lc"
  fi
}

# spec ref: G6 — localization packs available (minor/major only)
check_localization_packs() {
  local id="post-release/localization-packs" title="Localization packs"
  if [ "$RELEASE_TYPE" != "minor" ] && [ "$RELEASE_TYPE" != "major" ]; then
    emit "$id" "$title" "$HC_NA" "$HC_SEV_WARN" "only applies to minor/major (this is $RELEASE_TYPE)"
    return
  fi
  HC_LINK="https://api.prestashop-project.org/localization/full/${CORE_VERSION}.xml"
  _gate_published "$id" "$title" "$HC_SEV_WARN" || return
  if http_body_contains "https://api.prestashop-project.org/localization/full/${CORE_VERSION}.xml" "<"; then
    emit "$id" "$title" "$HC_PASS" "$HC_SEV_WARN" "localization pack reachable for $CORE_VERSION"
  else
    emit "$id" "$title" "$HC_FAIL" "$HC_SEV_WARN" "localization pack not yet available for $CORE_VERSION"
  fi
}

# spec ref: G7 — the distribution-api max-version change (F1) is now LIVE. Rather than trust a
# merged-PR search, verify the deployed result: the autoupgrade API lists the version as a
# max upgrade target (the end state of merging public/json/autoupgrade.json).
check_autoupgrade_pr_merged() {
  local id="post-release/autoupgrade-pr-merged" title="Autoupgrade max-version live" maxes
  HC_LINK="https://api.prestashop-project.org/autoupgrade"
  _gate_published "$id" "$title" "$HC_SEV_WARN" || return
  maxes=$(http_get "https://api.prestashop-project.org/autoupgrade" | jq -r '.prestashop[]?.prestashop_max' 2>/dev/null)
  if printf '%s\n' "$maxes" | grep -qx "$CORE_VERSION"; then
    emit "$id" "$title" "$HC_PASS" "$HC_SEV_WARN" "autoupgrade API lists $CORE_VERSION as a max upgrade target"
  else
    emit "$id" "$title" "$HC_FAIL" "$HC_SEV_WARN" "autoupgrade API does not list $CORE_VERSION as a max upgrade target yet"
  fi
}

run_post_release_checks() {
  HC_GROUP="Post-release verification"
  guard check_build_branch_merged_back
  guard check_tag_and_release_published
  guard check_distribution_api_fresh_install
  guard check_docker_images_published
  guard check_classic_feeds_updated
  guard check_localization_packs
  guard check_autoupgrade_pr_merged
}

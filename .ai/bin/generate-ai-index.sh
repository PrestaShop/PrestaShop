#!/usr/bin/env bash
# generate-ai-index.sh
#
# Generates compact AI-friendly codebase index files into .ai/generated/
# Output: cqrs.md, routes.md, entities.md, hooks.md
#
# Usage:
#   bash bin/generate-ai-index.sh
#   bash bin/generate-ai-index.sh --output .ai/generated
#
# Design: pure grep/awk/sed — no runtime dependencies beyond bash + coreutils.

set -euo pipefail

REPO_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
OUTPUT_DIR="${REPO_ROOT}/.ai/generated"
TODAY=$(date +%Y-%m-%d)

# Parse --output flag
while [[ $# -gt 0 ]]; do
    case "$1" in
        --output) OUTPUT_DIR="$2"; shift 2 ;;
        *) echo "Unknown option: $1" >&2; exit 1 ;;
    esac
done

mkdir -p "$OUTPUT_DIR"
echo "Generating AI indexes → $OUTPUT_DIR"

# ─────────────────────────────────────────────────────────────────────────────
# HELPERS
# ─────────────────────────────────────────────────────────────────────────────

# Extract the class name from a PHP file (handles final/abstract/readonly prefixes)
# Returns empty string for interfaces/traits — always exits 0.
php_classname() {
    grep -m1 "^class \|^final class \|^abstract class \|^readonly class \|^final readonly class " "$1" 2>/dev/null \
        | sed 's/.*class \([A-Za-z0-9_]*\).*/\1/' \
        | tr -d '\r' || true
}

# ─────────────────────────────────────────────────────────────────────────────
# 1. cqrs.md — Commands & Queries grouped by domain
# ─────────────────────────────────────────────────────────────────────────────
generate_cqrs() {
    local outfile="$OUTPUT_DIR/cqrs.md"
    local domain_dir="$REPO_ROOT/src/Core/Domain"

    local cmd_count query_count domain_count
    cmd_count=$(find "$domain_dir" -name "*.php" -path "*/Command/*.php" | wc -l | tr -d ' ')
    query_count=$(find "$domain_dir" -name "*.php" -path "*/Query/*.php" | wc -l | tr -d ' ')
    domain_count=$(find "$domain_dir" -maxdepth 1 -mindepth 1 -type d | wc -l | tr -d ' ')

    {
        echo "# CQRS Index (generated $TODAY)"
        echo "# $cmd_count commands · $query_count queries · $domain_count top-level domains"
        echo "#"
        echo "# Sub-domain shown in [brackets] when command/query lives below the top-level domain dir."
        echo ""
    } > "$outfile"

    while IFS= read -r domain_path; do
        local domain
        domain=$(basename "$domain_path")

        # Skip non-domain directories at root
        if [[ "$domain" == "QueryResult" || "$domain" == "ValueObject" || "$domain" == "Exception" ]]; then continue; fi

        # Collect commands (recursive — covers sub-domains)
        local wrote_domain=0 wrote_commands=0

        while IFS= read -r f; do
            local classname
            classname=$(php_classname "$f")
            [[ -z "$classname" ]] && continue

            # Compute sub-domain context (path between Domain/{Name}/ and /Command/)
            local rel subcontext
            rel="${f#${domain_path}/}"                      # e.g. Combination/Command/AddCombinationCommand.php
            subcontext="${rel%%/Command/*}"                 # e.g. Combination
            if [[ "$subcontext" == "$rel" ]]; then subcontext=""; fi  # no sub-domain

            if [[ $wrote_domain -eq 0 ]]; then
                echo "## $domain" >> "$outfile"
                wrote_domain=1
            fi
            if [[ $wrote_commands -eq 0 ]]; then
                echo "### Commands" >> "$outfile"
                wrote_commands=1
            fi

            if [[ -n "$subcontext" ]]; then
                echo "- $classname  [$subcontext]" >> "$outfile"
            else
                echo "- $classname" >> "$outfile"
            fi
        done < <(find "$domain_path" -name "*.php" -path "*/Command/*.php" 2>/dev/null | sort)

        # Collect queries
        local wrote_queries=0

        while IFS= read -r f; do
            local classname
            classname=$(php_classname "$f")
            [[ -z "$classname" ]] && continue

            local rel subcontext
            rel="${f#${domain_path}/}"
            subcontext="${rel%%/Query/*}"
            if [[ "$subcontext" == "$rel" ]]; then subcontext=""; fi

            if [[ $wrote_domain -eq 0 ]]; then
                echo "## $domain" >> "$outfile"
                wrote_domain=1
            fi
            if [[ $wrote_queries -eq 0 ]]; then
                echo "### Queries" >> "$outfile"
                wrote_queries=1
            fi

            if [[ -n "$subcontext" ]]; then
                echo "- $classname  [$subcontext]" >> "$outfile"
            else
                echo "- $classname" >> "$outfile"
            fi
        done < <(find "$domain_path" -name "*.php" -path "*/Query/*.php" 2>/dev/null | sort)

        if [[ $wrote_domain -eq 1 ]]; then echo "" >> "$outfile"; fi
    done < <(find "$domain_dir" -maxdepth 1 -mindepth 1 -type d | sort)

    echo "  ✓ cqrs.md        ($(wc -l < "$outfile" | tr -d ' ') lines)"
}

# ─────────────────────────────────────────────────────────────────────────────
# 2. routes.md — Symfony routes grouped by routing file
# ─────────────────────────────────────────────────────────────────────────────
generate_routes() {
    local outfile="$OUTPUT_DIR/routes.md"
    local routing_dir="$REPO_ROOT/src/PrestaShopBundle/Resources/config/routing"

    local total_routes
    total_routes=$(find "$routing_dir" -name "*.yml" -o -name "*.yaml" | \
        xargs grep -h "^[a-z_][a-z0-9_]*:" 2>/dev/null | wc -l | tr -d ' ')

    {
        echo "# Routes Index (generated $TODAY)"
        echo "# ~$total_routes routes across admin / admin-api / api"
        echo "#"
        echo "# Paths are relative to the routing file's prefix (see parent _*.yml for full prefix)."
        echo "# Route name is the canonical identifier — use it with \$this->generateUrl() or \$router->generate()."
        echo ""
    } > "$outfile"

    # Process each area separately
    for area in admin admin-api api; do
        local area_dir="$routing_dir/$area"
        [[ -d "$area_dir" ]] || continue

        local area_files
        area_files=$(find "$area_dir" -name "*.yml" -o -name "*.yaml" | grep -v "/_[^/]*$" | sort || true)
        if [[ -z "$area_files" ]]; then continue; fi

        echo "## $area" >> "$outfile"
        echo "" >> "$outfile"

        while IFS= read -r yaml_file; do
            # Relative path within routing dir for display
            local rel_path
            rel_path="${yaml_file#${routing_dir}/}"
            rel_path="${rel_path%.yml}"; rel_path="${rel_path%.yaml}"

            # Use awk to parse YAML route blocks
            local parsed
            parsed=$(awk '
                /^[a-zA-Z_][a-zA-Z0-9_]*:[ \t]*$/ {
                    # flush previous
                    if (rname != "" && (path != "" || ctrl != "")) {
                        m = methods == "" ? "?" : methods
                        gsub(/[\[\] \t]/, "", m)
                        # shorten controller: strip namespace, keep ClassName::method
                        c = ctrl
                        sub(/.*\\/, "", c)
                        printf "%-12s  %-40s  %s  [%s]\n", m, path, rname, c
                    }
                    rname = $0; sub(/:[ \t]*$/, "", rname)
                    path = ""; methods = ""; ctrl = ""; in_defaults = 0
                }
                /^  path:/ {
                    path = $0; sub(/.*path:[ \t]*/, "", path); gsub(/'"'"'|"/, "", path)
                }
                /^  methods:/ {
                    methods = $0; sub(/.*methods:[ \t]*/, "", methods)
                }
                /^  defaults:/ { in_defaults = 1 }
                in_defaults && /[ \t]+_controller:/ {
                    ctrl = $0; sub(/.*_controller:[ \t]*/, "", ctrl); gsub(/'"'"'|"/, "", ctrl)
                    in_defaults = 0
                }
                END {
                    if (rname != "" && (path != "" || ctrl != "")) {
                        m = methods == "" ? "?" : methods
                        gsub(/[\[\] \t]/, "", m)
                        c = ctrl
                        sub(/.*\\/, "", c)
                        printf "%-12s  %-40s  %s  [%s]\n", m, path, rname, c
                    }
                }
            ' "$yaml_file")

            if [[ -z "$parsed" ]]; then continue; fi

            echo "### $rel_path" >> "$outfile"
            echo '```' >> "$outfile"
            echo "$parsed" >> "$outfile"
            echo '```' >> "$outfile"
            echo "" >> "$outfile"
        done <<< "$area_files"
    done

    echo "  ✓ routes.md      ($(wc -l < "$outfile" | tr -d ' ') lines)"
}

# ─────────────────────────────────────────────────────────────────────────────
# 3. entities.md — Doctrine entity properties & relations
# ─────────────────────────────────────────────────────────────────────────────
generate_entities() {
    local outfile="$OUTPUT_DIR/entities.md"
    local entity_dir="$REPO_ROOT/src/PrestaShopBundle/Entity"

    local entity_count
    entity_count=$(find "$entity_dir" -maxdepth 1 -name "*.php" | wc -l | tr -d ' ')

    {
        echo "# Doctrine Entities Index (generated $TODAY)"
        echo "# $entity_count entities in src/PrestaShopBundle/Entity/"
        echo "#"
        echo "# Columns: scalar DB-mapped fields. Relations: association targets."
        echo ""
    } > "$outfile"

    while IFS= read -r f; do
        local classname
        classname=$(php_classname "$f")
        [[ -z "$classname" ]] && continue

        # Extract ORM Column property names using awk (annotation + property pair)
        # The property declaration comes AFTER the closing */ of the docblock,
        # so we keep in_col=1 through the */ and pick up the property on the next line.
        local columns
        columns=$(awk '
            /@ORM\\Column|#\[ORM\\Column/ { in_col=1 }
            in_col && /^[[:space:]]+(private|protected|public)[[:space:]]/ {
                match($0, /\$([a-zA-Z_][a-zA-Z0-9_]*)/, arr)
                if (arr[1] != "") printf "%s ", arr[1]
                in_col=0
            }
        ' "$f")

        # Extract relations (ManyToOne, OneToMany, ManyToMany, OneToOne) + target entity
        local relations
        relations=$(awk '
            /@ORM\\(ManyToOne|OneToMany|ManyToMany|OneToOne)|#\[ORM\\(ManyToOne|OneToMany|ManyToMany|OneToOne)/ {
                rel_type = $0
                sub(/.*ORM\\/, "", rel_type); sub(/[\(\[].*/, "", rel_type)
                # extract targetEntity
                target = $0
                if (match(target, /targetEntity[=:][ \t]*["\x27]?([A-Za-z\\]+)["\x27]?/, arr)) {
                    t = arr[1]; sub(/.*\\/, "", t)
                    printf "%s→%s ", rel_type, t
                } else if (match(target, /targetEntity[=:][ \t]*([A-Za-z][A-Za-z0-9_]+)::class/, arr)) {
                    printf "%s→%s ", rel_type, arr[1]
                }
            }
        ' "$f")

        echo "## $classname" >> "$outfile"
        if [[ -n "$columns" ]]; then echo "  columns: $columns" >> "$outfile"; fi
        if [[ -n "$relations" ]]; then echo "  relations: $relations" >> "$outfile"; fi
        echo "" >> "$outfile"
    done < <(find "$entity_dir" -maxdepth 1 -name "*.php" | sort)

    echo "  ✓ entities.md    ($(wc -l < "$outfile" | tr -d ' ') lines)"
}

# ─────────────────────────────────────────────────────────────────────────────
# 4. hooks.md — Hook names discovered in source
# ─────────────────────────────────────────────────────────────────────────────
generate_hooks() {
    local outfile="$OUTPUT_DIR/hooks.md"
    local src_dir="$REPO_ROOT/src"

    # Extract literal hook names passed to dispatch* methods
    # Pattern: dispatchWithParameters('hookName', ...) or dispatchWithParameters("hookName", ...)
    local all_hooks
    all_hooks=$(grep -rh \
        "dispatchWithParameters\|dispatchRenderingWithParameters\|dispatchHook\b\|dispatchRendering\b" \
        "$src_dir" --include="*.php" 2>/dev/null \
        | grep -oP "(?<=\(')[a-zA-Z][a-zA-Z0-9_]+(?=')|(?<=\")[a-zA-Z][a-zA-Z0-9_]+(?=\")" \
        | grep -E "^(action|display|filter|header|footer|Dashboard|leftColumn|rightColumn)" \
        | grep -E "^.{8,}" \
        | sort -u || true)

    # Also check classes/ legacy dir
    local legacy_hooks=""
    if [[ -d "$REPO_ROOT/classes" ]]; then
        legacy_hooks=$(grep -rh \
            "Hook::exec\b\|Hook::execWithoutCache\b" \
            "$REPO_ROOT/classes" --include="*.php" 2>/dev/null \
            | grep -oP "(?<=\(')[a-zA-Z][a-zA-Z0-9_]+(?=')|(?<=\")[a-zA-Z][a-zA-Z0-9_]+(?=\")" \
            | grep -E "^(action|display|filter|header|footer|Dashboard|leftColumn|rightColumn)" \
            | grep -E "^.{8,}" \
            | sort -u || true)
    fi

    local hooks
    hooks=$(printf '%s\n%s\n' "$all_hooks" "$legacy_hooks" | sort -u | grep -v "^$" || true)

    local total
    total=$(echo "$hooks" | grep -c "." || true)

    {
        echo "# Hook Names Index (generated $TODAY)"
        echo "# $total unique hook names discovered via static source analysis"
        echo "#"
        echo "# Source: dispatchWithParameters / Hook::exec calls in src/ and classes/"
        echo "# Dynamic hooks (computed names, hook names in DB) are not listed here."
        echo ""
    } > "$outfile"

    # Categorize
    local action_hooks display_hooks other_hooks
    action_hooks=$(echo "$hooks" | grep "^action" || true)
    display_hooks=$(echo "$hooks" | grep "^display\|^header\|^footer\|^leftColumn\|^rightColumn\|^Dashboard" || true)
    other_hooks=$(echo "$hooks" | grep -v "^action\|^display\|^header\|^footer\|^leftColumn\|^rightColumn\|^Dashboard" || true)

    if [[ -n "$action_hooks" ]]; then
        echo "## Action hooks" >> "$outfile"
        echo "$action_hooks" | while IFS= read -r h; do echo "- $h"; done >> "$outfile"
        echo "" >> "$outfile"
    fi

    if [[ -n "$display_hooks" ]]; then
        echo "## Display hooks" >> "$outfile"
        echo "$display_hooks" | while IFS= read -r h; do echo "- $h"; done >> "$outfile"
        echo "" >> "$outfile"
    fi

    if [[ -n "$other_hooks" ]]; then
        echo "## Other hooks" >> "$outfile"
        echo "$other_hooks" | while IFS= read -r h; do echo "- $h"; done >> "$outfile"
        echo "" >> "$outfile"
    fi

    echo "  ✓ hooks.md       ($(wc -l < "$outfile" | tr -d ' ') lines)"
}

# ─────────────────────────────────────────────────────────────────────────────
# 5. skill symlinks — ensure every .ai skill is linked in .claude/skills/
# ─────────────────────────────────────────────────────────────────────────────
sync_skill_symlinks() {
    local skills_dir="$REPO_ROOT/.claude/skills"
    mkdir -p "$skills_dir"

    local created=0 already=0 skipped=0

    while IFS= read -r skill_md; do
        local skill_dir skill_name rel_path target
        skill_dir="$(dirname "$skill_md")"
        skill_name="$(basename "$skill_dir")"
        rel_path="${skill_dir#${REPO_ROOT}/}"
        target="../../${rel_path}"

        local link="$skills_dir/$skill_name"

        if [[ -e "$link" || -L "$link" ]]; then
            already=$((already + 1))
        else
            ln -s "$target" "$link"
            created=$((created + 1))
        fi
    done < <(find "$REPO_ROOT/.ai" -name "SKILL.md" | sort)

    echo "  ✓ skill symlinks ($created created, $already already linked)"
}

# ─────────────────────────────────────────────────────────────────────────────
# RUN ALL
# ─────────────────────────────────────────────────────────────────────────────
cd "$REPO_ROOT"

generate_cqrs
generate_routes
generate_entities
generate_hooks
sync_skill_symlinks

echo ""
echo "Done. Files written to $OUTPUT_DIR/"
echo "Total lines: $(cat "$OUTPUT_DIR"/*.md | wc -l | tr -d ' ')"

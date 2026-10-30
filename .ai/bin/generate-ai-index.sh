#!/usr/bin/env bash
# generate-ai-index.sh
#
# Generates compact AI-friendly codebase index files into .ai/generated/
# Output: cqrs.md, routes.md, entities.md, hooks.md, api-resources.md (when modules/ps_apiresources is installed)
#
# Usage:
#   bash .ai/bin/generate-ai-index.sh
#   bash .ai/bin/generate-ai-index.sh --output .ai/generated
#
# Design: pure grep/awk/sed — no runtime dependencies beyond bash + coreutils.

set -euo pipefail

# Force C locale for deterministic sort order across macOS and Linux
export LC_ALL=C

REPO_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)"
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

# Replace $final with $tmp atomically only if their content differs modulo the
# `(generated YYYY-MM-DD)` date line in the header. Avoids pushing date-only diffs.
finalize_outfile() {
    local final="$1" tmp="$2" label="$3"
    local lines

    if [[ -f "$final" ]] && diff -q \
            <(sed -E 's/\(generated [0-9]{4}-[0-9]{2}-[0-9]{2}\)/(generated DATE)/' "$final") \
            <(sed -E 's/\(generated [0-9]{4}-[0-9]{2}-[0-9]{2}\)/(generated DATE)/' "$tmp") \
            > /dev/null 2>&1; then
        rm -f "$tmp"
        lines=$(wc -l < "$final" | tr -d ' ')
        printf "  = %-13s (unchanged, %s lines)\n" "$label" "$lines"
    else
        mv -f "$tmp" "$final"
        lines=$(wc -l < "$final" | tr -d ' ')
        printf "  ✓ %-13s (%s lines)\n" "$label" "$lines"
    fi
}

# ─────────────────────────────────────────────────────────────────────────────
# 1. cqrs.md — Commands & Queries grouped by domain
# ─────────────────────────────────────────────────────────────────────────────
generate_cqrs() {
    local final="$OUTPUT_DIR/cqrs.md"
    local outfile="$final.tmp"
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

    finalize_outfile "$final" "$outfile" "cqrs.md"
}

# ─────────────────────────────────────────────────────────────────────────────
# 2. routes.md — Symfony routes grouped by routing file
# ─────────────────────────────────────────────────────────────────────────────
generate_routes() {
    local final="$OUTPUT_DIR/routes.md"
    local outfile="$final.tmp"
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

    finalize_outfile "$final" "$outfile" "routes.md"
}

# ─────────────────────────────────────────────────────────────────────────────
# 3. entities.md — Doctrine entity properties & relations
# ─────────────────────────────────────────────────────────────────────────────
generate_entities() {
    local final="$OUTPUT_DIR/entities.md"
    local outfile="$final.tmp"
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
                if (match($0, /\$[a-zA-Z_][a-zA-Z0-9_]*/)) {
                    printf "%s ", substr($0, RSTART+1, RLENGTH-1)
                }
                in_col=0
            }
        ' "$f")

        # Extract relations (ManyToOne, OneToMany, ManyToMany, OneToOne) + target entity
        local relations
        relations=$(awk '
            /@ORM\\(ManyToOne|OneToMany|ManyToMany|OneToOne)|#\[ORM\\(ManyToOne|OneToMany|ManyToMany|OneToOne)/ {
                rel_type = $0
                sub(/.*ORM\\/, "", rel_type); sub(/[\(\[].*/, "", rel_type)
                # extract targetEntity value
                target = $0
                if (match(target, /targetEntity[=:][ \t]*/)) {
                    val = substr(target, RSTART + RLENGTH)
                    # strip leading quotes
                    gsub(/^["'"'"']/, "", val)
                    # take until non-identifier char (allow backslash for FQCN)
                    if (match(val, /[A-Za-z\\][A-Za-z0-9_\\]*/)) {
                        t = substr(val, RSTART, RLENGTH)
                        sub(/.*\\/, "", t)
                        sub(/::class$/, "", t)
                        printf "%s→%s ", rel_type, t
                    }
                }
            }
        ' "$f")

        echo "## $classname" >> "$outfile"
        if [[ -n "$columns" ]]; then echo "  columns: $columns" >> "$outfile"; fi
        if [[ -n "$relations" ]]; then echo "  relations: $relations" >> "$outfile"; fi
        echo "" >> "$outfile"
    done < <(find "$entity_dir" -maxdepth 1 -name "*.php" | sort)

    finalize_outfile "$final" "$outfile" "entities.md"
}

# ─────────────────────────────────────────────────────────────────────────────
# 4. hooks.md — Hook names discovered in source
# ─────────────────────────────────────────────────────────────────────────────
generate_hooks() {
    local final="$OUTPUT_DIR/hooks.md"
    local outfile="$final.tmp"
    local src_dir="$REPO_ROOT/src"

    # Extract literal hook names passed to dispatch* methods
    # Pattern: dispatchWithParameters('hookName', ...) or dispatchWithParameters("hookName", ...)
    # Uses sed instead of grep -P for macOS/Linux portability
    local all_hooks
    all_hooks=$(grep -rh -E \
        "dispatchWithParameters|dispatchRenderingWithParameters|dispatchHook[^a-zA-Z]|dispatchRendering[^a-zA-Z]" \
        "$src_dir" --include="*.php" 2>/dev/null \
        | sed -n "s/.*(['\"][[:space:]]*\([a-zA-Z][a-zA-Z0-9_]*\)['\"].*/\1/p" \
        | grep -E "^(action|display|filter|header|footer|Dashboard|leftColumn|rightColumn)" \
        | grep -E "^.{8,}" \
        | sort -u || true)

    # Also check classes/ legacy dir
    local legacy_hooks=""
    if [[ -d "$REPO_ROOT/classes" ]]; then
        legacy_hooks=$(grep -rh -E \
            "Hook::exec[^a-zA-Z]|Hook::execWithoutCache[^a-zA-Z]" \
            "$REPO_ROOT/classes" --include="*.php" 2>/dev/null \
            | sed -n "s/.*(['\"][[:space:]]*\([a-zA-Z][a-zA-Z0-9_]*\)['\"].*/\1/p" \
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

    finalize_outfile "$final" "$outfile" "hooks.md"
}

# ─────────────────────────────────────────────────────────────────────────────
# 4b. api-resources.md — Admin API endpoints per domain (from modules/ps_apiresources when installed)
# ─────────────────────────────────────────────────────────────────────────────
# "name|path" lines, sorted by name: one per sub-folder and one per class at the root of Resources/.
list_api_domains() {
    local resources_dir="$1"
    {
        find -L "$resources_dir" -mindepth 1 -maxdepth 1 -type d | while IFS= read -r d; do echo "$(basename "$d")|$d"; done
        find -L "$resources_dir" -maxdepth 1 -name "*.php" | while IFS= read -r f; do echo "$(basename "$f" .php)|$f"; done
    } | sort -t '|' -k1,1
}

# One line per operation of a resource class. The method follows the operation attribute
# (UpdatePosition is a PATCH, see PrestaShopBundle\ApiPlatform\Metadata\UpdatePosition).
api_resource_operations() {
    local php="$1" class
    class="$(php_classname "$php")"
    [[ -n "$class" ]] || return 0
    awk -v cls="$class" '
        /new (CQRSGet|CQRSCreate|CQRSPartialUpdate|CQRSUpdate|CQRSDelete|PaginatedList|CQRSPaginate|CQRSGetCollection|UpdatePosition|GetCollection|Get|Post|Patch|Put|Delete)\(/ {
            match($0, /new [A-Za-z]+\(/); op = substr($0, RSTART + 4, RLENGTH - 5)
        }
        /uriTemplate: '"'"'/ && op != "" {
            match($0, /uriTemplate: '"'"'[^'"'"']+'"'"'/); uri = substr($0, RSTART + 14, RLENGTH - 15)
            method = "GET"
            if (op ~ /Create|Post/) method = "POST"
            if (op ~ /PartialUpdate|Patch|UpdatePosition/) method = "PATCH"
            if (op == "CQRSUpdate" || op == "Put") method = "PUT"
            if (op ~ /Delete/) method = "DELETE"
            if (op ~ /PaginatedList|CQRSPaginate|GetCollection/) method = "GET (collection)"
            printf "- %s %s → %s (%s)\n", method, uri, cls, op
            op = ""
        }' "$php"
}

generate_api_resources() {
    local final="$OUTPUT_DIR/api-resources.md"
    local outfile="$final.tmp"
    # The module folder is gitignored and may be a symlink to a separate clone, hence find -L.
    local resources_dir="$REPO_ROOT/modules/ps_apiresources/src/ApiPlatform/Resources"

    if [[ ! -d "$resources_dir" ]]; then
        printf "  - %-13s (skipped: modules/ps_apiresources not installed, previous file kept)\n" "api-resources"
        return 0
    fi

    # A domain is a sub-folder, or a resource class sitting at the root of Resources/ (Hook.php)
    local file_count domain_count
    file_count=$(find -L "$resources_dir" -name "*.php" -not -path "*/Dto/*" | wc -l | tr -d ' ')
    domain_count=$(list_api_domains "$resources_dir" | wc -l | tr -d ' ')

    {
        echo "# Admin API resources index (generated $TODAY)"
        echo "# $file_count resource classes · $domain_count domains · source: modules/ps_apiresources/src/ApiPlatform/Resources"
        echo "#"
        echo "# One line per operation: METHOD uri → resource class (operation attribute). Sub objects live in {Domain}/Dto/."
        echo "# Conventions: modules/ps_apiresources/.ai/CONTEXT.md · review checklist: modules/ps_apiresources/.ai/REVIEW.md"
        echo ""

        while IFS='|' read -r domain path; do
            echo "## $domain"
            if [[ -d "$path" ]]; then
                while IFS= read -r php; do
                    api_resource_operations "$php"
                done < <(find -L "$path" -maxdepth 1 -name "*.php" | sort)
            else
                api_resource_operations "$path"
            fi
            echo ""
        done < <(list_api_domains "$resources_dir")
    } > "$outfile"

    finalize_outfile "$final" "$outfile" "api-resources.md"
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
    done < <(find -L "$REPO_ROOT/.ai" -iname "skill.md" | sort)

    echo "  ✓ skill symlinks ($created created, $already already linked)"
}

# ─────────────────────────────────────────────────────────────────────────────
# 5b. path-scoped rules — .ai/rules/*.md is the source (description + paths list + body); Claude Code reads it
#     through a symlink, the other tools get a generated copy with their own frontmatter keys
#     (see STRUCTURE.md → "Path-scoped rules")
# ─────────────────────────────────────────────────────────────────────────────
RULE_COPIES_WRITTEN=0
RULE_COPIES_UNCHANGED=0

# write_rule_copy <target> <frontmatter lines> <rule name> <body>
write_rule_copy() {
    local target="$1" frontmatter="$2" rule_name="$3" body="$4"
    local tmp="$target.tmp"
    mkdir -p "$(dirname "$target")"
    # a former symlink to the source is replaced by the generated copy
    [[ -L "$target" ]] && rm -f "$target"
    {
        echo "---"
        echo "$frontmatter"
        echo "---"
        echo "<!-- Generated from .ai/rules/$rule_name.md by .ai/bin/generate-ai-index.sh: edit the source, not this copy -->"
        echo "$body"
    } > "$tmp"
    if [[ -f "$target" ]] && cmp -s "$tmp" "$target"; then
        rm -f "$tmp"
        RULE_COPIES_UNCHANGED=$((RULE_COPIES_UNCHANGED + 1))
    else
        mv -f "$tmp" "$target"
        RULE_COPIES_WRITTEN=$((RULE_COPIES_WRITTEN + 1))
    fi
}

sync_rules() {
    local rules_dir="$REPO_ROOT/.ai/rules"
    [[ -d "$rules_dir" ]] || return 0

    local links=0
    while IFS= read -r rule_md; do
        local rule_name description globs body link
        rule_name="$(basename "$rule_md" .md)"
        # frontmatter: `description: "..."` and `paths:` as a YAML list (one `- "glob"` per line)
        description=$(awk '/^---$/ {c++; next} c == 1 && /^description:/ {sub(/^description:[[:space:]]*/, ""); print; exit}' "$rule_md")
        globs=$(awk '
            /^---$/ {c++; next}
            c == 1 && /^paths:/ {p = 1; next}
            c == 1 && p && /^[[:space:]]*-[[:space:]]*/ {sub(/^[[:space:]]*-[[:space:]]*/, ""); gsub(/"/, ""); printf "%s%s", (n++ ? "," : ""), $0; next}
            c == 1 && p {p = 0}
        ' "$rule_md")
        body=$(awk '/^---$/ {c++; next} c >= 2 {print}' "$rule_md")
        if [[ -z "$globs" ]]; then
            echo "  ⚠ rule $rule_name: no \`paths:\` list in the frontmatter, skipped" >&2
            continue
        fi

        # Claude Code reads `paths` natively: a symlink to the source
        link="$REPO_ROOT/.claude/rules/$rule_name.md"
        mkdir -p "$(dirname "$link")"
        if [[ ! -L "$link" ]]; then
            rm -f "$link"
            ln -s "../../.ai/rules/$rule_name.md" "$link"
            links=$((links + 1))
        fi

        # Cursor (`globs`, unquoted comma-separated), Windsurf (`trigger: glob` + `globs`), Copilot (`applyTo`)
        write_rule_copy "$REPO_ROOT/.cursor/rules/$rule_name.mdc" \
            "description: $description"$'\n'"globs: $globs"$'\n'"alwaysApply: false" "$rule_name" "$body"
        write_rule_copy "$REPO_ROOT/.windsurf/rules/$rule_name.md" \
            "trigger: glob"$'\n'"globs: $globs"$'\n'"description: $description" "$rule_name" "$body"
        write_rule_copy "$REPO_ROOT/.github/instructions/$rule_name.instructions.md" \
            "applyTo: \"$globs\"" "$rule_name" "$body"
    done < <(find "$rules_dir" -maxdepth 1 -name "*.md" | sort)

    echo "  ✓ rules          ($links Claude symlink(s) created, $RULE_COPIES_WRITTEN tool copies written, $RULE_COPIES_UNCHANGED unchanged)"
}

# ─────────────────────────────────────────────────────────────────────────────
# 6. discover_module_skills — suggest component associations for module-shipped skills
# ─────────────────────────────────────────────────────────────────────────────
discover_module_skills() {
    local modules_dir="$REPO_ROOT/modules"
    [[ -d "$modules_dir" ]] || return 0

    local total=0 unattached=0
    local suggestions=""

    while IFS= read -r skill_md; do
        local skill_dir skill_name module_path module_name
        skill_dir="$(dirname "$skill_md")"
        skill_name="$(basename "$skill_dir")"
        # extract module dir from: modules/<name>/(.claude|.ai)/skills/<skill_name>/SKILL.md
        module_path="${skill_dir%/.claude/skills/*}"
        module_path="${module_path%/.ai/skills/*}"
        module_name="$(basename "$module_path")"

        total=$((total + 1))

        # Already associated under any .ai/Component/*/skills/<skill_name> symlink?
        if find "$REPO_ROOT/.ai/Component" -maxdepth 3 -name "$skill_name" -type l 2>/dev/null | grep -q .; then
            continue
        fi

        unattached=$((unattached + 1))
        suggestions+="    - $module_name/$skill_name → consider symlinking under .ai/Component/{?}/skills/$skill_name"$'\n'
    done < <(find "$modules_dir" -mindepth 4 -maxdepth 5 \
                  \( -path "*/.claude/skills/*/SKILL.md" -o -path "*/.ai/skills/*/SKILL.md" \) \
                  -iname "skill.md" 2>/dev/null | sort)

    if [[ $unattached -gt 0 ]]; then
        echo "  ⚠ module skills  ($total found, $unattached unattached):"
        printf "%s" "$suggestions"
    else
        echo "  ✓ module skills  ($total found, all associated)"
    fi
}

# ─────────────────────────────────────────────────────────────────────────────
# RUN ALL
# ─────────────────────────────────────────────────────────────────────────────
cd "$REPO_ROOT"

generate_cqrs
generate_routes
generate_entities
generate_hooks
generate_api_resources
discover_module_skills
sync_skill_symlinks
sync_rules

echo ""
echo "Done. Files written to $OUTPUT_DIR/"
echo "Total lines: $(cat "$OUTPUT_DIR"/*.md | wc -l | tr -d ' ')"

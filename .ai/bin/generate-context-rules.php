<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

/*
 * generate-context-rules.php
 *
 * Writes one Claude path-scoped rule per .ai/ Domain and Component context into
 * .claude/rules/domain/ and .claude/rules/component/: a `paths` frontmatter (the files the context
 * covers, the same globs check-conventions.php matches) followed by a link to its CONTEXT.md, loaded
 * only for the files its `paths` match. The content of a context is never copied: a rule changes only
 * when the files the context covers change.
 *
 * Usage (run by generate-ai-index.sh):
 *   php .ai/bin/generate-context-rules.php [--dry-run]
 *
 *   --dry-run  write nothing; exit 1 when a rule is missing, stale or left over (CI).
 *
 * Needs PHP 8.1+ only.
 */

require __DIR__ . '/context-index.php';

const REPO_ROOT = __DIR__ . '/../..';

/**
 * The .ai/ markdown files of the working tree, the hand-written rules of .ai/rules/ included.
 *
 * @return array<string, string> path => content
 */
function readAiDocs(): array
{
    $docs = [];
    $files = array_merge(
        glob(REPO_ROOT . '/.ai/*.md') ?: [],
        glob(REPO_ROOT . '/.ai/rules/*.md') ?: [],
        glob(REPO_ROOT . '/.ai/{Domain,Component}/*/CONTEXT.md', GLOB_BRACE) ?: []
    );
    foreach ($files as $file) {
        $docs[substr($file, strlen(REPO_ROOT) + 1)] = (string) file_get_contents($file);
    }

    return $docs;
}

/**
 * The markdown files of the given folders, relative to the repository root.
 *
 * @param string[] $folders
 *
 * @return string[]
 */
function listFiles(array $folders): array
{
    $files = [];
    foreach ($folders as $folder) {
        foreach (glob(REPO_ROOT . '/' . $folder . '/*.md') ?: [] as $file) {
            $files[] = substr($file, strlen(REPO_ROOT) + 1);
        }
    }

    return $files;
}

/**
 * @param string[] $argv
 */
function main(array $argv): int
{
    $dryRun = in_array('--dry-run', $argv, true);
    [$rules, $warnings] = contextRules(readAiDocs());
    foreach ($warnings as $warning) {
        fwrite(STDERR, '  ⚠ ' . $warning . "\n");
    }

    $changed = array_keys(array_filter($rules, fn (string $content, string $path): bool => !is_file(REPO_ROOT . '/' . $path)
        || file_get_contents(REPO_ROOT . '/' . $path) !== $content, ARRAY_FILTER_USE_BOTH));
    $leftOver = array_values(array_diff(listFiles(RULE_FOLDERS), array_keys($rules)));

    if ($dryRun) {
        if (!$changed && !$leftOver) {
            printf("The %d context rules are up to date.\n", count($rules));

            return 0;
        }
        foreach ($changed as $path) {
            printf("%s %s\n", file_exists(REPO_ROOT . '/' . $path) ? 'stale  ' : 'missing', $path);
        }
        foreach ($leftOver as $path) {
            printf("extra   %s\n", $path);
        }
        echo "The context rules are out of date: run bash .ai/bin/generate-ai-index.sh and commit the result.\n";

        return 1;
    }

    foreach ($leftOver as $path) {
        unlink(REPO_ROOT . '/' . $path);
    }
    foreach ($changed as $path) {
        if (!is_dir(dirname(REPO_ROOT . '/' . $path))) {
            mkdir(dirname(REPO_ROOT . '/' . $path), 0777, true);
        }
        file_put_contents(REPO_ROOT . '/' . $path, $rules[$path]);
    }
    printf("  ✓ context rules  (%d written, %d unchanged, %d removed)\n",
        count($changed), count($rules) - count($changed), count($leftOver));

    return 0;
}

exit(main($argv));

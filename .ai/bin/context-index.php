<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

/*
 * context-index.php
 *
 * The files each .ai/ Domain and Component context covers, as globs, and the path-scoped rules
 * built from them. Shared by check-conventions.php (the contexts a diff falls under) and
 * generate-context-rules.php (the Claude rules of .claude/rules/, loaded only for the files they
 * cover), so that both always name the same contexts for a file.
 *
 * Needs PHP 8.1+ only.
 */

const ROOTS = ['src/', 'classes/', 'controllers/', 'admin-dev/', 'themes/', 'tests/', 'app/',
    'config/', 'install-dev/', 'modules/', 'js/', 'translations/', 'bin/', '.github/',
    'tools/', 'var/', 'img/', 'mails/', 'pdf/', 'webservice/'];
const FILE_EXT = '#\.(php|ya?ml|ts|js|vue|twig|tpl|xml|json|scss|sql|feature|md|sh)$#';

// Paths the Layers tables do not say on their own.
const EXTRA_GLOBS = [
    'Component/Behat' => ['tests/Integration/Behaviour/**'],
    'Component/Controller' => ['**/Controller/Admin/**'],
    'Component/Forms' => ['src/PrestaShopBundle/Form/**', '**/Form/**/*Type.php'],
    'Component/Grid' => ['**/Grid/**'],
    'Component/Javascript' => ['admin-dev/themes/new-theme/js/**'],
    'Component/Migration' => ['controllers/admin/**'],
    'Component/Playwright' => ['tests/UI/**'],
    'Component/Smarty' => ['**/*.tpl'],
    'Component/Twig' => ['**/*.twig'],
];

// Files many areas share: a context naming one of them does not own it.
const SHARED_FILES = ['install-dev/data/db_structure.sql', 'install-dev/data/xml/configuration.xml',
    'composer.json', 'composer.lock', 'app/config/config.yml', 'package.json', 'package-lock.json'];

// Where the generated rules go, by kind of context.
const RULE_FOLDERS = ['Domain' => '.claude/rules/domain', 'Component' => '.claude/rules/component'];

/**
 * a/{x,y}/b becomes [a/x/b, a/y/b]; a single word placeholder like {Domain} is kept.
 *
 * @return string[]
 */
function expandBraces(string $pattern): array
{
    if (!preg_match('#\{([^{}]*,[^{}]*)\}#', $pattern, $match, PREG_OFFSET_CAPTURE)) {
        return [$pattern];
    }
    $out = [];
    foreach (explode(',', $match[1][0]) as $option) {
        $expanded = substr($pattern, 0, $match[0][1]) . $option . substr($pattern, $match[0][1] + strlen($match[0][0]));
        $out = array_merge($out, expandBraces($expanded));
    }

    return $out;
}

/**
 * A path of a Layers table, or null for what is not one (a class name, a shared file, a folder
 * too broad to say anything).
 */
function keepToken(string $token): ?string
{
    $token = rtrim(explode(' ', trim($token))[0], ',;');
    if (in_array($token, SHARED_FILES, true) || !str_contains($token, '/')) {
        return null;
    }
    $rooted = false;
    foreach (array_merge(ROOTS, ['…/', '.../']) as $root) {
        $rooted = $rooted || str_starts_with($token, $root);
    }
    if (!$rooted) {
        return null;
    }
    $segments = array_filter(explode('/', $token), fn (string $s): bool => $s !== '' && $s !== '…' && $s !== '...');
    if (count($segments) < 3 && !preg_match(FILE_EXT, $token)) {
        return null; // src/PrestaShopBundle/ and the like would match half the repository
    }

    return $token;
}

/**
 * A path of a Layers table as globs: a folder covers what it holds, a leading …/ any depth, a
 * {Name} placeholder one segment.
 *
 * @return string[]
 */
function tokenToGlobs(string $token): array
{
    $glob = (string) preg_replace('#^(…|\.\.\.)/#', '**/', $token);
    $glob = (string) preg_replace('#\{[A-Za-z_]+\}#', '*', $glob);
    if (str_ends_with($glob, '/')) {
        return [$glob . '**'];
    }
    if (preg_match(FILE_EXT, $glob)) {
        return [$glob];
    }

    return [$glob, $glob . '/**']; // a folder named without its trailing slash, or a file name pattern
}

/**
 * Turns a glob into an anchored regex: ** spans folders, * stays in one.
 */
function globToRegex(string $glob): string
{
    $regex = '';
    foreach (preg_split('#(\*\*/|\*\*|\*|\?)#', $glob, -1, PREG_SPLIT_DELIM_CAPTURE) as $chunk) {
        $regex .= match ($chunk) {
            '**/' => '(.*/)?',
            '**' => '.*',
            '*' => '[^/]*',
            '?' => '[^/]',
            default => preg_quote($chunk, '#'),
        };
    }

    return '#^' . $regex . '$#';
}

/**
 * Whether a rule can use a glob: a negation, a `..` segment or a `repo:path` value names no file
 * of the repository.
 */
function isUsableGlob(string $glob): bool
{
    return !str_contains($glob, '!') && !str_contains($glob, ':') && !in_array('..', explode('/', $glob), true);
}

/**
 * Rule => [globs, targets, external], from the `paths` frontmatter of every hand-written rule,
 * .ai/rules/*.md. A
 * target is a context the rule's body names (Component/X, Domain/X) or a root guide (MULTISTORE.md...).
 * External files are the `.ai/` files of another root the body names, such as
 * `modules/ps_apiresources/.ai/CONTEXT.md`: listed as they are, since they live outside the core
 * history (a composer-installed module, often a symlink to a separate clone).
 *
 * @param array<string, string> $docs path => content of the .ai/ markdown files, .ai/rules/*.md included
 *
 * @return array<string, array{0: string[], 1: string[], 2: string[]}>
 */
function pathRules(array $docs): array
{
    $rules = [];
    foreach ($docs as $path => $text) {
        if (!preg_match('#^\.ai/rules/[^/]+\.md$#', $path) || !preg_match('#^---\s*\n(.*?)\n---\s*\n(.*)$#s', $text, $parts)) {
            continue;
        }
        if (!preg_match('#^paths:\s*\n((?:\s+-\s+.*\n?)+)#m', $parts[1], $list)) {
            continue;
        }
        preg_match_all('#^\s+-\s+["\']?([^"\'\n]+?)["\']?\s*$#m', $list[1], $globs);
        $targets = [];
        preg_match_all('#((?:Component|Domain)/[A-Za-z]+)/CONTEXT\.md#', $parts[2], $contexts);
        foreach ($contexts[1] as $context) {
            $targets[$context] = true;
        }
        preg_match_all('#\b([A-Z][A-Z_-]*\.md)\b#', $parts[2], $guides);
        foreach ($guides[1] as $guide) {
            if (!in_array($guide, ['CONTEXT.md', 'STRUCTURE.md'], true) && isset($docs['.ai/' . $guide])) {
                $targets[$guide] = true;
            }
        }
        preg_match_all('#`([A-Za-z0-9_][A-Za-z0-9_./-]*/\.ai/[A-Za-z0-9_./-]+\.md)`#', $parts[2], $external);
        $rules[$path] = [$globs[1], array_keys($targets), array_values(array_unique($external[1]))];
    }

    return $rules;
}

/**
 * Context => globs of the files it covers: the paths of the Layers table of its CONTEXT.md, the
 * Core and Adapter namespaces of a domain, EXTRA_GLOBS and the hand-written rules naming it. A glob
 * of a src/Core, src/Adapter or src/PrestaShopBundle folder also covers the tests/Unit and
 * tests/Integration folders mirroring it: a test is judged by the context of the code it covers.
 * A context that declares no path gets an empty list.
 *
 * @param array<string, string> $docs path => content of the .ai/ markdown files, .ai/rules/*.md included
 *
 * @return array<string, string[]>
 */
function contextGlobs(array $docs): array
{
    $index = [];
    foreach ($docs as $path => $text) {
        if (!preg_match('#^\.ai/((?:Component|Domain)/[^/]+)/CONTEXT\.md$#', $path, $match)) {
            continue;
        }
        $context = $match[1];
        $isDomain = str_starts_with($context, 'Domain/');
        $name = explode('/', $context)[1];
        // Only the Layers table maps paths, never the whole text of the context.
        $body = preg_match('#^\#\# Layers\s*\n(.*?)(?=^\#\# |\z)#sm', $text, $layers) ? $layers[1] : '';
        $globs = [];
        preg_match_all('#`([^`]+)`#', $body, $tokens);
        foreach ($tokens[1] as $token) {
            $token = keepToken($token);
            if ($token === null) {
                continue;
            }
            foreach (expandBraces($token) as $pattern) {
                // A domain naming a shared folder does not own it: keep the folders carrying its name.
                if ($isDomain && str_ends_with($pattern, '/') && stripos($pattern, $name) === false) {
                    continue;
                }
                foreach (tokenToGlobs($pattern) as $glob) {
                    $globs[] = $glob;
                }
            }
        }
        // A domain owns its Core and Adapter namespaces even when its table names a few files only.
        if ($isDomain) {
            $globs[] = 'src/Core/Domain/' . $name . '/**';
            $globs[] = 'src/Adapter/' . $name . '/**';
        }
        foreach ($globs as $glob) {
            if (preg_match('#^src/((?:Core|Adapter|PrestaShopBundle)/.*/\*\*)$#', $glob, $mirror)) {
                $globs[] = 'tests/Unit/' . $mirror[1];
                $globs[] = 'tests/Integration/' . $mirror[1];
            }
        }
        $index[$context] = array_merge($globs, EXTRA_GLOBS[$context] ?? []);
    }
    foreach (pathRules($docs) as [$globs, $targets]) {
        foreach ($targets as $target) {
            if (isset($index[$target])) {
                $index[$target] = array_merge($index[$target], $globs);
            }
        }
    }
    foreach ($index as $context => $globs) {
        $globs = array_values(array_unique($globs));
        // A glob under a folder the list already covers adds nothing.
        $globs = array_values(array_filter($globs, function (string $glob) use ($globs): bool {
            foreach ($globs as $folder) {
                $prefix = substr($folder, 0, -2);
                if ($folder !== $glob && str_ends_with($folder, '/**') && !str_contains($prefix, '*') && str_starts_with($glob, $prefix)) {
                    return false;
                }
            }

            return true;
        }));
        sort($globs);
        $index[$context] = $globs;
    }
    ksort($index);

    return $index;
}

/**
 * The kebab-case file name of a context: AdminAPI => admin-api, CmsPageCategory => cms-page-category.
 */
function ruleName(string $context): string
{
    $name = explode('/', $context)[1];

    return strtolower((string) preg_replace('/(?<=[a-z0-9])(?=[A-Z])|(?<=[A-Z])(?=[A-Z][a-z])/', '-', $name));
}

/**
 * The generated rules: path => content (the `paths` frontmatter and a link to the context), one per
 * context declaring at least one usable path, and the warnings about what was left out.
 *
 * @param array<string, string> $docs path => content of the .ai/ markdown files, .ai/rules/*.md included
 *
 * @return array{0: array<string, string>, 1: string[]}
 */
function contextRules(array $docs): array
{
    $rules = [];
    $warnings = [];
    foreach (contextGlobs($docs) as $context => $globs) {
        $usable = array_values(array_filter($globs, 'isUsableGlob'));
        foreach (array_diff($globs, $usable) as $glob) {
            $warnings[] = sprintf('%s: glob "%s" left out, a rule cannot use it', $context, $glob);
        }
        if (!$usable) {
            // A rule without paths would apply to the whole repository.
            $warnings[] = sprintf('%s: no path in its Layers table nor in EXTRA_GLOBS, no rule written', $context);
            continue;
        }
        $source = '.ai/' . $context . '/CONTEXT.md';
        $folder = RULE_FOLDERS[explode('/', $context)[0]];
        $rule = "---\npaths:\n";
        foreach ($usable as $glob) {
            $rule .= '  - "' . $glob . "\"\n";
        }
        $rule .= "---\n";
        $rule .= sprintf("<!-- Generated from %s by .ai/bin/generate-ai-index.sh: to change the files it covers, edit the Layers table of the context, not this file -->\n\n", $source);
        $rule .= sprintf(
            "Read [`%s`](%s%s) before changing or reviewing these files, or answering a question about them: it holds the conventions of the %s %s.\n",
            $source,
            str_repeat('../', substr_count($folder, '/') + 1),
            $source,
            explode('/', $context)[1],
            strtolower(explode('/', $context)[0])
        );
        $rules[$folder . '/' . ruleName($context) . '.md'] = $rule;
    }
    ksort($rules);

    return [$rules, $warnings];
}

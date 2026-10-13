<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

/*
 * check-conventions.php
 *
 * Checks the diff of a branch against the conventions written in .ai/, before a pull
 * request is opened or while it is reviewed.
 *
 * Usage (from anywhere in the repository):
 *   php .ai/bin/check-conventions.php [<base-ref>] [--head <ref>] [--json]
 *
 *   <base-ref>  the branch the work targets. Default: develop on the remote pointing to
 *               PrestaShop/PrestaShop (else origin/develop, else develop). Pass it
 *               explicitly for a bug fix, e.g. upstream/9.2.x.
 *   --head      the commit to check (default: HEAD). The diff is <base-ref>...<head>.
 *   --json      print the result as JSON instead of text.
 *
 * Three sections:
 *   1. the .ai/ files to read: the root CONTEXT.md, then each Domain or Component
 *      CONTEXT.md whose "## Layers" table declares a changed path, and the cross-cutting
 *      guides the diff calls for. The .ai/ folder is read from the base ref.
 *   2. PR hygiene (root CONTEXT.md, "PR hygiene"), on the diff.
 *   3. Coding standards (root CONTEXT.md, "Coding standards") a script can decide, on the
 *      added code only.
 * A finding is FAIL (a reviewer will ask for it) or CHECK (worth a look, may be
 * legitimate: fix it or say why in the pull request). Exit code 1 when a FAIL exists.
 *
 * Needs PHP 8.1+ and git only.
 */

const REPO_ROOT = __DIR__ . '/../..';
const ROOTS = ['src/', 'classes/', 'controllers/', 'admin-dev/', 'themes/', 'tests/', 'app/',
    'config/', 'install-dev/', 'modules/', 'js/', 'translations/', 'bin/', '.github/',
    'tools/', 'var/', 'img/', 'mails/', 'pdf/', 'webservice/'];
const FILE_EXT = '#\.(php|ya?ml|ts|js|vue|twig|tpl|xml|json|scss|sql|feature|md|sh)$#';

// Path rules for what the Layers tables do not say on their own.
const EXTRA_RULES = [
    '#^tests/UI/#' => 'Component/Playwright',
    '#^tests/Integration/Behaviour/#' => 'Component/Behat',
    '#\.twig$#' => 'Component/Twig',
    '#\.tpl$#' => 'Component/Smarty',
    '#^src/PrestaShopBundle/Form/|/Form/.*Type\.php$#' => 'Component/Forms',
    '#^admin-dev/themes/new-theme/js/#' => 'Component/Javascript',
    '#(^|/)Controller/Admin/#' => 'Component/Controller',
    '#^src/Core/Grid/|/Grid/#' => 'Component/Grid',
];

// Files many areas share: a context naming one of them does not own it.
const SHARED_FILES = ['install-dev/data/db_structure.sql', 'install-dev/data/xml/configuration.xml',
    'composer.json', 'composer.lock', 'app/config/config.yml', 'package.json', 'package-lock.json'];

const LOCKS = ['composer.lock' => 'composer.json', 'package-lock.json' => 'package.json'];
const IDE_FILES = '#(^|/)(\.idea|\.vscode|\.fleet|\.DS_Store|Thumbs\.db|.*\.swp)(/|$)#';
const BUILD_LEFTOVERS = '#(^|/)buildLock$|\.orig$|\.rej$#';
const PRODUCTION_PHP = '#^(src|classes|controllers)/.*\.php$#';
const TEST_FILES = '#^tests/|\.spec\.(js|ts)$|\.feature$#';
const NEW_CODE = '#^(src|tests)/#';

/**
 * Runs git in the repository and returns its output, or null when it fails.
 *
 * @param string[] $args
 */
function git(array $args): ?string
{
    $process = proc_open(
        array_merge(['git', '-C', REPO_ROOT], $args),
        [1 => ['pipe', 'w'], 2 => ['pipe', 'w']],
        $pipes
    );
    if (!is_resource($process)) {
        return null;
    }
    $output = stream_get_contents($pipes[1]);
    stream_get_contents($pipes[2]);
    fclose($pipes[1]);
    fclose($pipes[2]);

    return proc_close($process) === 0 ? (string) $output : null;
}

function defaultBase(): string
{
    $remote = null;
    foreach (explode("\n", (string) git(['remote', '-v'])) as $line) {
        $parts = preg_split('/\s+/', trim($line));
        if (count($parts) >= 2 && preg_match('#[:/]PrestaShop/PrestaShop(\.git)?$#', $parts[1])) {
            $remote = $parts[0];
            break;
        }
    }
    foreach (array_filter([$remote ? $remote . '/develop' : null, 'origin/develop', 'develop']) as $candidate) {
        if (git(['rev-parse', '--verify', '--quiet', $candidate . '^{commit}']) !== null) {
            return $candidate;
        }
    }

    fwrite(STDERR, "No develop branch found: pass the base ref, e.g. upstream/9.2.x\n");
    exit(2);
}

/**
 * Files of a unified diff, with their status and their added lines (new line numbers).
 *
 * @return array<int, array{path: string, status: string, added: array<int, array{0: int, 1: string}>, missingEofNewline: bool}>
 */
function parseDiff(string $diff): array
{
    $files = [];
    $current = null;
    $newLine = 0;
    $lastSide = null;
    foreach (explode("\n", $diff) as $raw) {
        if (str_starts_with($raw, 'diff --git ')) {
            if ($current !== null) {
                $files[] = $current;
            }
            $path = preg_match('#^diff --git a/(.+) b/(.+)$#', $raw, $match) ? $match[2] : $raw;
            $current = ['path' => $path, 'status' => 'modified', 'added' => [], 'missingEofNewline' => false];
            $lastSide = null;
            continue;
        }
        if ($current === null) {
            continue;
        }
        if (str_starts_with($raw, 'new file mode')) {
            $current['status'] = 'added';
        } elseif (str_starts_with($raw, 'deleted file mode')) {
            $current['status'] = 'deleted';
        } elseif (str_starts_with($raw, '@@')) {
            $newLine = preg_match('#^@@ -\d+(?:,\d+)? \+(\d+)#', $raw, $match) ? (int) $match[1] : 0;
            $lastSide = null;
        } elseif (str_starts_with($raw, '+++') || str_starts_with($raw, '---')) {
            continue;
        } elseif (str_starts_with($raw, '\\')) {
            if ($lastSide === '+') {
                $current['missingEofNewline'] = true;
            }
        } elseif (str_starts_with($raw, '+')) {
            $current['added'][] = [$newLine, substr($raw, 1)];
            ++$newLine;
            $lastSide = '+';
        } elseif (str_starts_with($raw, '-')) {
            $lastSide = '-';
        } elseif (str_starts_with($raw, ' ')) {
            ++$newLine;
            $lastSide = ' ';
        }
    }
    if ($current !== null) {
        $files[] = $current;
    }

    return $files;
}

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
 * Turns a declared path into an anchored regex. Directories match as prefixes.
 */
function toRegex(string $pattern): string
{
    $anywhere = false;
    foreach (['…/', '.../'] as $lead) {
        if (str_starts_with($pattern, $lead)) {
            $pattern = substr($pattern, strlen($lead));
            $anywhere = true;
        }
    }
    $regex = '';
    foreach (preg_split('#(\{[A-Za-z_]+\}|\*)#', $pattern, -1, PREG_SPLIT_DELIM_CAPTURE) as $chunk) {
        if ($chunk === '*') {
            $regex .= '[^/]*';
        } elseif (preg_match('#^\{[A-Za-z_]+\}$#', $chunk)) {
            $regex .= '[^/]+';
        } else {
            $regex .= preg_quote($chunk, '#');
        }
    }
    if (!str_ends_with($pattern, '/') && !preg_match(FILE_EXT, $pattern)) {
        $regex .= '(/|$)'; // a directory named without its trailing slash
    }

    return '#' . ($anywhere ? '(^|/)' : '^') . $regex . '#';
}

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
 * The .ai/ folder at a git ref.
 *
 * @return array<string, string> path => content, markdown files only
 */
function readAiFolder(string $ref): array
{
    $docs = [];
    foreach (preg_split('/\s+/', trim((string) git(['ls-tree', '-r', '--name-only', $ref, '.ai']))) as $path) {
        if (str_ends_with($path, '.md')) {
            $docs[$path] = (string) git(['show', $ref . ':' . $path]);
        }
    }

    return $docs;
}

/**
 * Context name => [regexes, owned roots], from the Layers table of every CONTEXT.md.
 *
 * @param array<string, string> $docs
 *
 * @return array<string, array{0: string[], 1: string[]}>
 */
function buildIndex(array $docs): array
{
    $index = [];
    foreach ($docs as $path => $text) {
        if (!preg_match('#^\.ai/((?:Component|Domain)/[^/]+)/CONTEXT\.md$#', $path, $match)) {
            continue;
        }
        $context = $match[1];
        $isDomain = str_starts_with($context, 'Domain/');
        $name = explode('/', $context)[1];
        $body = preg_match('#^\#\# Layers\s*\n(.*?)(?=^\#\# )#sm', $text, $layers) ? $layers[1] : $text;
        $patterns = [];
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
                $patterns[$pattern] = true;
            }
        }
        // A domain owns its Core and Adapter namespaces even when its table names a few files only.
        if ($isDomain) {
            $patterns['src/Core/Domain/' . $name . '/'] = true;
            $patterns['src/Adapter/' . $name . '/'] = true;
        }
        $patterns = array_keys($patterns);
        sort($patterns);
        // Fallback for files nothing matched: a component owns the namespace its folders sit in,
        // a domain only the folders it declares.
        $owned = [];
        foreach ($patterns as $pattern) {
            if (str_ends_with($pattern, '/') && !str_contains($pattern, '{') && !str_contains($pattern, '*')
                && (str_starts_with($pattern, 'src/Core/') || str_starts_with($pattern, 'src/Adapter/'))
                && substr_count($pattern, '/') >= 3) {
                $owned[$isDomain ? $pattern : implode('/', array_slice(explode('/', $pattern), 0, 3)) . '/'] = true;
            }
        }
        $index[$context] = [array_map('toRegex', $patterns), array_keys($owned)];
    }

    return $index;
}

/**
 * @param array<int, array<string, mixed>> $files
 * @param array<string, string> $docs
 * @param string[] $allAiFiles
 *
 * @return array{always: string[], crossCutting: array<string, string>, matched: array<string, array{files: string[], companions: string[], skills: string[]}>, possibly: array<string, string[]>, unmatched: string[]}
 */
function contextsToRead(array $files, array $docs, array $allAiFiles): array
{
    $index = buildIndex($docs);
    $paths = array_column(array_filter($files, fn (array $f): bool => $f['status'] !== 'deleted'), 'path');
    $hits = [];
    $unmatched = [];
    foreach ($paths as $path) {
        // A test is judged by the context of the code it covers: tests/Unit/Core/Grid/X.php -> src/Core/Grid/X.php
        $candidates = [$path];
        if (preg_match('#^tests/(?:Unit|Integration)/(Core|Adapter|PrestaShopBundle)/(.*)$#', $path, $mirror)) {
            $candidates[] = 'src/' . $mirror[1] . '/' . $mirror[2];
        }
        $matched = [];
        foreach ($index as $context => [$regexes]) {
            foreach ($regexes as $regex) {
                foreach ($candidates as $candidate) {
                    if (preg_match($regex, $candidate)) {
                        $matched[$context] = true;
                    }
                }
            }
        }
        if (!$matched) {
            foreach ($index as $context => [, $roots]) {
                foreach ($roots as $root) {
                    foreach ($candidates as $candidate) {
                        if (str_starts_with($candidate, $root)) {
                            $matched[$context] = true;
                        }
                    }
                }
            }
        }
        foreach (EXTRA_RULES as $regex => $context) {
            if (isset($index[$context]) && preg_match($regex, $path)) {
                $matched[$context] = true;
            }
        }
        foreach (array_keys($matched) as $context) {
            $hits[$context][] = $path;
        }
        if (!$matched && !str_starts_with($path, '.ai/')) {
            $unmatched[] = $path;
        }
    }

    // Weak signal: a domain name as CamelCase words of a file name that matched no domain.
    $possibly = [];
    foreach (array_keys($index) as $context) {
        if (!str_starts_with($context, 'Domain/') || isset($hits[$context])) {
            continue;
        }
        preg_match_all('#[A-Z][a-z]+#', explode('/', $context)[1], $parts);
        foreach ($paths as $path) {
            preg_match_all('#[A-Z][a-z]+#', basename($path), $words);
            if ($parts[0] && !array_diff($parts[0], $words[0])) {
                $possibly[$context][] = basename($path);
            }
        }
    }

    // Code only: the .ai/ files describe these keywords without using them
    $added = '';
    foreach ($files as $file) {
        if (str_starts_with($file['path'], '.ai/')) {
            continue;
        }
        foreach ($file['added'] as [, $text]) {
            $added .= $text . "\n";
        }
    }
    $crossCutting = [];
    if (isset($docs['.ai/MULTISTORE.md']) && preg_match_all('#ShopConstraint|MultistoreConfiguration|isAllShopContext|Shop::getContext|id_shop\b|shopIds?\b|multistore#i', $added, $m)) {
        $crossCutting['.ai/MULTISTORE.md'] = 'diff adds ' . implode(', ', array_slice(array_unique($m[0]), 0, 3));
    }
    $services = array_filter($paths, fn (string $p): bool => (bool) preg_match('#(^|/)config/services|^app/config/|Resources/config/|Kernel\.php$|services\.ya?ml$#', $p));
    if (isset($docs['.ai/CONTAINERS.md']) && $services) {
        $crossCutting['.ai/CONTAINERS.md'] = implode(', ', array_slice($services, 0, 3));
    }
    if (isset($docs['.ai/GOTCHAS.md']) && array_filter(array_keys($hits), fn (string $c): bool => str_starts_with($c, 'Domain/'))) {
        $crossCutting['.ai/GOTCHAS.md'] = 'a domain is touched';
    }
    if (in_array('install-dev/data/db_structure.sql', $paths, true)) {
        $skill = '.ai/Component/Migration/skills/write-upgrade-sql-script/SKILL.md';
        $crossCutting['schema change'] = 'db_structure.sql changed: shops upgrading need an upgrade SQL script'
            . (in_array($skill, $allAiFiles, true) ? ' (skill write-upgrade-sql-script)' : '');
    }
    if (array_filter($paths, fn (string $p): bool => str_starts_with($p, '.ai/'))) {
        $crossCutting['.ai/STRUCTURE.md'] = 'the diff edits .ai/ itself';
    }

    $matched = [];
    uksort($hits, fn (string $a, string $b): int => [count($hits[$b]), $a] <=> [count($hits[$a]), $b]);
    foreach ($hits as $context => $contextFiles) {
        $companions = [];
        preg_match_all('#\]\(([A-Z][A-Z_-]*\.md)\)#', $docs['.ai/' . $context . '/CONTEXT.md'] ?? '', $links);
        foreach (array_unique($links[1]) as $link) {
            if ($link !== 'CONTEXT.md' && isset($docs['.ai/' . $context . '/' . $link])) {
                $companions[] = '.ai/' . $context . '/' . $link;
            }
        }
        $skills = [];
        $prefix = '.ai/' . $context . '/skills/';
        foreach ($allAiFiles as $aiFile) {
            if (str_starts_with($aiFile, $prefix) && str_ends_with($aiFile, 'SKILL.md')) {
                $skills[explode('/', substr($aiFile, strlen($prefix)))[0]] = true;
            }
        }
        $matched['.ai/' . $context . '/CONTEXT.md'] = ['files' => $contextFiles, 'companions' => $companions, 'skills' => array_keys($skills)];
    }

    return ['always' => ['.ai/CONTEXT.md'], 'crossCutting' => $crossCutting, 'matched' => $matched, 'possibly' => $possibly, 'unmatched' => $unmatched];
}

/**
 * @param array<int, array<string, mixed>> $files
 *
 * @return string[] FAIL and CHECK lines
 */
function checkHygiene(array $files): array
{
    $out = [];
    $present = array_column(array_filter($files, fn (array $f): bool => $f['status'] !== 'deleted'), 'path');
    $all = array_column($files, 'path');
    foreach ($present as $path) {
        foreach (LOCKS as $lock => $manifest) {
            if ($path === $lock || str_ends_with($path, '/' . $lock)) {
                $sibling = substr($path, 0, -strlen($lock)) . $manifest;
                if (!in_array($sibling, $all, true)) {
                    $out[] = sprintf('CHECK %s changed without %s: intended bump, or a stray install?', $path, $sibling);
                }
            }
        }
        if (preg_match(IDE_FILES, $path)) {
            $out[] = 'FAIL  IDE or OS file: ' . $path;
        }
        if (preg_match(BUILD_LEFTOVERS, $path)) {
            $out[] = 'FAIL  build leftover: ' . $path;
        }
    }
    foreach ($files as $file) {
        if ($file['missingEofNewline']) {
            $out[] = 'FAIL  no newline at end of file: ' . $file['path'];
        }
    }
    $code = array_filter($present, fn (string $p): bool => (bool) preg_match(PRODUCTION_PHP, $p));
    if ($code && !array_filter($present, fn (string $p): bool => (bool) preg_match(TEST_FILES, $p))) {
        $out[] = sprintf('CHECK %d PHP file(s) changed and no test touched (a bug fix or a behaviour change comes with a test)', count($code));
    }

    return $out;
}

/**
 * @return array{0: string, 1: string}|null
 */
function unsortedUses(string $content): ?array
{
    $uses = [];
    foreach (explode("\n", $content) as $line) {
        if (preg_match('#^(final |abstract |readonly )*(class|interface|trait|enum) #', $line)) {
            break;
        }
        if (preg_match('#^use ([^;]+);#', $line, $match)) {
            $uses[] = trim($match[1]);
        }
    }
    for ($i = 1; $i < count($uses); ++$i) {
        if (strcasecmp($uses[$i - 1], $uses[$i]) > 0) {
            return [$uses[$i - 1], $uses[$i]];
        }
    }

    return null;
}

/**
 * @param array<int, array<string, mixed>> $files
 * @param array<string, string> $docs
 *
 * @return string[] FAIL and CHECK lines
 */
function checkCodingStandards(array $files, array $docs, string $head): array
{
    // Markers come from the contexts and the root guides, not from the templates of skills or generated indexes
    $legacy = [];
    foreach ($docs as $context => $text) {
        if (!preg_match('#^\.ai/((Domain|Component)/[^/]+/CONTEXT|[^/]+)\.md$#', $context)) {
            continue;
        }
        foreach (explode("\n", $text) as $line) {
            if (preg_match('#do not add logic|do not extend#i', $line) && preg_match_all('#`([^`]+\.php)`#', $line, $match)) {
                foreach ($match[1] as $path) {
                    $legacy[$path] ??= $context;
                }
            }
        }
    }

    $out = [];
    foreach ($files as $file) {
        $path = $file['path'];
        if ($file['status'] === 'deleted' || !str_ends_with($path, '.php')) {
            continue;
        }
        $content = (string) git(['show', $head . ':' . $path]);
        $isNew = $file['status'] === 'added';
        if ($isNew && preg_match(NEW_CODE, $path) && !str_contains($content, 'declare(strict_types=1);')) {
            $out[] = sprintf('FAIL  %s: new PHP file without declare(strict_types=1)', $path);
        }
        if ($isNew && str_starts_with($path, 'src/')
            && preg_match('#^((?:final |abstract |readonly )*)class \w+#m', $content, $declaration)
            && !preg_match('#final|abstract#', $declaration[1])) {
            $out[] = sprintf('CHECK %s: new class neither final nor abstract (classes are final by default)', $path);
        }
        if (preg_match(NEW_CODE, $path)) {
            foreach ($file['added'] as [$number, $text]) {
                if (str_contains($text, 'Db::getInstance(')) {
                    $out[] = str_starts_with($path, 'src/')
                        ? sprintf('FAIL  %s:%d: Db::getInstance() in new code, use a Doctrine connection or repository', $path, $number)
                        : sprintf('CHECK %s:%d: Db::getInstance() in a test, fine when no container is booted, else use Doctrine', $path, $number);
                }
            }
        }
        $addsCode = array_filter($file['added'], fn (array $added): bool => trim($added[1]) !== '');
        if (isset($legacy[$path]) && $addsCode) {
            $out[] = sprintf('CHECK %s: %s says not to add logic to this legacy file, move it or say why', $path, $legacy[$path]);
        }
        if (array_filter($file['added'], fn (array $added): bool => str_starts_with($added[1], 'use '))
            && ($pair = unsortedUses($content)) !== null) {
            $out[] = sprintf('CHECK %s: use statements not sorted (%s before %s)', $path, $pair[0], $pair[1]);
        }
    }

    return $out;
}

/**
 * @param array<int, array<string, mixed>> $files
 * @param array<string, mixed> $contexts
 * @param string[] $hygiene
 * @param string[] $standards
 */
function render(string $base, string $head, array $files, array $contexts, array $hygiene, array $standards): string
{
    $lines = [sprintf('# Convention check of %s...%s (%d changed files, .ai/ read from %s)', $base, $head, count($files), $base), ''];
    $lines[] = '## Context files to read';
    $lines[] = '- .ai/CONTEXT.md (coding standards, CQRS, testing, PR hygiene)';
    foreach ($contexts['crossCutting'] as $doc => $why) {
        $lines[] = sprintf('- %s <- %s', $doc, $why);
    }
    foreach ($contexts['matched'] as $context => $hit) {
        $shown = implode(', ', array_slice($hit['files'], 0, 3)) . (count($hit['files']) > 3 ? sprintf(' (+%d more)', count($hit['files']) - 3) : '');
        $lines[] = sprintf('- %s <- %d file(s): %s', $context, count($hit['files']), $shown);
        foreach ($hit['companions'] as $companion) {
            $lines[] = '  companion: ' . $companion;
        }
        if ($hit['skills']) {
            $lines[] = '  skills (expected pattern): ' . implode(', ', $hit['skills']);
        }
    }
    if ($contexts['possibly']) {
        $lines[] = '';
        $lines[] = '## Possibly related domains (file name only, open if the change is about it)';
        foreach ($contexts['possibly'] as $context => $names) {
            $lines[] = sprintf('- .ai/%s/CONTEXT.md <- %s', $context, implode(', ', array_slice($names, 0, 3)));
        }
    }
    if ($contexts['unmatched']) {
        $lines[] = '';
        $lines[] = sprintf('## No .ai/ context (%d files, judge them on the root rules alone)', count($contexts['unmatched']));
        foreach (array_slice($contexts['unmatched'], 0, 30) as $path) {
            $lines[] = '- ' . $path;
        }
    }
    foreach (['PR hygiene' => $hygiene, 'Coding standards' => $standards] as $title => $findings) {
        $lines[] = '';
        $lines[] = '## ' . $title;
        $lines = array_merge($lines, $findings ?: ['No finding.']);
    }
    $lines[] = '';
    $lines[] = 'Fix every FAIL. Fix each CHECK, or say in the pull request why it stays.';

    return implode("\n", $lines) . "\n";
}

/**
 * @param string[] $argv
 */
function main(array $argv): int
{
    $base = null;
    $head = 'HEAD';
    $json = false;
    for ($i = 1; $i < count($argv); ++$i) {
        if ($argv[$i] === '--head') {
            $head = $argv[++$i] ?? 'HEAD';
        } elseif ($argv[$i] === '--json') {
            $json = true;
        } elseif ($argv[$i] === '--help' || $argv[$i] === '-h') {
            echo "Usage: php .ai/bin/check-conventions.php [<base-ref>] [--head <ref>] [--json]\n";

            return 0;
        } else {
            $base = $argv[$i];
        }
    }
    $base ??= defaultBase();
    foreach ([$base, $head] as $ref) {
        if (git(['rev-parse', '--verify', '--quiet', $ref . '^{commit}']) === null) {
            fwrite(STDERR, sprintf("Unknown git ref: %s\n", $ref));

            return 2;
        }
    }

    $files = parseDiff((string) git(['diff', '--no-color', '--find-renames', $base . '...' . $head]));
    $docs = readAiFolder($base);
    $allAiFiles = preg_split('/\s+/', trim((string) git(['ls-tree', '-r', '--name-only', $base, '.ai'])));
    $contexts = contextsToRead($files, $docs, $allAiFiles);
    $hygiene = checkHygiene($files);
    $standards = checkCodingStandards($files, $docs, $head);

    if ($json) {
        echo json_encode([
            'base' => $base,
            'head' => $head,
            'files' => array_map(fn (array $f): array => ['path' => $f['path'], 'status' => $f['status']], $files),
            'contexts' => $contexts,
            'hygiene' => $hygiene,
            'codingStandards' => $standards,
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), "\n";
    } else {
        echo render($base, $head, $files, $contexts, $hygiene, $standards);
    }

    foreach (array_merge($hygiene, $standards) as $finding) {
        if (str_starts_with($finding, 'FAIL')) {
            return 1;
        }
    }

    return 0;
}

exit(main($argv));

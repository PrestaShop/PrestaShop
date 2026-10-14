<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Addon\Theme;

/**
 * Reads an SVG out of a theme's assets directory so that a template can inline it.
 *
 * Templates can only reach files through {include}, which compiles what it reads as a Smarty
 * template - so an SVG carrying minified CSS ({fill:red}) or a custom property ({--c:red}) makes
 * the compiler throw. This reads the file verbatim instead.
 */
final class ThemeAssetInliner
{
    private const ALLOWED_EXTENSION = 'svg';

    /**
     * Existing asset roots, resolved, in the order they are searched.
     *
     * @var string[]
     */
    private array $roots = [];

    /**
     * @param string[] $assetRoots asset directories to search, most specific first
     */
    public function __construct(array $assetRoots)
    {
        foreach ($assetRoots as $root) {
            $resolved = realpath($root);
            if ($resolved !== false && is_dir($resolved)) {
                $this->roots[] = $resolved;
            }
        }
    }

    /**
     * @param string $relativePath path of the SVG relative to an asset root, e.g. 'img/icon.svg'
     *
     * @return string|null the file contents, or null when it cannot be read from any root
     */
    public function inline(string $relativePath): ?string
    {
        $path = $this->resolve($relativePath);
        if ($path === null) {
            return null;
        }

        $contents = @file_get_contents($path);

        return $contents === false ? null : $contents;
    }

    private function resolve(string $relativePath): ?string
    {
        $relativePath = trim($relativePath);
        if ($relativePath === '' || str_contains($relativePath, "\0")) {
            return null;
        }

        // The extension whitelist is the security control here: it is what keeps this from being an
        // arbitrary file reader for anything a template can name.
        if (strtolower(pathinfo($relativePath, PATHINFO_EXTENSION)) !== self::ALLOWED_EXTENSION) {
            return null;
        }

        // Leading separators would otherwise turn the argument into an absolute path.
        $relativePath = ltrim($relativePath, '/\\');

        foreach ($this->roots as $root) {
            $candidate = realpath($root . DIRECTORY_SEPARATOR . $relativePath);
            if ($candidate === false || !is_file($candidate)) {
                continue;
            }

            // realpath() has already collapsed any ../ and followed any symlink, so this is what
            // rejects a path that climbs - or is linked - out of the theme.
            if (str_starts_with($candidate, $root . DIRECTORY_SEPARATOR)) {
                return $candidate;
            }
        }

        return null;
    }
}

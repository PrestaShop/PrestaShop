<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

use MatthiasMullie\Minify\CSS;

class CssMinifierCore
{
    /**
     * @param string[] $files
     * @param string $destination
     *
     * @return string Minified data
     */
    public static function minify(array $files, $destination)
    {
        $minifier = self::getMinifier();

        foreach ($files as $file) {
            $minifier->add($file);
        }

        return $minifier->minify($destination);
    }

    /**
     * The minifier rewrites every relative url() so that it still resolves from the cache directory the
     * combined file is written to, and canImportByPath() decides what is relative. A quoted fragment-only
     * url("#gooey") reaches it as "#gooey", is taken for a file name and becomes url(../css/"#gooey"),
     * which is invalid. Fragments are how filter, clip-path and mask point at an SVG inlined in the page,
     * so they are left untouched; url(file.svg#fragment) carries a real path and is still rewritten.
     *
     * @return CSS
     */
    private static function getMinifier()
    {
        return new class() extends CSS {
            protected function canImportByPath($path)
            {
                // The url() pattern makes the quotes optional, so a quoted fragment reaches here with its
                // opening quote still attached: url("#gooey") arrives as "#gooey".
                $unquoted = ltrim((string) $path, '"\'');
                if (isset($unquoted[0]) && '#' === $unquoted[0]) {
                    return false;
                }

                return parent::canImportByPath($path);
            }
        };
    }
}

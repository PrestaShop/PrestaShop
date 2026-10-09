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
     * combined file is written to. It decides what is relative in canImportByPath(), which only excludes
     * data:, http(s): and root-relative paths — so a fragment-only url(#gooey) is taken for a file name and
     * becomes url(../css/#gooey), which resolves to nothing.
     *
     * Those fragments are how a filter, clip-path or mask points at an SVG that is inlined in the page, and
     * they are valid CSS. Excluding them here leaves them untouched; url(file.svg#fragment) still carries a
     * real path and is still rewritten.
     *
     * @return CSS
     */
    private static function getMinifier()
    {
        return new class() extends CSS {
            protected function canImportByPath($path)
            {
                if (isset($path[0]) && '#' === $path[0]) {
                    return false;
                }

                return parent::canImportByPath($path);
            }
        };
    }
}

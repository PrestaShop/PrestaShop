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
        $minifier = new CSS();

        foreach ($files as $file) {
            $minifier->add($file);
        }

        return $minifier->minify($destination);
    }
}

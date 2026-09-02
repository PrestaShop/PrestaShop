<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

use MatthiasMullie\Minify\JS;

class JsMinifierCore
{
    /**
     * @param array $files
     * @param string $destination
     *
     * @return string
     */
    public static function minify(array $files, $destination)
    {
        $minifier = new JS();

        foreach ($files as $file) {
            $minifier->add($file);
        }

        return $minifier->minify($destination);
    }
}

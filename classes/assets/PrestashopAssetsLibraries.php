<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
class PrestashopAssetsLibraries
{
    public const css = 'registerStylesheet';
    public const js = 'registerJavascript';

    /**
     * List of libraries available.
     *
     * @var array
     */
    private static $assetsLibraries = [
        'font-awesome' => [
            // array of array because a library can have js & css
            [
                'id' => 'font-awesome-css',
                'type' => self::css,
                'path' => '/themes/_libraries/font-awesome/css/font-awesome.min.css',
                'params' => ['media' => 'all', 'priority' => 10],
            ],
        ],
        // can imagine others libraries
    ];

    /**
     * Get Library files from name.
     *
     * @param string $name
     *
     * @return bool|mixed
     */
    public static function getAssetsLibraries($name)
    {
        return array_key_exists($name, self::$assetsLibraries) ? self::$assetsLibraries[$name] : false;
    }
}

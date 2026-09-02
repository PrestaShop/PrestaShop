<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Language\Pack\Loader;

/**
 * Interface LanguagePackLoaderInterface defines contract for language pack loader.
 */
interface LanguagePackLoaderInterface
{
    /**
     * Gets language pack data.
     *
     * @return array - array key is the language locale and the value is language name
     */
    public function getLanguagePackList();
}

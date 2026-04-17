<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Localization;

use Exception;
use Translate;

/**
 * Wraps the Legacy translation system
 *
 * @deprecated The legacy translation system will be removed in the next major version
 */
class LegacyTranslator
{
    /**
     * @param string $moduleName Module name
     * @param string $originalString String to translate
     * @param string|false $source
     * @param string|array|null $sprintf
     * @param bool $js
     * @param string|null $locale
     * @param bool $fallback [default=true] If true, this method falls back to the new translation system if no translation is found
     *
     * @return mixed|string
     *
     * @throws Exception
     */
    public function translate(
        $moduleName,
        $originalString,
        $source,
        $sprintf = null,
        $js = false,
        $locale = null,
        $fallback = true,
        $escape = true
    ) {
        return Translate::getModuleTranslation(
            $moduleName,
            $originalString,
            $source,
            $sprintf,
            $js,
            $locale,
            $fallback,
            $escape
        );
    }
}

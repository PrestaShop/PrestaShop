<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Localization\Pack\Loader;

use ErrorException;
use SimpleXMLElement;

/**
 * Class AbstractLocalizationPackLoader is abstract localization pack loader that implements XML loading from file.
 */
abstract class AbstractLocalizationPackLoader implements LocalizationPackLoaderInterface
{
    /**
     * Loads XML from local or remote file.
     *
     * @param string $file
     *
     * @return SimpleXMLElement|null
     */
    protected function loadXml($file)
    {
        try {
            $xml = simplexml_load_file($file);
        } catch (ErrorException) {
            return null;
        }

        return false === $xml ? null : $xml;
    }
}

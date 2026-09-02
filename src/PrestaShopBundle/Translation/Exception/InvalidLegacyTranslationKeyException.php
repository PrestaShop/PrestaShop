<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Translation\Exception;

use Exception;

/**
 * Thrown when an invalid key is found in a legacy translation file
 */
class InvalidLegacyTranslationKeyException extends Exception
{
    /**
     * @var string The invalid key
     */
    private $key = '';

    /**
     * @param string $missingElement The missing element
     * @param string $key The offending key
     *
     * @return InvalidLegacyTranslationKeyException
     */
    public static function missingElementFromKey($missingElement, $key)
    {
        $instance = new self(
            sprintf('Invalid key in legacy translation file: "%s" (missing %s)', $key, $missingElement)
        );
        $instance->setKey($key);

        return $instance;
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @param string $key
     */
    private function setKey($key)
    {
        $this->key = $key;
    }
}

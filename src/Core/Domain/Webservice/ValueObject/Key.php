<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Webservice\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Webservice\Exception\WebserviceConstraintException;

/**
 * Encapsulates webservice key value
 */
class Key
{
    /**
     * @var int Required length of webservice key
     */
    public const LENGTH = 32;

    /**
     * @var string
     */
    private $key;

    /**
     * @param string $key
     */
    public function __construct($key)
    {
        $this->assertKeyIsStringAndRequiredLength($key);

        $this->key = $key;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->key;
    }

    /**
     * @param string $key
     */
    private function assertKeyIsStringAndRequiredLength($key)
    {
        if (!is_string($key) || strlen($key) !== self::LENGTH) {
            throw new WebserviceConstraintException(sprintf('Webservice key must be string of %d characters length but %s given', self::LENGTH, var_export($key, true)), WebserviceConstraintException::INVALID_KEY);
        }
    }
}

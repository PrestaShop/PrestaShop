<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Customer\Exception\CustomerConstraintException;

/**
 * Every business in France is classified under an activity code
 * entitled APE - Activite Principale de l’Entreprise
 */
class ApeCode
{
    /**
     * @var string
     */
    private $code;

    /**
     * @var string
     */
    public const PATTERN = '/^[0-9]{1,2}?\.?[0-9]{1,2}[a-zA-Z]{1}$/s';

    /**
     * @param mixed $code
     */
    public function __construct($code)
    {
        $this->assertIsApeCode($code);

        $this->code = $code;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->code;
    }

    private function assertIsApeCode($code)
    {
        if (!is_string($code)
            || (!empty($code) && !((bool) preg_match(self::PATTERN, $code)))
        ) {
            throw new CustomerConstraintException(sprintf('Invalid ape code %s provided', var_export($code, true)), CustomerConstraintException::INVALID_APE_CODE);
        }
    }
}

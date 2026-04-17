<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\OrderState\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\OrderState\Exception\OrderStateConstraintException;

/**
 * Stores order state's name
 */
class Name
{
    /**
     * @var int Maximum allowed length for name
     */
    public const MAX_LENGTH = 255;

    /**
     * @var string
     */
    private $name;

    /**
     * @param string $name
     */
    public function __construct($name)
    {
        $this->assertNameDoesNotExceedAllowedLength($name);
        $this->assertNameIsValid($name);

        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @throws OrderStateConstraintException
     */
    private function assertNameIsValid($name)
    {
        $matchesFirstNamePattern = preg_match('/^[^0-9!<>,;?=+()@#"°{}_$%:¤|]*$/u', stripslashes($name));

        if (!$matchesFirstNamePattern) {
            throw new OrderStateConstraintException(sprintf('Order state name %s is invalid', var_export($name, true)), OrderStateConstraintException::INVALID_NAME);
        }
    }

    /**
     * @param string $name
     *
     * @throws OrderStateConstraintException
     */
    private function assertNameDoesNotExceedAllowedLength($name)
    {
        $name = html_entity_decode($name, ENT_COMPAT, 'UTF-8');

        if (self::MAX_LENGTH < mb_strlen($name, 'UTF-8')) {
            throw new OrderStateConstraintException(sprintf('Order state name is too long. Max allowed length is %s', self::MAX_LENGTH), OrderStateConstraintException::INVALID_NAME);
        }
    }
}

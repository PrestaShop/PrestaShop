<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\OrderReturnState\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\OrderReturnState\Exception\OrderReturnStateConstraintException;

/**
 * Stores order return state's name
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
     * @throws OrderReturnStateConstraintException
     */
    private function assertNameIsValid($name)
    {
        // WHY: the pattern must be the one the name field itself accepts (Validate::isGenericName),
        // otherwise this object rejects names the shop stores happily. It used to carry a person name
        // pattern that forbids digits and most punctuation, so a status legitimately called "Status 2"
        // could not be represented here at all.
        $matchesGenericNamePattern = preg_match('/^[^<>{}]*$/u', stripslashes($name));

        if (!$matchesGenericNamePattern) {
            throw new OrderReturnStateConstraintException(sprintf('Order return state name %s is invalid', var_export($name, true)), OrderReturnStateConstraintException::INVALID_NAME);
        }
    }

    /**
     * @param string $name
     *
     * @throws OrderReturnStateConstraintException
     */
    private function assertNameDoesNotExceedAllowedLength($name)
    {
        $name = html_entity_decode($name, ENT_COMPAT, 'UTF-8');

        if (self::MAX_LENGTH < mb_strlen($name, 'UTF-8')) {
            throw new OrderReturnStateConstraintException(sprintf('Order return state name is too long. Max allowed length is %s', self::MAX_LENGTH), OrderReturnStateConstraintException::INVALID_NAME);
        }
    }
}

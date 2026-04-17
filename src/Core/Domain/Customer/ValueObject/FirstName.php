<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Customer\Exception\CustomerConstraintException;

/**
 * Stores customer's first name
 */
class FirstName
{
    /**
     * @var int Maximum allowed length for first name
     */
    public const MAX_LENGTH = 255;

    /**
     * @var string
     */
    private $firstName;

    /**
     * @param string $firstName
     */
    public function __construct($firstName)
    {
        $this->assertFirstNameDoesNotExceedAllowedLength($firstName);
        $this->assertFirstNameIsValid($firstName);

        $this->firstName = $firstName;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     *
     * @throws CustomerConstraintException
     */
    private function assertFirstNameIsValid($firstName)
    {
        $matchesFirstNamePattern = preg_match('/^[^0-9!<>,;?=+()@#"°{}_$%:¤|]*$/u', stripslashes($firstName));

        if (!$matchesFirstNamePattern) {
            throw new CustomerConstraintException(sprintf('Customer first name %s is invalid', var_export($firstName, true)), CustomerConstraintException::INVALID_FIRST_NAME);
        }
    }

    /**
     * @param string $firstName
     *
     * @throws CustomerConstraintException
     */
    private function assertFirstNameDoesNotExceedAllowedLength($firstName)
    {
        $firstName = html_entity_decode($firstName, ENT_COMPAT, 'UTF-8');

        if (self::MAX_LENGTH < mb_strlen($firstName, 'UTF-8')) {
            throw new CustomerConstraintException(sprintf('Customer first name is too long. Max allowed length is %s', self::MAX_LENGTH), CustomerConstraintException::INVALID_FIRST_NAME);
        }
    }
}

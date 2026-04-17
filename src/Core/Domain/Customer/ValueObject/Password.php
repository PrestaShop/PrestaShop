<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Customer\Exception\CustomerConstraintException;

/**
 * Stores customer's plain password
 */
class Password
{
    /**
     * @var int Minimum required password length for customer
     */
    public const MIN_LENGTH = 5;

    /**
     * @var int Maximum allowed password length for customer.
     *
     * It's limited to 72 chars because of PASSWORD_BCRYPT algorithm
     * used in password_hash() function.
     */
    public const MAX_LENGTH = 72;

    /**
     * @var string
     */
    private $password;

    /**
     * @param string $password
     */
    public function __construct($password)
    {
        $this->assertPasswordIsWithinAllowedLength($password);

        $this->password = $password;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    private function assertPasswordIsWithinAllowedLength($password)
    {
        $length = mb_strlen($password, 'UTF-8');

        if (self::MIN_LENGTH > $length || $length > self::MAX_LENGTH) {
            throw new CustomerConstraintException(sprintf('Customer password length must be between %s and %s', self::MIN_LENGTH, self::MAX_LENGTH), CustomerConstraintException::INVALID_PASSWORD);
        }
    }
}

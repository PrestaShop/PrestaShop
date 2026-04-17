<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Customer\Exception\CustomerException;

/**
 * Stores method in which customer can be deleted.
 */
class CustomerDeleteMethod
{
    /**
     * If this option is used, then deleted customer can register again using same email.
     */
    public const ALLOW_CUSTOMER_REGISTRATION = 'allow_registration_after';

    /**
     * If this option is used, then deleted customer won't be able to register again using same email.
     */
    public const DENY_CUSTOMER_REGISTRATION = 'deny_registration_after';

    /**
     * @var string
     */
    private $method;

    /**
     * @param string $method
     *
     * @throws CustomerException
     */
    public function __construct($method)
    {
        $this->assertMethodIsDefined($method);

        $this->method = $method;
    }

    /**
     * Check if customer can register after it's deletion.
     */
    public function isAllowedToRegisterAfterDelete()
    {
        return self::ALLOW_CUSTOMER_REGISTRATION === $this->method;
    }

    /**
     * @return string[]
     */
    public static function getAvailableMethods()
    {
        return [self::ALLOW_CUSTOMER_REGISTRATION, self::DENY_CUSTOMER_REGISTRATION];
    }

    /**
     * @param string $method
     *
     * @throws CustomerException
     */
    private function assertMethodIsDefined($method)
    {
        $definedMethods = [self::ALLOW_CUSTOMER_REGISTRATION, self::DENY_CUSTOMER_REGISTRATION];

        if (!in_array($method, $definedMethods)) {
            throw new CustomerException(sprintf('Supplied customer delete method "%s" does not exists. Available methods are: %s.', $method, implode(',', $definedMethods)));
        }
    }
}

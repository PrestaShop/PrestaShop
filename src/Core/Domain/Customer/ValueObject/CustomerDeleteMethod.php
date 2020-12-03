<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
    const ALLOW_CUSTOMER_REGISTRATION = 'allow_registration_after';

    /**
     * If this option is used, then deleted customer won't be able to register again using same email.
     */
    const DENY_CUSTOMER_REGISTRATION = 'deny_registration_after';

    /**
     * @var int
     */
    private $method;

    /**
     * @param $method
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
     */
    private function assertMethodIsDefined($method)
    {
        $definedMethods = [self::ALLOW_CUSTOMER_REGISTRATION, self::DENY_CUSTOMER_REGISTRATION];

        if (!in_array($method, $definedMethods)) {
            throw new CustomerException(sprintf('Supplied customer delete method "%s" does not exists. Available methods are: %s.', $method, implode(',', $definedMethods)));
        }
    }
}

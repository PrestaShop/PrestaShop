<?php
/**
 * 2007-2019 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Customer\Exception\CustomerConstraintException;

/**
 * Stores customer's plain password
 */
class Password
{
    /**
     * @var string Minimum required password length for customer
     */
    const MIN_LENGTH = 5;

    /**
     * @var string Maximum allowed password length for customer.
     *
     * It's limited to 72 chars because of PASSWORD_BCRYPT algorithm
     * used in password_hash() function.
     */
    const MAX_LENGTH = 72;

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
        $length = function_exists('mb_strlen') ? mb_strlen($password, 'UTF-8') : strlen($password);

        if (self::MIN_LENGTH > $length || $length > self::MAX_LENGTH) {
            throw new CustomerConstraintException(
                sprintf('Customer password length must be between %s and %s', self::MIN_LENGTH, self::MAX_LENGTH),
                CustomerConstraintException::INVALID_PASSWORD
            );
        }
    }
}

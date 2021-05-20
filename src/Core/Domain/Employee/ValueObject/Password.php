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

namespace PrestaShop\PrestaShop\Core\Domain\Employee\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Employee\Exception\EmployeeConstraintException;

/**
 * Stores employee's plain password.
 */
class Password
{
    /**
     * @var string minimum required password length
     */
    public const MIN_LENGTH = 8;

    /**
     * @var string maximum allowed password length
     */
    public const MAX_LENGTH = 255;

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
            throw new EmployeeConstraintException(sprintf('Employee password length must be between %s and %s', self::MIN_LENGTH, self::MAX_LENGTH), EmployeeConstraintException::INVALID_PASSWORD);
        }
    }
}

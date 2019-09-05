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

namespace PrestaShop\PrestaShop\Core\Domain\Employee\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Employee\Exception\EmployeeConstraintException;

/**
 * Stores employee's last name
 */
class LastName
{
    /**
     * @var string Maximum allowed length for last name
     */
    const MAX_LENGTH = 255;

    /**
     * @var string
     */
    private $lastName;

    /**
     * @param string $lastName
     */
    public function __construct($lastName)
    {
        $this->assertLastNameDoesNotExceedAllowedLength($lastName);
        $this->assertLastNameIsValid($lastName);

        $this->lastName = $lastName;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     *
     * @throws EmployeeConstraintException
     */
    private function assertLastNameIsValid($lastName)
    {
        $matchesLastNamePattern = preg_match('/^[^0-9!<>,;?=+()@#"°{}_$%:¤|]*$/u', stripslashes($lastName));

        if (!$matchesLastNamePattern) {
            throw new EmployeeConstraintException(
                sprintf('Employee last name %s is invalid', var_export($lastName, true)),
                EmployeeConstraintException::INVALID_LAST_NAME
            );
        }
    }

    /**
     * @param string $lastName
     *
     * @throws EmployeeConstraintException
     */
    private function assertLastNameDoesNotExceedAllowedLength($lastName)
    {
        $lastName = html_entity_decode($lastName, ENT_COMPAT, 'UTF-8');

        $length = function_exists('mb_strlen') ? mb_strlen($lastName, 'UTF-8') : strlen($lastName);
        if (self::MAX_LENGTH < $length) {
            throw new EmployeeConstraintException(
                sprintf('Employee last name is too long. Max allowed length is %s', self::MAX_LENGTH),
                EmployeeConstraintException::INVALID_LAST_NAME
            );
        }
    }
}

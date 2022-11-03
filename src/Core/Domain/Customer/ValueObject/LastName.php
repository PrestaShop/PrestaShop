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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Customer\Exception\CustomerConstraintException;

/**
 * Stores customer's last name
 */
class LastName
{
    /**
     * @var int Maximum allowed length for customer's last name
     */
    public const MAX_LENGTH = 255;

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
     * @throws CustomerConstraintException
     */
    private function assertLastNameIsValid($lastName)
    {
        $matchesLastNamePattern = preg_match('/^[^0-9!<>,;?=+()@#"°{}_$%:¤|]*$/u', stripslashes($lastName));

        if (!$matchesLastNamePattern) {
            throw new CustomerConstraintException(sprintf('Customer last name %s is invalid', var_export($lastName, true)), CustomerConstraintException::INVALID_LAST_NAME);
        }
    }

    /**
     * @param string $lastName
     *
     * @throws CustomerConstraintException
     */
    private function assertLastNameDoesNotExceedAllowedLength($lastName)
    {
        $lastName = html_entity_decode($lastName, ENT_COMPAT, 'UTF-8');

        if (self::MAX_LENGTH < mb_strlen($lastName, 'UTF-8')) {
            throw new CustomerConstraintException(sprintf('Customer email is too long. Max allowed length is %s', self::MAX_LENGTH), CustomerConstraintException::INVALID_LAST_NAME);
        }
    }
}

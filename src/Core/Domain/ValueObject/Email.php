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

namespace PrestaShop\PrestaShop\Core\Domain\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Exception\DomainConstraintException;

/**
 * Class Email is responsible for providing valid email value.
 */
class Email
{
    /**
     * @var int Maximum allowed length for email
     */
    const MAX_LENGTH = 255;

    /**
     * @var string
     */
    private $email;

    /**
     * @param string $email
     *
     * @throws DomainConstraintException
     */
    public function __construct($email)
    {
        $this->assertEmailIsString($email);
        $this->assertEmailDoesNotExceedAllowedLength($email);
        $this->assertEmailIsValid($email);

        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->email;
    }

    /**
     * Check if given email is the same as current
     *
     * @param Email $email
     *
     * @return bool
     */
    public function isEqualTo(Email $email)
    {
        return $email->getValue() === $this->getValue();
    }

    /**
     * Assert that email is in valid format
     *
     * @param string $email
     *
     * @throws DomainConstraintException
     */
    private function assertEmailIsValid($email)
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new DomainConstraintException(
                sprintf('Email %s is invalid.', var_export($email, true)),
                DomainConstraintException::INVALID_EMAIL
            );
        }
    }

    /**
     * Assert that email length does not exceed allowed value
     *
     * @param string $email
     *
     * @throws DomainConstraintException
     */
    private function assertEmailDoesNotExceedAllowedLength($email)
    {
        $email = html_entity_decode($email, ENT_COMPAT, 'UTF-8');

        $length = function_exists('mb_strlen') ? mb_strlen($email, 'UTF-8') : strlen($email);
        if (self::MAX_LENGTH < $length) {
            throw new DomainConstraintException(
                sprintf('Email is too long. Max allowed length is %s', self::MAX_LENGTH),
                DomainConstraintException::INVALID_EMAIL
            );
        }
    }

    /**
     * Assert email is of type string
     *
     * @param string $email
     *
     * @throws DomainConstraintException
     */
    private function assertEmailIsString($email)
    {
        if (!is_string($email)) {
            throw new DomainConstraintException(
                'Email must be of type string',
                DomainConstraintException::INVALID_EMAIL
            );
        }
    }
}

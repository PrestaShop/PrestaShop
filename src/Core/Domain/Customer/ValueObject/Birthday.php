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

use DateTime;
use PrestaShop\PrestaShop\Core\Domain\Customer\Exception\CustomerConstraintException;

/**
 * Defines rules for customer birthday and stores it's value
 */
class Birthday
{
    /**
     * @var string empty birthday value
     *
     * It is used as a placeholder value when real birthday is not provided
     */
    public const EMPTY_BIRTHDAY = '0000-00-00';

    /**
     * @var string Date in format of Y-m-d or empty string for non defined birthday
     */
    private $birthday;

    /**
     * @return Birthday
     */
    public static function createEmpty()
    {
        return new self(self::EMPTY_BIRTHDAY);
    }

    /**
     * @param string $birthday
     */
    public function __construct($birthday)
    {
        $this->assertBirthdayIsInValidFormat($birthday);
        $this->assertBirthdayIsNotAFutureDate($birthday);

        $this->birthday = $birthday;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->birthday;
    }

    /**
     * @return bool
     */
    public function isEmpty()
    {
        return self::EMPTY_BIRTHDAY === $this->birthday;
    }

    /**
     * Birthday cannot be date in a future
     *
     * @param string $birthday
     */
    private function assertBirthdayIsNotAFutureDate($birthday)
    {
        if (self::EMPTY_BIRTHDAY === $birthday) {
            return;
        }

        $birthdayDateTime = new DateTime($birthday);
        $now = new DateTime();

        if ($birthdayDateTime > $now) {
            throw new CustomerConstraintException(sprintf('Invalid birthday "%s" provided. Birthday must be a past date.', $birthdayDateTime->format('Y-m-d')), CustomerConstraintException::INVALID_BIRTHDAY);
        }
    }

    /**
     * Assert that birthday is actual date
     *
     * @param string $birthday
     */
    private function assertBirthdayIsInValidFormat($birthday)
    {
        if (self::EMPTY_BIRTHDAY === $birthday) {
            return;
        }

        if (!is_string($birthday) || false === strtotime($birthday)) {
            throw new CustomerConstraintException(sprintf('Invalid birthday %s value provided.', var_export($birthday, true)), CustomerConstraintException::INVALID_BIRTHDAY);
        }
    }
}

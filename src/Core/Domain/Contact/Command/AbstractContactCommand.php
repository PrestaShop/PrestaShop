<?php
/**
 * 2007-2020 PrestaShop SA and Contributors
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
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Domain\Contact\Command;

/**
 * Class AbstractContactCommand is responsible for providing common behavior for AddContactCommand and EditContactCommand.
 */
abstract class AbstractContactCommand
{
    /**
     * @param array $values
     *
     * @return bool
     */
    protected function assertIsNotEmptyAndContainsAllNonEmptyStringValues(array $values)
    {
        $filterNonEmptyStrings = function ($value) {
            return is_string($value) && $value;
        };

        return !empty($values) && count($values) === count(array_filter($values, $filterNonEmptyStrings));
    }

    /**
     * @param $value
     *
     * @return false|int
     */
    protected function assertIsGenericName($value)
    {
        return preg_match('/^[^<>={}]*$/u', $value);
    }

    /**
     * @param array $values
     *
     * @return bool
     */
    protected function assertArrayContainsAllIntegerValues(array $values)
    {
        $filterAllIntegers = function ($value) {
            return is_int($value);
        };

        return !empty($values) && count($values) === count(array_filter($values, $filterAllIntegers));
    }
}

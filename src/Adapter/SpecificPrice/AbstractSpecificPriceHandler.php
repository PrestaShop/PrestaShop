<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
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

namespace PrestaShop\PrestaShop\Adapter\SpecificPrice;

use DateTime;
use PrestaShop\PrestaShop\Core\Domain\SpecificPrice\Exception\SpecificPriceConstraintException;
use PrestaShop\PrestaShop\Core\Domain\SpecificPrice\Exception\SpecificPriceException;
use PrestaShop\PrestaShop\Core\Domain\SpecificPrice\Exception\SpecificPriceNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\SpecificPrice\ValueObject\SpecificPriceId;
use PrestaShopException;
use SpecificPrice;

/**
 * Provides reusable methods for specific price handlers
 */
abstract class AbstractSpecificPriceHandler
{
    /**
     * Gets legacy SpecificPrice object
     *
     * @param SpecificPriceId $specificPriceId
     *
     * @return SpecificPrice
     *
     * @throws SpecificPriceException
     * @throws SpecificPriceNotFoundException
     */
    protected function getSpecificPrice(SpecificPriceId $specificPriceId): SpecificPrice
    {
        $specificPriceIdValue = $specificPriceId->getValue();

        try {
            $specificPrice = new SpecificPrice($specificPriceIdValue);
        } catch (PrestaShopException $e) {
            throw new SpecificPriceException('Failed to fetch new specific price', 0, $e);
        }

        if ($specificPrice->id !== $specificPriceIdValue) {
            throw new SpecificPriceNotFoundException(sprintf('Specific price with id "%s" was not found.', $specificPriceIdValue));
        }

        return $specificPrice;
    }

    /**
     * Checks if date range values are not inverse. (range from not bigger than range to)
     *
     * @param DateTime $from
     * @param DateTime $to
     *
     * @throws SpecificPriceConstraintException
     */
    protected function assertDateRangeIsNotInverse(DateTime $from, DateTime $to)
    {
        if ($from->diff($to)->invert) {
            throw new SpecificPriceConstraintException('The date time for specific price cannot be inverse', SpecificPriceConstraintException::INVALID_DATE_RANGE);
        }
    }
}

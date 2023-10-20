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

namespace PrestaShop\PrestaShop\Adapter\CatalogPriceRule;

use DateTime;
use PrestaShop\PrestaShop\Core\Domain\CatalogPriceRule\Exception\CatalogPriceRuleConstraintException;
use PrestaShop\PrestaShop\Core\Domain\CatalogPriceRule\Exception\CatalogPriceRuleException;
use PrestaShop\PrestaShop\Core\Domain\CatalogPriceRule\Exception\CatalogPriceRuleNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\CatalogPriceRule\ValueObject\CatalogPriceRuleId;
use PrestaShopException;
use SpecificPriceRule;

/**
 * Provides reusable methods for CatalogPriceRule handlers
 */
abstract class AbstractCatalogPriceRuleHandler
{
    /**
     * Gets legacy SpecificPriceRule
     *
     * @param CatalogPriceRuleId $catalogPriceRuleId
     *
     * @return SpecificPriceRule
     */
    protected function getSpecificPriceRule(CatalogPriceRuleId $catalogPriceRuleId): SpecificPriceRule
    {
        try {
            $specificPriceRule = new SpecificPriceRule($catalogPriceRuleId->getValue());
        } catch (PrestaShopException $e) {
            throw new CatalogPriceRuleException('Failed to create new SpecificPriceRule object', 0, $e);
        }

        if ($specificPriceRule->id !== $catalogPriceRuleId->getValue()) {
            throw new CatalogPriceRuleNotFoundException(sprintf('SpecificPriceRule with id "%s" was not found.', $catalogPriceRuleId->getValue()));
        }

        return $specificPriceRule;
    }

    /**
     * Deletes legacy SpecificPriceRule
     *
     * @param SpecificPriceRule $specificPriceRule
     *
     * @return bool
     *
     * @throws CatalogPriceRuleException
     */
    protected function deleteSpecificPriceRule(SpecificPriceRule $specificPriceRule)
    {
        try {
            return $specificPriceRule->delete();
        } catch (PrestaShopException $e) {
            throw new CatalogPriceRuleException(sprintf('An error occurred when deleting SpecificPriceRule object with id "%s".', $specificPriceRule->id));
        }
    }

    /**
     * @param DateTime $from
     * @param DateTime $to
     *
     * @throws CatalogPriceRuleConstraintException
     */
    protected function assertDateRangeIsNotInverse(DateTime $from, DateTime $to)
    {
        if ($from->diff($to)->invert) {
            throw new CatalogPriceRuleConstraintException('The date time for catalog price rule cannot be inverse', CatalogPriceRuleConstraintException::INVALID_DATE_RANGE);
        }
    }
}

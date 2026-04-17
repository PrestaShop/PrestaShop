<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
        } catch (PrestaShopException) {
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

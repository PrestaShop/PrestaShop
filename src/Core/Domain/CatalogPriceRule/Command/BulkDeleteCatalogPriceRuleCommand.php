<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\CatalogPriceRule\Command;

use PrestaShop\PrestaShop\Core\Domain\CatalogPriceRule\Exception\CatalogPriceRuleConstraintException;
use PrestaShop\PrestaShop\Core\Domain\CatalogPriceRule\ValueObject\CatalogPriceRuleId;

/**
 * Deletes catalog price rules in bulk acton
 */
class BulkDeleteCatalogPriceRuleCommand
{
    /**
     * @var CatalogPriceRuleId[]
     */
    private $catalogPriceRuleIds;

    /**
     * @param int[] $catalogPriceRuleIds
     *
     * @throws CatalogPriceRuleConstraintException
     */
    public function __construct(array $catalogPriceRuleIds)
    {
        $this->setCatalogPriceRuleIds($catalogPriceRuleIds);
    }

    /**
     * @return CatalogPriceRuleId[]
     */
    public function getCatalogPriceRuleIds()
    {
        return $this->catalogPriceRuleIds;
    }

    /**
     * @param int[] $catalogPriceRuleIds
     *
     * @throws CatalogPriceRuleConstraintException
     */
    private function setCatalogPriceRuleIds(array $catalogPriceRuleIds)
    {
        foreach ($catalogPriceRuleIds as $catalogPriceRuleId) {
            $this->catalogPriceRuleIds[] = new CatalogPriceRuleId($catalogPriceRuleId);
        }
    }
}

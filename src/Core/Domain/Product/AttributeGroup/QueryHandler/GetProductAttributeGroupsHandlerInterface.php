<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Product\AttributeGroup\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\QueryResult\AttributeGroup;
use PrestaShop\PrestaShop\Core\Domain\Product\AttributeGroup\Query\GetProductAttributeGroups;

/**
 * Handles @see GetProductAttributeGroups query
 */
interface GetProductAttributeGroupsHandlerInterface
{
    /**
     * @param GetProductAttributeGroups $query
     *
     * @return AttributeGroup[]
     */
    public function handle(GetProductAttributeGroups $query): array;
}

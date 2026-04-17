<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\AttributeGroup\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Query\GetAttributeGroupList;

/**
 * Defines contract to handle @see GetAttributeGroupList
 */
interface GetAttributeGroupListHandlerInterface
{
    /**
     * @param GetAttributeGroupList $query
     *
     * @return array
     */
    public function handle(GetAttributeGroupList $query): array;
}

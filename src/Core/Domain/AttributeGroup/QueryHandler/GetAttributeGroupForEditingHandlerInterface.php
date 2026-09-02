<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\AttributeGroup\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Query\GetAttributeGroupForEditing;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\QueryResult\EditableAttributeGroup;

/**
 * Describes attribute group for editing handler.
 */
interface GetAttributeGroupForEditingHandlerInterface
{
    /**
     * @param GetAttributeGroupForEditing $query
     *
     * @return EditableAttributeGroup
     */
    public function handle(GetAttributeGroupForEditing $query): EditableAttributeGroup;
}

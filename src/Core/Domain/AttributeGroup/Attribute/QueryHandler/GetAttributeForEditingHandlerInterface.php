<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Attribute\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Attribute\Query\GetAttributeForEditing;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Attribute\QueryResult\EditableAttribute;

/**
 * Describes attribute for editing handler.
 */
interface GetAttributeForEditingHandlerInterface
{
    /**
     * @param GetAttributeForEditing $query
     *
     * @return EditableAttribute
     */
    public function handle(GetAttributeForEditing $query): EditableAttribute;
}

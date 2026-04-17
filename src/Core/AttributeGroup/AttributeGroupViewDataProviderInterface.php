<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\AttributeGroup;

/**
 * Defines contract for services that provides attribute group view action data
 */
interface AttributeGroupViewDataProviderInterface
{
    /**
     * @param int $attributeGroupId
     *
     * @return bool
     */
    public function isColorGroup($attributeGroupId);

    /**
     * Provides the name of attribute group by its id
     *
     * @param int $attributeGroupId
     *
     * @return string
     */
    public function getAttributeGroupNameById($attributeGroupId);
}

<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Query;

use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;

/**
 * Get list of Attribute groups in the shop with their associated attributes
 */
class GetAttributeGroupList
{
    /**
     * @var ShopConstraint
     */
    private $shopConstraint;

    /**
     * @param ShopConstraint $shopConstraint
     */
    public function __construct(ShopConstraint $shopConstraint)
    {
        $this->shopConstraint = $shopConstraint;
    }

    /**
     * @return ShopConstraint
     */
    public function getShopConstraint(): ShopConstraint
    {
        return $this->shopConstraint;
    }
}

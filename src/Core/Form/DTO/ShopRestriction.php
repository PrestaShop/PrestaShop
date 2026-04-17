<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Form\DTO;

/**
 * Shop restriction object holds the shop ids for which restriction is being applied and the fields which are impacted
 * by the certain shop restriction.
 */
class ShopRestriction
{
    /**
     * @var int[]
     */
    private $shopIds;

    /**
     * @var ShopRestrictionField[]
     */
    private $shopRestrictionFields;

    /**
     * @param int[] $shopIds
     * @param ShopRestrictionField[] $shopRestrictionFields
     */
    public function __construct(array $shopIds, array $shopRestrictionFields)
    {
        $this->shopIds = $shopIds;
        $this->shopRestrictionFields = $shopRestrictionFields;
    }

    /**
     * @return int[]
     */
    public function getShopIds()
    {
        return $this->shopIds;
    }

    /**
     * @return ShopRestrictionField[]
     */
    public function getShopRestrictionFields()
    {
        return $this->shopRestrictionFields;
    }
}

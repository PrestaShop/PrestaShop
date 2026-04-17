<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Shop;

use Context as LegacyContext;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Multistore\MultistoreContextCheckerInterface;
use PrestaShop\PrestaShop\Core\Shop\ShopConstraintContextInterface;
use PrestaShop\PrestaShop\Core\Shop\ShopContextInterface;
use Shop;
use ShopGroup;

/**
 * This class will provide legacy shop context.
 */
class Context implements MultistoreContextCheckerInterface, ShopContextInterface, ShopConstraintContextInterface
{
    /**
     * Get shops list.
     *
     * @param bool $active
     * @param bool $get_as_list_id
     *
     * @return array
     */
    public function getShops($active = true, $get_as_list_id = false)
    {
        return Shop::getShops($active, Shop::getContextShopGroupID(), $get_as_list_id);
    }

    /**
     * Get current ID of shop if context is CONTEXT_SHOP.
     *
     * @return int
     */
    public function getContextShopID($null_value_without_multishop = false)
    {
        return Shop::getContextShopID($null_value_without_multishop);
    }

    public function getContextShopIds(): array
    {
        return array_map(static function ($shopId): int {
            return (int) $shopId;
        }, $this->getContextListShopID());
    }

    /**
     * Get a list of ID concerned by the shop context (E.g. if context is shop group, get list of children shop ID).
     *
     * @param bool|string $share If false, dont check share datas from group. Else can take a Shop::SHARE_* constant value
     *
     * @return array
     */
    public function getContextListShopID($share = false)
    {
        return Shop::getContextListShopID($share);
    }

    /**
     * Return the result of getContextListShopID() for customers usecase
     * This handles the "multishop sharing customer" feature setting
     *
     * @return array
     */
    public function getContextListShopIDUsingCustomerSharingSettings()
    {
        $groupSettings = Shop::getGroupFromShop(Shop::getContextShopID(), false);

        if (!empty($groupSettings['share_customer'])) {
            return Shop::getContextListShopID(Shop::SHARE_CUSTOMER);
        } else {
            return Shop::getContextListShopID();
        }
    }

    /**
     * Get if it's a Shop context.
     *
     * @return bool
     */
    public function isShopContext()
    {
        return Shop::getContext() === Shop::CONTEXT_SHOP;
    }

    /**
     * Check if shop context is Shop.
     *
     * @return bool
     */
    public function isSingleShopContext()
    {
        if (!Shop::isFeatureActive()) {
            return true;
        }

        return $this->isShopContext();
    }

    /**
     * Update Multishop context for only one shop.
     *
     * @param int $id Shop id to set in the current context
     */
    public function setShopContext($id)
    {
        Shop::setContext(Shop::CONTEXT_SHOP, $id);
    }

    /**
     * Update Multishop context for only one shop group.
     *
     * @param int $id Shop id to set in the current context
     */
    public function setShopGroupContext($id)
    {
        Shop::setContext(Shop::CONTEXT_GROUP, $id);
    }

    /**
     * Update Multishop context for only one shop group.
     *
     * @param int $id Shop id to set in the current context
     */
    public function setAllContext($id)
    {
        Shop::setContext(Shop::CONTEXT_ALL, $id);
    }

    public function getContextShopGroup()
    {
        return Shop::getContextShopGroup();
    }

    /**
     * Retrieve group ID of a shop.
     *
     * @param int $shopId
     * @param bool $asId
     *
     * @return int
     */
    public function getGroupFromShop($shopId, $asId = true)
    {
        return Shop::getGroupFromShop($shopId, $asId);
    }

    /**
     * @param int $shopGroupId
     *
     * @return ShopGroup
     */
    public function ShopGroup($shopGroupId)
    {
        return new ShopGroup($shopGroupId);
    }

    /**
     * {@inheritdoc}
     */
    public function isAllShopContext()
    {
        return Shop::getContext() === Shop::CONTEXT_ALL;
    }

    /**
     * {@inheritdoc}
     */
    public function isGroupShopContext()
    {
        return Shop::getContext() === Shop::CONTEXT_GROUP;
    }

    /**
     * Get list of all shop IDs.
     *
     * @return array
     */
    public function getAllShopIds()
    {
        return Shop::getCompleteListOfShopsID();
    }

    /**
     * {@inheritdoc}
     */
    public function getShopName()
    {
        return LegacyContext::getContext()->shop->name;
    }

    /**
     * @param bool $strict
     *
     * @return ShopConstraint
     */
    public function getShopConstraint(bool $strict = false): ShopConstraint
    {
        if ($this->isShopContext()) {
            return ShopConstraint::shop((int) $this->getContextShopID(), $strict);
        } elseif ($this->isGroupShopContext()) {
            return ShopConstraint::shopGroup((int) $this->getContextShopGroup()->id, $strict);
        }

        return ShopConstraint::allShops();
    }
}

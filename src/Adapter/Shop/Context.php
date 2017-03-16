<?php
/**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\Shop;

/**
 * This class will provide legacy shop context
 */
class Context
{
    /**
     * Get shops list
     *
     * @param bool $active
     * @param bool $get_as_list_id
     * @return array
     */
    public function getShops($active = true, $get_as_list_id = false)
    {
        return \ShopCore::getShops($active, \ShopCore::getContextShopGroupID(), $get_as_list_id);
    }

    /**
     * Get current ID of shop if context is CONTEXT_SHOP
     *
     * @return int
     */
    public function getContextShopID($null_value_without_multishop = false)
    {
        return \ShopCore::getContextShopID($null_value_without_multishop);
    }

    /**
     * Get a list of ID concerned by the shop context (E.g. if context is shop group, get list of children shop ID)
     *
     * @param bool|string $share If false, dont check share datas from group. Else can take a Shop::SHARE_* constant value
     * @return array
     */
    public function getContextListShopID($share = false)
    {
        return \ShopCore::getContextListShopID($share);
    }

    /**
     * Get if it's a GroupShop context
     *
     * @return bool
     */
    public function isShopGroupContext()
    {
        return \ShopCore::getContext() === \ShopCore::CONTEXT_GROUP;
    }

    /**
     * Get if it's a Shop context
     *
     * @return bool
     */
    public function isShopContext()
    {
        return \ShopCore::getContext() === \ShopCore::CONTEXT_SHOP;
    }

    /**
     * Get if it's a All context
     *
     * @return bool
     */
    public function isAllContext()
    {
        return \ShopCore::getContext() === \ShopCore::CONTEXT_ALL;
    }


}

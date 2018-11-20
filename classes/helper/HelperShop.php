<?php
/**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
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
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

class HelperShopCore extends Helper
{
    /**
     * Render shop list
     *
     * @return string
     */
    public function getRenderedShopList()
    {
        if (!Shop::isFeatureActive() || Shop::getTotalShops(false, null) < 2) {
            return '';
        }

        $shop_context = Shop::getContext();
        $context = Context::getContext();

        if ($this->noShopSelection()) {
            $current_shop_value = '';
        } elseif ($shop_context == Shop::CONTEXT_GROUP) {
            $current_shop_value = 'g-'.Shop::getContextShopGroupID();
        } else {
            $current_shop_value = 's-'.Shop::getContextShopID();
        }

        $tpl = $this->createTemplate('helpers/shops_list/list.tpl');
        $tpl->assign(array(
            'link' => $context->link,
            'tree' => Shop::getTree(),
            'current_shop_name' => $this->getCurrentShopName(),
            'current_shop_value' => $current_shop_value,
            'multishop_context' => $context->controller->multishop_context,
            'multishop_context_group' => $context->controller->multishop_context_group,
            'is_shop_context'  => ($context->controller->multishop_context & Shop::CONTEXT_SHOP),
            'is_group_context' => ($context->controller->multishop_context & Shop::CONTEXT_GROUP),
            'is_all_context' => ($context->controller->multishop_context & Shop::CONTEXT_ALL),
            'shop_context' => $shop_context,
            'url' => $_SERVER['REQUEST_URI'].(($_SERVER['QUERY_STRING']) ? '&' : '?').'setShopContext='
        ));

        return $tpl->fetch();
    }

    public function getCurrentShopName()
    {
        $shop_context = Shop::getContext();
        $tree = Shop::getTree();

        if ($this->noShopSelection()) {
            $current_shop_name = Translate::getAdminTranslation('All shops');
        } elseif ($shop_context == Shop::CONTEXT_GROUP) {
            $current_shop_name = sprintf(Translate::getAdminTranslation('%s group'), $tree[Shop::getContextShopGroupID()]['name']);
        } else {
            foreach ($tree as $group_id => $group_data) {
                foreach ($group_data['shops'] as $shop_id => $shop_data) {
                    if ($shop_id == Shop::getContextShopID()) {
                        $current_shop_name = $shop_data['name'];
                        break;
                    }
                }
            }
        }

        return $current_shop_name;
    }

    /**
     * @return bool
     */
    protected function noShopSelection()
    {
        $shop_context = Shop::getContext();
        $context = Context::getContext();

        return $shop_context == Shop::CONTEXT_ALL ||
        ($context->controller->multishop_context_group == false && $shop_context == Shop::CONTEXT_GROUP);
    }
}

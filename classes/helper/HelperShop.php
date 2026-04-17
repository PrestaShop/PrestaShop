<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
class HelperShopCore extends Helper
{
    /**
     * Render shop list.
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
            $current_shop_value = 'g-' . Shop::getContextShopGroupID();
        } else {
            $current_shop_value = 's-' . Shop::getContextShopID();
        }

        $tpl = $this->createTemplate('helpers/shops_list/list.tpl');
        $tpl->assign([
            'link' => $context->link,
            'tree' => Shop::getTree(),
            'current_shop_name' => $this->getCurrentShopName(),
            'current_shop_value' => $current_shop_value,
            'multishop_context' => $context->controller->multishop_context,
            'multishop_context_group' => $context->controller->multishop_context_group,
            'is_shop_context' => ($context->controller->multishop_context & Shop::CONTEXT_SHOP),
            'is_group_context' => ($context->controller->multishop_context & Shop::CONTEXT_GROUP),
            'is_all_context' => ($context->controller->multishop_context & Shop::CONTEXT_ALL),
            'shop_context' => $shop_context,
            'url' => $_SERVER['REQUEST_URI'] . (($_SERVER['QUERY_STRING']) ? '&' : '?') . 'setShopContext=',
        ]);

        return $tpl->fetch();
    }

    public function getCurrentShopName()
    {
        $shop_context = Shop::getContext();
        $tree = Shop::getTree();

        $current_shop_name = '';
        if ($this->noShopSelection()) {
            $current_shop_name = Context::getContext()->getTranslator()->trans('All stores');
        } elseif ($shop_context == Shop::CONTEXT_GROUP) {
            $current_shop_name = sprintf(Context::getContext()->getTranslator()->trans('%s group'), $tree[Shop::getContextShopGroupID()]['name']);
        } else {
            foreach ($tree as $group_data) {
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

        return $shop_context == Shop::CONTEXT_ALL
        || ($context->controller->multishop_context_group == false && $shop_context == Shop::CONTEXT_GROUP);
    }
}

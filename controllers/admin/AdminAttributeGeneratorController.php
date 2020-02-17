<?php
/**
 * 2007-2020 PrestaShop SA and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
@ini_set('max_execution_time', 3600);

/**
 * @property Product $object
 */
class AdminAttributeGeneratorControllerCore extends AdminController
{
    protected $combinations = [];

    /** @var Product */
    protected $product;

    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'product_attribute';
        $this->className = 'Product';
        $this->multishop_context_group = false;

        parent::__construct();
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);
        $this->addJS(_PS_JS_DIR_ . 'admin/attributes.js');
    }

    protected function addAttribute($attributes, $price = 0, $weight = 0)
    {
        foreach ($attributes as $attribute) {
            $price += (float) preg_replace('/[^0-9.-]/', '', str_replace(',', '.', Tools::getValue('price_impact_' . (int) $attribute)));
            $weight += (float) preg_replace('/[^0-9.]/', '', str_replace(',', '.', Tools::getValue('weight_impact_' . (int) $attribute)));
        }
        if ($this->product->id) {
            return [
                'id_product' => (int) $this->product->id,
                'price' => (float) $price,
                'weight' => (float) $weight,
                'ecotax' => 0,
                'quantity' => (int) Tools::getValue('quantity'),
                'reference' => pSQL($_POST['reference']),
                'default_on' => 0,
                'available_date' => '0000-00-00',
            ];
        }

        return [];
    }

    public static function createCombinations($list)
    {
        if (count($list) <= 1) {
            return count($list) ? array_map(function ($v) { return [$v]; }, $list[0]) : $list;
        }
        $res = [];
        $first = array_pop($list);
        foreach ($first as $attribute) {
            $tab = AdminAttributeGeneratorController::createCombinations($list);
            foreach ($tab as $to_add) {
                $res[] = is_array($to_add) ? array_merge($to_add, [$attribute]) : [$to_add, $attribute];
            }
        }

        return $res;
    }

    public function initProcess()
    {
        if (!defined('PS_MASS_PRODUCT_CREATION')) {
            define('PS_MASS_PRODUCT_CREATION', true);
        }

        if (Tools::isSubmit('generate')) {
            if ($this->access('edit')) {
                $this->action = 'generate';
            } else {
                $this->errors[] = $this->trans('You do not have permission to add this.', [], 'Admin.Notifications.Error');
            }
        }
        parent::initProcess();
    }

    public function postProcess()
    {
        $this->product = new Product((int) Tools::getValue('id_product'));
        $this->product->loadStockData();
        parent::postProcess();
    }

    public function processGenerate()
    {
        if (!is_array(Tools::getValue('options'))) {
            $this->errors[] = $this->trans('Please select at least one attribute.', [], 'Admin.Catalog.Notification');
        } else {
            $tab = array_values(Tools::getValue('options'));
            if (count($tab) && Validate::isLoadedObject($this->product)) {
                AdminAttributeGeneratorController::setAttributesImpacts($this->product->id, $tab);
                $this->combinations = array_values(AdminAttributeGeneratorController::createCombinations($tab));
                $values = array_values(array_map([$this, 'addAttribute'], $this->combinations));

                // @since 1.5.0
                if ($this->product->depends_on_stock == 0) {
                    $attributes = Product::getProductAttributesIds($this->product->id, true);
                    foreach ($attributes as $attribute) {
                        StockAvailable::removeProductFromStockAvailable($this->product->id, $attribute['id_product_attribute'], Context::getContext()->shop);
                    }
                }

                SpecificPriceRule::disableAnyApplication();

                $this->product->deleteProductAttributes();
                $this->product->generateMultipleCombinations($values, $this->combinations);

                // Reset cached default attribute for the product and get a new one
                Product::getDefaultAttribute($this->product->id, 0, true);
                Product::updateDefaultAttribute($this->product->id);

                // @since 1.5.0
                if ($this->product->depends_on_stock == 0) {
                    $attributes = Product::getProductAttributesIds($this->product->id, true);
                    $quantity = (int) Tools::getValue('quantity');
                    foreach ($attributes as $attribute) {
                        if (Shop::getContext() == Shop::CONTEXT_ALL) {
                            $shops_list = Shop::getShops();
                            if (is_array($shops_list)) {
                                foreach ($shops_list as $current_shop) {
                                    if (isset($current_shop['id_shop']) && (int) $current_shop['id_shop'] > 0) {
                                        StockAvailable::setQuantity($this->product->id, (int) $attribute['id_product_attribute'], $quantity, (int) $current_shop['id_shop']);
                                    }
                                }
                            }
                        } else {
                            StockAvailable::setQuantity($this->product->id, (int) $attribute['id_product_attribute'], $quantity);
                        }
                    }
                } else {
                    StockAvailable::synchronize($this->product->id);
                }

                SpecificPriceRule::enableAnyApplication();
                SpecificPriceRule::applyAllRules([(int) $this->product->id]);

                Tools::redirectAdmin($this->context->link->getAdminLink('AdminProducts') . '&id_product=' . (int) Tools::getValue('id_product') . '&updateproduct&key_tab=Combinations&conf=4');
            } else {
                $this->errors[] = $this->trans('Unable to initialize these parameters. A combination is missing or an object cannot be loaded.', [], 'Admin.Catalog.Notification');
            }
        }
    }

    protected static function setAttributesImpacts($id_product, $tab)
    {
        $attributes = [];
        foreach ($tab as $group) {
            foreach ($group as $attribute) {
                $price = preg_replace('/[^0-9.]/', '', str_replace(',', '.', Tools::getValue('price_impact_' . (int) $attribute)));
                $weight = preg_replace('/[^0-9.]/', '', str_replace(',', '.', Tools::getValue('weight_impact_' . (int) $attribute)));
                $attributes[] = '(' . (int) $id_product . ', ' . (int) $attribute . ', ' . (float) $price . ', ' . (float) $weight . ')';
            }
        }

        return Db::getInstance()->execute('
		INSERT INTO `' . _DB_PREFIX_ . 'attribute_impact` (`id_product`, `id_attribute`, `price`, `weight`)
		VALUES ' . implode(',', $attributes) . '
		ON DUPLICATE KEY UPDATE `price` = VALUES(price), `weight` = VALUES(weight)');
    }

    public function initGroupTable()
    {
        $combinations_groups = $this->product->getAttributesGroups($this->context->language->id);
        $attributes = [];
        $impacts = Product::getAttributesImpacts($this->product->id);
        foreach ($combinations_groups as &$combination) {
            $target = &$attributes[$combination['id_attribute_group']][$combination['id_attribute']];
            $target = $combination;
            if (isset($impacts[$combination['id_attribute']])) {
                $target['price'] = $impacts[$combination['id_attribute']]['price'];
                $target['weight'] = $impacts[$combination['id_attribute']]['weight'];
            }
        }
        $this->context->smarty->assign([
            'currency_sign' => $this->context->currency->sign,
            'weight_unit' => Configuration::get('PS_WEIGHT_UNIT'),
            'attributes' => $attributes,
        ]);
    }

    public function initPageHeaderToolbar()
    {
        parent::initPageHeaderToolbar();

        $this->page_header_toolbar_title = $this->trans('Attributes generator', [], 'Admin.Catalog.Feature');
        $this->page_header_toolbar_btn['back'] = [
            'href' => $this->context->link->getAdminLink('AdminProducts') . '&id_product=' . (int) Tools::getValue('id_product') . '&updateproduct&key_tab=Combinations',
            'desc' => $this->trans('Back to the product', [], 'Admin.Catalog.Feature'),
        ];
    }

    public function initBreadcrumbs($tab_id = null, $tabs = null)
    {
        $this->display = 'generator';

        return parent::initBreadcrumbs();
    }

    public function initContent()
    {
        if (!Combination::isFeatureActive()) {
            $adminPerformanceUrl = $this->context->link->getAdminLink('AdminPerformance');

            $url = '<a href="' . $adminPerformanceUrl . '#featuresDetachables">' .
                    $this->trans('Performance', [], 'Admin.Global') . '</a>';
            $this->displayWarning($this->trans('This feature has been disabled. You can activate it here: %link%.', ['%link%' => $url], 'Admin.Catalog.Notification'));

            return;
        }

        // Init toolbar
        $this->initPageHeaderToolbar();
        $this->initGroupTable();

        $attributes = Attribute::getAttributes(Context::getContext()->language->id, true);
        $attribute_js = [];

        foreach ($attributes as $k => $attribute) {
            $attribute_js[$attribute['id_attribute_group']][$attribute['id_attribute']] = $attribute['name'];
        }

        $attribute_groups = AttributeGroup::getAttributesGroups($this->context->language->id);
        $this->product = new Product((int) Tools::getValue('id_product'));

        $this->context->smarty->assign([
            'tax_rates' => $this->product->getTaxesRate(),
            'generate' => isset($_POST['generate']) && !count($this->errors),
            'combinations_size' => count($this->combinations),
            'product_name' => $this->product->name[$this->context->language->id],
            'product_reference' => $this->product->reference,
            'url_generator' => self::$currentIndex . '&id_product=' . (int) Tools::getValue('id_product') . '&attributegenerator&token=' . Tools::getValue('token'),
            'attribute_groups' => $attribute_groups,
            'attribute_js' => $attribute_js,
            'toolbar_btn' => $this->toolbar_btn,
            'toolbar_scroll' => true,
            'show_page_header_toolbar' => $this->show_page_header_toolbar,
            'page_header_toolbar_title' => $this->page_header_toolbar_title,
            'page_header_toolbar_btn' => $this->page_header_toolbar_btn,
        ]);
    }
}

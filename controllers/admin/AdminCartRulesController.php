<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

/**
 * @property CartRule $object
 */
class AdminCartRulesControllerCore extends AdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'cart_rule';
        $this->className = 'CartRule';
        $this->lang = true;
        $this->addRowAction('edit');
        $this->addRowAction('delete');
        $this->_orderWay = 'DESC';

        parent::__construct();

        $this->bulk_actions = [
            'delete' => [
                'text' => $this->trans('Delete selected', [], 'Admin.Actions'),
                'icon' => 'icon-trash',
                'confirm' => $this->trans('Delete selected items?', [], 'Admin.Notifications.Warning'),
            ],
        ];

        $this->fields_list = [
            'id_cart_rule' => ['title' => $this->trans('ID', [], 'Admin.Global'), 'align' => 'center', 'class' => 'fixed-width-xs'],
            'name' => ['title' => $this->trans('Name', [], 'Admin.Global')],
            'priority' => ['title' => $this->trans('Priority', [], 'Admin.Global'), 'align' => 'center', 'class' => 'fixed-width-xs'],
            'code' => ['title' => $this->trans('Code', [], 'Admin.Global'), 'class' => 'fixed-width-sm'],
            'quantity' => ['title' => $this->trans('Quantity', [], 'Admin.Catalog.Feature'), 'align' => 'center', 'class' => 'fixed-width-xs'],
            'date_to' => ['title' => $this->trans('Expiration date', [], 'Admin.Catalog.Feature'), 'type' => 'datetime', 'class' => 'fixed-width-lg'],
            'active' => ['title' => $this->trans('Status', [], 'Admin.Global'), 'active' => 'status', 'type' => 'bool', 'align' => 'center', 'class' => 'fixed-width-xs', 'orderby' => false],
        ];
    }

    public function ajaxProcessLoadCartRules()
    {
        if (!$this->access('view')) {
            return die(json_encode(['error' => 'You do not have the right permission']));
        }

        $type = $token = $search = '';
        $limit = $count = $id_cart_rule = 0;
        if (Tools::getIsset('limit')) {
            $limit = Tools::getValue('limit');
        }

        if (Tools::getIsset('type')) {
            $type = Tools::getValue('type');
        }

        if (Tools::getIsset('count')) {
            $count = Tools::getValue('count');
        }

        if (Tools::getIsset('id_cart_rule')) {
            $id_cart_rule = Tools::getValue('id_cart_rule');
        }

        if (Tools::getIsset('search')) {
            $search = Tools::getValue('search');
        }

        $page = (int) floor($count / $limit);

        $html = '';
        $next_link = '';

        if (($page * $limit) + 1 == $count || $count == 0) {
            if ($count == 0) {
                $count = 1;
            }

            /** @var CartRule $current_object */
            $current_object = $this->loadObject(true);
            $cart_rules = $current_object->getAssociatedRestrictions('cart_rule', false, true, $page * $limit, $limit, $search);

            if ($type == 'selected') {
                $i = 1;
                foreach ($cart_rules['selected'] as $cart_rule) {
                    $html .= '<option value="' . (int) $cart_rule['id_cart_rule'] . '">&nbsp;' . Tools::safeOutput($cart_rule['name']) . '</option>';
                    if ($i == $limit) {
                        break;
                    }
                    ++$i;
                }
                if ($i == $limit) {
                    $next_link = Context::getContext()->link->getAdminLink('AdminCartRules') . '&ajaxMode=1&ajax=1&id_cart_rule=' . (int) $id_cart_rule . '&action=loadCartRules&limit=' . (int) $limit . '&type=selected&count=' . ($count - 1 + count($cart_rules['selected']) . '&search=' . urlencode($search));
                }
            } else {
                $i = 1;
                foreach ($cart_rules['unselected'] as $cart_rule) {
                    $html .= '<option value="' . (int) $cart_rule['id_cart_rule'] . '">&nbsp;' . Tools::safeOutput($cart_rule['name']) . '</option>';
                    if ($i == $limit) {
                        break;
                    }
                    ++$i;
                }
                if ($i == $limit) {
                    $next_link = Context::getContext()->link->getAdminLink('AdminCartRules') . '&ajaxMode=1&ajax=1&id_cart_rule=' . (int) $id_cart_rule . '&action=loadCartRules&limit=' . (int) $limit . '&type=unselected&count=' . ($count - 1 + count($cart_rules['unselected']) . '&search=' . urlencode($search));
                }
            }
        }
        echo json_encode(['html' => $html, 'next_link' => $next_link]);
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);
        $this->addJqueryPlugin(['typewatch', 'fancybox', 'autocomplete']);
    }

    public function initPageHeaderToolbar()
    {
        if (empty($this->display)) {
            $this->page_header_toolbar_btn['new_cart_rule'] = [
                'href' => self::$currentIndex . '&addcart_rule&token=' . $this->token,
                'desc' => $this->trans('Add new cart rule', [], 'Admin.Catalog.Feature'),
                'icon' => 'process-icon-new',
            ];
        }

        parent::initPageHeaderToolbar();
    }

    public function postProcess()
    {
        if (Tools::isSubmit('submitAddcart_rule') || Tools::isSubmit('submitAddcart_ruleAndStay')) {
            // If the reduction is associated to a specific product, then it must be part of the product restrictions
            if ((int) Tools::getValue('reduction_product') && Tools::getValue('apply_discount_to') == 'specific' && Tools::getValue('apply_discount') != 'off') {
                $reduction_product = (int) Tools::getValue('reduction_product');

                // First, check if it is not already part of the restrictions
                $already_restricted = false;
                if (is_array($rule_group_array = Tools::getValue('product_rule_group')) && count($rule_group_array) && Tools::getValue('product_restriction')) {
                    foreach ($rule_group_array as $rule_group_id) {
                        if (is_array($rule_array = Tools::getValue('product_rule_' . $rule_group_id)) && count($rule_array)) {
                            foreach ($rule_array as $rule_id) {
                                if (Tools::getValue('product_rule_' . $rule_group_id . '_' . $rule_id . '_type') == 'products'
                                    && in_array($reduction_product, Tools::getValue('product_rule_select_' . $rule_group_id . '_' . $rule_id))) {
                                    $already_restricted = true;

                                    break 2;
                                }
                            }
                        }
                    }
                }
                if ($already_restricted == false) {
                    // Check the product restriction
                    $_POST['product_restriction'] = 1;

                    // Add a new rule group
                    $rule_group_id = 1;
                    if (is_array($rule_group_array)) {
                        // Find the first rule_group_id that is not available in the array
                        while (in_array($rule_group_id, $rule_group_array)) {
                            ++$rule_group_id;
                        }
                        $_POST['product_rule_group'][] = $rule_group_id;
                    } else {
                        $_POST['product_rule_group'] = [$rule_group_id];
                    }

                    // Set a quantity of 1 for this new rule group
                    $_POST['product_rule_group_' . $rule_group_id . '_quantity'] = 1;
                    // Add one rule to the new rule group
                    $_POST['product_rule_' . $rule_group_id] = [1];
                    // Set a type 'product' for this 1 rule
                    $_POST['product_rule_' . $rule_group_id . '_1_type'] = 'products';
                    // Add the product in the selected products
                    $_POST['product_rule_select_' . $rule_group_id . '_1'] = [$reduction_product];
                }
            }

            // These are checkboxes (which aren't sent through POST when they are not checked), so they are forced to 0
            foreach (['country', 'carrier', 'group', 'cart_rule', 'product', 'shop'] as $type) {
                if (!Tools::getValue($type . '_restriction')) {
                    $_POST[$type . '_restriction'] = 0;
                }
            }

            // If the restriction is checked, but no item is selected, raise an error
            foreach (['country', 'carrier', 'group'] as $type) {
                if (Tools::getValue($type . '_restriction') && empty(Tools::getValue($type . '_select'))) {
                    switch ($type) {
                        case 'country':
                            $restriction_name = $this->trans('Country selection', [], 'Admin.Catalog.Feature');
                            break;
                        case 'carrier':
                            $restriction_name = $this->trans('Carrier selection', [], 'Admin.Catalog.Feature');
                            break;
                        default:
                            $restriction_name = $this->trans('Customer group selection', [], 'Admin.Catalog.Feature');
                            break;
                    }
                    $this->errors[] = $this->trans('The "%s" restriction is checked, but no item is selected.', [$restriction_name], 'Admin.Catalog.Notification');
                }
            }

            // Remove the gift if the radio button is set to "no"
            if (!(int) Tools::getValue('free_gift')) {
                $_POST['gift_product'] = 0;
            }

            // Retrieve the product attribute id of the gift (if available)
            if ($id_product = (int) Tools::getValue('gift_product')) {
                $_POST['gift_product_attribute'] = (int) Tools::getValue('ipa_' . $id_product);
            }

            // Idiot-proof control
            if (strtotime(Tools::getValue('date_from')) > strtotime(Tools::getValue('date_to'))) {
                $this->errors[] = $this->trans('The voucher cannot end before it begins.', [], 'Admin.Catalog.Notification');
            }
            if ((int) Tools::getValue('minimum_amount') < 0) {
                $this->errors[] = $this->trans('The minimum amount cannot be lower than zero.', [], 'Admin.Catalog.Notification');
            }
            if ((float) Tools::getValue('reduction_percent') < 0 || (float) Tools::getValue('reduction_percent') > 100) {
                $this->errors[] = $this->trans('Reduction percentage must be between 0% and 100%', [], 'Admin.Catalog.Notification');
            }
            if ((int) Tools::getValue('reduction_amount') < 0) {
                $this->errors[] = $this->trans('Reduction amount cannot be lower than zero.', [], 'Admin.Catalog.Notification');
            }
            if (Tools::getValue('code') && ($same_code = (int) CartRule::getIdByCode(Tools::getValue('code'))) && $same_code != Tools::getValue('id_cart_rule')) {
                $this->errors[] = $this->trans('This cart rule code is already used (conflict with cart rule %rulename%)', ['%rulename%' => $same_code], 'Admin.Catalog.Notification');
            }
            if (Tools::getValue('apply_discount') == 'off' && !Tools::getValue('free_shipping') && !Tools::getValue('free_gift')) {
                $this->errors[] = $this->trans('An action is required for this cart rule.', [], 'Admin.Catalog.Notification');
            }
        }

        return parent::postProcess();
    }

    public function processDelete()
    {
        $res = parent::processDelete();
        if (Tools::isSubmit('delete' . $this->table)) {
            $back = rawurldecode(Tools::getValue('back', ''));
            if (!empty($back)) {
                $back .= (strpos($back, '?') === false ? '?' : '&') . 'conf=1';
                $this->redirect_after = $back;
            }
        }

        return $res;
    }

    protected function updateAssoShop($id_object)
    {
        //@todo:Now we override so the cart_rule_shop table is not cleaned up every time. But need to back to this to check employee access to shops
    }

    /**
     * @param $current_object
     *
     * @return bool|void
     *
     * @throws PrestaShopDatabaseException
     */
    protected function afterUpdate($current_object)
    {
        // All the associations are deleted for an update, then recreated when we call the "afterAdd" method
        $id_cart_rule = Tools::getValue('id_cart_rule');
        foreach (['country', 'carrier', 'group', 'product_rule_group'] as $type) {
            Db::getInstance()->delete('cart_rule_' . $type, '`id_cart_rule` = ' . (int) $id_cart_rule);
        }

        Db::getInstance()->delete('cart_rule_product_rule', 'NOT EXISTS (SELECT 1 FROM `' . _DB_PREFIX_ . 'cart_rule_product_rule_group`
			WHERE `' . _DB_PREFIX_ . 'cart_rule_product_rule`.`id_product_rule_group` = `' . _DB_PREFIX_ . 'cart_rule_product_rule_group`.`id_product_rule_group`)');
        Db::getInstance()->delete('cart_rule_product_rule_value', 'NOT EXISTS (SELECT 1 FROM `' . _DB_PREFIX_ . 'cart_rule_product_rule`
			WHERE `' . _DB_PREFIX_ . 'cart_rule_product_rule_value`.`id_product_rule` = `' . _DB_PREFIX_ . 'cart_rule_product_rule`.`id_product_rule`)');
        Db::getInstance()->delete('cart_rule_combination', '`id_cart_rule_1` = ' . (int) $id_cart_rule . ' OR `id_cart_rule_2` = ' . (int) $id_cart_rule);

        $this->afterAdd($current_object);
    }

    public function processAdd()
    {
        if ($cart_rule = parent::processAdd()) {
            $this->context->smarty->assign('new_cart_rule', $cart_rule);
        }
        if (Tools::getValue('submitFormAjax')) {
            $this->redirect_after = null;
            if ($cart_rule) {
                $this->context->smarty->assign('refresh_cart', true);
                $this->display = 'edit';
            }
        }

        return $cart_rule;
    }

    /**
     * @param CartRule $cartRule
     *
     * @return bool
     */
    protected function beforeAdd($cartRule)
    {
        $selectedShopIds = Tools::getValue('shop_select');
        if (Tools::getValue('shop_restriction') && is_array($selectedShopIds)) {
            $cartRule->id_shop_list = array_map(static function ($shopId): int {
                return (int) $shopId;
            }, $selectedShopIds);
        }

        return true;
    }

    /**
     * @TODO Move this function into CartRule
     *
     * @param ObjectModel $currentObject
     *
     * @return bool|void
     *
     * @throws PrestaShopDatabaseException
     */
    protected function afterAdd($currentObject)
    {
        // Add restrictions for generic entities like country, carrier and group
        foreach (['country', 'carrier', 'group'] as $type) {
            if (Tools::getValue($type . '_restriction') && is_array($array = Tools::getValue($type . '_select')) && count($array)) {
                $values = [];
                foreach ($array as $id) {
                    $values[] = '(' . (int) $currentObject->id . ',' . (int) $id . ')';
                }
                Db::getInstance()->execute('INSERT INTO `' . _DB_PREFIX_ . 'cart_rule_' . $type . '` (`id_cart_rule`, `id_' . $type . '`) VALUES ' . implode(',', $values));
            }
        }
        // Add cart rule restrictions
        if (Tools::getValue('cart_rule_restriction') && is_array($array = Tools::getValue('cart_rule_select')) && count($array)) {
            $values = [];
            foreach ($array as $id) {
                $values[] = '(' . (int) $currentObject->id . ',' . (int) $id . ')';
            }
            Db::getInstance()->execute('INSERT INTO `' . _DB_PREFIX_ . 'cart_rule_combination` (`id_cart_rule_1`, `id_cart_rule_2`) VALUES ' . implode(',', $values));
        }
        // Add product rule restrictions
        if (Tools::getValue('product_restriction') && is_array($ruleGroupArray = Tools::getValue('product_rule_group')) && count($ruleGroupArray)) {
            foreach ($ruleGroupArray as $ruleGroupId) {
                Db::getInstance()->execute('INSERT INTO `' . _DB_PREFIX_ . 'cart_rule_product_rule_group` (`id_cart_rule`, `quantity`)
				VALUES (' . (int) $currentObject->id . ', ' . (int) Tools::getValue('product_rule_group_' . $ruleGroupId . '_quantity') . ')');
                $id_product_rule_group = Db::getInstance()->Insert_ID();

                if (is_array($ruleArray = Tools::getValue('product_rule_' . $ruleGroupId)) && count($ruleArray)) {
                    foreach ($ruleArray as $ruleId) {
                        Db::getInstance()->execute('INSERT INTO `' . _DB_PREFIX_ . 'cart_rule_product_rule` (`id_product_rule_group`, `type`)
						VALUES (' . (int) $id_product_rule_group . ', "' . pSQL(Tools::getValue('product_rule_' . $ruleGroupId . '_' . $ruleId . '_type')) . '")');
                        $id_product_rule = Db::getInstance()->Insert_ID();

                        $values = [];
                        foreach (Tools::getValue('product_rule_select_' . $ruleGroupId . '_' . $ruleId) as $id) {
                            $values[] = '(' . (int) $id_product_rule . ',' . (int) $id . ')';
                        }
                        $values = array_unique($values);
                        if (count($values)) {
                            Db::getInstance()->execute('INSERT INTO `' . _DB_PREFIX_ . 'cart_rule_product_rule_value` (`id_product_rule`, `id_item`) VALUES ' . implode(',', $values));
                        }
                    }
                }
            }
        }

        // If the new rule has no cart rule restriction, then it must be added to the white list of the other cart rules that have restrictions
        if (!Tools::getValue('cart_rule_restriction')) {
            Db::getInstance()->execute('
			INSERT INTO `' . _DB_PREFIX_ . 'cart_rule_combination` (`id_cart_rule_1`, `id_cart_rule_2`) (
				SELECT id_cart_rule, ' . (int) $currentObject->id . ' FROM `' . _DB_PREFIX_ . 'cart_rule` WHERE cart_rule_restriction = 1
			)');
        } else {
            // And if the new cart rule has restrictions, previously unrestricted cart rules may now be restricted (a mug of coffee is strongly advised to understand this sentence)
            $ruleCombinations = Db::getInstance()->executeS('
			SELECT cr.id_cart_rule
			FROM ' . _DB_PREFIX_ . 'cart_rule cr
			WHERE cr.id_cart_rule != ' . (int) $currentObject->id . '
			AND cr.cart_rule_restriction = 0
			AND NOT EXISTS (
				SELECT 1
				FROM ' . _DB_PREFIX_ . 'cart_rule_combination
				WHERE cr.id_cart_rule = ' . _DB_PREFIX_ . 'cart_rule_combination.id_cart_rule_2 AND ' . (int) $currentObject->id . ' = id_cart_rule_1
			)
			AND NOT EXISTS (
				SELECT 1
				FROM ' . _DB_PREFIX_ . 'cart_rule_combination
				WHERE cr.id_cart_rule = ' . _DB_PREFIX_ . 'cart_rule_combination.id_cart_rule_1 AND ' . (int) $currentObject->id . ' = id_cart_rule_2
			)
			');
            foreach ($ruleCombinations as $incompatibleRule) {
                Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . 'cart_rule` SET cart_rule_restriction = 1 WHERE id_cart_rule = ' . (int) $incompatibleRule['id_cart_rule'] . ' LIMIT 1');
                Db::getInstance()->execute('
				INSERT IGNORE INTO `' . _DB_PREFIX_ . 'cart_rule_combination` (`id_cart_rule_1`, `id_cart_rule_2`) (
					SELECT id_cart_rule, ' . (int) $incompatibleRule['id_cart_rule'] . ' FROM `' . _DB_PREFIX_ . 'cart_rule`
					WHERE active = 1
					AND id_cart_rule != ' . (int) $currentObject->id . '
					AND id_cart_rule != ' . (int) $incompatibleRule['id_cart_rule'] . '
				)');
            }
        }
    }

    /**
     * Retrieve the cart rule product rule groups in the POST data
     * if available, and in the database if there is none.
     *
     * @param CartRule $cart_rule
     *
     * @return array
     */
    public function getProductRuleGroupsDisplay($cart_rule)
    {
        $productRuleGroupsArray = [];
        if (Tools::getValue('product_restriction') && is_array($array = Tools::getValue('product_rule_group')) && count($array)) {
            $i = 1;
            foreach ($array as $ruleGroupId) {
                $productRulesArray = [];
                $arrayPRGroupId = Tools::getValue('product_rule_' . $ruleGroupId);
                if (is_array($arrayPRGroupId) && count($arrayPRGroupId)) {
                    foreach ($arrayPRGroupId as $ruleId) {
                        $productRulesArray[] = $this->getProductRuleDisplay(
                            $ruleGroupId,
                            $ruleId,
                            Tools::getValue('product_rule_' . $ruleGroupId . '_' . $ruleId . '_type'),
                            Tools::getValue('product_rule_select_' . $ruleGroupId . '_' . $ruleId)
                        );
                    }
                }

                $productRuleGroupsArray[] = $this->getProductRuleGroupDisplay(
                    $i++,
                    (int) Tools::getValue('product_rule_group_' . $ruleGroupId . '_quantity'),
                    $productRulesArray
                );
            }
        } else {
            $i = 1;
            foreach ($cart_rule->getProductRuleGroups() as $productRuleGroup) {
                $j = 1;
                $productRulesDisplay = [];
                foreach ($productRuleGroup['product_rules'] as $productRule) {
                    $productRulesDisplay[] = $this->getProductRuleDisplay($i, $j++, $productRule['type'], $productRule['values']);
                }
                $productRuleGroupsArray[] = $this->getProductRuleGroupDisplay($i++, $productRuleGroup['quantity'], $productRulesDisplay);
            }
        }

        return $productRuleGroupsArray;
    }

    /* Return the form for a single cart rule group either with or without product_rules set up */
    public function getProductRuleGroupDisplay($product_rule_group_id, $product_rule_group_quantity = 1, $product_rules = null)
    {
        Context::getContext()->smarty->assign('product_rule_group_id', $product_rule_group_id);
        Context::getContext()->smarty->assign('product_rule_group_quantity', $product_rule_group_quantity);
        Context::getContext()->smarty->assign('product_rules', $product_rules);

        return $this->createTemplate('product_rule_group.tpl')->fetch();
    }

    public function getProductRuleDisplay($product_rule_group_id, $product_rule_id, $product_rule_type, $selected = [])
    {
        Context::getContext()->smarty->assign(
            [
                'product_rule_group_id' => (int) $product_rule_group_id,
                'product_rule_id' => (int) $product_rule_id,
                'product_rule_type' => $product_rule_type,
            ]
        );

        switch ($product_rule_type) {
            case 'attributes':
                $attributes = ['selected' => [], 'unselected' => []];
                $results = Db::getInstance()->executeS('
				SELECT CONCAT(agl.name, " - ", al.name) as name, a.id_attribute as id
				FROM ' . _DB_PREFIX_ . 'attribute_group_lang agl
				LEFT JOIN ' . _DB_PREFIX_ . 'attribute a ON a.id_attribute_group = agl.id_attribute_group
				LEFT JOIN ' . _DB_PREFIX_ . 'attribute_lang al ON (a.id_attribute = al.id_attribute AND al.id_lang = ' . (int) Context::getContext()->language->id . ')
				WHERE agl.id_lang = ' . (int) Context::getContext()->language->id . '
				ORDER BY agl.name, al.name');
                foreach ($results as $row) {
                    $attributes[in_array($row['id'], $selected) ? 'selected' : 'unselected'][] = $row;
                }
                Context::getContext()->smarty->assign('product_rule_itemlist', $attributes);
                $choose_content = $this->createTemplate('controllers/cart_rules/product_rule_itemlist.tpl')->fetch();
                Context::getContext()->smarty->assign('product_rule_choose_content', $choose_content);

                break;
            case 'products':
                $products = ['selected' => [], 'unselected' => []];
                $results = Db::getInstance()->executeS('
				SELECT DISTINCT name, p.id_product as id
				FROM ' . _DB_PREFIX_ . 'product p
				LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl
					ON (p.`id_product` = pl.`id_product`
					AND pl.`id_lang` = ' . (int) Context::getContext()->language->id . Shop::addSqlRestrictionOnLang('pl') . ')
				' . Shop::addSqlAssociation('product', 'p') . '
				WHERE id_lang = ' . (int) Context::getContext()->language->id . '
				ORDER BY name');
                foreach ($results as $row) {
                    $products[in_array($row['id'], $selected) ? 'selected' : 'unselected'][] = $row;
                }
                Context::getContext()->smarty->assign('product_rule_itemlist', $products);
                $choose_content = $this->createTemplate('product_rule_itemlist.tpl')->fetch();
                Context::getContext()->smarty->assign('product_rule_choose_content', $choose_content);

                break;
            case 'manufacturers':
                $products = ['selected' => [], 'unselected' => []];
                $results = Db::getInstance()->executeS('
				SELECT name, id_manufacturer as id
				FROM ' . _DB_PREFIX_ . 'manufacturer
				ORDER BY name');
                foreach ($results as $row) {
                    $products[in_array($row['id'], $selected) ? 'selected' : 'unselected'][] = $row;
                }
                Context::getContext()->smarty->assign('product_rule_itemlist', $products);
                $choose_content = $this->createTemplate('product_rule_itemlist.tpl')->fetch();
                Context::getContext()->smarty->assign('product_rule_choose_content', $choose_content);

                break;
            case 'suppliers':
                $products = ['selected' => [], 'unselected' => []];
                $results = Db::getInstance()->executeS('
				SELECT name, id_supplier as id
				FROM ' . _DB_PREFIX_ . 'supplier
				ORDER BY name');
                foreach ($results as $row) {
                    $products[in_array($row['id'], $selected) ? 'selected' : 'unselected'][] = $row;
                }
                Context::getContext()->smarty->assign('product_rule_itemlist', $products);
                $choose_content = $this->createTemplate('product_rule_itemlist.tpl')->fetch();
                Context::getContext()->smarty->assign('product_rule_choose_content', $choose_content);

                break;
            case 'categories':
                $categories = ['selected' => [], 'unselected' => []];
                $categoryTree = Category::getNestedCategories(Category::getRootCategory()->id, (int) Context::getContext()->language->id, false);

                $flatCategories = $this->populateCategories([], $categoryTree);

                foreach ($flatCategories as $row) {
                    $categories[in_array($row['id'], $selected) ? 'selected' : 'unselected'][] = $row;
                }
                Context::getContext()->smarty->assign('product_rule_itemlist', $categories);
                $choose_content = $this->createTemplate('product_rule_itemlist.tpl')->fetch();
                Context::getContext()->smarty->assign('product_rule_choose_content', $choose_content);

                break;
            default:
                Context::getContext()->smarty->assign('product_rule_itemlist', ['selected' => [], 'unselected' => []]);
                Context::getContext()->smarty->assign('product_rule_choose_content', '');
        }

        return $this->createTemplate('product_rule.tpl')->fetch();
    }

    public function populateCategories(array $flatCategories, array $currentCategoryTree, string $currentPath = ''): array
    {
        if (!$currentCategoryTree) {
            return $flatCategories;
        }

        $separator = ' > ';
        foreach ($currentCategoryTree as $categoryArray) {
            $fullName = ($currentPath ? $currentPath . $separator : '') . $categoryArray['name'];
            $flatCategories[] = ['id' => $categoryArray['id_category'], 'name' => $fullName];
            // recursive call for childrens
            if (!empty($categoryArray['children'])) {
                $flatCategories = $this->populateCategories($flatCategories, $categoryArray['children'], $fullName);
            }
        }

        return $flatCategories;
    }

    public function ajaxProcess()
    {
        if (Tools::isSubmit('newProductRule')) {
            die($this->getProductRuleDisplay(Tools::getValue('product_rule_group_id'), Tools::getValue('product_rule_id'), Tools::getValue('product_rule_type')));
        }
        if (Tools::isSubmit('newProductRuleGroup') && $product_rule_group_id = Tools::getValue('product_rule_group_id')) {
            die($this->getProductRuleGroupDisplay($product_rule_group_id, Tools::getValue('product_rule_group_' . $product_rule_group_id . '_quantity', 1)));
        }

        if (Tools::isSubmit('customerFilter')) {
            $display_shop_name = Shop::isFeatureActive() && Shop::getContext() != Shop::CONTEXT_SHOP;
            $search_query = trim(Tools::getValue('q'));
            $customers = Db::getInstance()->executeS('
			SELECT c.`id_customer`, c.`email`, CONCAT(c.`firstname`, \' \', c.`lastname`) as cname' .
                ($display_shop_name ? ', s.name as shop_name' : '') . '
			FROM `' . _DB_PREFIX_ . 'customer` c ' .
                ($display_shop_name ? ' INNER JOIN `' . _DB_PREFIX_ . 'shop` s ON s.id_shop = c.id_shop' : '') . '
			WHERE c.`deleted` = 0 AND c.is_guest = 0 AND c.active = 1
			AND (
				c.`id_customer` = ' . (int) $search_query . '
				OR c.`email` LIKE "%' . pSQL($search_query) . '%"
				OR c.`firstname` LIKE "%' . pSQL($search_query) . '%"
				OR c.`lastname` LIKE "%' . pSQL($search_query) . '%"
			)
			' . Shop::addSqlRestriction(Shop::SHARE_CUSTOMER, 'c') . '
			ORDER BY c.`firstname`, c.`lastname`
			LIMIT 50');
            die(json_encode($customers));
        }
        // Both product filter (free product and product discount) search for products
        if (Tools::isSubmit('giftProductFilter') || Tools::isSubmit('reductionProductFilter')) {
            $products = Product::searchByName(Context::getContext()->language->id, trim(Tools::getValue('q')));
            die(json_encode($products));
        }
    }

    protected function searchProducts($search)
    {
        if ($products = Product::searchByName((int) $this->context->language->id, $search)) {
            foreach ($products as &$product) {
                $combinations = [];
                $productObj = new Product((int) $product['id_product'], false, (int) $this->context->language->id);
                $attributes = $productObj->getAttributesGroups((int) $this->context->language->id);
                $product['formatted_price'] = $product['price_tax_incl']
                    ? $this->context->getCurrentLocale()->formatPrice(Tools::convertPrice($product['price_tax_incl'], $this->context->currency), $this->context->currency->iso_code)
                    : '';

                foreach ($attributes as $attribute) {
                    if (!isset($combinations[$attribute['id_product_attribute']]['attributes'])) {
                        $combinations[$attribute['id_product_attribute']]['attributes'] = '';
                    }
                    $combinations[$attribute['id_product_attribute']]['attributes'] .= $attribute['attribute_name'] . ' - ';
                    $combinations[$attribute['id_product_attribute']]['id_product_attribute'] = $attribute['id_product_attribute'];
                    $combinations[$attribute['id_product_attribute']]['default_on'] = $attribute['default_on'];
                    $price_tax_incl = Product::getPriceStatic((int) $product['id_product'], true, $attribute['id_product_attribute']);
                    $combinations[$attribute['id_product_attribute']]['formatted_price'] = $price_tax_incl
                        ? $this->context->getCurrentLocale()->formatPrice(Tools::convertPrice($price_tax_incl, $this->context->currency), $this->context->currency->iso_code)
                        : '';
                }

                foreach ($combinations as &$combination) {
                    $combination['attributes'] = rtrim($combination['attributes'], ' - ');
                }
                $product['combinations'] = $combinations;
            }

            return [
                'products' => $products,
                'found' => true,
            ];
        } else {
            return ['found' => false, 'notfound' => $this->trans('No product has been found.', [], 'Admin.Catalog.Notification')];
        }
    }

    public function ajaxProcessSearchProducts()
    {
        $array = $this->searchProducts(Tools::getValue('product_search'));
        $this->content = trim(json_encode($array));
    }

    public function renderForm()
    {
        $limit = 40;
        $this->toolbar_btn['save'] = [
            'href' => '#',
            'desc' => $this->trans('Save', [], 'Admin.Actions'),
        ];

        $this->toolbar_btn['save-and-stay'] = [
            'href' => '#',
            'desc' => $this->trans('Save and stay', [], 'Admin.Actions'),
        ];

        /** @var CartRule $current_object */
        $current_object = $this->loadObject(true);

        // All the filter are prefilled with the correct information
        $customer_filter = '';
        if (Validate::isUnsignedId($current_object->id_customer) &&
            Validate::isLoadedObject($customer = new Customer($current_object->id_customer))
        ) {
            $customer_filter = $customer->firstname . ' ' . $customer->lastname . ' (' . $customer->email . ')';
        }

        $gift_product_filter = '';
        if (Validate::isUnsignedId($current_object->gift_product) &&
            Validate::isLoadedObject($product = new Product($current_object->gift_product, false, $this->context->language->id))
        ) {
            $gift_product_filter = (!empty($product->reference) ? $product->reference : $product->name);
        }

        $reduction_product_filter = '';
        if (Validate::isUnsignedId($current_object->reduction_product) &&
            Validate::isLoadedObject($product = new Product($current_object->reduction_product, false, $this->context->language->id))
        ) {
            $reduction_product_filter = (!empty($product->reference) ? $product->reference : $product->name);
        }

        $product_rule_groups = $this->getProductRuleGroupsDisplay($current_object);

        $attribute_groups = AttributeGroup::getAttributesGroups($this->context->language->id);
        $currencies = Currency::getCurrencies(false, true, true);
        $languages = Language::getLanguages();
        $countries = $current_object->getAssociatedRestrictions('country', true, true);
        $groups = $current_object->getAssociatedRestrictions('group', false, true);
        $shops = $current_object->getAssociatedRestrictions('shop', false, false);
        $cart_rules = $current_object->getAssociatedRestrictions('cart_rule', false, true, 0, $limit);
        $carriers = $current_object->getAssociatedRestrictions('carrier', true, true);

        foreach ($carriers as &$carriers2) {
            $prev_id_carrier = 0;

            foreach ($carriers2 as $key => &$carrier) {
                if ($prev_id_carrier == $carrier['id_carrier']) {
                    unset($carriers2[$key]);

                    continue;
                }

                foreach ($carrier as $field => &$value) {
                    if ($field == 'name') {
                        if ($value == '0') {
                            $value = $carrier['id_carrier'] . ' - ' . Configuration::get('PS_SHOP_NAME');
                        } else {
                            $value = $carrier['id_carrier'] . ' - ' . $carrier['name'];
                            if (!empty($carrier['delay'])) {
                                $value .= ' (' . $carrier['delay'] . ')';
                            }
                        }
                    }
                }

                $prev_id_carrier = $carrier['id_carrier'];
            }
        }

        $gift_product_select = '';
        $gift_product_attribute_select = '';
        if ((int) $current_object->gift_product) {
            $search_products = $this->searchProducts($gift_product_filter);
            if (isset($search_products['products']) && is_array($search_products['products'])) {
                foreach ($search_products['products'] as $product) {
                    $gift_product_select .= '
					<option value="' . $product['id_product'] . '" ' . ($product['id_product'] == $current_object->gift_product ? 'selected="selected"' : '') . '>
						' . $product['name'] . (count($product['combinations']) == 0 ? ' - ' . $product['formatted_price'] : '') . '
					</option>';

                    if (count($product['combinations'])) {
                        $gift_product_attribute_select .= '<select class="control-form id_product_attribute" id="ipa_' . $product['id_product'] . '" name="ipa_' . $product['id_product'] . '">';
                        foreach ($product['combinations'] as $combination) {
                            $gift_product_attribute_select .= '
							<option ' . ($combination['id_product_attribute'] == $current_object->gift_product_attribute ? 'selected="selected"' : '') . ' value="' . $combination['id_product_attribute'] . '">
								' . $combination['attributes'] . ' - ' . $combination['formatted_price'] . '
							</option>';
                        }
                        $gift_product_attribute_select .= '</select>';
                    }
                }
            }
        }

        $product = new Product($current_object->gift_product);
        $this->context->smarty->assign(
            [
                'show_toolbar' => true,
                'toolbar_btn' => $this->toolbar_btn,
                'toolbar_scroll' => $this->toolbar_scroll,
                'title' => $this->trans('Cart Rules', [], 'Admin.Catalog.Feature'),
                'defaultDateFrom' => date('Y-m-d H:00:00'),
                'defaultDateTo' => date('Y-m-d H:00:00', strtotime('+1 month')),
                'customerFilter' => $customer_filter,
                'giftProductFilter' => $gift_product_filter,
                'gift_product_select' => $gift_product_select,
                'gift_product_attribute_select' => $gift_product_attribute_select,
                'reductionProductFilter' => $reduction_product_filter,
                'defaultCurrency' => Currency::getDefaultCurrencyId(),
                'id_lang_default' => Configuration::get('PS_LANG_DEFAULT'),
                'languages' => $languages,
                'currencies' => $currencies,
                'countries' => $countries,
                'carriers' => $carriers,
                'groups' => $groups,
                'shops' => $shops,
                'all_shops' => Shop::getCompleteListOfShopsID(),
                'cart_rules' => $cart_rules,
                'product_rule_groups' => $product_rule_groups,
                'product_rule_groups_counter' => count($product_rule_groups),
                'attribute_groups' => $attribute_groups,
                'currentIndex' => self::$currentIndex,
                'currentToken' => $this->token,
                'currentObject' => $current_object,
                'currentTab' => $this,
                'hasAttribute' => $product->hasAttributes(),
            ]
        );
        Media::addJsDef(['baseHref' => $this->context->link->getAdminLink('AdminCartRules') . '&ajaxMode=1&ajax=1&id_cart_rule=' .
                                     (int) Tools::getValue('id_cart_rule') . '&action=loadCartRules&limit=' . (int) $limit . '&count=0', ]);
        $this->content .= $this->createTemplate('form.tpl')->fetch();

        $this->addJqueryUI('ui.datepicker');
        $this->addJqueryPlugin(['jscroll', 'typewatch']);

        return parent::renderForm();
    }

    public function displayAjaxSearchCartRuleVouchers()
    {
        $found = false;
        if ($vouchers = CartRule::getCartsRuleByCode(Tools::getValue('q'), (int) $this->context->language->id, true)) {
            $found = true;
        }
        echo json_encode(['found' => $found, 'vouchers' => $vouchers]);
    }

    /**
     * For the listing, Override the method displayDeleteLink for the HelperList
     * That allows to have links with all characters (like < & >)
     *
     * @param string $token
     * @param string $id
     * @param string|null $name
     *
     * @return string
     */
    public function displayDeleteLink(string $token, string $id, ?string $name = null): string
    {
        if (!$this->access('delete')) {
            return '';
        }

        $tpl = $this->createTemplate('helpers/list/list_action_delete.tpl');

        if (!array_key_exists('Delete', self::$cache_lang)) {
            self::$cache_lang['Delete'] = $this->trans('Delete', [], 'Admin.Actions');
        }
        if (!array_key_exists('DeleteItem', self::$cache_lang)) {
            self::$cache_lang['DeleteItem'] = $this->trans('Delete selected item?', [], 'Admin.Notifications.Info');
        }
        if (!array_key_exists('Name', self::$cache_lang)) {
            self::$cache_lang['Name'] = $this->trans('Name:', [], 'Admin.Shipping.Feature');
        }

        if (null !== $name) {
            // \n\n is not between double quotes because in js/jquery/plugins/alerts/jquery.alerts.js, \n is replaced by <br>.
            $name = '\n\n' . self::$cache_lang['Name'] . ' ' . $name;
        }

        $data = [
            $this->identifier => $id,
            'href' => $this->context->link->getAdminLink(
                'AdminCartRules',
                true, [],
                [
                    'id_cart_rule' => (int) $id,
                    'deletecart_rule' => 1,
                ]
            ),
            'action' => self::$cache_lang['Delete'],
        ];

        if ($this->specificConfirmDelete !== false) {
            $data['confirm'] = null !== $this->specificConfirmDelete
                ? '\r' . $this->specificConfirmDelete
                : Tools::htmlentitiesDecodeUTF8(self::$cache_lang['DeleteItem'] . $name);
        }

        $tpl->assign(array_merge($this->tpl_delete_link_vars, $data));

        return $tpl->fetch();
    }
}

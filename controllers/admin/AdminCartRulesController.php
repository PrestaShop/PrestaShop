<?php
/*
* 2007-2013 PrestaShop
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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2013 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class AdminCartRulesControllerCore extends AdminController
{
	public function __construct()
	{
		$this->table = 'cart_rule';
	 	$this->className = 'CartRule';
	 	$this->lang = true;
		$this->addRowAction('edit');
		$this->addRowAction('delete');
	 	$this->bulk_actions = array('delete' => array('text' => $this->l('Delete selected'), 'confirm' => $this->l('Delete selected items?')));

		$this->fields_list = array(
			'id_cart_rule' => array('title' => $this->l('ID'), 'align' => 'center', 'width' => 25),
			'name' => array('title' => $this->l('Name')),
			'priority' => array('title' => $this->l('Priority')),
			'code' => array('title' => $this->l('Code')),
			'quantity' => array('title' => $this->l('Quantity')),
			'date_to' => array('title' => $this->l('Until')),
			'active' => array('title' => $this->l('Status'), 'align' => 'center', 'active' => 'status', 'type' => 'bool', 'orderby' => false),
		);

		parent::__construct();
	}

	public function setMedia()
	{
		parent::setMedia();
		$this->addJqueryPlugin(array('typewatch', 'fancybox', 'autocomplete'));
	}

	public function postProcess()
	{
		if (Tools::isSubmit('submitAddcart_rule') || Tools::isSubmit('submitAddcart_ruleAndStay'))
		{
			// If the reduction is associated to a specific product, then it must be part of the product restrictions
			if ((int)Tools::getValue('reduction_product') && Tools::getValue('apply_discount_to') == 'specific' && Tools::getValue('apply_discount') != 'off')
			{
				$reduction_product = (int)Tools::getValue('reduction_product');
				
				// First, check if it is not already part of the restrictions
				$already_restricted = false;
				if (is_array($rule_group_array = Tools::getValue('product_rule_group')) && count($rule_group_array) && Tools::getValue('product_restriction'))
					foreach ($rule_group_array as $rule_group_id)
						if (is_array($rule_array = Tools::getValue('product_rule_'.$rule_group_id)) && count($rule_array))
							foreach ($rule_array as $rule_id)
								if (Tools::getValue('product_rule_'.$rule_group_id.'_'.$rule_id.'_type') == 'products'
									&& in_array($reduction_product, Tools::getValue('product_rule_select_'.$rule_group_id.'_'.$rule_id)))
								{
									$already_restricted = true;
									break 2;
								}
			
				if ($already_restricted == false)
				{
					// Check the product restriction
					$_POST['product_restriction'] = 1;
					
					// Add a new rule group
					$rule_group_id = 1;
					if (is_array($rule_group_array))
					{
						// Empty for (with a ; at the end), that just find the first rule_group_id available in rule_group_array
						for ($rule_group_id = 1; in_array($rule_group_id, $rule_group_array); ++$rule_group_id)
							42;
						$_POST['product_rule_group'][] = $rule_group_id;
					}
					else
						$_POST['product_rule_group'] = array($rule_group_id);
					
					// Set a quantity of 1 for this new rule group
					$_POST['product_rule_group_'.$rule_group_id.'_quantity'] = 1;
					// Add one rule to the new rule group
					$_POST['product_rule_'.$rule_group_id] = array(1);
					// Set a type 'product' for this 1 rule
					$_POST['product_rule_'.$rule_group_id.'_1_type'] = 'products';
					// Add the product in the selected products
					$_POST['product_rule_select_'.$rule_group_id.'_1'] = array($reduction_product);
				}
			}
			
			// These are checkboxes (which aren't sent through POST when they are not check), so they are forced to 0
			foreach (array('country', 'carrier', 'group', 'cart_rule', 'product', 'shop') as $type)
				if (!Tools::getValue($type.'_restriction'))
					$_POST[$type.'_restriction'] = 0;
			
			// Remove the gift if the radio button is set to "no"
			if (!(int)Tools::getValue('free_gift'))
				$_POST['gift_product'] = 0;

			// Retrieve the product attribute id of the gift (if available)
			if ($id_product = (int)Tools::getValue('gift_product'))
				$_POST['gift_product_attribute'] = (int)Tools::getValue('ipa_'.$id_product);

			// Idiot-proof control
			if (strtotime(Tools::getValue('date_from')) > strtotime(Tools::getValue('date_to')))
				$this->errors[] = Tools::displayError('The voucher cannot end before it begins.');
			if ((int)Tools::getValue('minimum_amount') < 0)
				$this->errors[] = Tools::displayError('The minimum amount cannot be lower than zero.');
			if ((float)Tools::getValue('reduction_percent') < 0 || (float)Tools::getValue('reduction_percent') > 100)
				$this->errors[] = Tools::displayError('Reduction percent must be between 0% and 100%');
			if ((int)Tools::getValue('reduction_amount') < 0)
				$this->errors[] = Tools::displayError('Reduction amount cannot be lower than zero.');
			if (Tools::getValue('code') && ($same_code = (int)CartRule::getIdByCode(Tools::getValue('code'))) && $same_code != Tools::getValue('id_cart_rule'))
				$this->errors[] = sprintf(Tools::displayError('This cart rule code is already used (conflict with cart rule %d)'), $same_code);
			if (Tools::getValue('apply_discount') == 'off' && !Tools::getValue('free_shipping') && !Tools::getValue('free_gift'))
				$this->errors[] = Tools::displayError('An action is required for this cart rule.');
		}

		return parent::postProcess();
	}

	protected function afterUpdate($current_object)
	{
		// All the associations are deleted for an update, then recreated when we call the "afterAdd" method
		$id_cart_rule = Tools::getValue('id_cart_rule');
		foreach (array('country', 'carrier', 'group', 'product_rule_group', 'shop') as $type)
			Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'cart_rule_'.$type.'` WHERE `id_cart_rule` = '.(int)$id_cart_rule);
		Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'cart_rule_product_rule` WHERE `id_product_rule_group` NOT IN (SELECT `id_product_rule_group` FROM `'._DB_PREFIX_.'cart_rule_product_rule_group`)');
		Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'cart_rule_product_rule_value` WHERE `id_product_rule` NOT IN (SELECT `id_product_rule` FROM `'._DB_PREFIX_.'cart_rule_product_rule`)');
		Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'cart_rule_combination` WHERE `id_cart_rule_1` = '.(int)$id_cart_rule.' OR `id_cart_rule_2` = '.(int)$id_cart_rule);

		$this->afterAdd($current_object);
	}

	public function processAdd()
	{
		if ($cart_rule = parent::processAdd())
			$this->context->smarty->assign('new_cart_rule', $cart_rule);
		if (Tools::getValue('submitFormAjax'))
			$this->redirect_after = false;

		return $cart_rule;
	}
	/* @TODO Move this function into CartRule */
	protected function afterAdd($currentObject)
	{
		// Add restrictions for generic entities like country, carrier and group
		foreach (array('country', 'carrier', 'group', 'shop') as $type)
			if (Tools::getValue($type.'_restriction') && is_array($array = Tools::getValue($type.'_select')) && count($array))
			{
				$values = array();
				foreach ($array as $id)
					$values[] = '('.(int)$currentObject->id.','.(int)$id.')';
				Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'cart_rule_'.$type.'` (`id_cart_rule`, `id_'.$type.'`) VALUES '.implode(',', $values));
			}
		// Add cart rule restrictions
		if (Tools::getValue('cart_rule_restriction') && is_array($array = Tools::getValue('cart_rule_select')) && count($array))
		{
			$values = array();
			foreach ($array as $id)
				$values[] = '('.(int)$currentObject->id.','.(int)$id.')';
			Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'cart_rule_combination` (`id_cart_rule_1`, `id_cart_rule_2`) VALUES '.implode(',', $values));
		}
		// Add product rule restrictions
		if (Tools::getValue('product_restriction') && is_array($ruleGroupArray = Tools::getValue('product_rule_group')) && count($ruleGroupArray))
		{
			foreach ($ruleGroupArray as $ruleGroupId)
			{
				Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'cart_rule_product_rule_group` (`id_cart_rule`, `quantity`)
				VALUES ('.(int)$currentObject->id.', '.(int)Tools::getValue('product_rule_group_'.$ruleGroupId.'_quantity').')');
				$id_product_rule_group = Db::getInstance()->Insert_ID();

				if (is_array($ruleArray = Tools::getValue('product_rule_'.$ruleGroupId)) && count($ruleArray))
					foreach ($ruleArray as $ruleId)
					{
						Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'cart_rule_product_rule` (`id_product_rule_group`, `type`)
						VALUES ('.(int)$id_product_rule_group.', "'.pSQL(Tools::getValue('product_rule_'.$ruleGroupId.'_'.$ruleId.'_type')).'")');
						$id_product_rule = Db::getInstance()->Insert_ID();

						$values = array();
						foreach (Tools::getValue('product_rule_select_'.$ruleGroupId.'_'.$ruleId) as $id)
							$values[] = '('.(int)$id_product_rule.','.(int)$id.')';
						$values = array_unique($values);
						if (count($values))
							Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'cart_rule_product_rule_value` (`id_product_rule`, `id_item`) VALUES '.implode(',', $values));
					}
			}
		}

		// If the new rule has no cart rule restriction, then it must be added to the white list of the other cart rules that have restrictions
		if (!Tools::getValue('cart_rule_restriction'))
		{
			Db::getInstance()->execute('
			INSERT INTO `'._DB_PREFIX_.'cart_rule_combination` (`id_cart_rule_1`, `id_cart_rule_2`) (
				SELECT id_cart_rule, '.(int)$currentObject->id.' FROM `'._DB_PREFIX_.'cart_rule` WHERE cart_rule_restriction = 1
			)');
		}
		// And if the new cart rule has restrictions, previously unrestricted cart rules may now be restricted (a mug of coffee is strongly advised to understand this sentence)
		else
		{
			$ruleCombinations = Db::getInstance()->executeS('
			SELECT cr.id_cart_rule
			FROM '._DB_PREFIX_.'cart_rule cr
			WHERE cr.id_cart_rule != '.(int)$currentObject->id.'
			AND cr.cart_rule_restriction = 0
			AND cr.id_cart_rule NOT IN (
				SELECT IF(id_cart_rule_1 = '.(int)$currentObject->id.', id_cart_rule_2, id_cart_rule_1)
				FROM '._DB_PREFIX_.'cart_rule_combination
				WHERE '.(int)$currentObject->id.' = id_cart_rule_1
				OR '.(int)$currentObject->id.' = id_cart_rule_2
			)');
			foreach ($ruleCombinations as $incompatibleRule)
			{
				Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'cart_rule` SET cart_rule_restriction = 1 WHERE id_cart_rule = '.(int)$incompatibleRule['id_cart_rule'].' LIMIT 1');
				Db::getInstance()->execute('
				INSERT IGNORE INTO `'._DB_PREFIX_.'cart_rule_combination` (`id_cart_rule_1`, `id_cart_rule_2`) (
					SELECT id_cart_rule, '.(int)$incompatibleRule['id_cart_rule'].' FROM `'._DB_PREFIX_.'cart_rule`
					WHERE active = 1
					AND id_cart_rule != '.(int)$currentObject->id.'
					AND id_cart_rule != '.(int)$incompatibleRule['id_cart_rule'].'
				)');
			}
		}
	}

	/* Retrieve the cart rule product rule groups in the POST data if available, and in the database if there is none */
	public function getProductRuleGroupsDisplay($cart_rule)
	{
		$productRuleGroupsArray = array();
		if (Tools::getValue('product_restriction') && is_array($array = Tools::getValue('product_rule_group')) && count($array))
		{
			$i = 1;
			foreach ($array as $ruleGroupId)
			{
				$productRulesArray = array();
				if (is_array($array = Tools::getValue('product_rule_'.$ruleGroupId)) && count($array))
				{
					foreach ($array as $ruleId)
					{
						$productRulesArray[] = $this->getProductRuleDisplay(
							$ruleGroupId,
							$ruleId,
							Tools::getValue('product_rule_'.$ruleGroupId.'_'.$ruleId.'_type'),
							Tools::getValue('product_rule_select_'.$ruleGroupId.'_'.$ruleId)
						);
					}
				}

				$productRuleGroupsArray[] = $this->getProductRuleGroupDisplay(
					$i++,
					(int)Tools::getValue('product_rule_group_'.$ruleGroupId.'_quantity'),
					$productRulesArray
				);
			}
		}
		else
		{
			$i = 1;
			foreach ($cart_rule->getProductRuleGroups() as $productRuleGroup)
			{
				$j = 1;
				$productRulesDisplay = array();
				foreach ($productRuleGroup['product_rules'] as $id_product_rule => $productRule)
					$productRulesDisplay[] = $this->getProductRuleDisplay($i, $j++, $productRule['type'], $productRule['values']);
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
		return Context::getContext()->smarty->fetch('controllers/cart_rules/product_rule_group.tpl');
	}

	public function getProductRuleDisplay($product_rule_group_id, $product_rule_id, $product_rule_type, $selected = array())
	{
		Context::getContext()->smarty->assign(
			array(
				'product_rule_group_id' => (int)$product_rule_group_id,
				'product_rule_id' => (int)$product_rule_id,
				'product_rule_type' => $product_rule_type,
			)
		);

		switch ($product_rule_type)
		{
			case 'attributes':
				$attributes = array('selected' => array(), 'unselected' => array());
				$results = Db::getInstance()->executeS('
				SELECT CONCAT(agl.name, " - ", al.name) as name, a.id_attribute as id
				FROM '._DB_PREFIX_.'attribute_group_lang agl
				LEFT JOIN '._DB_PREFIX_.'attribute a ON a.id_attribute_group = agl.id_attribute_group
				LEFT JOIN '._DB_PREFIX_.'attribute_lang al ON (a.id_attribute = al.id_attribute AND al.id_lang = '.(int)Context::getContext()->language->id.')
				WHERE agl.id_lang = '.(int)Context::getContext()->language->id.'
				ORDER BY agl.name, al.name');
				foreach ($results as $row)
					$attributes[in_array($row['id'], $selected) ? 'selected' : 'unselected'][] = $row;
				Context::getContext()->smarty->assign('product_rule_itemlist', $attributes);
				$choose_content = $this->createTemplate('controllers/cart_rules/product_rule_itemlist.tpl')->fetch();
				Context::getContext()->smarty->assign('product_rule_choose_content', $choose_content);
				break;
			case 'products':
				$products = array('selected' => array(), 'unselected' => array());
				$results = Db::getInstance()->executeS('
				SELECT DISTINCT name, p.id_product as id
				FROM '._DB_PREFIX_.'product p
				LEFT JOIN `'._DB_PREFIX_.'product_lang` pl
					ON (p.`id_product` = pl.`id_product`
					AND pl.`id_lang` = '.(int)Context::getContext()->language->id.Shop::addSqlRestrictionOnLang('pl').')
				'.Shop::addSqlAssociation('product', 'p').'
				WHERE id_lang = '.(int)Context::getContext()->language->id.'
				ORDER BY name');
				foreach ($results as $row)
					$products[in_array($row['id'], $selected) ? 'selected' : 'unselected'][] = $row;
				Context::getContext()->smarty->assign('product_rule_itemlist', $products);
				$choose_content = $this->createTemplate('controllers/cart_rules/product_rule_itemlist.tpl')->fetch();
				Context::getContext()->smarty->assign('product_rule_choose_content', $choose_content);
				break;
			case 'manufacturers':
				$products = array('selected' => array(), 'unselected' => array());
				$results = Db::getInstance()->executeS('
				SELECT name, id_manufacturer as id
				FROM '._DB_PREFIX_.'manufacturer
				ORDER BY name');
				foreach ($results as $row)
					$products[in_array($row['id'], $selected) ? 'selected' : 'unselected'][] = $row;
				Context::getContext()->smarty->assign('product_rule_itemlist', $products);
				$choose_content = $this->createTemplate('controllers/cart_rules/product_rule_itemlist.tpl')->fetch();
				Context::getContext()->smarty->assign('product_rule_choose_content', $choose_content);
				break;
			case 'suppliers':
				$products = array('selected' => array(), 'unselected' => array());
				$results = Db::getInstance()->executeS('
				SELECT name, id_supplier as id
				FROM '._DB_PREFIX_.'supplier
				ORDER BY name');
				foreach ($results as $row)
					$products[in_array($row['id'], $selected) ? 'selected' : 'unselected'][] = $row;
				Context::getContext()->smarty->assign('product_rule_itemlist', $products);
				$choose_content = $this->createTemplate('controllers/cart_rules/product_rule_itemlist.tpl')->fetch();
				Context::getContext()->smarty->assign('product_rule_choose_content', $choose_content);
				break;
			case 'categories':
				$categories = array('selected' => array(), 'unselected' => array());
				$results = Db::getInstance()->executeS('
				SELECT DISTINCT name, c.id_category as id
				FROM '._DB_PREFIX_.'category c
				LEFT JOIN `'._DB_PREFIX_.'category_lang` cl
					ON (c.`id_category` = cl.`id_category`
					AND cl.`id_lang` = '.(int)Context::getContext()->language->id.Shop::addSqlRestrictionOnLang('cl').')
				'.Shop::addSqlAssociation('category', 'c').'
				WHERE id_lang = '.(int)Context::getContext()->language->id.'
				ORDER BY name');
				foreach ($results as $row)
					$categories[in_array($row['id'], $selected) ? 'selected' : 'unselected'][] = $row;
				Context::getContext()->smarty->assign('product_rule_itemlist', $categories);
				$choose_content = $this->createTemplate('controllers/cart_rules/product_rule_itemlist.tpl')->fetch();
				Context::getContext()->smarty->assign('product_rule_choose_content', $choose_content);
				break;
			default :
				Context::getContext()->smarty->assign('product_rule_itemlist', array('selected' => array(), 'unselected' => array()));
				Context::getContext()->smarty->assign('product_rule_choose_content', '');
		}

		return $this->createTemplate('controllers/cart_rules/product_rule.tpl')->fetch();
	}

	public function ajaxProcess()
	{
		if (Tools::isSubmit('newProductRule'))
			die ($this->getProductRuleDisplay(Tools::getValue('product_rule_group_id'), Tools::getValue('product_rule_id'), Tools::getValue('product_rule_type')));
		if (Tools::isSubmit('newProductRuleGroup') && $product_rule_group_id = Tools::getValue('product_rule_group_id'))
			die ($this->getProductRuleGroupDisplay($product_rule_group_id, Tools::getValue('product_rule_group_'.$product_rule_group_id.'_quantity', 1)));

		if (Tools::isSubmit('customerFilter'))
		{
			$search_query = trim(Tools::getValue('q'));
			$customers = Db::getInstance()->executeS('
			SELECT `id_customer`, `email`, CONCAT(`firstname`, \' \', `lastname`) as cname
			FROM `'._DB_PREFIX_.'customer`
			WHERE `deleted` = 0 AND is_guest = 0 AND active = 1
			AND (
				`id_customer` = '.(int)$search_query.'
				OR `email` LIKE "%'.pSQL($search_query).'%"
				OR `firstname` LIKE "%'.pSQL($search_query).'%"
				OR `lastname` LIKE "%'.pSQL($search_query).'%"
			)
			ORDER BY `firstname`, `lastname` ASC
			LIMIT 50');
			die(Tools::jsonEncode($customers));
		}
		// Both product filter (free product and product discount) search for products
		if (Tools::isSubmit('giftProductFilter') || Tools::isSubmit('reductionProductFilter'))
		{
			$products = Product::searchByName(Context::getContext()->language->id, trim(Tools::getValue('q')));
			die(Tools::jsonEncode($products));
		}
	}
	
	protected function searchProducts($search)
	{
		if ($products = Product::searchByName((int)$this->context->language->id, $search))
		{
			foreach ($products as &$product)
			{
				$combinations = array();
				$productObj = new Product((int)$product['id_product'], false, (int)$this->context->language->id);
				$attributes = $productObj->getAttributesGroups((int)$this->context->language->id);
				$product['formatted_price'] = Tools::displayPrice(Tools::convertPrice($product['price_tax_incl'], $this->context->currency), $this->context->currency);

				foreach ($attributes as $attribute)
				{
					if (!isset($combinations[$attribute['id_product_attribute']]['attributes']))
						$combinations[$attribute['id_product_attribute']]['attributes'] = '';
					$combinations[$attribute['id_product_attribute']]['attributes'] .= $attribute['attribute_name'].' - ';
					$combinations[$attribute['id_product_attribute']]['id_product_attribute'] = $attribute['id_product_attribute'];
					$combinations[$attribute['id_product_attribute']]['default_on'] = $attribute['default_on'];
					if (!isset($combinations[$attribute['id_product_attribute']]['price']))
					{
						$price_tax_incl = Product::getPriceStatic((int)$product['id_product'], true, $attribute['id_product_attribute']);
						$combinations[$attribute['id_product_attribute']]['formatted_price'] = Tools::displayPrice(Tools::convertPrice($price_tax_incl, $this->context->currency), $this->context->currency);
					}
				}

				foreach ($combinations as &$combination)
					$combination['attributes'] = rtrim($combination['attributes'], ' - ');
				$product['combinations'] = $combinations;
			}
			return array(
				'products' => $products,
				'found' => true
			);
		}
		else
			return array('found' => false, 'notfound' => Tools::displayError('No product has been found.'));
	}
	
	public function ajaxProcessSearchProducts()
	{
		$array = $this->searchProducts(Tools::getValue('product_search'));
		$this->content = trim(Tools::jsonEncode($array));
	}

	public function renderForm()
	{
		$back = Tools::safeOutput(Tools::getValue('back', ''));
		if (empty($back))
			$back = self::$currentIndex.'&token='.$this->token;

		$this->toolbar_btn['save-and-stay'] = array(
			'href' => '#',
			'desc' => $this->l('Save and Stay')
		);

		$current_object = $this->loadObject(true);

		// All the filter are prefilled with the correct information
		$customer_filter = '';
		if (Validate::isUnsignedId($current_object->id_customer) &&
			($customer = new Customer($current_object->id_customer)) &&
			Validate::isLoadedObject($customer))
			$customer_filter = $customer->firstname.' '.$customer->lastname.' ('.$customer->email.')';

		$gift_product_filter = '';
		if (Validate::isUnsignedId($current_object->gift_product) &&
			($product = new Product($current_object->gift_product, false, $this->context->language->id)) &&
			Validate::isLoadedObject($product))
			$gift_product_filter = (!empty($product->reference) ? $product->reference : $product->name);

		$reduction_product_filter = '';
		if (Validate::isUnsignedId($current_object->reduction_product) &&
			($product = new Product($current_object->reduction_product, false, $this->context->language->id)) &&
			Validate::isLoadedObject($product))
			$reduction_product_filter = (!empty($product->reference) ? $product->reference : $product->name);

		$product_rule_groups = $this->getProductRuleGroupsDisplay($current_object);
		
		$attribute_groups = AttributeGroup::getAttributesGroups($this->context->language->id);
		$currencies = Currency::getCurrencies();
		$languages = Language::getLanguages();
		$countries = $current_object->getAssociatedRestrictions('country', true, true);
		$groups = $current_object->getAssociatedRestrictions('group', false, true);
		$shops = $current_object->getAssociatedRestrictions('shop', false, false);
		$cart_rules = $current_object->getAssociatedRestrictions('cart_rule', false, true);
		$carriers = $current_object->getAssociatedRestrictions('carrier', true, false);
		foreach ($carriers as &$carriers2)
			foreach ($carriers2 as &$carrier)
				foreach ($carrier as $field => &$value)
					if ($field == 'name' && $value == '0')
						$value = Configuration::get('PS_SHOP_NAME');
		
		$gift_product_select = '';
		$gift_product_attribute_select = '';
		if ((int)$current_object->gift_product)
		{
			$search_products = $this->searchProducts($gift_product_filter);
			foreach ($search_products['products'] as $product)
			{
				$gift_product_select .= '
				<option value="'.$product['id_product'].'" '.($product['id_product'] == $current_object->gift_product ? 'selected="selected"' : '').'>
					'.$product['name'].(count($product['combinations']) == 0 ? ' - '.$product['formatted_price'] : '').'
				</option>';
				
				if (count($product['combinations']))
				{
					$gift_product_attribute_select .= '<select class="id_product_attribute" id="ipa_'.$product['id_product'].'" name="ipa_'.$product['id_product'].'">';
					foreach ($product['combinations'] as $combination)
					{
						$gift_product_attribute_select .= '
						<option '.($combination['id_product_attribute'] == $current_object->gift_product_attribute ? 'selected="selected"' : '').' value="'.$combination['id_product_attribute'].'">
							'.$combination['attributes'].' - '.$combination['formatted_price'].'
						</option>';
					}
					$gift_product_attribute_select .= '</select>';
				}
			}
		}

		$product = new Product($current_object->gift_product);
		$this->context->smarty->assign(
			array(
				'show_toolbar' => true,
				'toolbar_btn' => $this->toolbar_btn,
				'toolbar_scroll' => $this->toolbar_scroll,
				'title' => array($this->l('Payment: '), $this->l('Cart Rules')),
				'defaultDateFrom' => date('Y-m-d H:00:00'),
				'defaultDateTo' => date('Y-m-d H:00:00', strtotime('+1 month')),
				'customerFilter' => $customer_filter,
				'giftProductFilter' => $gift_product_filter,
				'gift_product_select' => $gift_product_select,
				'gift_product_attribute_select' => $gift_product_attribute_select,
				'reductionProductFilter' => $reduction_product_filter,
				'defaultCurrency' => Configuration::get('PS_CURRENCY_DEFAULT'),
				'id_lang_default' => Configuration::get('PS_LANG_DEFAULT'),
				'languages' => $languages,
				'currencies' => $currencies,
				'countries' => $countries,
				'carriers' => $carriers,
				'groups' => $groups,
				'shops' => $shops,
				'cart_rules' => $cart_rules,
				'product_rule_groups' => $product_rule_groups,
				'product_rule_groups_counter' => count($product_rule_groups),
				'attribute_groups' => $attribute_groups,
				'currentIndex' => self::$currentIndex,
				'currentToken' => $this->token,
				'currentObject' => $current_object,
				'currentTab' => $this,
				'hasAttribute' => $product->hasAttributes(),
			)
		);

		$this->content .= $this->createTemplate('form.tpl')->fetch();

		$this->addJqueryUI('ui.datepicker');
		return parent::renderForm();
	}

	public function displayAjaxSearchCartRuleVouchers()
	{
		$found = false;
		if ($vouchers = CartRule::getCartsRuleByCode(Tools::getValue('q'), (int)$this->context->language->id))
			$found = true;
		echo Tools::jsonEncode(array('found' => $found, 'vouchers' => $vouchers));
	}
}
<?php
/*
* 2007-2012 PrestaShop
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
*  @copyright  2007-2012 PrestaShop SA
*  @version  Release: $Revision: 6844 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

@ini_set('max_execution_time', 3600);

class AdminAttributeGeneratorControllerCore extends AdminController
{
	private $combinations = array();
	private $product;

	public function __construct()
	{
	 	$this->table = 'product_attribute';
		$this->className = 'Product';
		parent::__construct();
	}

	protected function addAttribute($arr, $price = 0, $weight = 0)
	{
		foreach ($arr as $attr)
		{
			$price += (float)$_POST['price_impact_'.(int)$attr];
			$weight += (float)$_POST['weight_impact'][(int)$attr];
		}
		if ($this->product->id)
		{
			return (array(
					'id_product' => (int)$this->product->id,
					'price' => (float)$price,
					'weight' => (float)$weight,
					'ecotax' => 0,
					'quantity' => (int)$_POST['quantity'],
					'reference' => pSQL($_POST['reference']),
					'default_on' => 0,
					'available_date' => '0000-00-00'));
		}
		return array();
	}

	private static function createCombinations($list)
	{
		if (count($list) <= 1)
			return count($list) ? array_map(create_function('$v', 'return (array($v));'), $list[0]) : $list;
		$res = array();
		$first = array_pop($list);
		foreach ($first as $attribute)
		{
			$tab = AdminAttributeGeneratorController::createCombinations($list);
			foreach ($tab as $to_add)
				$res[] = is_array($to_add) ? array_merge($to_add, array($attribute)) : array($to_add, $attribute);
		}
		return $res;
	}

	public function postProcess()
	{
		$this->product = new Product((int)Tools::getValue('id_product'));
		$this->product->loadStockData();

		if (isset($_POST['generate']))
		{
			if (!is_array(Tools::getValue('options')))
				$this->errors[] = Tools::displayError('Please choose at least 1 attribute.');
			else
			{
				$tab = array_values($_POST['options']);
				if (count($tab) && Validate::isLoadedObject($this->product))
				{
					AdminAttributeGeneratorController::setAttributesImpacts($this->product->id, $tab);
					$this->combinations = array_values(AdminAttributeGeneratorController::createCombinations($tab));
					$values = array_values(array_map(array($this, 'addAttribute'), $this->combinations));

					// @since 1.5.0
					if ($this->product->depends_on_stock == 0)
					{
						$attributes = Product::getProductAttributesIds($this->product->id);
						foreach ($attributes as $attribute)
							StockAvailable::removeProductFromStockAvailable($this->product->id, $attribute['id_product_attribute'], $this->context->shop->id);
					}

					$this->product->deleteProductAttributes();
					$res = $this->product->addProductAttributeMultiple($values);
					$this->product->addAttributeCombinationMultiple($res, $this->combinations);

					// @since 1.5.0
					if ($this->product->depends_on_stock == 0)
					{
						$attributes = Product::getProductAttributesIds($this->product->id);
						$quantity = (int)Tools::getValue('quantity');
						foreach ($attributes as $attribute)
							StockAvailable::setQuantity($this->product->id, $attribute['id_product_attribute'], $quantity, $this->context->shop->id);
					}
					Tools::redirectAdmin($this->context->link->getAdminLink('AdminProducts').'&id_product='.(int)Tools::getValue('id_product').'&addproduct&action=Combinations');
				}
				else
					$this->errors[] = Tools::displayError('Unable to initialize parameters, combination is missing or object cannot be loaded.');
			}
		}
		else if (isset($_POST['back']))
			Tools::redirectAdmin(self::$currentIndex.'&id_product='.(int)Tools::getValue('id_product').'&addproduct'.'&tabs=3&token='.Tools::getValue('token'));
		parent::postProcess();
	}

	private static function displayAndReturnAttributeJs()
	{
		$attributes = Attribute::getAttributes(Context::getContext()->language->id, true);
		$attribute_js = array();
		foreach ($attributes as $k => $attribute)
			$attribute_js[$attribute['id_attribute_group']][$attribute['id_attribute']] = $attribute['name'];
		echo '
		<script type="text/javascript">
			var attrs = new Array();
			attrs[0] = new Array(0, \'---\');';
		foreach ($attribute_js as $idgrp => $group)
		{
			echo '
				attrs['.$idgrp.'] = new Array(0, \'---\' ';
			foreach ($group as $idattr => $attrname)
				echo ', '.$idattr.', \''.addslashes(($attrname)).'\'';
			echo ');';
		}
		echo '
		</script>';
		return $attribute_js;
	}

    private static function setAttributesImpacts($id_product, $tab)
    {
        $attributes = array();
        foreach ($tab as $group)
            foreach ($group as $attribute)
                $attributes[] = '('.(int)$id_product.', '.(int)$attribute.', '.(float)$_POST['price_impact_'.(int)$attribute].', '.(float)$_POST['weight_impact'][(int)$attribute].')';

		return Db::getInstance()->execute(
	        'INSERT INTO `'._DB_PREFIX_.'attribute_impact` (`id_product`, `id_attribute`, `price`, `weight`)
	        VALUES '.implode(',', $attributes).'
	        ON DUPLICATE KEY UPDATE `price`=VALUES(price), `weight`=VALUES(weight)'
		);
    }

    private static function getAttributesImpacts($id_product)
    {
        $tab = array();
        $result = Db::getInstance()->executeS(
        'SELECT ai.`id_attribute`, ai.`price`, ai.`weight`
		FROM `'._DB_PREFIX_.'attribute_impact` ai
		WHERE ai.`id_product` = '.(int)$id_product);
        if (!$result)
            return array();
        foreach ($result as $impact)
        {
            $tab[$impact['id_attribute']]['price'] = (float)$impact['price'];
            $tab[$impact['id_attribute']]['weight'] = (float)$impact['weight'];
        }
        return $tab;
    }

	public function initGroupTable()
	{
		$combinations_groups = $this->product->getAttributesGroups($this->context->language->id);
		$attributes = array();
        $impacts = AdminAttributeGeneratorController::getAttributesImpacts($this->product->id);
		foreach ($combinations_groups as &$combination)
		{
            $target = &$attributes[$combination['id_attribute_group']][$combination['id_attribute']];
			$target = $combination;
            if (isset($impacts[$combination['id_attribute']]))
            {
				$target['price'] = $impacts[$combination['id_attribute']]['price'];
				$target['weight'] = $impacts[$combination['id_attribute']]['weight'];
			}
		}
        $this->context->smarty->assign(array(
        	'currency_sign' => $this->context->currency->sign,
        	'weight_unit' => Configuration::get('PS_WEIGHT_UNIT'),
        	'attributes' => $attributes,
		));
	}

	public function initToolbar()
	{
		$this->toolbar_btn['back'] = array(
			'href' => $this->context->link->getAdminLink('AdminProducts').'&id_product='.(int)Tools::getValue('id_product').'&addproduct&action=Combinations',
			'desc' => $this->l('Back to product')
		);
	}

	public function initContent()
	{
		if (!Combination::isFeatureActive())
		{
			$this->displayWarning($this->l('This feature has been disabled, you can active this feature at this page:').'
				<a href="index.php?tab=AdminPerformance&token='.Tools::getAdminTokenLite('AdminPerformance').'#featuresDetachables">'.
					$this->l('Performances').'</a>');
			return;
		}

		// Init toolbar
		$this->initToolbarTitle();
		$this->initToolbar();

		$this->initGroupTable();

		$js_attributes = AdminAttributeGeneratorController::displayAndReturnAttributeJs();
		$attribute_groups = AttributeGroup::getAttributesGroups($this->context->language->id);
		$this->product = new Product((int)Tools::getValue('id_product'));

		$this->context->smarty->assign(array(
			'tax_rates' => $this->product->getTaxesRate(),
			'generate' => isset($_POST['generate']) && !count($this->errors),
			'combinations_size' => count($this->combinations),
			'product_name' => $this->product->name[$this->context->language->id],
			'product_reference' => $this->product->reference,
			'url_generator' => self::$currentIndex.'&id_product='.(int)Tools::getValue('id_product').'&attributegenerator&token='.Tools::getValue('token'),
			'attribute_groups' => $attribute_groups,
			'attribute_js' => $js_attributes,
			'toolbar_btn' => $this->toolbar_btn,
			'title' => $this->toolbar_title,
		));
	}
}
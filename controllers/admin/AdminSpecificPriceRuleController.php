<?php
/*
* 2007-2011 PrestaShop
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
*  @copyright  2007-2011 PrestaShop SA
*  @version  Release: $Revision: 8971 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class AdminSpecificPriceRuleController extends AdminController
{
	public function __construct()
	{
	 	$this->table = 'specific_price_rule';
		$this->className = 'SpecificPriceRule';
	 	$this->lang = false;

		$this->addRowAction('edit');
		$this->addRowAction('delete');

		$this->context = Context::getContext();

		$this->_select = 's.name shop_name, cu.name currency_name, cl.name country_name, gl.name group_name';
		$this->_join = 'LEFT JOIN '._DB_PREFIX_.'shop s ON (s.id_shop = a.id_shop)
		LEFT JOIN '._DB_PREFIX_.'currency cu ON (cu.id_currency = a.id_currency)
		LEFT JOIN '._DB_PREFIX_.'country_lang cl ON (cl.id_country = a.id_country AND cl.id_lang='.(int)$this->context->language->id.')
		LEFT JOIN '._DB_PREFIX_.'group_lang gl ON (gl.id_group = a.id_group AND gl.id_lang='.(int)$this->context->language->id.')';
		
	 	$this->bulk_actions = array('delete' => array('text' => $this->l('Delete selected'), 'confirm' => $this->l('Delete selected items?')));
	 	
		$this->fieldsDisplay = array(
			'id_specific_price_rule' => array(
				'title' => $this->l('ID'),
				'align' => 'center',
				'width' => 25
			),
			'name' => array(
				'title' => $this->l('Name'),
				'width' => 200
			),
			'shop_name' => array(
				'title' => $this->l('Shop')
			),
			'currency_name' => array(
				'title' => $this->l('Currency'),
				'align' => 'center',
			),
			'country_name' => array(
				'title' => $this->l('Country'),
				'align' => 'center',
			),
			'group_name' => array(
				'title' => $this->l('Group'),
				'align' => 'center',
			),
			'from_quantity' => array(
				'title' => $this->l('From quantity'),
				'align' => 'center',
			),
			'reduction_type' => array(
				'title' => $this->l('Reduction type'),
				'align' => 'center',
				'type' => 'select',
				'filter_key' => 'a!reduction_type',
				'list' => array(
					'percentage' => $this->l('Percentage'),
					'amount' => $this->l('Amount')
				),
			),
			'reduction' => array(
				'title' => $this->l('Reduction'),
				'align' => 'center',
			),
			'from' => array(
				'title' => $this->l('From'),
				'align' => 'center',
			),
			'to' => array(
				'title' => $this->l('To'),
				'align' => 'center',
			),
		);
		
		parent::__construct();
	}
	public function renderForm()
	{
		$this->fields_form = array(
			'legend' => array(
				'title' => $this->l('Specific price rules'),
			),
			'input' => array(
				array(
					'type' => 'text',
					'label' => $this->l('Name:'),
					'name' => 'name',
					'size' => 33,
					'maxlength' => 32,
					'required' => true,
					'hint' => $this->l('Forbidden characters:').' <>;=#{}'
				),
				array(
					'type' => 'select',
					'label' => $this->l('Shop:'),
					'name' => 'id_shop',
					'options' => array(
						'query' => array_merge(array(0 => array('id_shop' => 0, 'name' => $this->l('All shops'))), Shop::getShops()),
						'id' => 'id_shop',
						'name' => 'name'
					),
					'condition' => Shop::isFeatureActive()
				),
				array(
					'type' => 'select',
					'label' => $this->l('Currency:'),
					'name' => 'id_currency',
					'options' => array(
						'query' => array_merge(array(0 => array('id_currency' => 0, 'name' => $this->l('All currencies'))), Currency::getCurrencies()),
						'id' => 'id_currency',
						'name' => 'name'
					),
				),
				array(
					'type' => 'select',
					'label' => $this->l('Country:'),
					'name' => 'id_country',
					'options' => array(
						'query' => array_merge(array(0 => array('id_country' => 0, 'name' => $this->l('All countries'))), Country::getCountries((int)$this->context->language->id)),
						'id' => 'id_country',
						'name' => 'name'
					),
				),
				array(
					'type' => 'select',
					'label' => $this->l('Group:'),
					'name' => 'id_group',
					'options' => array(
						'query' => array_merge(array(0 => array('id_group' => 0, 'name' => $this->l('All groups'))), Group::getGroups((int)$this->context->language->id)),
						'id' => 'id_group',
						'name' => 'name'
					),
				),
				array(
					'type' => 'text',
					'label' => $this->l('From quantity:'),
					'name' => 'from_quantity',
					'size' => 6,
					'maxlength' => 10,
					'value' => '1',
					'required' => true
				),
				array(
					'type' => 'text',
					'label' => $this->l('Price:'),
					'name' => 'price',
					'size' => 6,
					'maxlength' => 10,
					'value' => '0',
					'suffix' => $this->context->currency->getSign('right'),
				),
				array(
					'type' => 'date',
					'label' => $this->l('From:'),
					'name' => 'from',
					'size' => 12,
					'required' => true
				),
				array(
					'type' => 'date',
					'label' => $this->l('To:'),
					'name' => 'to',
					'size' => 12,
					'required' => true
				),
				array(
					'type' => 'select',
					'label' => $this->l('Reduction type:'),
					'name' => 'reduction_type',
					'options' => array(
						'query' => array(array('reduction_type' => 'amount', 'name' => $this->l('Amount')), array('reduction_type' => 'percentage', 'name' => $this->l('Percentage'))),
						'id' => 'reduction_type',
						'name' => 'name'
					),
				),
				array(
					'type' => 'text',
					'label' => $this->l('Reduction:'),
					'name' => 'reduction',
					'required' => true,
				),
			),
			'submit' => array(
				'title' => $this->l('   Save   '),
				'class' => 'button'
			),
		);

		$attribute_groups = array();
		$attributes = Attribute::getAttributes((int)$this->context->language->id);
		foreach ($attributes as $attribute)
		{
			if (!isset($attribute_groups[$attribute['id_attribute_group']]))
				$attribute_groups[$attribute['id_attribute_group']]  = array(
																							'id_attribute_group' => $attribute['id_attribute_group'],
																							'name' => $attribute['attribute_group']
																						);
			$attribute_groups[$attribute['id_attribute_group']]['attributes'][] = array(
																											'id_attribute' => $attribute['id_attribute'],
																											'name' => $attribute['name']
																										);
		}
		$features = Feature::getFeatures((int)$this->context->language->id);
		foreach ($features AS &$feature)
			$feature['values'] = FeatureValue::getFeatureValuesWithLang((int)$this->context->language->id, $feature['id_feature'], true);
				
		$this->tpl_form_vars = array(
										'manufacturers' => Manufacturer::getManufacturers(),
										'suppliers' => Supplier::getSuppliers(),
										'attributes_group' => $attribute_groups,
										'features' => $features,
										'categories' => Category::getSimpleCategories((int)$this->context->language->id),
										'conditions' => $this->object->getConditions(),
										'is_multishop' => Shop::isFeatureActive()
										);
		
		return parent::renderForm();
	}
	
	public function processSave($token)
	{
		$conditions = array();
		if (Validate::isLoadedObject(($object = parent::processSave($token))))
		{
			foreach ($_POST as $key => $values)
			{
				if (preg_match('/^condition_group_([0-9]+)$/Ui', $key, $condition_group))
				{
					$conditions = array();
					foreach ($values as $value)
					{
						$condition = explode('_', $value);
						$conditions[] = array('type' => $condition[0], 'value' => $condition[1]);
					}
					$object->addConditions($conditions);
				}
			}
			$object->apply();
		}
	}
}

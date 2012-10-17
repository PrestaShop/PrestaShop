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
*  @version  Release: $Revision: 7158 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class SpecificPriceRuleCore extends ObjectModel
{
	public	$name;
	public	$id_shop;
	public	$id_currency;
	public	$id_country;
	public	$id_group;
	public	$from_quantity;
	public	$price;
	public	$reduction;
	public	$reduction_type;
	public	$from;
	public	$to;

	/**
	 * @see ObjectModel::$definition
	 */
	public static $definition = array(
		'table' => 'specific_price_rule',
		'primary' => 'id_specific_price_rule',
		'fields' => array(
			'name' => 			array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true),
			'id_shop' => 		array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
			'id_country' => 	array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
			'id_currency' => 	array('type' => self::TYPE_INT, 'required' => true),
			'id_group' => 		array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
			'from_quantity' => 	array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true),
			'price' => 		array('type' => self::TYPE_FLOAT, 'validate' => 'isNegativePrice', 'required' => true),
			'reduction' => 		array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice', 'required' => true),
			'reduction_type' => array('type' => self::TYPE_STRING, 'validate' => 'isReductionType', 'required' => true),
			'from' => 			array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat', 'required' => false),
			'to' => 			array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat', 'required' => false),
		),
	);

	protected $webserviceParameters = array(
		'objectsNodeName' => 'specific_price_rules',
		'objectNodeName' => 'specific_price_rule',
	        'fields' => array(
			'id_shop' => 				array('xlink_resource' => 'shops', 'required' => true),
			'id_country' => 			array('xlink_resource' => 'countries', 'required' => true),
			'id_currency' => 			array('xlink_resource' => 'currencies', 'required' => true),
			'id_group' => 				array('xlink_resource' => 'groups', 'required' => true),
	      	),
	);

	public function delete()
	{
		$this->deleteConditions();
		Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'specific_price WHERE id_specific_price_rule='.(int)$this->id);
		return parent::delete();
	}

	public function deleteConditions()
	{
		$ids_condition_group = Db::getInstance()->executeS('SELECT id_specific_price_rule_condition_group
																		 FROM '._DB_PREFIX_.'specific_price_rule_condition_group
																		 WHERE id_specific_price_rule='.(int)$this->id);
		if ($ids_condition_group)
			foreach ($ids_condition_group as $row)
			{
				Db::getInstance()->delete('specific_price_rule_condition_group', 'id_specific_price_rule_condition_group='.(int)$row['id_specific_price_rule_condition_group']);
				Db::getInstance()->delete('specific_price_rule_condition', 'id_specific_price_rule_condition_group='.(int)$row['id_specific_price_rule_condition_group']);
			}
	}

	public function addConditions($conditions)
	{
		if (!is_array($conditions))
			return;

		$result = Db::getInstance()->insert('specific_price_rule_condition_group', array(
			'id_specific_price_rule_condition_group' =>	'',
			'id_specific_price_rule' =>	(int)$this->id
		));
		if (!$result)
			return false;
		$id_specific_price_rule_condition_group = (int)Db::getInstance()->Insert_ID();
		foreach ($conditions as $condition)
		{
			$result = Db::getInstance()->insert('specific_price_rule_condition', array(
				'id_specific_price_rule_condition' => '',
				'id_specific_price_rule_condition_group' => (int)$id_specific_price_rule_condition_group,
				'type' => pSQL($condition['type']),
				'value' => (float)$condition['value'],
			));
			if (!$result)
				return false;
		}
		return true;
	}

	public function apply($products = false)
	{
		$this->resetApplication($products);
		$products = $this->getAffectedProducts($products);
		foreach ($products as $product)
			SpecificPriceRule::applyRuleToProduct((int)$this->id, (int)$product['id_product'], (int)$product['id_product_attribute']);
	}

	public function resetApplication($products = false)
	{
		$where = '';
		if ($products && count($products))
			$where .= ' AND id_product IN ('.implode(', ', array_map('intval', $products)).')';
		return Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'specific_price WHERE id_specific_price_rule='.(int)$this->id.$where);
	}

	public static function applyAllRules($products = false)
	{
		$rules = new Collection('SpecificPriceRule');
		foreach ($rules as $rule)
			$rule->apply($products);
	}

	public function getConditions()
	{
		$conditions = Db::getInstance()->executeS('
			SELECT g.*, c.*
			FROM '._DB_PREFIX_.'specific_price_rule_condition_group g
			LEFT JOIN '._DB_PREFIX_.'specific_price_rule_condition c
				ON (c.id_specific_price_rule_condition_group = g.id_specific_price_rule_condition_group)
			WHERE g.id_specific_price_rule='.(int)$this->id
		);
		$conditions_group = array();
		if ($conditions)
		{
			foreach ($conditions as &$condition)
			{
				if ($condition['type'] == 'attribute')
					$condition['id_attribute_group'] = Db::getInstance()->getValue('SELECT id_attribute_group
																										FROM '._DB_PREFIX_.'attribute
																										WHERE id_attribute='.(int)$condition['value']);
				elseif ($condition['type'] == 'feature')
					$condition['id_feature'] = Db::getInstance()->getValue('SELECT id_feature
																								FROM '._DB_PREFIX_.'feature_value
																								WHERE id_feature_value='.(int)$condition['value']);
				$conditions_group[(int)$condition['id_specific_price_rule_condition_group']][] = $condition;
			}
		}
		return $conditions_group;
	}

	public function getAffectedProducts($products = false)
	{
		$conditions_group = $this->getConditions();

		$query = new DbQuery();
		$query->select('p.id_product');
		$query->from('product', 'p');
		$query->join(Shop::addSqlAssociation('product', 'p'));
		$query->groupBy('p.id_product');

		$attributes = false;
		$categories = false;
		$features = false;
		$suppliers = false;
		$where = '1';

		if ($conditions_group)
		{
			$where .= ' AND (';
			foreach ($conditions_group as $id_condition_group => $condition_group)
			{
				$fields = array(
					'category' => array(
						'name' => 'cp.id_category',
						'values' => array()
					),
					'manufacturer' => array(
						'name' => 'p.id_manufacturer',
						'values' => array(),
					),
					'supplier' => array(
						'name' => 'pss.id_supplier',
						'values' => array()
					),
					'feature' => array(
						'name' => 'fp.id_feature_value',
						'values' => array()
					),
					'attribute' => array(
						'name'=> 'pac.id_attribute',
						'values' => array()
					)
				);
				
				foreach ($condition_group as $condition)
				{
					if ($condition['type'] == 'category')
						$categories = true;
					elseif ($condition['type'] == 'feature')
						$features = true;
					elseif ($condition['type'] == 'attribute')
						$attributes = true;
					elseif ($condition['type'] == 'supplier')
						$suppliers = true;
						
					$fields[$condition['type']]['values'][] = $condition['value'];
				}
				
				foreach ($fields as $field)
				{
					if (!$n_conditions = count($field['values']))
						continue;

					$where .= $field['name'].' IN ('.implode(',', array_map('intval', $field['values'])).') AND ';
					if ($n_conditions > 1)
						$query->having('COUNT('.bqSQL($field['name']).') >='.(int)$n_conditions);
				}
				
				$where = rtrim($where, ' AND ').') OR (';
			}
			$where = rtrim($where, 'OR (');
		}
		if ($products && count($products))
			$where .= ' AND p.id_product IN ('.implode(', ', array_map('intval', $products)).')';
		if ($attributes)
		{
			$query->select('pa.id_product_attribute');
			$query->leftJoin('product_attribute', 'pa', 'p.id_product = pa.id_product');
			$query->join(Shop::addSqlAssociation('product_attribute', 'pa', false));
			$query->leftJoin('product_attribute_combination', 'pac', 'pa.id_product_attribute = pac.id_product_attribute');
			$query->groupBy('pa.id_product_attribute');
		}
		else
			$query->select('NULL as id_product_attribute');

		if ($features)
			$query->leftJoin('feature_product', 'fp', 'p.id_product = fp.id_product');			
		if ($categories)
			$query->leftJoin('category_product', 'cp', 'p.id_product = cp.id_product');

		if ($suppliers)
			$query->leftJoin('product_supplier', 'pss', 'p.id_product = pss.id_product');

		if ($where)
			$query->where($where);

		return Db::getInstance()->executeS($query);
	}

	public static function applyRuleToProduct($id_rule, $id_product, $id_product_attribute = null)
	{
		$rule = new SpecificPriceRule((int)$id_rule);
		if (!Validate::isLoadedObject($rule))
			return false;

		$specific_price = new SpecificPrice();
		$specific_price->id_specific_price_rule = (int)$rule->id;
		$specific_price->id_product = (int)$id_product;
		$specific_price->id_product_attribute = (int)$id_product_attribute;
		$specific_price->id_customer = 0;
		$specific_price->id_shop = (int)$rule->id_shop;
		$specific_price->id_country = (int)$rule->id_country;
		$specific_price->id_currency = (int)$rule->id_currency;
		$specific_price->id_group = (int)$rule->id_group;
		$specific_price->from_quantity = (int)$rule->from_quantity;
		$specific_price->price = (int)$rule->price;
		$specific_price->reduction_type = $rule->reduction_type;
		$specific_price->reduction = ($rule->reduction_type == 'percentage' ? $rule->reduction / 100 : (float)$rule->reduction);
		$specific_price->from = $rule->from;
		$specific_price->to = $rule->to;

		return $specific_price->add();
	}
}

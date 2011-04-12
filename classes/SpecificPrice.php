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
*  @version  Release: $Revision: 1.4 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class SpecificPriceCore extends ObjectModel
{
	public	$id_product;
	public	$id_shop;
	public	$id_currency;
	public	$id_country;
	public	$id_group;
	public	$price;
	public	$from_quantity;
	public	$reduction;
	public	$reduction_type;
	public	$from;
	public	$to;

 	protected 	$fieldsRequired = array('id_product', 'id_shop', 'id_currency', 'id_country', 'id_group', 'price', 'from_quantity', 'reduction', 'reduction_type', 'from', 'to');
 	protected 	$fieldsValidate = array('id_product' => 'isUnsignedId', 'id_shop' => 'isUnsignedId', 'id_country' => 'isUnsignedId', 'id_group' => 'isUnsignedId', 'price' => 'isPrice', 'from_quantity' => 'isUnsignedInt', 'reduction' => 'isPrice', 'reduction_type' => 'isReductionType', 'from' => 'isDateFormat', 'to' => 'isDateFormat');

	protected 	$table = 'specific_price';
	protected 	$identifier = 'id_specific_price';

	protected static $_specificPriceCache = array();
	protected static $_cache_priorities = array();

	public function getFields()
	{
		parent::validateFields();
		$fields['id_product'] = (int)($this->id_product);
		$fields['id_shop'] = (int)($this->id_shop);
		$fields['id_currency'] = (int)($this->id_currency);
		$fields['id_country'] = (int)($this->id_country);
		$fields['id_group'] = (int)($this->id_group);
		$fields['price'] = (float)($this->price);
		$fields['from_quantity'] = (int)($this->from_quantity);
		$fields['reduction'] = (float)($this->reduction);
		$fields['reduction_type'] = pSQL($this->reduction_type);
		$fields['from'] = pSQL($this->from);
		$fields['to'] = pSQL($this->to);
		return $fields;
	}

	static public function getByProductId($id_product)
	{
		return Db::getInstance()->ExecuteS('
			SELECT * FROM `'._DB_PREFIX_.'specific_price` WHERE `id_product` = '.(int)($id_product)
		);
	}

	static public function getIdsByProductId($id_product)
	{
		return Db::getInstance()->ExecuteS('
			SELECT `id_specific_price` FROM `'._DB_PREFIX_.'specific_price` WHERE `id_product` = '.(int)$id_product.'
		');
	}

   // score generation for quantity discount
	protected static function _getScoreQuery($id_product, $id_shop, $id_currency, $id_country, $id_group)
	{
	    $select = '(';

       $now = date('Y-m-d H:i:s');
       $select .= ' IF (\''.$now.'\' >= `from` AND \''.$now.'\' <= `to`, '.pow(2, 0).', 0) + ';

	    $priority = SpecificPrice::getPriority($id_product);
	    foreach (array_reverse($priority) AS $k => $field)
           $select .= ' IF (`'.$field.'` = '.(int)(${$field}).', '.pow(2, $k + 1).', 0) + ';

	    return rtrim($select, ' +').') AS `score`';
	}

    public static function getPriority($id_product)
    {

    	if (!isset(self::$_cache_priorities[(int)$id_product]))
    	{
		   self::$_cache_priorities[(int)$id_product] = Db::getInstance()->getValue('
		   SELECT `priority`
			FROM `'._DB_PREFIX_.'specific_price_priority`
			WHERE `id_product` = '.(int)$id_product);
		}

		$priority = self::$_cache_priorities[(int)$id_product];

	    if (!$priority)
	        $priority = Configuration::get('PS_SPECIFIC_PRICE_PRIORITIES');

	    return preg_split('/;/', $priority);
    }

	static public function getSpecificPrice($id_product, $id_shop, $id_currency, $id_country, $id_group, $quantity)
	{
		/*
		** The date is not taken into account for the cache, but this is for the better because it keeps the consistency for the whole script.
		** The price must not change between the top and the bottom of the page
		*/

		$key = ((int)$id_product.'-'.(int)$id_shop.'-'.(int)$id_currency.'-'.(int)$id_country.'-'.(int)$id_group.'-'.(int)$quantity);
		if (!array_key_exists($key, self::$_specificPriceCache))
		{
			$now = date('Y-m-d H:i:s');
			self::$_specificPriceCache[$key] = Db::getInstance()->getRow('
				SELECT *, '.self::_getScoreQuery($id_product, $id_shop, $id_currency, $id_country, $id_group).'
				FROM `'._DB_PREFIX_.'specific_price`
				WHERE `id_product` IN (0, '.(int)$id_product.')
				AND `id_shop` IN (0, '.(int)$id_shop.')
				AND `id_currency` IN (0, '.(int)$id_currency.')
				AND `id_country` IN (0, '.(int)$id_country.')
				AND `id_group` IN (0, '.(int)$id_group.')
				AND `from_quantity` <= '.(int)$quantity.'
				AND	(`from` = \'0000-00-00 00:00:00\' OR (\''.$now.'\' >= `from` AND \''.$now.'\' <= `to`))
				ORDER BY `score` DESC, `from_quantity` DESC');
		}
		return self::$_specificPriceCache[$key];
	}

	static public function setPriorities($priorities)
	{
		$value = '';
		foreach ($priorities as $priority)
			$value .= pSQL($priority).';';

        SpecificPrice::deletePriorities();

		return Configuration::updateValue('PS_SPECIFIC_PRICE_PRIORITIES', rtrim($value, ';'));
	}

	public static function deletePriorities()
	{
	    return Db::getInstance()->Execute('
	    TRUNCATE `'._DB_PREFIX_.'specific_price_priority`
	    ');
	}

	static public function setSpecificPriority($id_product, $priorities)
	{
		$fields = '';
		$value = '';
		foreach ($priorities as $priority)
			$value .= pSQL($priority).';';

		return Db::getInstance()->Execute('
		INSERT INTO `'._DB_PREFIX_.'specific_price_priority` (`id_product`, `priority`)
		VALUES ('.(int)$id_product.',\''.rtrim($value, ';').'\')
		ON DUPLICATE KEY UPDATE `priority` = \''.rtrim($value, ';').'\'
		');
	}

	static public function getQuantityDiscounts($id_product, $id_shop, $id_currency, $id_country, $id_group)
	{
		$now = date('Y-m-d H:i:s');
		$res =  Db::getInstance()->ExecuteS('
			SELECT *,
					'.self::_getScoreQuery($id_product, $id_shop, $id_currency, $id_country, $id_group).'
			FROM `'._DB_PREFIX_.'specific_price`
			WHERE	`id_product` IN(0, '.(int)($id_product).') AND
					`id_shop` IN(0, '.(int)($id_shop).') AND
					`id_currency` IN(0, '.(int)($id_currency).') AND
					`id_country` IN(0, '.(int)($id_country).') AND
					`id_group` IN(0, '.(int)($id_group).') AND
					(`from` = \'0000-00-00 00:00:00\' OR (\''.$now.'\' >= `from` AND \''.$now.'\' <= `to`))
					ORDER BY `score`  DESC, `from_quantity` DESC
		');

		$targeted_prices = array();
		$max_score = NULL;

		foreach($res as $specific_price)
		{
		    if (!isset($max_score))
		        $max_score = $specific_price['score'];
		    else if ($max_score != $specific_price['score'])
		        break;

            if ($specific_price['from_quantity'] > 1)
    		    $targeted_prices[] = $specific_price;
		}

		return $targeted_prices;
	}

	static public function getQuantityDiscount($id_product, $id_shop, $id_currency, $id_country, $id_group, $quantity)
	{
		$now = date('Y-m-d H:i:s');
		return Db::getInstance()->getRow('
			SELECT *,
					'.self::_getScoreQuery($id_product, $id_shop, $id_currency, $id_country, $id_group).'
			FROM `'._DB_PREFIX_.'specific_price`
			WHERE	`id_product` IN(0, '.(int)($id_product).') AND
					`id_shop` IN(0, '.(int)($id_shop).') AND
					`id_currency` IN(0, '.(int)($id_currency).') AND
					`id_country` IN(0, '.(int)($id_country).') AND
					`id_group` IN(0, '.(int)($id_group).') AND
					`from_quantity` >= '.(int)($quantity).' AND
					(`from` = \'0000-00-00 00:00:00\' OR (\''.$now.'\' >= `from` AND \''.$now.'\' <= `to`))
					ORDER BY `score` DESC, `from_quantity` DESC
		');
	}

	static public function getProductIdByDate($id_shop, $id_currency, $id_country, $id_group, $beginning, $ending)
	{
		$resource = Db::getInstance()->ExecuteS('
			SELECT `id_product`
			FROM `'._DB_PREFIX_.'specific_price`
			WHERE	`id_shop` IN(0, '.(int)($id_shop).') AND
					`id_currency` IN(0, '.(int)($id_currency).') AND
					`id_country` IN(0, '.(int)($id_country).') AND
					`id_group` IN(0, '.(int)($id_group).') AND
					`from_quantity` = 1 AND
					(`from` = \'0000-00-00 00:00:00\' OR (\''.$beginning.'\' >= `from` AND \''.$ending.'\' <= `to`)) AND
					`reduction` > 0
		', false);
		$ids_product = array();
		while ($row = DB::getInstance()->nextRow($resource))
			$ids_product[] = (int)($row['id_product']);
		return $ids_product;
	}

	static public function deleteByProductId($id_product)
	{
		return Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'specific_price` WHERE `id_product` = '.(int)($id_product));
	}

	public function duplicate($id_product = false)
	{
		if ($id_product)
			$this->id_product = (int)($id_product);
		return $this->add();
	}
}


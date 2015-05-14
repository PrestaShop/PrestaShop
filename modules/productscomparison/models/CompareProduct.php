<?php
/**
* 2007-2015 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
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
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2015 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class CompareProduct extends ObjectModel
{
	public $id_compare;

	public $id_customer;

	public $date_add;

	public $date_upd;

	/**
	 * @see ObjectModel::$definition
	 */
	public static $definition = array(
		'table' => 'compare',
		'primary' => 'id_compare',
		'fields' => array(
			'id_compare' => 	array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true),
			'id_customer' => 	array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true),
		),
	);

	/**
	 * Get all compare products of the customer
	 * @param int $id_customer
	 * @return array
	 */
	public static function getCompareProducts($id_compare)
	{
		$results = Db::getInstance()->executeS('
		SELECT DISTINCT `id_product`
		FROM `'._DB_PREFIX_.'compare` c
		LEFT JOIN `'._DB_PREFIX_.'compare_product` cp ON (cp.`id_compare` = c.`id_compare`)
		WHERE cp.`id_compare` = '.(int)$id_compare);

		$compare_products = null;

		if ($results)
			foreach ($results as $result)
				$compare_products[] = (int)$result['id_product'];

		return $compare_products;
	}


	/**
	 * Add a compare product for the customer
	 * @param int $id_customer, int $id_product
	 * @return bool
	 */
	public static function addCompareProduct($id_compare, $id_product)
	{
		// Check if compare row exists
		$id_compare = Db::getInstance()->getValue('
			SELECT `id_compare`
			FROM `'._DB_PREFIX_.'compare`
			WHERE `id_compare` = '.(int)$id_compare);

		if (!(int)$id_compare)
		{
			$id_customer = false;
			if (Context::getContext()->customer)
				$id_customer = (int)Context::getContext()->customer->id;
			$sql = Db::getInstance()->execute('
			INSERT INTO `'._DB_PREFIX_.'compare` (`id_compare`, `id_customer`) VALUES (NULL, "'.((int)$id_customer ? (int)$id_customer: '0').'")');
			if ($sql)
			{
				$id_compare = (int)Db::getInstance()->getValue('SELECT MAX(`id_compare`) FROM `'._DB_PREFIX_.'compare`');
				Context::getContext()->cookie->id_compare = (int)$id_compare;
			}
		}

		return Db::getInstance()->execute('
			INSERT IGNORE INTO `'._DB_PREFIX_.'compare_product` (`id_compare`, `id_product`, `date_add`, `date_upd`)
			VALUES ('.(int)$id_compare.', '.(int)$id_product.', NOW(), NOW())');
	}

	/**
	 * Remove a compare product for the customer
	 * @param int $id_compare
	 * @param int $id_product
	 * @return bool
	 */
	public static function removeCompareProduct($id_compare, $id_product)
	{
		return Db::getInstance()->execute('
		DELETE cp FROM `'._DB_PREFIX_.'compare_product` cp, `'._DB_PREFIX_.'compare` c
		WHERE cp.`id_compare`=c.`id_compare`
		AND cp.`id_product` = '.(int)$id_product.'
		AND c.`id_compare` = '.(int)$id_compare);
	}

	/**
	 * Get the number of compare products of the customer
	 * @param int $id_compare
	 * @return int
	 */
	public static function getNumberProducts($id_compare)
	{
		return (int)Db::getInstance()->getValue('
			SELECT count(`id_compare`)
			FROM `'._DB_PREFIX_.'compare_product`
			WHERE `id_compare` = '.(int)$id_compare);
	}


	/**
	 * Clean entries which are older than the period
	 * @param string $period
	 * @return void
	 */
	public static function cleanCompareProducts($period = 'week')
	{
		if ($period === 'week')
			$interval = '1 WEEK';
		elseif ($period === 'month')
			$interval = '1 MONTH';
		elseif ($period === 'year')
			$interval = '1 YEAR';
		else
			return;

		if ($interval != null)
		{
			Db::getInstance()->execute('
			DELETE cp, c FROM `'._DB_PREFIX_.'compare_product` cp, `'._DB_PREFIX_.'compare` c
			WHERE cp.date_upd < DATE_SUB(NOW(), INTERVAL 1 WEEK) AND c.`id_compare`=cp.`id_compare`');
		}
	}

	/**
	 * Get the id_compare by id_customer
	 * @param int $id_customer
	 * @return int $id_compare
	 */
	public static function getIdCompareByIdCustomer($id_customer)
	{
		return (int)Db::getInstance()->getValue('
		SELECT `id_compare`
		FROM `'._DB_PREFIX_.'compare`
		WHERE `id_customer`= '.(int)$id_customer);
	}


	public static function getFeaturesForComparison($list_ids_product, $id_lang)
	{
		if (!Feature::isFeatureActive())
			return false;

		$ids = '';
		foreach ($list_ids_product as $id)
			$ids .= (int)$id.',';

		$ids = rtrim($ids, ',');

		if (empty($ids))
			return false;

		return Db::getInstance()->executeS('
			SELECT f.*, fl.*
			FROM `'._DB_PREFIX_.'feature` f
			LEFT JOIN `'._DB_PREFIX_.'feature_product` fp
				ON f.`id_feature` = fp.`id_feature`
			LEFT JOIN `'._DB_PREFIX_.'feature_lang` fl
				ON f.`id_feature` = fl.`id_feature`
			WHERE fp.`id_product` IN ('.$ids.')
			AND `id_lang` = '.(int)$id_lang.'
			GROUP BY f.`id_feature`
			ORDER BY f.`position` ASC
		');
	}
}

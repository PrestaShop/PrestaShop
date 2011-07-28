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
*  @version  Release: $Revision$
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class CompareProduct extends ObjectModel
{
	public		$id;
	
	public 		$id_product;
	
	public 		$id_guest;
	
	public 		$id_customer;
	
	public 		$date_add;
	
	public 		$date_upd;
	
	protected 	$fieldRequired = array(
		'id_product', 
		'id_guest', 
		'id_customer');
	
	protected 	$fieldsValidate = array(
		'id_product' => 'isUnsignedInt',
		'id_guest' => 'isUnsignedInt',
		'id_customer' => 'isUnsignedInt'
	);
	
	protected $table = 'compare_product';
	
	protected $identifier = 'id_compare_product';
	
	
	/**
	 * Get all compare products of the guest
	 * @param int $id_guest
	 * @return array
	 */
	public static function getGuestCompareProducts($id_guest)
	{
		$results = Db::getInstance()->ExecuteS('
		SELECT DISTINCT `id_product`
		FROM `'._DB_PREFIX_.'compare_product`
		WHERE `id_guest` = '.(int)($id_guest));
	
		$compareProducts = null;
		
		foreach($results as $result)
			$compareProducts[] = $result['id_product'];
		
		return $compareProducts; 
	}
	
	
	/**
	 * Add a compare product for the guest
	 * @param int $id_guest, int $id_product
	 * @return boolean
	 */
	public static function addGuestCompareProduct($id_guest, $id_product)
	{
		return Db::getInstance()->Execute('
			INSERT INTO `'._DB_PREFIX_.'compare_product` (`id_product`, `id_guest`, `date_add`, `date_upd`) 
			VALUES ('.(int)($id_product).', '.(int)($id_guest).', NOW(), NOW())
		');
	}
	
	
	/**
	 * Remove a compare product for the guest
	 * @param int $id_guest, int $id_product
	 * @return boolean
	 */
	public static function removeGuestCompareProduct($id_guest, $id_product)
	{
		return Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'compare_product` WHERE `id_guest` = '.(int)($id_guest).' AND `id_product` = '.(int)($id_product));
	}
		
	
	/**
	 * Get the number of compare products of the guest
	 * @param int $id_guest
	 * @return int
	 */
	public static function getGuestNumberProducts($id_guest)
	{
		return (int)(Db::getInstance()->getValue('
			SELECT count(`id_compare_product`)
			FROM `'._DB_PREFIX_.'compare_product`
			WHERE `id_guest` = '.(int)($id_guest)));;
	}

	
	/**
	 * Get all comapare products of the customer
	 * @param int $id_customer
	 * @return array
	 */
	public static function getCustomerCompareProducts($id_customer)
	{
		$results = Db::getInstance()->ExecuteS('
		SELECT DISTINCT `id_product`
		FROM `'._DB_PREFIX_.'compare_product`
		WHERE `id_customer` = '.(int)($id_customer));
	
		$compareProducts = null;
		
		foreach($results as $result)
			$compareProducts[] = $result['id_product'];
		
		return $compareProducts; 
	}
	
	
	/**
	 * Add a compare product for the customer
	 * @param int $id_customer, int $id_product
	 * @return boolean
	 */
	public static function addCustomerCompareProduct($id_customer, $id_product)
	{
		 return Db::getInstance()->Execute('INSERT INTO `'._DB_PREFIX_.'compare_product` (`id_product`, `id_customer`, `date_add`, `date_upd`) VALUES ('.(int)($id_product).', '.(int)($id_customer).', NOW(), NOW())');
	}
	
	
	/**
	 * Remove a compare product for the customer
	 * @param int $id_customer, int $id_product
	 * @return boolean
	 */
	public static function removeCustomerCompareProduct($id_customer, $id_product)
	{
		return Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'compare_product` WHERE `id_customer` = '.(int)($id_customer).' AND `id_product` = '.(int)($id_product));
	}	
	
	
	/**
	 * Get the number of compare products of the customer
	 * @param int $id_customer
	 * @return int
	 */
	public static function getCustomerNumberProducts($id_customer)
	{
		return (int)(Db::getInstance()->getValue('
			SELECT count(`id_compare_product`)
			FROM `'._DB_PREFIX_.'compare_product`
			WHERE `id_customer` = '.(int)($id_customer)));
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
			Db::getInstance()->Execute('
			DELETE FROM `'._DB_PREFIX_.'compare_product`
			WHERE date_upd < DATE_SUB(NOW(), INTERVAL '.pSQL($interval).')');
		}
	}
}
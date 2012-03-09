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

class AccountingCore
{
	const CONF_NAME = 'ACCOUNTING_CONFIGURATION';
	
	/**
     * Default Values
     *
     * @TODO: Check to put the default value into the localization pack
     * This configuration is applied for a specific shop
     *
     * @var array
     */
	public static $acc_conf = array(
		'customer_prefix' => '411',
		'journal' => 'VE',
		'account_length' => 13,
		'account_submit_shipping_charge' => '708510',
		'account_unsubmit_shipping_charge' => '708520',
		'account_gift_wripping' => '',
        'account_handling' => ''
	);

	public static $acc_conf_cached = false;

	/**
	* Set an account number to a zone (will be refactoring for a dynamic use depending of the Controller)
	* @var array $assoZoneShopList correspond to an associated list of id_zone - id_shop - num
	* @return bool To know if any modification in the database succeed
	*/
	public static function setAccountNumberByZoneShop($assoZoneShopList)
	{
		$query = '
			REPLACE INTO`'._DB_PREFIX_.'accounting_zone_shop`
			(id_zone, id_shop, account_number)
			VALUES %s';
		
		$values = '';
		
		// Build the query for the update 
		foreach ($assoZoneShopList as $asso)
			if (array_key_exists('id_zone', $asso) &&
					array_key_exists('id_shop', $asso) &&
					array_key_exists('num', $asso))
				$values .= '('.(int)$asso['id_zone'].','.(int)$asso['id_shop'].', \''.pSQL($asso['num']).'\'), ';
		$query = sprintf($query, rtrim($values, ', '));
		
		if (!empty($values))
			return Db::getInstance()->execute($query);
		return false;
	}
	
	/**
	* Add or update product accounting information for a product (will be refactoring for a dynamic use depending of the Controller)
	* @param array $assoProductZoneShop
    * @return mixed bool|array
	*/
	public static function saveProductAccountingInformations($assoProductZoneShop)
	{
		$query = '
			REPLACE INTO`'._DB_PREFIX_.'accounting_product_zone_shop`
			(id_zone, id_shop, id_product, account_number)
			VALUES %s';
		
		$values = '';
		foreach ($assoProductZoneShop as $asso)
			if (array_key_exists('id_zone', $asso) &&
					array_key_exists('id_shop', $asso) &&
					array_key_exists('id_product', $asso) &&
					array_key_exists('num', $asso))
				$values .= '('.(int)$asso['id_zone'].','.(int)$asso['id_shop'].','.(int)$asso['id_product'].', \''.pSQL($asso['num']).'\'), ';
		$query = sprintf($query, rtrim($values, ', '));
		
		if (!empty($values))
			return Db::getInstance()->execute($query);
		return false;
	}
	
	/**
	* Get product account number list by zone (will be refactoring for a dynamic use depending of the Controller)
	* @var int $id_zone
	* @var int $id_shop
	* @return array
	*/	
	public static function getProductAccountNumberZoneShop($id_product, $id_shop)
	{
		return Db::getInstance()->executeS('
			SELECT `account_number`, `id_zone`
			FROM `'._DB_PREFIX_.'accounting_product_zone_shop`
			WHERE `id_product` = '.(int)$id_product.'
			AND `id_shop` = '.(int)$id_shop);
	}
		
	/**
	* Get shop account number list by zone (will be refactoring for a dynamic use depending of the Controller)
	* @var int $id_zone
	* @var int $id_shop
	* @return array
	*/	
	public static function getAccountNumberZoneShop($id_shop)
	{
		return Db::getInstance()->executeS('
			SELECT `id_shop`, `id_zone`, `account_number` 
			FROM `'._DB_PREFIX_.'accounting_zone_shop`
			WHERE `id_shop` = '.(int)$id_shop);
	}

	/**
	 * Get the Accounting Configuration
	 * If a key is defined, then it will try to get the value
	 *
	 * @static
	 * @param null $key
	 * @return array|bool
	 */
	public static function getConfiguration($key = null)
	{
		// Cache for call performance
		if (!self::$acc_conf_cached)
		{
			// Merge default values with the configured values
			if ($conf = unserialize(Configuration::get(Accounting::CONF_NAME)))
				self::$acc_conf = array_merge(self::$acc_conf, $conf);
			self::$acc_conf_cached = true;
		}


		// Return value key or the complete configuration depending of the $key definition
		return (!$key) ? self::$acc_conf : ((isset(self::$acc_conf[$key]) ? self::$acc_conf[$key] : false));
	}

	/**
	 * Get the list of export done
	 *
	 * @static
	 * @return array
	 */
	public static function getExportedList()
	{
		return Db::getInstance()->executeS('
			SELECT * FROM `'._DB_PREFIX_.'accounting_export` ORDER BY `date` DESC');
	}

	/**
	 * Get the displayed customer account.
	 * Pad with / without prefix if the account is set
	 *
	 * @static
	 * @param $id_customer
	 * @param $default_value
	 * @return string
	 */
	public static function getDisplayedCustomerAccount($id_customer, $default_value = false)
	{
		$acc_num = Db::getInstance()->getValue('
			SELECT account_number FROM `'._DB_PREFIX_.'customer`
			WHERE id_customer = '.(int)$id_customer);

		$display = $acc_num;
		if (empty($acc_num) || $default_value)
		{
			$display = Accounting::getConfiguration('customer_prefix');
			$max_len = Accounting::getConfiguration('account_length');
			$len = Tools::strlen($display) + Tools::strlen((string)$id_customer);

			// Pad the displayed string
			while ($max_len > 0 && $max_len > $len)
			{
				$display .= '0';
				--$max_len;
			}
			$display .= (string)$id_customer;
		}
		return $display;
	}
}

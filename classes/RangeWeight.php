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

class RangeWeightCore extends ObjectModel
{
	public $id_carrier;
	public $delimiter1;
	public $delimiter2;

	/**
	 * @see ObjectModel::$definition
	 */
	public static $definition = array(
		'table' => 'range_weight',
		'primary' => 'id_range_weight',
		'fields' => array(
			'id_carrier' => array('type' => self::TYPE_INT, 'validate' => 'isInt', 'required' => true),
			'delimiter1' => array('type' => self::TYPE_FLOAT, 'validate' => 'isUnsignedFloat', 'required' => true),
			'delimiter2' => array('type' => self::TYPE_FLOAT, 'validate' => 'isUnsignedFloat', 'required' => true),
		),
	);

	protected	$webserviceParameters = array(
			'objectNodeName' => 'weight_range',
			'objectsNodeName' => 'weight_ranges',
			'fields' => array(
			'id_carrier' => array('xlink_resource' => 'carriers'),
		)
	);

	/**
	* Get all available weight ranges
	*
	* @return array Ranges
	*/
	public static function getRanges($id_carrier)
	{
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
		SELECT *
		FROM `'._DB_PREFIX_.'range_weight`
		WHERE `id_carrier` = '.(int)$id_carrier.'
		ORDER BY `delimiter1` ASC');
	}

	/**
	 * Override add to create delivery value for all zones
	 * @see classes/ObjectModelCore::add()
	 * 
	 * @param bool $null_values
	 * @param bool $autodate
	 * @return boolean Insertion result
	 */
	public function add($autodate = true, $null_values = false)
	{
		if (!parent::add($autodate, $null_values) || !Validate::isLoadedObject($this))
			return false;

		$carrier = new Carrier((int)$this->id_carrier);
		$price_list = array();
		foreach ($carrier->getZones() as $zone)
			$price_list[] = array(
				'id_range_price' => 0,
				'id_range_weight' => (int)$this->id,
				'id_carrier' => (int)$this->id_carrier,
				'id_zone' => (int)$zone['id_zone'],
				'price' => 0,
			);
		$carrier->addDeliveryPrice($price_list);

		return true;
	}
}

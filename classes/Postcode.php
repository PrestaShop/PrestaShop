<?php
/*
* 2007-2015 PrestaShop
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
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class PostcodeCore extends ObjectModel
{
	/** @var integer Country id which state belongs */
	public $id_country;

	/** @var integer Zone id which state belongs */
	public $id_zone;

	/** @var string Postcode */
	public $postcode;

	/** @var boolean Status for delivery */
	public $active = true;

	/**
	 * @see ObjectModel::$definition
	 */
	public static $definition = array(
		'table' => 'postcode',
		'primary' => 'id_postcode',
		'fields' => array(
			'id_country' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
			'id_zone' => 	array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
			'postcode' => 	array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true, 'size' => 32),
			'active' => 	array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
		),
	);

	protected $webserviceParameters = array(
		'fields' => array(
			'id_zone' => array('xlink_resource'=> 'zones'),
			'id_country' => array('xlink_resource'=> 'countries')
		),
	);

	public static function getPostcodes($id_lang = false, $active = false)
	{
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
		SELECT `id_postcode`, `id_country`, `id_zone`, `postcode`, `active`
		FROM `'._DB_PREFIX_.'postcode`
		'.($active ? 'WHERE active = 1' : '').'
		ORDER BY `postcode` ASC');
	}

	/**
	 * Get a postcode with its ID
	 *
	 * @param integer $id_postcode Postcode ID
	 * @return string Postcode
	 */
	public static function getPostcodeById($id_postcode)
	{
		if (!$id_state)
			return false;
		$cache_id = 'Postcode::getPostcodeById_'.(int)$id_postcode;
		if (!Cache::isStored($cache_id))
		{
			$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
				SELECT `postcode`
				FROM `'._DB_PREFIX_.'postcode`
				WHERE `id_postcode` = '.(int)$id_postcode
			);
			Cache::store($cache_id, $result);
		}
		return Cache::retrieve($cache_id);
	}

	/**
	 * Get a postcode id with its postcode
	 *
	 * @param string $postcode Postcode
	 * @return integer postcode id
	 */
	public static function getIdByPostcode($postcode)
	{
		if (empty($postcode))
			return false;
		$cache_id = 'Postcode::getIdByPostcode_'.pSQL($postcode);
		if (!Cache::isStored($cache_id))
		{
			$result = (int)Db::getInstance()->getValue('
				SELECT `id_postcode`
				FROM `'._DB_PREFIX_.'postcode`
				WHERE `postcode` LIKE \''.pSQL($postcode).'\'
			');
			Cache::store($cache_id, $result);
		}
		return Cache::retrieve($cache_id);
	}

	/**
	* Delete a postcode
	*
	* @return boolean
	*/
	public function delete()
	{
		// Database deletion
		$result = Db::getInstance()->delete($this->def['table'], '`'.$this->def['primary'].'` = '.(int)$this->id);
		if (!$result)
			return false;

		// Database deletion for multilingual fields related to the object
		if (!empty($this->def['multilang']))
			Db::getInstance()->delete(bqSQL($this->def['table']).'_lang', '`'.$this->def['primary'].'` = '.(int)$this->id);
		return $result;
	}

    public static function getPostcodesByIdCountry($id_country)
    {
        if (empty($id_country))
            die(Tools::displayError());

        return Db::getInstance()->executeS('
        SELECT *
        FROM `'._DB_PREFIX_.'postcode` p
        WHERE p.`id_country` = '.(int)$id_country
        );
    }

	public static function getIdZone($id_state)
	{
		if (!Validate::isUnsignedId($id_state))
			die(Tools::displayError());

		return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
			SELECT `id_zone`
			FROM `'._DB_PREFIX_.'postcode`
			WHERE `id_postcode` = '.(int)$id_postcode
		);
	}

	/**
	 * @param $ids_postcode
	 * @param $id_zone
	 * @return bool
	 */
	public function affectZoneToSelection($ids_postcode, $id_zone)
	{
		// cast every array values to int (security)
		$ids_postcode = array_map('intval', $ids_postcode);
		return Db::getInstance()->execute('
		UPDATE `'._DB_PREFIX_.'postcode` SET `id_zone` = '.(int)$id_zone.' WHERE `id_postcode` IN ('.implode(',', $ids_postcode).')
		');
	}
}


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
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

/**
 * @since 1.5.0
 */
class GenderCore extends ObjectModel
{
	public $id_gender;
	public $name;
	public $type;

	/**
	 * @see ObjectModel::$definition
	 */
	public static $definition = array(
		'table' => 'gender',
		'primary' => 'id_gender',
		'multilang' => true,
		'fields' => array(
			'type' => array('type' => self::TYPE_INT, 'required' => true),

			// Lang fields
			'name' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isString', 'required' => true, 'size' => 20),
		),
	);

	public function __construct($id = null, $id_lang = null, $id_shop = null)
	{
		parent::__construct($id, $id_lang, $id_shop);

		$this->image_dir = _PS_GENDERS_DIR_;
	}

	public static function getGenders($id_lang = null)
	{
		if (is_null($id_lang))
			$id_lang = Context::getContext()->language->id;

		$genders = new Collection('Gender', $id_lang);
		return $genders;
	}

	public function getImage($use_unknown = false)
	{
		if (!file_exists(_PS_GENDERS_DIR_.$this->id.'.jpg'))
			return ($use_unknown) ?  _PS_ADMIN_IMG_.'unknown.gif' : false;
		return _THEME_GENDERS_DIR_.$this->id.'.jpg';
	}
}
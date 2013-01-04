<?php
/*
* 2007-2013 PrestaShop
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
*  @copyright  2007-2013 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class QuickAccessCore extends ObjectModel
{
 	/** @var string Name */
	public $name;

	/** @var string Link */
	public $link;

	/** @var boolean New windows or not */
	public $new_window;

	/**
	 * @see ObjectModel::$definition
	 */
	public static $definition = array(
		'table' => 'quick_access',
		'primary' => 'id_quick_access',
		'multilang' => true,
		'fields' => array(
			'link' => 		array('type' => self::TYPE_STRING, 'validate' => 'isUrl', 'required' => true, 'size' => 128),
			'new_window' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => true),

			// Lang fields
			'name' => 		array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'required' => true, 'size' => 32),
		),
	);

	/**
	* Get all available quick_accesses
	*
	* @return array QuickAccesses
	*/
	public static function getQuickAccesses($id_lang)
	{
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
		SELECT *
		FROM `'._DB_PREFIX_.'quick_access` qa
		LEFT JOIN `'._DB_PREFIX_.'quick_access_lang` qal ON (qa.`id_quick_access` = qal.`id_quick_access` AND qal.`id_lang` = '.(int)$id_lang.')
		ORDER BY `name` ASC');
	}
}


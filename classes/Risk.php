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
*  @version  Release: $Revision: 11158 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

/**
 * @since 1.5.0
 */
class RiskCore extends ObjectModel
{
	public $id_risk;
	public $name;
	public $color;
	public $percent;

	protected $fieldsRequired = array('percent');
	protected $fieldsSize = array();
	protected $fieldsValidate = array();
	protected $fieldsRequiredLang = array('name');
	protected $fieldsSizeLang = array('name' => 20);
	protected $fieldsValidateLang = array('name' => 'isString');

	public static $definition = array(
		'table' => 'risk',
		'primary' => 'id_risk',
		'multilang' => true,
	);

	public function getFields()
	{
		$this->validateFields();
		$fields['id_risk'] = (int)$this->id_risk;
		$fields['color'] = pSQL($this->color);
		$fields['percent'] = (int)$this->percent;
		return $fields;
	}

	/**
	 * Check then return multilingual fields for database interaction
	 *
	 * @return array Multilingual fields
	 */
	public function getTranslationsFieldsChild()
	{
		$this->validateFieldsLang();
		return $this->getTranslationsFields(array(
			'name',
		));
	}

	public static function getRisks($id_lang = null)
	{
		if (is_null($id_lang))
			$id_lang = Context::getContext()->language->id;

		$risks = new Collection('Risk', $id_lang);
		return $risks;
	}
}
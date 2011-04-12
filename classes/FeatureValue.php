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

class FeatureValueCore extends ObjectModel
{
	/** @var integer Group id which attribute belongs */
	public		$id_feature;
	
	/** @var string Name */
	public 		$value;
	
	/** @var boolean Custom */
	public 		$custom = 0;
	
 	protected 	$fieldsRequired = array('id_feature');
	protected 	$fieldsValidate = array('id_feature' => 'isUnsignedId', 'custom' => 'isBool');
 	protected 	$fieldsRequiredLang = array('value');
 	protected 	$fieldsSizeLang = array('value' => 255);
 	protected 	$fieldsValidateLang = array('value' => 'isGenericName');
		
	protected 	$table = 'feature_value';
	protected 	$identifier = 'id_feature_value';
	
	protected	$webserviceParameters = array(
		'objectsNodeName' => 'product_feature_values',
		'objectNodeName' => 'product_feature_value',
		'fields' => array(
			'id_feature' => array('xlink_resource'=> 'product_features'),
		),
	);

	public function getFields()
	{
		parent::validateFields();

		$fields['id_feature'] = (int)$this->id_feature;
		$fields['custom'] = (int)$this->custom;

		return $fields;
	}
	
	/**
	* Check then return multilingual fields for database interaction
	*
	* @return array Multilingual fields
	*/
	public function getTranslationsFieldsChild()
	{
		parent::validateFieldsLang();
		return parent::getTranslationsFields(array('value'));
	}
	
	/**
	 * Get all values for a given feature
	 *
	 * @param boolean $id_feature Feature id
	 * @return array Array with feature's values
	 * @static
	 */
	static public function getFeatureValues($id_feature)
	{
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT *
		FROM `'._DB_PREFIX_.'feature_value`
		WHERE `id_feature` = '.(int)$id_feature);
	}
	
	/**
	 * Get all values for a given feature and language
	 *
	 * @param integer $id_lang Language id
	 * @param boolean $id_feature Feature id
	 * @return array Array with feature's values
	 * @static
	 */
	static public function getFeatureValuesWithLang($id_lang, $id_feature)
	{
		return Db::getInstance()->ExecuteS('
		SELECT *
		FROM `'._DB_PREFIX_.'feature_value` v
		LEFT JOIN `'._DB_PREFIX_.'feature_value_lang` vl ON (v.`id_feature_value` = vl.`id_feature_value` AND vl.`id_lang` = '.(int)$id_lang.')
		WHERE v.`id_feature` = '.(int)$id_feature.' AND (v.`custom` IS NULL OR v.`custom` = 0)
		ORDER BY vl.`value` ASC');
	}

	/**
	 * Get all language for a given value
	 *
	 * @param boolean $id_feature_value Feature value id
	 * @return array Array with value's languages
	 * @static
	 */
	static public function getFeatureValueLang($id_feature_value)
	{
		return Db::getInstance()->ExecuteS('
		SELECT *
		FROM `'._DB_PREFIX_.'feature_value_lang`
		WHERE `id_feature_value` = '.(int)$id_feature_value.'
		ORDER BY `id_lang`');
	}
	
	/**
	 * Select the good lang in tab
	 *
	 * @param array $lang Array with all language
	 * @param integer $id_lang Language id
	 * @return string String value name selected
	 * @static
	 */
	static public function selectLang($lang, $id_lang)
	{
		foreach ($lang as $tab)
			if ($tab['id_lang'] == $id_lang)
				return $tab['value'];
	}
	
	static public function addFeatureValueImport($id_feature, $name)
	{
		$rq = Db::getInstance()->ExecuteS('
		SELECT fv.`id_feature_value`
		FROM '._DB_PREFIX_.'feature_value fv
		LEFT JOIN '._DB_PREFIX_.'feature_value_lang fvl ON (fvl.`id_feature_value` = fv.`id_feature_value`)
		WHERE `value` = \''.pSQL($name).'\'
		AND fv.`id_feature` = '.(int)$id_feature.'
		GROUP BY fv.`id_feature_value` LIMIT 1');

		if (!isset($rq[0]['id_feature_value']) OR !$id_feature_value = (int)$rq[0]['id_feature_value'])
		{
			// Feature doesn't exist, create it
			$featureValue = new FeatureValue();
			
			$languages = Language::getLanguages();
			foreach ($languages AS $language)
				$featureValue->value[$language['id_lang']] = strval($name);

			$featureValue->id_feature = (int)$id_feature;
			$featureValue->custom = 1;
			$featureValue->add();

			return (int)$featureValue->id;
		}
		return (int)$id_feature_value;
	}

	public function delete()
	{
		/* Also delete related products */
		Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'feature_product` WHERE `id_feature_value` = '.(int)$this->id);
		return parent::delete();
	}
}
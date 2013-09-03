<?php
/**
 * 2007-2013 PrestaShop
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
 *  @author PrestaShop SA <contact@prestashop.com>
 *  @copyright 2007-2013 PrestaShop SA : 6 rue lacepede, 75005 PARIS
 *  @version  Release: $Revision: 16855 $
 *  @license	http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 **/

/**
 * This class works with the Twenga class to control params 
 * used for Twenga API method.
 * 
 * This class allow to  :
 * - set fields with validators, the length of value, if field is required,
 * - check each fields if they saved according to params (see above),
 * - compare an array of values to check if required fields are set.
 * - use the twenga API method and throw exceptions if error or exceptions occurred.
 * - Transform the xml response into PHP values.
 * @version 1.3
 */
abstract class TwengaFields
{
	/**
	 * This var must be filled by inherited constructor.
	 * 
	 * Each item must look like :
	 * item[(string)'name-of-the-field'] = array(#param1, #param2, #param3);
	 * #param1 : (int) length of the field 0 for no restrictions
	 * #param2 : (array) possibles validators. A validator could be a 
	 * 			 - basic php function,
	 * 			 - Validate class method,
	 * 			 - Subclass method
	 * #param3 : [optionnal](boolean) if is required or not.
	 * @var array which saved possible fields with their specification
	 * @see TwengaFields::checkFields() AND TwengaFields::checkFieldAttributs()
	 */
	protected $fields;
	
	/** 
	 * @var string Need to save the class name for used with validators
	 * @see TwengaFields::isValidate();
	 */
	protected $className;
	
	/**
	 * @var array which save each required fields
	 * @see TwengaFields::checkFields() for know how it's filled
	 */
	protected $requiredFields;
	
	/**
	 * @var boolean
	 */
	protected $noRequiredFields = false;
	
	/**
	 * @var array Each item is value for field which are defined by subclass.
	 * 		Key of the item must be the name of the field.
	 */
	protected $params;
   
	/**
	 * Constructor control each fields filled by subclass.
	 * /!\ must be used by subclass after filled TwengaFields::$fields.
	 * @see TwengaFields::checkFields()
	 * @throws TwengaFieldsException throwing by TwengaFields::checkFields()
	 */
	public function __construct()
	{
		$this->className = get_class($this);
		try {
			$this->checkFields();
		} catch (TwengaFieldsException $e) {
			throw $e;
		}
	}
	
	/**
	 * Check fields attributs.
	 * @throws TwengaFieldsException if TwengaFields::$fields was not filled.
	 * @throws TwengaFieldsException throwing by TwengaFields::checkFieldAttributs()
	 */
	private function checkFields()
	{
		if (!is_array($this->fields))
			throw new TwengaFieldsException('Some fields must be set');
		
		// if $noRequiredFields is set to false, means that we don't need to check fields.
		if (!is_array($this->requiredFields) AND $this->noRequiredFields === false)
		{
			$this->requiredFields = array();
			foreach ($this->fields as $name=>$attributs)
			{
				try {
					$this->checkFieldAttributs($name, $attributs);
				} catch (TwengaFieldsException $e) {
					throw $e;
				}
			}
			if (empty($this->requiredFields))
				$this->noRequiredFields = true;
		}
	}
	
	/**
	 * Check attributs validity and in case of field is required,
	 * save it in (array)TwengaFields::$requiredFields.
	 * @param string $name of the field
	 * @param array field attributs
	 * @throws TwengaFieldsException if an attribut of a field is not set correctly 
	 * 		   (much more debugging rather than production).
	 */
	private function checkFieldAttributs ($name, $field_attributs)
	{
		// attribut at key 0 is the length of the required value
		if (!Validate::isInt($field_attributs[0]))
			throw new TwengaFieldsException(Tools::displayError('To add a field, you have to set a maximum length for checking the value. Error occurred for the value').' : '.$name);
		
		// attribut at key 1 is an array list of the validator
		if (!is_array($field_attributs[1]))
			throw new TwengaFieldsException(Tools::displayError('To add a field, you have to set validators for checking value. Error occurred for the value').' : '.$name);
		
		// Check if validators setted are valid. 
		foreach ($field_attributs[1] as $validator)
		{
			if (!function_exists($validator) && !method_exists('Validate', $validator) && !method_exists($this->className, $validator))
				throw new TwengaFieldsException (Tools::displayError('The Validator').' '.$validator.' '.Tools::displayError('does\'nt exist'));
		}
			
		// attribut at key 2 means that this fields is required or not
		if (isset($field_attributs[2]) AND Validate::isBool($field_attributs[2]))
			$this->requiredFields[$name] = false;
	}
	
	/**
	 * Get a field by his name and return his atribut to check his validity.
	 * @param string $name
	 * @throws TwengaFieldsException if a TwengaFields::$field is an array,
	 * @throws TwengaFieldsException if the choosen field is not set.
	 * @return array field attributs
	 */
	public function getField($name)
	{
		if (!is_array($this->fields))
			throw new TwengaFieldsException(Tools::displayError('To get a field you have to saved some fields !'));
		if (!key_exists($name, $this->fields))
			throw new TwengaFieldsException(Tools::displayError('The field').' <b>'.$name.'</b> '.Tools::displayError('doesn\'t exist.'));
		return $this->fields[$name];
	}
	
	/**
	 * Check the value of one field by his name
	 * @param string $key is field name to check. 
	 * @param string $value is field value.
	 * @return string empty if it's ok, otherwise a string of errors.
	 */
	private function isValidate($key, $value)
	{
		$fieldValidate = $this->getField($key);
		$str_return = '';
		// check the length
		if (strlen((string)$value) > $fieldValidate[0] AND  $fieldValidate[0] !== 0)
			return Tools::displayError('Wrong length of the value. Must be set between 1 and ').$fieldValidate[0].'<br />'."\n";
		
		// check each validators.
		foreach ($fieldValidate[1] as $validator)
		{
			$user_function = function_exists($validator) ? $validator : '';
			$user_function = method_exists('Validate', $validator) ? array('Validate', $validator) : $user_function;
			$user_function = method_exists($this->className, $validator) ? array($this->className, $validator) : $user_function;
			try {
				$bool = call_user_func($user_function, $value);
				if (!$bool)
					$str_return .= Tools::displayError('Value don\'t respect the validator : ').'<b>'.$validator.'</b><br />'."\n";;
			} catch (TwengaFieldsException $e) {
				$str_return .= $e->getMessage();
			}
		}
		return $str_return;
	}
	
	/**
	 * Compare an array of values to each required fields.
	 * 
	 * The method doesn't work if they are no required fields.
	 * 
	 * No return value for this method, if compared array key
	 * and TwengaFields::$requiredFields key match, 
	 * the TwengaFields::$requiredFields value become true.
	 * 
	 * In this way if false value is found in TwengaFields::$requiredFields array,
	 * it means that compared array is not filled enough.
	 * @see TwengaFields::requiredFieldsAreSet() is the one using TwengaFields::compareFields().
	 */
	private function compareFields()
	{
		if (is_array($this->requiredFields) AND !empty($this->requiredFields))
		{
			$arr_compare = array_intersect_key($this->requiredFields, $this->params);
			foreach ($this->requiredFields as $key=>&$value)
				$value = key_exists($key, $arr_compare);
		}
	}
	
	/**
	 * Check if each required fields are set
	 * see TwengaFields::compareFields().
	 * Else the method throw an error message.
	 * @return boolean if it's ok
	 * @throws TwengaFieldsException in case of a required field missing.
	 */
	private function requiredFieldsAreSet()
	{
		if (is_array($this->requiredFields) AND !empty($this->requiredFields))
		{
			$fields_not_set = array_keys($this->requiredFields, false);
			if (!empty($fields_not_set))
				throw new TwengaFieldsException (Tools::displayError('Some fields must be set').' : <b>'.implode(', ', $fields_not_set).'</b>');
		}
		return true;
	}
	
	/**
	 * Set values params
	 * @param array $params
	 * @return TwengaFields current instance for chainability
	 */
	public function setParams($params)
	{
		if (empty($params))
			throw new TwengaFieldsException(Tools::displayError('Params must be filled'));
		$this->params = $params;
		return $this;
	}
	
	/**
	 * Check params validity (length, validators, is required) 
	 * for each TwengaFields::$params value.
	 * @see TwengaFields::compareFields() and TwengaFields::requiredFieldsAreSet()
	 * 		to know required fields checking method.
	 * @see TwengaFields::isValidate() to know the checking behaviour (length and Validators).
	 * @throws TwengaFieldsException if TwengaFields::$params not set before.
	 * @throws TwengaFieldsException with list of not validated values.
	 * 		   Using TwengaFields::isValidate() method to work.
	 * @throws TwengaFieldsException occurred while compared
	 * 		   TwengaFields::$params and TwengaFields::$fields 
	 * *	   to check if required fields are filled.
	 */
	public function checkParams()
	{
		if (!is_array($this->params) || empty($this->params))
			throw new TwengaFieldsException(Tools::displayError('Params must be setted before check it'));
		try {
			$this->compareFields();
			$this->requiredFieldsAreSet();
		} catch (TwengaFieldsException $e) {
			throw $e;
		}
		$str_message = '';
		foreach ($this->params as $key=>$value)
		{
			$validate = $this->isValidate($key, $value);
			if ($validate !== '')
				$str_message .= Tools::displayError('The field').' <b>'.$key.'</b> '.Tools::displayError('is a wrong value, see details :').'<br />'."\n<em>".$validate.'</em>';
		}
		if ($str_message !== '')
			throw new TwengaFieldsException($str_message);
	}
	
	/**
	 * @return array TwengaFields::$params
	 */
	public function getParams()
	{
		return $this->params;
	}
	/**
	 * Get translation for a given module text
	 *
	 * @param string $string String to translate
	 * @return string Translation
	 */
	public function l($string, $specific = false)
	{
		global $_MODULES, $_MODULE, $cookie;

		$id_lang = (!isset($cookie) || !is_object($cookie)) ? (int)(Configuration::get('PS_LANG_DEFAULT')) : (int)($cookie->id_lang);
		$file = _PS_MODULE_DIR_.$this->name.'/'.Language::getIsoById($id_lang).'.php';
		if (file_exists($file) && include_once($file))
			$_MODULES = !empty($_MODULES) ? array_merge($_MODULES, $_MODULE) : $_MODULE;

		if (!is_array($_MODULES))
			return (str_replace('"', '&quot;', $string));

		$source = Tools::strtolower($specific ? $specific : get_class($this));
		$string2 = str_replace('\'', '\\\'', $string);
		$currentKey = '<{'.$this->name.'}'._THEME_NAME_.'>'.$source.'_'.md5($string2);
		$defaultKey = '<{'.$this->name.'}prestashop>'.$source.'_'.md5($string2);

		if (key_exists($currentKey, $_MODULES))
			$ret = stripslashes($_MODULES[$currentKey]);
		elseif (key_exists($defaultKey, $_MODULES))
			$ret = stripslashes($_MODULES[$defaultKey]);
		else
			$ret = $string;
		return str_replace('"', '&quot;', $ret);
	}
}
class TwengaFieldsException extends Exception {}

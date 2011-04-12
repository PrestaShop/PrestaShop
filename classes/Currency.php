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

class CurrencyCore extends ObjectModel
{
	public 		$id;

	/** @var string Name */
	public 		$name;

	/** @var string Iso code */
	public 		$iso_code;

	/** @var string Iso code numeric */
	public 		$iso_code_num;

	/** @var string Symbol for short display */
	public 		$sign;

	/** @var int bool used for displaying blank between sign and price */
	public		$blank;

	/** @var string Conversion rate from euros */
	public 		$conversion_rate;

	/** @var boolean True if currency has been deleted (staying in database as deleted) */
	public 		$deleted = 0;

	/** @var int ID used for displaying prices */
	public		$format;

	/** @var int bool Display decimals on prices */
	public		$decimals;

	/** @var int bool active */
	public		$active;

 	protected 	$fieldsRequired = array('name', 'iso_code', 'sign', 'conversion_rate', 'format', 'decimals');
 	protected 	$fieldsSize = array('name' => 32, 'iso_code' => 3, 'iso_code_num' => 3, 'sign' => 8);
 	protected 	$fieldsValidate = array('name' => 'isGenericName', 'iso_code' => 'isLanguageIsoCode', 'iso_code_num' => 'isNumericIsoCode', 'blank' => 'isInt', 'sign' => 'isGenericName',
		'format' => 'isUnsignedId', 'decimals' => 'isBool', 'conversion_rate' => 'isFloat', 'deleted' => 'isBool', 'active' => 'isBool');

	protected 	$table = 'currency';
	protected 	$identifier = 'id_currency';

	/** @var Currency Current currency */
	static protected	$current = NULL;
	/** @var array Currency cache */
	static protected	$currencies = array();

	protected	$webserviceParameters = array(
		'fields' => array(
		),
	);


	/**
	 * Overriding check if currency with the same iso code already exists.
	 * If it's true, currency is doesn't added.
	 *
	 * @see ObjectModelCore::add()
	 */
	public function add($autodate = true, $nullValues = false)
	{
		return Currency::exists($this->iso_code) ? false : parent::add();
	}

	/**
	 * Check if a curency already exists.
	 *
	 * @param int|string $iso_code int for iso code number string for iso code
	 * @return boolean
	 */
	public static function exists ($iso_code)
	{
		if(is_int($iso_code))
			$id_currency_exists = Currency::getIdByIsoCodeNum($iso_code);
		else
			$id_currency_exists = Currency::getIdByIsoCode($iso_code);

		if ($id_currency_exists){
			return true;
		} else {
			return false;
		}
	}
	public function getFields()
	{
		parent::validateFields();

		$fields['name'] = pSQL($this->name);
		$fields['iso_code'] = pSQL($this->iso_code);
		$fields['iso_code_num'] = pSQL($this->iso_code_num);
		$fields['sign'] = pSQL($this->sign);
		$fields['format'] = (int)($this->format);
		$fields['decimals'] = (int)($this->decimals);
		$fields['blank'] = (int)($this->blank);
		$fields['conversion_rate'] = (float)($this->conversion_rate);
		$fields['deleted'] = (int)($this->deleted);
		$fields['active'] = (int)($this->active);

		return $fields;
	}

	public function deleteSelection($selection)
	{
		if (!is_array($selection) OR !Validate::isTableOrIdentifier($this->identifier) OR !Validate::isTableOrIdentifier($this->table))
			die(Tools::displayError());
		$result = true;
		foreach ($selection AS $id)
		{
			$obj = new Currency((int)($id));
			$res[$id] = $obj->delete();
		}
		foreach ($res AS $value)
			if (!$value)
				return false;
		return true;
	}

	public function delete()
	{
		if ($this->id == Configuration::get('PS_CURRENCY_DEFAULT'))
		{
			$result = Db::getInstance()->getRow('SELECT `id_currency` FROM '._DB_PREFIX_.'currency WHERE `id_currency` != '.(int)($this->id).' AND `deleted` = 0');
			if (!$result['id_currency'])
				return false;
			Configuration::updateValue('PS_CURRENCY_DEFAULT', $result['id_currency']);
		}
		$this->deleted = 1;
		return $this->update();
	}

	/**
	  * Return formated sign
	  *
	  * @param string $side left or right
	  * @return string formated sign
	  */
	public function getSign($side=NULL)
	{
		if (!$side)
			return $this->sign;
		$formated_strings = array(
			'left' => $this->sign.' ',
			'right' => ' '.$this->sign
		);
		$formats = array(
			1 => array('left' => &$formated_strings['left'], 'right' => ''),
			2 => array('left' => '', 'right' => &$formated_strings['right']),
			3 => array('left' => &$formated_strings['left'], 'right' => ''),
			4 => array('left' => '', 'right' => &$formated_strings['right']),
		);
		return ($formats[$this->format][$side]);
	}

	/**
	  * Return available currencies
	  *
	  * @return array Currencies
	  */
	static public function getCurrencies($object = false, $active = 1)
	{
		$tab = Db::getInstance()->ExecuteS('
		SELECT *
		FROM `'._DB_PREFIX_.'currency`
		WHERE `deleted` = 0
		'.($active == 1 ? 'AND `active` = 1' : '').'
		ORDER BY `name` ASC');
		if ($object)
			foreach ($tab as $key => $currency)
				$tab[$key] = Currency::getCurrencyInstance($currency['id_currency']);
		return $tab;
	}

	static public function getPaymentCurrenciesSpecial($id_module)
	{
		return Db::getInstance()->getRow('
		SELECT mc.*
		FROM `'._DB_PREFIX_.'module_currency` mc
		WHERE mc.`id_module` = '.(int)($id_module));
	}

	static public function getPaymentCurrencies($id_module)
	{
		return Db::getInstance()->ExecuteS('
		SELECT c.*
		FROM `'._DB_PREFIX_.'module_currency` mc
		LEFT JOIN `'._DB_PREFIX_.'currency` c ON c.`id_currency` = mc.`id_currency`
		WHERE c.`deleted` = 0
		AND mc.`id_module` = '.(int)($id_module).'
		ORDER BY c.`name` ASC');
	}

	static public function checkPaymentCurrencies($id_module)
	{
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT mc.*
		FROM `'._DB_PREFIX_.'module_currency` mc
		WHERE mc.`id_module` = '.(int)($id_module));
	}

	static public function getCurrency($id_currency)
	{
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
		SELECT *
		FROM `'._DB_PREFIX_.'currency`
		WHERE `deleted` = 0
		AND `id_currency` = '.(int)($id_currency));
	}

	static public function getIdByIsoCode($iso_code)
	{
		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
		SELECT `id_currency`
		FROM `'._DB_PREFIX_.'currency`
		WHERE `deleted` = 0
		AND `iso_code` = \''.pSQL($iso_code).'\'');
		return $result['id_currency'];
	}
	static public function getIdByIsoCodeNum($iso_code)
	{
		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
		SELECT `id_currency`
		FROM `'._DB_PREFIX_.'currency`
		WHERE `deleted` = 0
		AND `iso_code_num` = \''.pSQL($iso_code).'\'');
		return (int)$result['id_currency'];
	}

	/**
	* Refresh the currency conversion rate
	* The XML file define conversion rate for each from a default currency ($isoCodeSource).
	*
	* @param $data XML content which contains all the conversion rates
	* @param $isoCodeSource The default currency used in the XML file
	* @param $defaultCurrency The default currency object
	*/
	public function refreshCurrency($data, $isoCodeSource, $defaultCurrency)
	{
		// fetch the conversion rate of the default currency
		$conversion_rate = 1;
		if ($defaultCurrency->iso_code != $isoCodeSource)
		{
			foreach ($data->currency AS $currency)
				if ($currency['iso_code'] == $defaultCurrency->iso_code)
				{
					$conversion_rate = round((float)$currency['rate'], 6);
					break;
				}
		}

		if ($defaultCurrency->iso_code == $this->iso_code)
			$this->conversion_rate = 1;
		else
		{
			if ($this->iso_code == $isoCodeSource)
				$rate = 1;
			else
			{
				foreach ($data->currency AS $obj)
					if ($this->iso_code == strval($obj['iso_code']))
					{
						$rate = (float) $obj['rate'];
						break;
					}
			}

			$this->conversion_rate = round($rate /  $conversion_rate, 6);
		}
		$this->update();
	}

	/**
 	* @deprecated
	**/
	static public function refreshCurrenciesGetDefault($data, $isoCodeSource, $idCurrency)
	{
		Tools::displayAsDeprecated();

		$defaultCurrency = new Currency($idCurrency);

		/* Change defaultCurrency rate if not as currency of feed source */
		if ($defaultCurrency->iso_code != $isoCodeSource)
			foreach ($data->currency AS $obj)
				if ($defaultCurrency->iso_code == strval($obj['iso_code']))
					$defaultCurrency->conversion_rate = round((float)($obj['rate']), 6);

		return $defaultCurrency;
	}

	public static function getDefaultCurrency()
	{
		$id_currency = (int)Configuration::get('PS_CURRENCY_DEFAULT');
		if ($id_currency == 0)
			return false;

		return new Currency($id_currency);
	}

	static public function refreshCurrencies()
	{
		// Parse
		if (!$feed = @simplexml_load_file('http://www.prestashop.com/xml/currencies.xml'))
			return Tools::displayError('Cannot parse feed.');

		// Default feed currency (EUR)
		$isoCodeSource = strval($feed->source['iso_code']);

		if (!$default_currency = self::getDefaultCurrency())
			return Tools::displayError('No default currency');

		$currencies = self::getCurrencies(true);
		foreach ($currencies as $currency)
			$currency->refreshCurrency($feed->list, $isoCodeSource, $default_currency);

	}

	static public function getCurrent()
	{
		global $cookie;

		if (!self::$current)
		{
			if (isset($cookie->id_currency) AND $cookie->id_currency)
				self::$current = new Currency((int)($cookie->id_currency));
			else
				self::$current = new Currency((int)(Configuration::get('PS_CURRENCY_DEFAULT')));
		}
		return self::$current;
	}

	static public function getCurrencyInstance($id)
	{
		if (!array_key_exists($id, self::$currencies))
			self::$currencies[(int)($id)] = new Currency((int)($id));
		return self::$currencies[(int)($id)];
	}
}


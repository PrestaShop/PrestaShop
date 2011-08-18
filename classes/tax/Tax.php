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
*  @version  Release: $Revision: 6844 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class TaxCore extends ObjectModel
{
 	/** @var string Name */
	public 		$name;

	/** @var float Rate (%) */
	public 		$rate;

	/** @var bool active state */
	public 		$active;

 	protected 	$fieldsRequired = array('rate');
 	protected 	$fieldsValidate = array('rate' => 'isFloat');
 	protected 	$fieldsRequiredLang = array('name');
 	protected 	$fieldsSizeLang = array('name' => 32);
 	protected 	$fieldsValidateLang = array('name' => 'isGenericName');

	protected 	$table = 'tax';
	protected 	$identifier = 'id_tax';

	protected static $_product_country_tax = array();
	protected static $_product_tax_via_rules = array();

	public function getFields()
	{
		$this->validateFields();
		$fields['rate'] = (float)($this->rate);
		$fields['active'] = (int)($this->active);
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
		return $this->getTranslationsFields(array('name'));
	}

	public function delete()
	{
		/* Clean associations */
		TaxRule::deleteTaxRuleByIdTax((int)$this->id);
		return parent::delete();
	}

	public function toggleStatus()
	{
	    if (parent::toggleStatus())
            return $this->_onStatusChange();

        return false;
	}

	public function update($nullValues = false)
	{
	    if (parent::update($nullValues))
            return $this->_onStatusChange();

        return false;
	}

	protected function _onStatusChange()
	{
        if (!$this->active)
            return TaxRule::deleteTaxRuleByIdTax($this->id);

        return true;
	}

	/**
	* Get all available taxes
	*
	* @return array Taxes
	*/
	public static function getTaxes($id_lang = false, $active = 1)
	{
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT t.id_tax, t.rate'.((int)($id_lang) ? ', tl.name, tl.id_lang ' : '').'
		FROM `'._DB_PREFIX_.'tax` t
		'.((int)($id_lang) ? 'LEFT JOIN `'._DB_PREFIX_.'tax_lang` tl ON (t.`id_tax` = tl.`id_tax` AND tl.`id_lang` = '.(int)($id_lang).')'
		.($active == 1 ? 'WHERE t.`active` = 1' : '').'
		ORDER BY `name` ASC' : ''));
	}

	public static function excludeTaxeOption()
	{
		return !Configuration::get('PS_TAX');
	}

	/**
	* Return the tax id associated to the specified name
	*
	* @param string $tax_name
	* @param boolean $active (true by default)
	*/
	public static function getTaxIdByName($tax_name, $active = 1)
	{
		$tax = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
			SELECT t.`id_tax`
			FROM `'._DB_PREFIX_.'tax` t
			LEFT JOIN `'._DB_PREFIX_.'tax_lang` tl ON (tl.id_tax = t.id_tax)
			WHERE tl.`name` = \''.pSQL($tax_name).'\' '.
			($active == 1 ? ' AND t.`active` = 1' : ''));

		return $tax ? (int)($tax['id_tax']) : false;
	}

	/**
	 * Returns the product tax
	 *
	 * @param integer $id_product
	 * @param integer $id_country
	 * @return Tax
	 *
	 * @deprecated use $product->getTaxesRate() instead
	 */
	public static function getProductTaxRate($id_product, $id_address = NULL)
	{
		$address = Tax::initializeAddress($id_address);
		$id_tax_rules = (int)Product::getIdTaxRulesGroupByIdProduct($id_product);

		$tax_manager = TaxManagerFactory::getManager($address, $id_tax_rules);
		$tax_calculator = $tax_manager->getTaxCalculator();

		return $tax_calculator->getTaxesRate();
	}

	/**
	* Returns the ecotax tax rate
	*
	* @param id_address
	* @return float $tax_rate
	*/
	public static function getProductEcotaxRate($id_address = NULL)
	{
		$address = Tax::initializeAddress($id_address);

		$tax_manager = TaxManagerFactory::getManager($address, (int)Configuration::get('PS_ECOTAX_TAX_RULES_GROUP_ID'));
		$tax_calculator = $tax_manager->getTaxCalculator();

		return $tax_calculator->getTaxesRate();
	}

	/**
	* Returns the carrier tax rate
	*
	* @param id_address
	* @return float $tax_rate
	*/
	public static function getCarrierTaxRate($id_carrier, $id_address = NULL)
	{
		$address = Tax::initializeAddress($id_address);
		$id_tax_rules = (int)Carrier::getIdTaxRulesGroupByIdCarrier((int)$id_carrier);

		$tax_manager = TaxManagerFactory::getManager($address, $id_tax_rules);
		$tax_calculator = $tax_manager->getTaxCalculator();

		return $tax_calculator->getTaxesRate();
	}

	/**
	* Initiliaze an address corresponding to the id address if any or to the
	* default shop configuration
	*
	* @param int $id_address
	* @return Address address
	*/
	public static function initializeAddress($id_address = NULL)
	{
		// set the default address
		$address = new Address();
		$address->id_country = (int)Context::getContext()->country->id;
		$address->id_state = 0;
		$address->postcode = 0;

		// if an id_address has been specified retrieve the address
		if ($id_address)
		{
			$address = new Address((int)$id_address);

			if (!Validate::isLoadedObject())
				throw new Exception('Invalid address');
		}

		return $address;
	}

	/**
	 * Return the product tax rate using the tax rules system
	 *
	 * @param integer $id_product
	 * @param integer $id_country
	 * @return Tax
	 *
	 * @deprecated since 1.5
	 */
	public static function getProductTaxRateViaRules($id_product, $id_country, $id_state, $zipcode)
	{
		Tools::displayAsDeprecated();

		if (!isset(self::$_product_tax_via_rules[$id_product.'-'.$id_country.'-'.$id_state.'-'.$zipcode]))
		{
		    $tax_rate = TaxRulesGroup::getTaxesRate((int)Product::getIdTaxRulesGroupByIdProduct((int)$id_product), (int)$id_country, (int)$id_state, $zipcode);
		    self::$_product_tax_via_rules[$id_product.'-'.$id_country.'-'.$zipcode] =  $tax_rate;
		}

		return self::$_product_tax_via_rules[$id_product.'-'.$id_country.'-'.$zipcode];
	}
}


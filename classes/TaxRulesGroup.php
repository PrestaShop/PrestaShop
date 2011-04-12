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


class TaxRulesGroupCore extends ObjectModel
{
    public $name;

    /** @var bool active state */
    public 		$active;

 	protected 	$fieldsRequired = array('name');
 	protected 	$fieldsSize = array('name' => 64);
 	protected 	$fieldsValidate = array('name' => 'isGenericName');

	protected 	$table = 'tax_rules_group';
	protected 	$identifier = 'id_tax_rules_group';

    protected static $_taxes = array();

	public function getFields()
	{
		parent::validateFields();
		$fields['name'] = ($this->name);
		$fields['active'] = (int)($this->active);
		return $fields;
	}

	public static function getTaxRulesGroups($only_active = true)
	{
	    return Db::getInstance()->ExecuteS('
	    SELECT *
	    FROM `'._DB_PREFIX_.'tax_rules_group` g'
	    .($only_active ? ' WHERE g.`active` = 1' : '')
	    );
	}

	public static function getTaxRulesGroupsForOptions()
	{
		$tax_rules[] = array('id_tax_rules_group' => 0, 'name' => Tools::displayError('No tax'));
		return array_merge($tax_rules, TaxRulesGroup::getTaxRulesGroups());
	}

	public static function getTaxes($id_tax_rules_group, $id_country, $id_state, $id_county)
	{
	    if (empty($id_tax_rules_group) OR empty($id_country))
	        return array(new Tax()); // No Tax

       if (isset(self::$_taxes[$id_tax_rules_group.'-'.$id_country.'-'.$id_state.'-'.$id_county]))
            return self::$_taxes[$id_tax_rules_group.'-'.$id_country.'-'.$id_state.'-'.$id_county];

	    $rows = Db::getInstance()->ExecuteS('
	    SELECT *
	    FROM `'._DB_PREFIX_.'tax_rule`
	    WHERE `id_country` = '.(int)$id_country.'
	    AND `id_tax_rules_group` = '.(int)$id_tax_rules_group.'
	    AND `id_state` IN (0, '.(int)$id_state.')
	    AND `id_county` IN (0, '.(int)$id_county.')
	    ORDER BY `id_county` DESC, `id_state` DESC'
	    );


	    $taxes = array();
	    foreach ($rows AS $row)
	    {
          if ($row['id_county'] != 0)
          {
          	switch($row['county_behavior'])
          	{
          		case County::USE_BOTH_TAX:
                 $taxes[] = new Tax($row['id_tax']);
          		break;

          		case County::USE_COUNTY_TAX:
                  $taxes = array(new Tax($row['id_tax']));
          		break 2;

          		case County::USE_STATE_TAX: // do nothing
          		break;
          	}
          }
	       else if ($row['id_state'] != 0)
	       {
	            switch($row['state_behavior'])
	            {
	                case PS_STATE_TAX: // use only product tax
                        $taxes[] = new Tax($row['id_tax']);
    	                break 2; // switch + foreach

    	            case PS_BOTH_TAX:
    	                $taxes[] = new Tax($row['id_tax']);
    	                break;

	                case PS_PRODUCT_TAX: // do nothing use country tax
	                    break;
	            }
	       }
	       else
	            $taxes[] = new Tax((int)$row['id_tax']);
	    }

	    self::$_taxes[$id_tax_rules_group.'-'.$id_country.'-'.$id_state.'-'.$id_county] = $taxes;
       return $taxes;
	}

	public static function getAssociatedTaxRatesByIdCountry($id_country)
	{
	    $rows = Db::getInstance()->ExecuteS('
	    SELECT rg.`id_tax_rules_group`, t.`rate`
	    FROM `'._DB_PREFIX_.'tax_rules_group` rg
   	    LEFT JOIN `'._DB_PREFIX_.'tax_rule` tr ON (tr.`id_tax_rules_group` = rg.`id_tax_rules_group`)
	    LEFT JOIN `'._DB_PREFIX_.'tax` t ON (t.`id_tax` = tr.`id_tax`)
	    WHERE tr.`id_country` = '.(int)$id_country.'
	    AND tr.`id_state` = 0
	    AND tr.`id_county` = 0'
	    );

	    $res = array();
	    foreach ($rows AS $row)
	        $res[$row['id_tax_rules_group']] = $row['rate'];

	    return $res;
	}

	public static function getTaxesRate($id_tax_rules_group, $id_country, $id_state, $id_county)
	{
	    $rate = 0;
	    foreach (TaxRulesGroup::getTaxes($id_tax_rules_group, $id_country, $id_state, $id_county) AS $tax)
	        $rate += (float)$tax->rate;

	    return $rate;
	}

	public static function getIdByName($name)
	{
	    return Db::getInstance()->getValue(
	    'SELECT `id_tax_rules_group`
	    FROM `'._DB_PREFIX_.'tax_rules_group` rg
	    WHERE `name` = \''.pSQL($name).'\''
	    );
	}
}
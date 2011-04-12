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

class TaxRuleCore extends ObjectModel
{
    public $id_tax_rules_group;
    public $id_country;
    public $id_state;
    public $id_county;
    public $id_tax;
    public $state_behavior;
    public $county_behavior;

 	protected 	$fieldsRequired = array('id_tax_rules_group', 'id_country', 'id_tax');
 	protected 	$fieldsValidate = array('id_tax_rules_group' => 'isUnsignedId', 'id_country' => 'isUnsignedId', 'id_state' => 'isUnsignedId', 'id_county' => 'isUnsignedId', 'id_tax' => 'isUnsignedId', 'state_behavior' => 'isUnsignedInt', 'county_behavior' => 'isUnsignedInt');

	protected 	$table = 'tax_rule';
	protected 	$identifier = 'id_tax_rule';

	public function getFields()
	{
	  parent::validateFields();
      $fields['id_tax_rules_group'] = (int)($this->id_tax_rules_group);
      $fields['id_country'] = (int)$this->id_country;
      $fields['id_state'] = (int)$this->id_state;
      $fields['id_county'] = (int)$this->id_county;
      $fields['state_behavior'] = (int)$this->state_behavior;
      $fields['county_behavior'] = (int)$this->county_behavior;
	  $fields['id_tax'] = (int)($this->id_tax);

	  return $fields;
	}

    public static function deleteByGroupId($id_group)
    {
        if (empty($id_group))
            die(Tools::displayError());

        return Db::getInstance()->Execute('
        DELETE FROM `'._DB_PREFIX_.'tax_rule`
        WHERE `id_tax_rules_group` = '.(int)$id_group
        );
    }

    public static function getTaxRulesByGroupId($id_group)
    {
        if (empty($id_group))
            die(Tools::displayError());

        $results = Db::getInstance()->ExecuteS('
        SELECT *
        FROM `'._DB_PREFIX_.'tax_rule`
        WHERE `id_tax_rules_group` = '.(int)$id_group
        );

        $res = array();
        foreach ($results AS $row)
            $res[$row['id_country']][$row['id_state']][$row['id_county']] = array('id_tax' => $row['id_tax'], 'state_behavior' => $row['state_behavior'], 'county_behavior' => $row['county_behavior']);

        return $res;
    }

    public static function deleteTaxRuleByIdTax($id_tax)
    {
        return Db::getInstance()->Execute('
        DELETE FROM `'._DB_PREFIX_.'tax_rule`
        WHERE `id_tax` = '.(int)$id_tax
        );
    }


	public static function deleteTaxRuleByIdCounty($id_county)
	{
		return Db::getInstance()->Execute('
		DELETE FROM `'._DB_PREFIX_.'tax_rule`
		WHERE `id_county` = '.(int)$id_county
		);
	}

    public static function isTaxInUse($id_tax)
    {
        return Db::getInstance()->getValue('
        SELECT COUNT(*) FROM `'._DB_PREFIX_.'tax_rule` WHERE `id_tax` = '.(int)$id_tax
        );
    }
}


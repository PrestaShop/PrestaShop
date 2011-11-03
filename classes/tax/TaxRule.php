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

class TaxRuleCore extends ObjectModel
{
	 public $id_tax_rules_group;
	 public $id_country;
	 public $id_state;
	 public $zipcode_from;
	 public $zipcode_to;
	 public $id_tax;
	 public $behavior;
	 public $description;

	 protected $fieldsRequired = array('id_tax_rules_group', 'id_country', 'id_tax');
	 protected $fieldsValidate = array('id_tax_rules_group' => 'isUnsignedId',
	 											   'id_country' => 'isUnsignedId',
	 											   'id_state' => 'isUnsignedId',
	 											   'zipcode_from' => 'isPostCode',
	 											   'zipcode_to' => 'isPostCode',
	 											   'id_tax' => 'isUnsignedId',
	 											   'behavior' => 'isUnsignedInt',
	 											   'description' => 'isString');

	protected $table = 'tax_rule';
	protected $identifier = 'id_tax_rule';

	public function getFields()
	{
		$this->validateFields();
		$fields['id_tax_rules_group'] = (int)$this->id_tax_rules_group;
		$fields['id_country'] = (int)$this->id_country;
		$fields['id_state'] = (int)$this->id_state;
		$fields['zipcode_from'] = (int)$this->zipcode_from;
		$fields['zipcode_to'] = (int)$this->zipcode_to;
		$fields['behavior'] = (int)$this->behavior;
		$fields['id_tax'] = (int)$this->id_tax;
		$fields['description'] = $this->description;

		return $fields;
	}

    public static function deleteByGroupId($id_group)
    {
        if (empty($id_group))
            die(Tools::displayError());

        return Db::getInstance()->execute('
        DELETE FROM `'._DB_PREFIX_.'tax_rule`
        WHERE `id_tax_rules_group` = '.(int)$id_group
        );
    }

    public static function retrieveById($id_tax_rule)
    {
    	return Db::getInstance()->getRow('
    	SELECT * FROM `'._DB_PREFIX_.'tax_rule`
    	WHERE `id_tax_rule` = '.(int)$id_tax_rule);
    }

	public static function getTaxRulesByGroupId($id_lang, $id_group)
	{
		return Db::getInstance()->executeS('
		SELECT g.`id_tax_rule`,
				 c.`name` AS country_name,
				 s.`name` AS state_name,
				 t.rate,
				 g.`zipcode_from`, g.`zipcode_to`,
				 g.`description`,
				 g.`behavior`
		FROM `'._DB_PREFIX_.'tax_rule` g
		LEFT JOIN `'._DB_PREFIX_.'country_lang` c ON (g.`id_country` = c.`id_country` AND id_lang = '.(int)$id_lang.')
		LEFT JOIN `'._DB_PREFIX_.'state` s ON (g.`id_state` = s.`id_state`)
		LEFT JOIN `'._DB_PREFIX_.'tax` t ON (g.`id_tax` = t.`id_tax`)
		WHERE `id_tax_rules_group` = '.(int)$id_group.'
		ORDER BY `country_name` ASC, `state_name` ASC, `zipcode_from` ASC, `zipcode_to` ASC'
		);
	}

    public static function deleteTaxRuleByIdTax($id_tax)
    {
        return Db::getInstance()->execute('
        DELETE FROM `'._DB_PREFIX_.'tax_rule`
        WHERE `id_tax` = '.(int)$id_tax
        );
    }


	/**
	* @deprecated since 1.5
	*/
	public static function deleteTaxRuleByIdCounty($id_county)
	{
		Tools::displayAsDeprecated();
		return true;
	}

	/**
	* @param int $id_tax
	* @return boolean
	*/
    public static function isTaxInUse($id_tax)
    {
        return Db::getInstance()->getValue('
        SELECT COUNT(*) FROM `'._DB_PREFIX_.'tax_rule` WHERE `id_tax` = '.(int)$id_tax
        );
    }


	 /**
	  * @param string $zipcode a range of zipcode (eg: 75000 / 75000-75015)
	  * @return array an array containing two zipcode ordered by zipcode
	  */
	public function breakDownZipCode($zip_codes)
	{
		$zip_codes = preg_split('/-/', $zip_codes);

		if (count($zip_codes) == 2)
		{
			$from = $zip_codes[0];
			$to   = $zip_codes[1];
			if ($zip_codes[0] > $zip_codes[1])
			{
				$from = $zip_codes[1];
				$to   = $zip_codes[0];
			}
			else if ($zip_codes[0] == $zip_codes[1])
			{
				$from = $zip_codes[0];
				$to   = 0;
			}
		}
		else if (count($zip_codes) == 1)
		{
			$from = $zip_codes[0];
			$to = 0;
		}

		return array($from, $to);
	}

	/**
	* Replace a tax_rule id by an other one in the tax_rule table
	*
	* @param int $old_id
	* @param int $new_id
	*/
	public static function swapTaxId($old_id, $new_id)
	{
		return Db::getInstance()->execute('
		UPDATE `'._DB_PREFIX_.'tax_rule`
		SET `id_tax` = '.(int)$new_id.'
		WHERE `id_tax` = '.(int)$old_id
		);
	}
}


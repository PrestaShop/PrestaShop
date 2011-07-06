<?php

function generate_tax_rules()
{
    $taxes = Tax::getTaxes(Configuration::get('PS_LANG_DEFAULT'), true);
    $countries = Country::getCountries(Configuration::get('PS_LANG_DEFAULT'));

    foreach ($taxes AS $tax)
    {
        $insert = '';
        $id_tax = $tax['id_tax'];

        $group = new TaxRulesGroup();
        $group->active = 1;
        $group->name = 'Rule '.$tax['rate'].'%';
        $group->save();
        $id_tax_rules_group = $group->id;


        $countries = Db::getInstance()->ExecuteS('
        SELECT * FROM `'._DB_PREFIX_.'country` c
        LEFT JOIN `'._DB_PREFIX_.'zone` z ON (c.`id_zone` = z.`id_zone`)
        LEFT JOIN `'._DB_PREFIX_.'tax_zone` tz ON (tz.`id_zone` = z.`id_zone`)
        WHERE `id_tax` = '.(int)$id_tax
        );
        if ($countries)
        {
            foreach ($countries AS $country)
            {
					 $res = Db::getInstance()->Execute('
					 INSERT INTO `'._DB_PREFIX_.'tax_rule` (`id_tax_rules_group`, `id_country`, `id_state`, `state_behavior`, `id_tax`)
					 VALUES (
					 '.(int)$group->id.',
					 '.(int)$country['id_country'].',
					 0,
					 0,
					 '.(int)$id_tax.
					 ')');

            }
        }

        $states = Db::getInstance()->ExecuteS('
        SELECT * FROM `'._DB_PREFIX_.'states s
        LEFT JOIN `'._DB_PREFIX_.'tax_state ts ON (ts.`id_state` = s.`id_state`)
        WHERE `id_tax` = '.(int)$id_tax
        );

        if ($states)
        {
            foreach ($states AS $state)
            {
                if (!in_array($state['tax_behavior'], array(PS_PRODUCT_TAX, PS_STATE_TAX, PS_BOTH_TAX)))
                    $tax_behavior = PS_PRODUCT_TAX;
                else
                    $tax_behavior = $state['tax_behavior'];

					 $res = Db::getInstance()->Execute('
					 INSERT INTO `'._DB_PREFIX_.'tax_rule` (`id_tax_rules_group`, `id_country`, `id_state`, `state_behavior`, `id_tax`)
					 VALUES (
					 '.(int)$group->id.',
					 '.(int)$state['id_country'].',
					 '.(int)$state['id_state'].',
					 '.(int)$tax_behavior.',
					 '.(int)$id_tax.
					 ')');
            }
        }

        Db::getInstance()->Execute('
        UPDATE `'._DB_PREFIX_.'product`
        SET `id_tax_rules_group` = '.(int)$group->id.'
        WHERE `id_tax` = '.(int)$id_tax
        );

        Db::getInstance()->Execute('
        UPDATE `'._DB_PREFIX_.'carrier`
        SET `id_tax_rules_group` = '.(int)$group->id.'
        WHERE `id_tax` = '.(int)$id_tax
        );


        if (Configuration::get('SOCOLISSIMO_OVERCOST_TAX') == $id_tax)
            Configuration::updateValue('SOCOLISSIMO_OVERCOST_TAX', $group->id);
    }
}


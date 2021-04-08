<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */
if (!defined('_PS_MYSQL_REAL_ESCAPE_STRING_')) {
    define('_PS_MYSQL_REAL_ESCAPE_STRING_', function_exists('mysql_real_escape_string'));
}

function latin1_database_to_utf8()
{
    global $requests, $warningExist;

    $tables = [
        ['name' => 'address', 'id' => 'id_address', 'fields' => ['alias', 'company', 'name', 'surname', 'address1', 'address2', 'postcode', 'city', 'other', 'phone', 'phone_mobile']],
        ['name' => 'alias', 'id' => 'id_alias', 'fields' => ['alias', 'search']],
        ['name' => 'attribute_group_lang', 'id' => 'id_attribute_group', 'lang' => true, 'fields' => ['name', 'public_name']],
        ['name' => 'attribute_lang', 'id' => 'id_attribute', 'lang' => true, 'fields' => ['name']],
        ['name' => 'carrier', 'id' => 'id_carrier', 'fields' => ['name', 'url']],
        ['name' => 'carrier_lang', 'id' => 'id_carrier', 'lang' => true, 'fields' => ['delay']],
        ['name' => 'cart', 'id' => 'id_cart', 'fields' => ['gift_message']],
        ['name' => 'category_lang', 'id' => 'id_category', 'lang' => true, 'fields' => ['name', 'description', 'link_rewrite', 'meta_title', 'meta_keywords', 'meta_description']],
        ['name' => 'configuration', 'id' => 'id_configuration', 'fields' => ['name', 'value']],
        ['name' => 'configuration_lang', 'id' => 'id_configuration', 'lang' => true, 'fields' => ['value']],
        ['name' => 'contact', 'id' => 'id_contact', 'fields' => ['email']],
        ['name' => 'contact_lang', 'id' => 'id_contact', 'lang' => true, 'fields' => ['name', 'description']],
        ['name' => 'country', 'id' => 'id_country', 'fields' => ['iso_code']],
        ['name' => 'country_lang', 'id' => 'id_country', 'lang' => true, 'fields' => ['name']],
        ['name' => 'currency', 'id' => 'id_currency', 'fields' => ['name', 'iso_code', 'sign']],
        ['name' => 'customer', 'id' => 'id_customer', 'fields' => ['email', 'passwd', 'name', 'surname']],
        ['name' => 'discount', 'id' => 'id_discount', 'fields' => ['name']],
        ['name' => 'discount_lang', 'id' => 'id_discount', 'lang' => true, 'fields' => ['description']],
        ['name' => 'discount_type_lang', 'id' => 'id_discount_type', 'lang' => true, 'fields' => ['name']],
        ['name' => 'employee', 'id' => 'id_employee', 'fields' => ['name', 'surname', 'email', 'passwd']],
        ['name' => 'feature_lang', 'id' => 'id_feature', 'lang' => true, 'fields' => ['name']],
        ['name' => 'feature_value_lang', 'id' => 'id_feature_value', 'lang' => true, 'fields' => ['value']],
        ['name' => 'hook', 'id' => 'id_hook', 'fields' => ['name', 'title', 'description']],
        ['name' => 'hook_module_exceptions', 'id' => 'id_hook_module_exceptions', 'fields' => ['file_name']],
        ['name' => 'image_lang', 'id' => 'id_image', 'lang' => true, 'fields' => ['legend']],
        ['name' => 'image_type', 'id' => 'id_image_type', 'fields' => ['name']],
        ['name' => 'lang', 'id' => 'id_lang', 'fields' => ['name', 'iso_code']],
        ['name' => 'manufacturer', 'id' => 'id_manufacturer', 'fields' => ['name']],
        ['name' => 'message', 'id' => 'id_message', 'fields' => ['message']],
        ['name' => 'module', 'id' => 'id_module', 'fields' => ['name']],
        ['name' => 'orders', 'id' => 'id_order', 'fields' => ['payment', 'module', 'gift_message', 'shipping_number']],
        ['name' => 'order_detail', 'id' => 'id_order_detail', 'fields' => ['product_name', 'product_reference', 'tax_name', 'download_hash']],
        ['name' => 'order_discount', 'id' => 'id_order_discount', 'fields' => ['name']],
        ['name' => 'order_state', 'id' => 'id_order_state', 'fields' => ['color']],
        ['name' => 'order_state_lang', 'id' => 'id_order_state', 'lang' => true, 'fields' => ['name', 'template']],
        ['name' => 'product', 'id' => 'id_product', 'fields' => ['ean13', 'reference']],
        ['name' => 'product_attribute', 'id' => 'id_product_attribute', 'fields' => ['reference', 'ean13']],
        ['name' => 'product_download', 'id' => 'id_product_download', 'fields' => ['display_filename', 'physically_filename']],
        ['name' => 'product_lang', 'id' => 'id_product', 'lang' => true, 'fields' => ['description', 'description_short', 'link_rewrite', 'meta_description', 'meta_keywords', 'meta_title', 'name', 'availability']],
        ['name' => 'profile_lang', 'id' => 'id_profile', 'lang' => true, 'fields' => ['name']],
        ['name' => 'quick_access', 'id' => 'id_quick_access', 'fields' => ['link']],
        ['name' => 'quick_access_lang', 'id' => 'id_quick_access', 'lang' => true, 'fields' => ['name']],
        ['name' => 'supplier', 'id' => 'id_supplier', 'fields' => ['name']],
        ['name' => 'tab', 'id' => 'id_tab', 'fields' => ['class_name']],
        ['name' => 'tab_lang', 'id' => 'id_tab', 'lang' => true, 'fields' => ['name']],
        ['name' => 'tag', 'id' => 'id_tag', 'fields' => ['name']],
        ['name' => 'tax_lang', 'id' => 'id_tax', 'lang' => true, 'fields' => ['name']],
        ['name' => 'zone', 'id' => 'id_zone', 'fields' => ['name']],
    ];

    foreach ($tables as $table) {
        /* Latin1 datas' selection */
        if (!Db::getInstance()->execute('SET NAMES latin1')) {
            echo 'Cannot change the sql encoding to latin1!';
        }
        $query = 'SELECT `' . $table['id'] . '`';
        foreach ($table['fields'] as $field) {
            $query .= ', `' . $field . '`';
        }
        if (isset($table['lang']) && $table['lang']) {
            $query .= ', `id_lang`';
        }
        $query .= ' FROM `' . _DB_PREFIX_ . $table['name'] . '`';
        $latin1Datas = Db::getInstance()->executeS($query);
        if ($latin1Datas === false) {
            $warningExist = true;
            $requests .= '
				<request result="fail">
					<sqlQuery><![CDATA[' . htmlentities($query) . ']]></sqlQuery>
					<sqlMsgError><![CDATA[' . htmlentities(Db::getInstance()->getMsgError()) . ']]></sqlMsgError>
					<sqlNumberError><![CDATA[' . htmlentities(Db::getInstance()->getNumberError()) . ']]></sqlNumberError>
				</request>' . "\n";
        }

        if (Db::getInstance()->numRows()) {
            /* Utf-8 datas' restitution */
            if (!Db::getInstance()->execute('SET NAMES utf8')) {
                echo 'Cannot change the sql encoding to utf8!';
            }
            foreach ($latin1Datas as $latin1Data) {
                $query = 'UPDATE `' . _DB_PREFIX_ . $table['name'] . '` SET';
                foreach ($table['fields'] as $field) {
                    $query .= ' `' . $field . '` = \'' . pSQL($latin1Data[$field]) . '\',';
                }
                $query = rtrim($query, ',');
                $query .= ' WHERE `' . $table['id'] . '` = ' . (int) ($latin1Data[$table['id']]);
                if (isset($table['lang']) && $table['lang']) {
                    $query .= ' AND `id_lang` = ' . (int) ($latin1Data['id_lang']);
                }
                if (!Db::getInstance()->execute($query)) {
                    $warningExist = true;
                    $requests .= '
						<request result="fail">
							<sqlQuery><![CDATA[' . htmlentities($query) . ']]></sqlQuery>
							<sqlMsgError><![CDATA[' . htmlentities(Db::getInstance()->getMsgError()) . ']]></sqlMsgError>
							<sqlNumberError><![CDATA[' . htmlentities(Db::getInstance()->getNumberError()) . ']]></sqlNumberError>
						</request>' . "\n";
                }
            }
        }
    }
}

<?php
/**
 * 2007-2016 PrestaShop
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
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2016 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

function p15010_drop_column_id_address_if_exists()
{
    $res = true;
    $exists = Db::getInstance()->executeS('SHOW TABLES LIKE "'._DB_PREFIX_.'supplier"');
    if (count($exists)) {
        $fields = Db::getInstance()->executeS('SHOW FIELDS FROM `'._DB_PREFIX_.'supplier`');
        foreach ($fields as $k => $field) {
            $fields[$k] = $field['Field'];
        }

        if (in_array('id_address', $fields)) {
            $res &= Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.'supplier`
				DROP `id_address`');
        }
    }
    return $res;
}

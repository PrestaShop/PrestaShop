<?php
/**
 * 2007-2017 PrestaShop
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
 * @copyright 2007-2017 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

function ps1700_stores()
{
    $stores = Db::getInstance()->executeS('
        SELECT `id_store`, `hours`
        FROM `'._DB_PREFIX_.'store`
    ');

    $result = false;
    foreach ($stores as $store) {
        $hours = Tools::unSerialize($store['hours']);
        if (is_array($hours)) {
            foreach ($hours as $key => $h) {
                $hours[$key] = [$h];
            }
        } else {
            $hours = array();
        }
        $hours = json_encode($hours);

        $result &= Db::getInstance()->execute('
            UPDATE `'._DB_PREFIX_.'store`
            SET `hours` = '.$hours.'
            WHERE `id_store` = '.$store['id_store']
        );
    }

    return $result;
}

<?php
/**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

/**
 * Select all current payment modules for the carrier restriction
 */
function select_current_payment_modules()
{
    $shops = Db::getInstance()->executeS('
			SELECT `id_shop`
			FROM `'._DB_PREFIX_.'shop`'
    );
    $carriers = Db::getInstance()->executeS('
			SELECT DISTINCT `id_reference`
			FROM `'._DB_PREFIX_.'carrier`
			WHERE `active` = 1
			AND `deleted` = 0'
    );
    $modules = Db::getInstance()->executeS('
			SELECT m.`id_module`
			FROM `'._DB_PREFIX_.'module` m
			LEFT JOIN `'._DB_PREFIX_.'hook_module` hm ON hm.`id_module` = m.`id_module`
			LEFT JOIN `'._DB_PREFIX_.'hook` h ON hm.`id_hook` = h.`id_hook`
			WHERE h.`name` = \'displayPayment\' OR h.`name` = \'displayPaymentEU\' OR h.`name` = \'paymentOptions\''
    );

    foreach ($shops as $shop) {
        foreach ($carriers as $carrier) {
            foreach ($modules as $module) {
                Db::getInstance()->insert(
                    'module_carrier',
                    array(
                        'id_reference' => (int)$carrier['id_reference'],
                        'id_module' => (int)$module['id_module'],
                        'id_shop' => (int)$shop['id_shop']
                    ),
                    false,
                    false,
                    Db::INSERT_IGNORE
                );
            }
        }
    }
}

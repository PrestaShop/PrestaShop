<?php
/**
 * 2007-2019 PrestaShop and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

function ps1605_change_index()
{
    $index = Db::getInstance()->executeS('SHOW INDEX FROM `'._DB_PREFIX_.'order_detail_tax` WHERE Key_name = "id_tax"');
    if (is_array($index) && count($index)) {
        Db::getInstance()->execute('ALTER IGNORE TABLE `'._DB_PREFIX_.'order_detail_tax` DROP INDEX `id_tax`');
    }
    Db::getInstance()->execute('ALTER IGNORE TABLE `'._DB_PREFIX_.'order_detail_tax` ADD INDEX (`id_tax`)');
}

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

/**
 * Preset enabled new column in tabs to true for all (except for disabled modules)
 */
function ps_1770_preset_tab_enabled() {
    //First set all tabs enabled
    $result = Db::getInstance()->execute(
        'UPDATE `'._DB_PREFIX_.'tab` SET `enabled` = 1'
    );

    //Then search for inactive modules and disable their tabs
    $inactiveModules = Db::getInstance()->executeS(
        'SELECT `name` FROM `'._DB_PREFIX_.'module` WHERE `active` != 1'
    );
    $moduleNames = [];
    foreach ($inactiveModules as $inactiveModule) {
        $moduleNames[] = $inactiveModule['name'];
    }
    if (count($moduleNames) > 0) {
        $result &= Db::getInstance()->execute(
            'UPDATE `'._DB_PREFIX_.'tab` SET `enabled` = 0 WHERE `module` IN (' . implode(',', $moduleNames) . ')'
        );
    }

    return $result;
}

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

function ps_1770_update_charset()
{
    $adminFilterTableExists = $adminFilterFilterIdExists = $moduleHistoryTableExists = $translationTableExists = false;

    try {
        $adminFilterTableExists = (bool) Db::getInstance()->executeS(
            'SELECT count(*) FROM ' . _DB_PREFIX_ . 'admin_filter'
        );
        if ($adminFilterTableExists) {
            $adminFilterFilterIdExists = (bool) Db::getInstance()->executeS(
                'SELECT count(filter_id) FROM ' . _DB_PREFIX_ . 'admin_filter'
            );
        }
    } catch (Exception $e) {

    }

    try {
        $moduleHistoryTableExists = (bool) Db::getInstance()->executeS(
            'SELECT count(*) FROM ' . _DB_PREFIX_ . 'module_history'
        );
    } catch (Exception $e) {

    }

    try {
        $translationTableExists = (bool) Db::getInstance()->executeS(
            'SELECT count(*) FROM ' . _DB_PREFIX_ . 'translation'
        );

    } catch (Exception $e) {

    }

    $result = true;

    if ($adminFilterTableExists) {
        if ($adminFilterFilterIdExists) {
            $result &= Db::getInstance()->execute(
                'UPDATE ' . _DB_PREFIX_ . '`admin_filter` SET `filter_id` = SUBSTRING(`filter_id`, 1, 191)'
            );
            $result &= Db::getInstance()->execute(
                'ALTER TABLE ' . _DB_PREFIX_ . '`admin_filter` CHANGE `filter_id` `filter_id` VARCHAR(191) NOT NULL'
            );
        }
        $result &= Db::getInstance()->execute(
            'ALTER TABLE `PREFIX_admin_filter` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci'
        );
    }

    if ($moduleHistoryTableExists) {
        $result &= Db::getInstance()->execute(
            'ALTER TABLE `PREFIX_module_history` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci'
        );
    }


    if ($translationTableExists) {
        $result &= Db::getInstance()->execute(
            'ALTER TABLE `PREFIX_translation` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci'
        );
    }

    return (bool) $result;
}

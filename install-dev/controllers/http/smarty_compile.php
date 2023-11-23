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

if (Tools::getValue('bo')) {
    if (!is_dir(_PS_ROOT_DIR_ . '/admin/')) {
        exit;
    }
    define('_PS_ADMIN_DIR_', _PS_ROOT_DIR_ . '/admin/');
    $directory = _PS_ADMIN_DIR_ . 'themes/default/';
} else {
    $directory = _PS_THEME_DIR_ . 'templates/';
}

require_once _PS_ROOT_DIR_ . '/config/smarty.config.inc.php';
/** @phpstan-ignore-next-line */
$smarty->setTemplateDir($directory);
ob_start();
/** @phpstan-ignore-next-line */
$smarty->compileAllTemplates('.tpl', false);
if (ob_get_level() && ob_get_length() > 0) {
    ob_end_clean();
}

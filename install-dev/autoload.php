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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */
spl_autoload_register(function ($className) {
    if (0 === strpos($className, 'InstallControllerConsole')) {
        $fileName = strtolower(str_replace('InstallControllerConsole', '', $className));
        require_once sprintf('%s/controllers/console/%s.php', __DIR__, $fileName);
    } else if (0 === strpos($className, 'InstallControllerHttp')) {
        $fileName = strtolower(str_replace('InstallControllerHttp', '', $className));
        require_once sprintf('%s/controllers/http/%s.php', __DIR__, $fileName);
    } else if (file_exists(__DIR__ . '/classes/' . $className . '.php')) {
        require_once sprintf('%s/classes/%s.php', __DIR__, $className);
    }
});

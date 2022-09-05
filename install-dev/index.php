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

require_once 'install_version.php';

if (
    !defined('PHP_VERSION_ID') // PHP_VERSION_ID is available since 5.2.7
    || PHP_VERSION_ID < _PS_INSTALL_MINIMUM_PHP_VERSION_ID_
    || PHP_VERSION_ID > _PS_INSTALL_MAXIMUM_PHP_VERSION_ID_ 
    || !extension_loaded('SimpleXML') /** @phpstan-ignore-line */
    || !extension_loaded('zip') /** @phpstan-ignore-line */
    || !is_writable(
        __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'var'.DIRECTORY_SEPARATOR.'cache'
    )
) {
    require_once dirname(__FILE__).'/missing_requirement.php';
    exit();
}
/** @phpstan-ignore-next-line */
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'init.php';
require_once(__DIR__).DIRECTORY_SEPARATOR.'autoload.php';

try {
    if (_PS_MODE_DEV_) {
        Symfony\Component\Debug\Debug::enable();
    }

    require_once _PS_INSTALL_PATH_.'classes'.DIRECTORY_SEPARATOR.'controllerHttp.php';
    require_once _PS_INSTALL_PATH_.'classes'.DIRECTORY_SEPARATOR.'HttpConfigureInterface.php';
    InstallControllerHttp::execute();
} catch (PrestashopInstallerException $e) {
    $e->displayMessage();
}

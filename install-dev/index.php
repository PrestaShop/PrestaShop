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


if (!extension_loaded('SimpleXML') || !extension_loaded('zip') || PHP_VERSION_ID < 50400 || !is_writable(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'var'.DIRECTORY_SEPARATOR.'cache')) {
    require_once dirname(__FILE__).'/missing_requirement.php';
    exit();
}

require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'init.php');
require_once(__DIR__).DIRECTORY_SEPARATOR.'autoload.php';

try {
    require_once(_PS_INSTALL_PATH_.'classes'.DIRECTORY_SEPARATOR.'controllerHttp.php');
    require_once(_PS_INSTALL_PATH_.'classes'.DIRECTORY_SEPARATOR.'HttpConfigureInterface.php');
    InstallControllerHttp::execute();
} catch (PrestashopInstallerException $e) {
    $e->displayMessage();
}

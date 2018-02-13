<?php
/**
 * 2007-2018 PrestaShop
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
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

use PrestaShop\PrestaShop\Core\Cldr\Composer\Hook;

/* Redefine REQUEST_URI */
$_SERVER['REQUEST_URI'] = '/install/index_cli.php';
require_once dirname(__FILE__).'/init.php';
require_once(__DIR__).DIRECTORY_SEPARATOR.'autoload.php';
require_once _PS_INSTALL_PATH_.'classes/datas.php';
ini_set('memory_limit', '256M');
try {
    require_once _PS_INSTALL_PATH_.'classes/controllerConsole.php';
    InstallControllerConsole::execute($argc, $argv);
    echo '-- Installation successful! --'."\n";
    Hook::init(null);
    exit(0);
} catch (PrestashopInstallerException $e) {
    $e->displayMessage();
} catch (Exception $e) {
    echo $e->getMessage();
}
exit(1);

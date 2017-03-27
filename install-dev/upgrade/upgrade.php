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


use PrestaShopBundle\Install\Upgrade;

$filePrefix = 'PREFIX_';
$engineType = 'ENGINE_TYPE';
define('PS_IN_UPGRADE', 1);

// remove old unsupported classes
@unlink(__DIR__.'/../../classes/db/MySQL.php');

// Set execution time and time_limit to infinite if available
@set_time_limit(0);
@ini_set('max_execution_time', '0');

// setting the memory limit to 128M only if current is lower
$memory_limit = ini_get('memory_limit');
if (substr($memory_limit, -1) != 'G'
    and ((substr($memory_limit, -1) == 'M' and substr($memory_limit, 0, -1) < 128)
    or is_numeric($memory_limit) and (intval($memory_limit) < 131072) and $memory_limit > 0)
) {
    @ini_set('memory_limit', '128M');
}

// redefine REQUEST_URI if empty (on some webservers...)
if (!isset($_SERVER['REQUEST_URI']) || $_SERVER['REQUEST_URI'] == '') {
    if (!isset($_SERVER['SCRIPT_NAME']) && isset($_SERVER['SCRIPT_FILENAME'])) {
        $_SERVER['SCRIPT_NAME'] = $_SERVER['SCRIPT_FILENAME'];
    } else {
        $_SERVER['REQUEST_URI'] = $_SERVER['SCRIPT_NAME'];
    }
}

if ($tmp = strpos($_SERVER['REQUEST_URI'], '?')) {
    $_SERVER['REQUEST_URI'] = substr($_SERVER['REQUEST_URI'], 0, $tmp);
}
$_SERVER['REQUEST_URI'] = str_replace('//', '/', $_SERVER['REQUEST_URI']);

if (isset($_GET['adminDir']) && $_GET['adminDir'] && !defined('_PS_ADMIN_DIR_')) {
    define('_PS_ADMIN_DIR_', base64_decode($_GET['adminDir']));
}

require_once(dirname(__FILE__).'/../init.php');
Upgrade::migrateSettingsFile();
require_once(_PS_CONFIG_DIR_.'bootstrap.php');

$logDir = _PS_ROOT_DIR_.'/'.(_PS_MODE_DEV_ ? 'dev' : 'prod').'/log/';
@mkdir($logDir, 0777, true);

$upgrade = new Upgrade($logDir, dirname(dirname(__FILE__)).'/');
if (isset($_GET['autoupgrade']) && $_GET['autoupgrade'] == 1) {
    $upgrade->setInAutoUpgrade(true);
}

if (isset($_GET['deactivateCustomModule']) && $_GET['deactivateCustomModule'] == '1') {
    $upgrade->setDisableCustomModules(true);
}

if (isset($_GET['updateDefaultTheme']) && $_GET['updateDefaultTheme']
    && 'classic' === _THEME_NAME_) {
    $upgrade->setUpdateDefaultTheme(true);
}

if (isset($_GET['adminDir']) && $_GET['adminDir']) {
    $upgrade->setAdminDir(base64_decode($_GET['adminDir']));
}

if (isset($_GET['idEmployee'])) {
    $upgrade->setIdEmployee($_GET['idEmployee']);
}

if (isset($_GET['changeToDefaultTheme']) && $_GET['changeToDefaultTheme'] == 1) {
    $upgrade->setChangeToDefaultTheme(true);
}

if (function_exists('date_default_timezone_set')) {
    date_default_timezone_set('Europe/Paris');
}

if (isset($_GET['action']) && method_exists($upgrade, 'do'.$_GET['action'])) {
    $action = 'do'.$_GET['action'];
    $upgrade->$action();
} else {
    $upgrade->run();
}

$result = '<?xml version="1.0" encoding="UTF-8"?>';
if (empty($upgrade->hasFailure())) {
    if (!isset($_GET['action']) || 'UpgradeComplete' === $_GET['action']) {
        Configuration::updateValue('PS_HIDE_OPTIMIZATION_TIPS', 0);
        Configuration::updateValue('PS_NEED_REBUILD_INDEX', 1);
        Configuration::updateValue('PS_VERSION_DB', _PS_INSTALL_VERSION_);
    }

    $result .= '<action result="ok" id="">'."\n";
    foreach ($upgrade->getInfoList() as $info) {
        $result .= $info."\n";
    }

    foreach ($upgrade->getWarningList() as $warning) {
        $result .= $warning."\n";
    }
} else {
    foreach ($upgrade->getFailureList() as $failure) {
        $result .= $failure."\n";
    }
}

if ($upgrade->getInAutoUpgrade()) {
    header('Content-Type: application/json');
    echo json_encode(array(
        'nextQuickInfo' => $upgrade->getNextQuickInfo(),
        'nextErrors' => $upgrade->getNextErrors(),
        'next' => $upgrade->getNext(),
        'nextDesc' => $upgrade->getNextDesc(),
        'warningExists' => $upgrade->hasWarning()
    ));
} else {
    header('Content-Type: text/xml');
    echo $result;
}

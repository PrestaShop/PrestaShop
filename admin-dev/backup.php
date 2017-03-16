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

if (!defined('_PS_ADMIN_DIR_')) {
    define('_PS_ADMIN_DIR_', getcwd());
}
include(_PS_ADMIN_DIR_.'/../config/config.inc.php');

if (!Context::getContext()->employee->isLoggedBack()) {
    Tools::redirectAdmin(Context::getContext()->link->getAdminLink('AdminLogin'));
}

$tabAccess = Profile::getProfileAccess(
    Context::getContext()->employee->id_profile,
    Tab::getIdFromClassName('AdminBackup')
);

if ($tabAccess['view'] !== '1') {
    die(Context::getContext()->getTranslator()->trans(
        'You do not have permission to view this.',
        array(),
        'Admin.Advparameters.Notification'
    ));
}

$backupdir = realpath(PrestaShopBackup::getBackupPath());

if ($backupdir === false) {
    die(Context::getContext()->getTranslator()->trans(
        'There is no "/backup" directory.',
        array(),
        'Admin.Advparameters.Notification'
    ));
}

if (!$backupfile = Tools::getValue('filename')) {
    die(Context::getContext()->getTranslator()->trans(
        'No file has been specified.',
        array(),
        'Admin.Advparameters.Notification'
    ));
}

// Check the realpath so we can validate the backup file is under the backup directory
$backupfile = realpath($backupdir.DIRECTORY_SEPARATOR.$backupfile);

if ($backupfile === false || strncmp($backupdir, $backupfile, strlen($backupdir)) != 0) {
    die(Tools::dieOrLog('The backup file does not exist.'));
}

if (substr($backupfile, -4) == '.bz2') {
    $contentType = 'application/x-bzip2';
} elseif (substr($backupfile, -3) == '.gz') {
    $contentType = 'application/x-gzip';
} else {
    $contentType = 'text/x-sql';
}
$fp = @fopen($backupfile, 'r');

if ($fp === false) {
    die(Context::getContext()->getTranslator()->trans(
            'Unable to open backup file(s).',
            array(),
            'Admin.Advparameters.Notification'
        ).' "'.addslashes($backupfile).'"'
    );
}

// Add the correct headers, this forces the file is saved
header('Content-Type: '.$contentType);
header('Content-Disposition: attachment; filename="'.Tools::getValue('filename'). '"');

if (ob_get_level() && ob_get_length() > 0) {
    ob_clean();
}
$ret = @fpassthru($fp);

fclose($fp);

if ($ret === false) {
    die(Context::getContext()->getTranslator()->trans(
            'Unable to display backup file(s).',
            array(),
            'Admin.Advparameters.Notification'
        ).' "'.addslashes($backupfile).'"'
    );
}

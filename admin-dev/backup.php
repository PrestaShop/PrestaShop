<?php
/*
* 2007-2011 PrestaShop 
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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2011 PrestaShop SA
*  @version  Release: $Revision: 1.4 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

define('PS_ADMIN_DIR', getcwd());

include(PS_ADMIN_DIR.'/../config/config.inc.php');

/* Header can't be included, so cookie must be created here */
$cookie = new Cookie('psAdmin');
if (!$cookie->id_employee)
	Tools::redirectAdmin('login.php');

$tabAccess = Profile::getProfileAccess($cookie->profile, Tab::getIdFromClassName('AdminBackup'));

if ($tabAccess['view'] !== '1')
	die (Tools::displayError('You do not have permission to view here'));

$backupdir = realpath( PS_ADMIN_DIR . '/backups/');

if ($backupdir === false)
	die (Tools::displayError('Backups directory does not exist.'));
	
if (!$backupfile = Tools::getValue('filename'))
	die (Tools::displayError('No file specified'));

// Check the realpath so we can validate the backup file is under the backup directory
$backupfile = realpath($backupdir.'/'.$backupfile);

if ($backupfile === false OR strncmp($backupdir, $backupfile, strlen($backupdir)) != 0 )
	die (Tools::displayError());

if (substr($backupfile, -4) == '.bz2')
    $contentType = 'application/x-bzip2';
else if (substr($backupfile, -3) == '.gz')
    $contentType = 'application/x-gzip';
else
    $contentType = 'text/x-sql';
$fp = @fopen($backupfile, 'r'); 

if ($fp === false)
	die (Tools::displayError('Unable to open backup file').' "'.addslashes($backupfile).'"');

// Add the correct headers, this forces the file is saved
header('Content-Type: '.$contentType);
header('Content-Disposition: attachment; filename="'.Tools::getValue('filename'). '"');

$ret = @fpassthru($fp);

fclose($fp);

if ($ret === false)
	die (Tools::displayError('Unable to display backup file').' "'.addslashes($backupfile).'"');



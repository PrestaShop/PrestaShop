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
*  @version  Release: $Revision: 7310 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

define('PS_ADMIN_DIR', getcwd());

include(PS_ADMIN_DIR.'/../config/config.inc.php');
include(PS_ADMIN_DIR.'/functions.php');

$file = 'request_sql_'.Tools::getValue('id_request_sql').'.csv';
if ($csv = fopen(PS_ADMIN_DIR.'/export/'.$file, 'w'))
{
	$sql = RequestSql::getRequestSqlById(Tools::getValue('id_request_sql'));

	if ($sql)
	{
		$results = Db::getInstance()->ExecuteS($sql[0]['sql']);
		foreach (array_keys($results[0]) as $key)
		{
			$tab_key[] = $key;
			fputs($csv, $key.';');
		}
		foreach ($results as $result)
		{
			fputs($csv, "\n");
			foreach ($tab_key as $name)
				fputs($csv, $result[$name].';');
		}
		if (file_exists(PS_ADMIN_DIR.'/export/'.$file))
		{
			$filesize = filesize(PS_ADMIN_DIR.'/export/'.$file);
			$upload_max_filesize = return_bytes(ini_get('upload_max_filesize'));
			if ($filesize < $upload_max_filesize)
			{
				header("Content-type: text/csv");
				header("Cache-Control: no-store, no-cache");
				header("Content-Disposition: attachment; filename=\"$file\"");
				header("Content-Length: ".$filesize);
				readfile(PS_ADMIN_DIR.'/export/'.$file);
				die();
			}
			else
			{
				header('Location: '.$_SERVER['HTTP_REFERER'].'&maxsize=1');
				die();
			}
		}
	}
	else
	{
		header('Location: '.$_SERVER['HTTP_REFERER']);
		die();
	}
}
else
{
	header('Location: '.$_SERVER['HTTP_REFERER']);
	die();
}

function return_bytes($val)
{
    $val = trim($val);
    $last = strtolower($val[strlen($val)-1]);
    switch ($last)
	{
        // Le modifieur 'G' est disponible depuis PHP 5.1.0
        case 'g':
            $val *= 1024;
        case 'm':
            $val *= 1024;
        case 'k':
            $val *= 1024;
    }

    return $val;
}
<?php
	/**
	 * create a folder
	 * @author Logan Cai (cailongqun [at] yahoo [dot] com [dot] cn)
	 * @link www.phpletter.com
	 * @since 22/May/2007
	 *
	 */
	if (!defined('_PS_ADMIN_DIR_'))
		define('_PS_ADMIN_DIR_', getcwd());
	require_once('../../config/config.inc.php');
	require_once('../init.php');
	require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . "inc" . DIRECTORY_SEPARATOR . "config.php");
	@ob_start();
	$safe_post = array();
	foreach ($_POST AS $key => $value)
		$safe_post[Tools::safeOutput($key)] = Tools::safeOutput($value);
	displayArray($safe_post);
	writeInfo(@ob_get_clean());
	echo "{";
	$error = "";
	$info = "";
/*	$_POST['new_folder'] = substr(md5(time()), 1, 5);
	$_POST['currentFolderPath'] = "../../uploaded/";*/
	if(CONFIG_SYS_VIEW_ONLY || !CONFIG_OPTIONS_NEWFOLDER)
	{
		$error = SYS_DISABLED;
	}
	elseif(empty($_POST['new_folder']))
	{
		$error  =  ERR_FOLDER_NAME_EMPTY;
	}elseif(!preg_match("/^[a-zA-Z0-9_\- ]+$/", $_POST['new_folder']))
	{
		$error  =  ERR_FOLDER_FORMAT;
	}else if(empty($_POST['currentFolderPath']) || !isUnderRoot($_POST['currentFolderPath']))
	{
		$error = ERR_FOLDER_PATH_NOT_ALLOWED;
	}
	elseif(file_exists(addTrailingSlash($_POST['currentFolderPath']) . $_POST['new_folder']))
	{
		$error = ERR_FOLDER_EXISTS;
	}else
	{
	include_once(CLASS_FILE);
		$file = new file();
		if($file->mkdir(addTrailingSlash($_POST['currentFolderPath']) . $_POST['new_folder'], 0775))
		{
					include_once(CLASS_MANAGER);
					$manager = new manager(addTrailingSlash($_POST['currentFolderPath']) . $_POST['new_folder'], false);
					$pathInfo = $manager->getFolderInfo(addTrailingSlash($_POST['currentFolderPath']) . $_POST['new_folder']);
					foreach($pathInfo as $k=>$v)
					{
						switch ($k)
						{


							case "ctime";
							case "mtime":
							case "atime":
								$v = date(DATE_TIME_FORMAT, $v);
								break;
							case 'name':
								$info .= sprintf(", %s:'%s'", 'short_name', shortenFileName($v));
								break;
							case 'cssClass':
								$v = 'folderEmpty';
								break;
						}
						$info .= sprintf(", %s:'%s'", $k, $v);
					}
		}else
		{
			$error = ERR_FOLDER_CREATION_FAILED;
		}
		//$error = "For security reason, folder creation function has been disabled.";
	}
	echo "error:'" . $error . "'";
	echo $info;
	echo "}";
?>

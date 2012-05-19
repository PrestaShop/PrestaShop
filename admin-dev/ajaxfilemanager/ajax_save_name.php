<?php
	if (!defined('_PS_ADMIN_DIR_'))
		define('_PS_ADMIN_DIR_', getcwd());
	require_once('../../config/config.inc.php');
	require_once('../init.php');
	/**
	 * ajax save name
	 * @author Logan Cai (cailongqun [at] yahoo [dot] com [dot] cn)
	 * @link www.phpletter.com
	 * @since 22/May/2007
	 *
	 */
	require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . "inc" . DIRECTORY_SEPARATOR . "config.php");
	$error = '';
	$fileInfo = array();
	if(CONFIG_SYS_VIEW_ONLY || !CONFIG_OPTIONS_RENAME)
	{
		$error = SYS_DISABLED;
	}
	elseif(empty($_POST['name']))
	{
		$error = ERR_RENAME_EMPTY;
	}elseif(!preg_match("/^[a-zA-Z0-9 _\-.]+$/", $_POST['name']))
	{
		$error = ERR_RENAME_FORMAT;
	}elseif(empty($_POST['original_path']) || !file_exists($_POST['original_path']))
	{
		$error = ERR_RENAME_FILE_NOT_EXISTS;
	}elseif(substr(slashToBackslash(removeTrailingSlash($_POST['original_path'])), strrpos(slashToBackslash(removeTrailingSlash($_POST['original_path'])), "/") + 1) == $_POST['name'])
	{
		$error = ERR_NO_CHANGES_MADE;
	}elseif(file_exists(addTrailingSlash(getParentPath($_POST['original_path'])) . $_POST['name']))
	{
		$error = ERR_RENAME_EXISTS;
	}elseif(is_file($_POST['original_path']) && !isValidExt($_POST['name'], explode(",", CONFIG_UPLOAD_VALID_EXTS), explode(",", CONFIG_UPLOAD_INVALID_EXTS)))
	{
		$error = ERR_RENAME_FILE_TYPE_NOT_PERMITED;
	}elseif(!rename(removeTrailingSlash($_POST['original_path']), addTrailingSlash(getParentPath($_POST['original_path'])) . $_POST['name']))
	{
		$error = ERR_RENAME_FAILED;
	}else 
	{
		//update record of session if image exists in session for cut or copy
		include_once(CLASS_SESSION_ACTION);
		$sessionAction = new SessionAction();		
		$selectedDocuments = $sessionAction->get();
		if(removeTrailingSlash($sessionAction->getFolder()) == getParentPath($_POST['original_path']) && sizeof($selectedDocuments))
		{
			if(($key = array_search(basename($_POST['original_path']), $selectedDocuments)) !== false)
			{
				$selectedDocuments[$key] = $_POST['name'];
				$sessionAction->set($selectedDocuments);
				
			}
			
		}elseif(removeTrailingSlash($sessionAction->getFolder()) == removeTrailingSlash($_POST['original_path']))
		{
			$sessionAction->setFolder($_POST['original_path']);
		}	
		$path = addTrailingSlash(getParentPath($_POST['original_path'])) . $_POST['name'];
		if(is_file($path))
		{
			include_once(CLASS_FILE);
			$file = new file($path);
			$fileInfo = $file->getFileInfo();
		}else
		{
			include_once(CLASS_MANAGER);
			$manager = new manager($path, false);
			$fileInfo = $manager->getFolderInfo();
		}
	}
	
	echo "{";
	echo "error:'" . $error . "' ";
	foreach ($fileInfo as $k=>$v)
	{
		echo "," . $k . ":'" . $v . "' ";
	}
	echo "}";
	
	
?>
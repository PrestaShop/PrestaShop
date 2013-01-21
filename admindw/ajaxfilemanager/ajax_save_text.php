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
	$path = addTrailingSlash(backslashToSlash($_POST['folder'])) . $_POST['name'];
	if(CONFIG_SYS_VIEW_ONLY || !CONFIG_OPTIONS_EDITABLE)
	{
		$error = SYS_DISABLED;
	}
	elseif(isset($_POST['save_as_request']))
	{		
		if(!preg_match('/^[a-zA-Z0-9_\-.]+$/', $_POST['name']))
		{
			$error = TXT_SAVE_AS_ERR_NAME_INVALID;
		}elseif(array_search(strtolower(getFileExt($_POST['name'])), getValidTextEditorExts()) === false)
		{
			$error = TXT_DISALLOWED_EXT;
		}elseif(!isUnderRoot($_POST['folder']))
		{
			$error = ERR_FOLDER_PATH_NOT_ALLOWED;
		}
		else
		{
			
			if(!empty($_POST['save_as_request']))
			{//save as request
				if(file_exists($path))
				{
					$error = TXT_FILE_EXIST;
				}else 
				{
					if(($fp = @fopen($path, 'w+')) !== false)
					{
						if(@fwrite($fp, $_POST['text']))
						{
							@fclose($fp);
						}else 
						{
							$error = TXT_CONTENT_WRITE_FAILED;
						}
					}else 
					{
						$error = TXT_CREATE_FAILED;
					}					
				}

				
			}else 
			{
				if(!file_exists($path))
				{
					$error = TXT_FILE_NOT_EXIST;
				}else 
				{
					if(($fp = @fopen($path, 'w')) !== false)
					{
						if(@fwrite($fp, $_POST['text']))
						{
							@fclose($fp);
						}else 
						{
							$error = TXT_CONTENT_UPDATE_FAILED;
						}
					}else 
					{
						$error = TXT_FILE_OPEN_FAILED;
					}						
				}
			}
			
		}

	}else 
	{
		$error = TXT_UNKNOWN_REQUEST;
	}
		echo "{";
		echo "error:'" . Tools::safeOutput($error) . "',\n";
		echo  "path:'" . Tools::safeOutput($path) . "'";
		echo "}";
	
?>

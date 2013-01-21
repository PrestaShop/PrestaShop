<?php
	if (!defined('_PS_ADMIN_DIR_'))
		define('_PS_ADMIN_DIR_', getcwd());
	require_once('../../config/config.inc.php');
	require_once('../init.php');
	/**
	 * ajax image undo function
	 * @author Logan Cai (cailongqun [at] yahoo [dot] com [dot] cn)
	 * @link www.phpletter.com
	 * @since 22/May/2007
	 *
	 */
	require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . "inc" . DIRECTORY_SEPARATOR . "config.php");	
	require_once(CLASS_HISTORY);
	$history = new History($_POST['file_path'], $session);
	$lastestSessionImageInfo = $history->getLastestRestorable();
	echo "{";
	$error = "";
	$info = "";
	if(CONFIG_SYS_VIEW_ONLY)
	{
		$error = SYS_DISABLED;
	}
	elseif(empty($_POST['file_path']))
	{
		$error  =  IMG_SAVE_EMPTY_PATH;
	}elseif(!file_exists($_POST['file_path']))
	{
		$error  =  IMG_SAVE_NOT_EXISTS;
	}elseif(!isUnderRoot($_POST['file_path']))
	{
		$error = IMG_SAVE_PATH_DISALLOWED;
	}else if(!sizeof($lastestSessionImageInfo))
	{
		$error = IMG_UNDO_NO_HISTORY_AVAIALBE;
	}
	else
	{		
			//get the original image which is the lastest session image if any when the system is in demo
			$sessionImage = $session->getSessionDir() . $lastestSessionImageInfo['name'];
			$originalSessionImageInfo = $history->getOriginalImage();
			if(CONFIG_SYS_DEMO_ENABLE && sizeof($originalSessionImageInfo))
			{
				$originalImage = $session->getSessionDir() . $originalSessionImageInfo['info']['name'];				
			}else 
			{
				$originalImage = $_POST['file_path'];
			}		
			 
				include_once(CLASS_IMAGE);
				$image = new Image();
				if($image->loadImage($sessionImage))
				{
					$imageInfo = $image->getOriginalImageInfo();
					if(!@copy($sessionImage, $originalImage))
					{
						$error = IMG_UNDO_COPY_FAILED;
					}else 
					{
						
						//remove the session image
						if(@unlink($sessionImage))
						{
							$history->restore();
						}
						//only one left, remove the session original if demo
						
						if($history->getNumRestorable() == 0 && CONFIG_SYS_DEMO_ENABLE && sizeof($originalSessionImageInfo))
						{
							@unlink($session->getSessionDir() . $originalSessionImageInfo['info']['name']);	
							$originalImage = $_POST['file_path'];
						}
					}
					$imagePath = $originalImage;
					
				}else 
				{
					$error = IMG_SAVE_IMG_OPEN_FAILED;
				}
					if(isset($imageInfo))
					{
							$info .= ",width:" . $imageInfo['width'] . "\n";
							$info .= ",height:" . $imageInfo['height'] . "\n";
							$info .= ",size:'" . transformFileSize($imageInfo['size']) . "'\n";
							$info .= ",path:'" . backslashToSlash($imagePath) . "'\n";						
					}	

	}
	
	echo "error:'" . $error . "'\n";
	if(isset($image) && is_object($image))
	{
		$image->DestroyImages();
	}
	echo $info;
	echo ",history:" . ($history->getNumRestorable()) . "\n";
	echo "}";
?>
<?php
	/**
	 * reset the image
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
	if(!isset($_POST['path']))
	{
		$_POST['path'] = "uploaded/Winter.jpg" .  "?" . makeQueryString(array('path'));
		//for crop
		$_POST['mode'] = "crop";
		$_POST['x'] = 100;
		$_POST['y'] = 100;
		$imageInfo = @GetImageSize($_POST['path']);
		$_POST['width'] = $imageInfo[0];
		$_POST['height'] = $imageInfo[1];
		
	}
	initSessionHistory($_POST['path']);
	
	echo "{";
	$error = "";
	$info = "";
	
	if(empty($_POST['path']))
	{
		$error  =  IMG_SAVE_EMPTY_PATH;
	}elseif(!file_exists($_POST['path']))
	{
		$error  =  IMG_SAVE_NOT_EXISTS;
	}else if(!isUnderRoot($_POST['path']))
	{
		$error = IMG_SAVE_PATH_DISALLOWED;
	}
	else
	{
		if(!empty($_POST['mode']))
		{
			
			include_once(CLASS_IMAGE);
			$image = new Image();
			$image->loadImage($_POST['path']);
			
			switch($_POST['mode'])
			{
				case "resize":					
					if(!$image->resize($_POST['width'], $_POST['height'], (!empty($_POST['constraint'])?true:false)))
					{
						$error = IMG_SAVE_RESIZE_FAILED;
					}					
					break;
				case "crop":
					if(!$image->cropToDimensions($_POST['x'], $_POST['y'], (int)($_POST['x']) + (int)($_POST['width']), (int)($_POST['y']) + (int)($_POST['height'])))
					{
						$error = IMG_SAVE_CROP_FAILED;
					}
					
					break;
				case "flip":
					if(!$image->flip($_POST['flip_angle']))
					{
						$error = IMG_SAVE_FLIP_FAILED;
					}
					break;
				case "rotate":
					if(!$image->rotate((int)($_POST['angle'])))
					{
						$error = IMG_SAVE_ROTATE_FAILED;
					}
					break;
				default:
					$error = IMG_SAVE_UNKNOWN_MODE;
			}
			if(empty($error))
			{
				
				$sessionNewPath = $session->getSessionDir() . uniqid(md5(time())) . "." . getFileExt($_POST['path']);
				if(!@copy($_POST['path'], $sessionNewPath))
				{
					$error = IMG_SAVE_BACKUP_FAILED;
				}else 
				{
					addSessionHistory($_POST['path'], $sessionNewPath);
					if($image->saveImage($_POST['path']))
					{						
						$imageInfo = $image->getFinalImageInfo();	
						$info .= ",width:" . $imageInfo['width'] . "\n";
						$info .= ",height:" . $imageInfo['height'] . "\n";
						$info .= ",size:'" . transformFileSize($imageInfo['size']) . "'\n";
					}else 
					{
						$error = IMG_SAVE_FAILED;
					}			
				}				
			}else 
			{
				//$image->DestroyImages();
			}
		}else 
		{
			$error = IMG_SAVE_UNKNOWN_MODE;
		}
	}
	
	echo "error:'" . $error . "'\n";
	if(isset($image) && is_object($image))
	{
		$image->DestroyImages();
	}
	echo $info;
	echo ",history:" . sizeof($_SESSION[$_POST['path']]) . "\n";
	echo "}";
?>
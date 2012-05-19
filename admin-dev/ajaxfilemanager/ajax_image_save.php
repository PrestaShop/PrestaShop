<?php
	if (!defined('_PS_ADMIN_DIR_'))
		define('_PS_ADMIN_DIR_', getcwd());
	require_once('../../config/config.inc.php');
	require_once('../init.php');
	/**
	 * image save function
	 * @author Logan Cai (cailongqun [at] yahoo [dot] com [dot] cn)
	 * @link www.phpletter.com
	 * @since 22/May/2007
	 *
	 */
	require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . "inc" . DIRECTORY_SEPARATOR . "config.php");	
	

	$error = "";
	$info = "";
	if(CONFIG_SYS_VIEW_ONLY || !CONFIG_OPTIONS_EDITABLE)
	{
		$error = SYS_DISABLED;
	}
	elseif(empty($_POST['path']))
	{
		$error  =  IMG_SAVE_EMPTY_PATH;
	}elseif(!file_exists($_POST['path']))
	{		
		$error  =  IMG_SAVE_NOT_EXISTS;
	}elseif(!isUnderRoot($_POST['path']))
	{
		$error = IMG_SAVE_PATH_DISALLOWED;
	}elseif(($sessionDir = $session->getSessionDir()) == '')
	{
		$error = SESSION_PERSONAL_DIR_NOT_FOUND;
	}
	else
	{	
		require_once(CLASS_HISTORY);
		$history = new History($_POST['path'], $session);
		if(!empty($_POST['mode']))
		{
			//get the original image which is the lastest session image if any when the system is in demo
			$lastestSessionImageInfo = $history->getLastestRestorable();
			if(sizeof($lastestSessionImageInfo) && CONFIG_SYS_DEMO_ENABLE)
			{				
				$originalSessionImageInfo = $history->getOriginalImage();
				if(sizeof($originalSessionImageInfo))
				{					
					$originalImage = $sessionDir . $originalSessionImageInfo['info']['name'];
				}				
			}
			if(empty($originalImage))
			{
				$originalImage = $_POST['path'];
			}
			
			include_once(CLASS_IMAGE);
			$image = new Image();
			if($image->loadImage($originalImage))
			{				
				
				switch($_POST['mode'])
				{
					case "resize":					
						if(!$image->resize($_POST['width'], $_POST['height'], (!empty($_POST['constraint'])?true:false)))
						{
							$error = IMG_SAVE_RESIZE_FAILED;
						}					
						break;
					case "crop":	
						if(!$image->crop($_POST['x'], $_POST['y'], $_POST['width'], $_POST['height']))
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
					$sessionNewPath = $sessionDir  . uniqid(md5(time())) . "." . getFileExt($_POST['path']);					
					if(!@copy($originalImage, $sessionNewPath))
					{//keep a copy under the session folder
						$error = IMG_SAVE_BACKUP_FAILED;						
					}else 
					{
						
						$isSaveAsRequest = (!empty($_POST['new_name']) && !empty($_POST['save_to'])?true:false);
						//save the modified image
						$sessionImageInfo = array('name'=>basename($sessionNewPath), 'restorable'=>1);
						$history->add($sessionImageInfo);
						if(CONFIG_SYS_DEMO_ENABLE)
						{//demo only
							if(isset($originalSessionImageInfo) && sizeof($originalSessionImageInfo))
							{
								$imagePath = $sessionDir . $originalSessionImageInfo['info']['name'];
							}else 
							{	
								$imagePath = $sessionDir  . uniqid(md5(time())) . "." . getFileExt($_POST['path']);
							}
						}else 
						{	
							if($isSaveAsRequest)
							{//save as request
								//check save to folder if exists
								$imagePath = addTrailingSlash(backslashToSlash($_POST['save_to'])) . $_POST['new_name'] . "." . getFileExt($_POST['path']); 
								if(!file_exists($_POST['save_to']) || !is_dir($_POST['save_to']))
								{
									$error = IMG_SAVE_AS_FOLDER_NOT_FOUND;
								}elseif(file_exists($imagePath)) 
								{
									$error = IMG_SAVE_AS_NEW_IMAGE_EXISTS;
								}elseif(!preg_match("/^[a-zA-Z0-9_\- ]+$/", $_POST['new_name']))
								{
									$error = IMG_SAVE_AS_ERR_NAME_INVALID;
								}
								
							}else 
							{//save request
								$imagePath = $originalImage;	
							}
												
						}

						if($image->saveImage($imagePath))
						{		
									
							if(CONFIG_SYS_DEMO_ENABLE)
							{
								if(!isset($originalSessionImageInfo) || !sizeof($originalSessionImageInfo))
								{//keep this original image information on session for future reference if demo only	
									$originalSessionImageInfo = array('name'=>basename($imagePath), 'restorable'=>0, 'is_original'=>1);
									$history->add($originalSessionImageInfo);
								}
							}
							$imageInfo = $image->getFinalImageInfo();								
						}else 
						{
							$error = IMG_SAVE_FAILED;
							
						}							
						if(isset($imageInfo))
						{
								$info .= ",width:" . $imageInfo['width'] . "";
								$info .= ",height:" . $imageInfo['height'] . "";
								$info .= ",size:'" . transformFileSize($imageInfo['size']) . "'";
								if($isSaveAsRequest)
								{
									$info .= ",save_as:'1'";
								}else 
								{
									$info .= ",save_as:'0'";
								}
								$info .= ",folder_path:'" . dirname($imagePath) . "'";
								$info .= ",path:'" . backslashToSlash($imagePath) . "'";	
													
						}				
			
					}				
								
				}			
			}else 
			{
				$error = IMG_SAVE_IMG_OPEN_FAILED;
			}		

		}else 
		{
			$error = IMG_SAVE_UNKNOWN_MODE;
		}
	}
	echo "{";	
	echo "error:'" . $error . "'";
	if(isset($image) && is_object($image))
	{
		$image->DestroyImages();
	}
	echo $info;
	echo ",history:" . (isset($history) && is_object($history)?($history->getNumRestorable()):0)  . "";
	echo "}";
	
	
?>
<?php
	if (!defined('_PS_ADMIN_DIR_'))
		define('_PS_ADMIN_DIR_', getcwd());
	require_once('../../config/config.inc.php');
	require_once('../init.php');
	/**
	 * ajax preview
	 * @author Logan Cai (cailongqun [at] yahoo [dot] com [dot] cn)
	 * @link www.phpletter.com
	 * @since 22/April/2007
	 *
	 */
	include_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . "inc" . DIRECTORY_SEPARATOR . "config.php");	
	if(!empty($_GET['path']) && file_exists($_GET['path']) && is_file($_GET['path']))
	{
		include_once(CLASS_IMAGE);
		$image = new Image(true);
		if($image->loadImage($_GET['path']))
		{
			if($image->resize(CONFIG_IMG_THUMBNAIL_MAX_X, CONFIG_IMG_THUMBNAIL_MAX_Y, true, true))
			{
				$image->showImage();
			}else 
			{
				echo PREVIEW_NOT_PREVIEW . ".";	
			}
		}else 
		{
			echo PREVIEW_NOT_PREVIEW . "..";			
		}

			
	}else 
	{
		echo PREVIEW_NOT_PREVIEW . "...";
	}


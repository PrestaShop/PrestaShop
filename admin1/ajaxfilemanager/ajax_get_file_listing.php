<?php 

	/**
	 * the php script used to get the list of file or folders under a specific folder
	 * @author Logan Cai (cailongqun [at] yahoo [dot] com [dot] cn)
	 * @link www.phpletter.com
	 * @since 22/May/2007
	 *
	 */
	if (!defined('_PS_ADMIN_DIR_'))
		define('_PS_ADMIN_DIR_', getcwd());
	require_once('../../config/config.inc.php');
	require_once('../init.php');

	if(!isset($manager))
	{
		/**
		 *  this is part of  script for processing file paste 
		 */
		//$_GET = $_POST;
		include_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . "inc" . DIRECTORY_SEPARATOR . "config.php");
		include_once(CLASS_PAGINATION);
		$pagination = new pagination(false);
		if(!empty($_GET['search']))
		{
			include_once(CLASS_SEARCH);
			
			$search  = new Search($_GET['search_folder']);
			$search->addSearchKeyword('recursive', @$_GET['search_recursively']);
			$search->addSearchKeyword('mtime_from', @$_GET['search_mtime_from']);
			$search->addSearchKeyword('mtime_to', @$_GET['search_mtime_to']);
			$search->addSearchKeyword('size_from', @$_GET['search_size_from']);
			$search->addSearchKeyword('size_to', @$_GET['search_size_to']);
			$search->addSearchKeyword('recursive', @$_GET['search_recursively']);
			$search->addSearchKeyword('name', @$_GET['search_name']);
			$search->doSearch();
			$fileList = $search->getFoundFiles();
			$folderInfo = $search->getRootFolderInfo();			
			
		}else 
		{
			include_once(CLASS_MANAGER);
			include_once(CLASS_SESSION_ACTION);
			$sessionAction = new SessionAction();
			include_once(DIR_AJAX_INC . "class.manager.php");
		
			$manager = new manager();
			$manager->setSessionAction($sessionAction);
		
			$fileList = $manager->getFileList();
			$folderInfo = $manager->getFolderInfo();	
						
		}
		$pagination->setUrl(CONFIG_URL_FILEnIMAGE_MANAGER);	

	}else 
	{
		include_once(CLASS_PAGINATION);
		$pagination = new pagination(false);			
	}

		
		$pagination->setTotal(sizeof($fileList));
		$pagination->setFirstText(PAGINATION_FIRST);
		$pagination->setPreviousText(PAGINATION_PREVIOUS);
		$pagination->setNextText(PAGINATION_NEXT);
		$pagination->setLastText(PAGINATION_LAST);
		$pagination->setLimit(!empty($_GET['limit'])?(int)($_GET['limit']):CONFIG_DEFAULT_PAGINATION_LIMIT);
		echo $pagination->getPaginationHTML();
		echo "<script type=\"text/javascript\">\n";
		
        echo "parentFolder = {path:'" . getParentFolderPath($folderInfo['path']). "'};\n"; 
		echo 'currentFolder ={'; 
		$count =1;
		foreach($folderInfo as $k=>$v)
		{
			echo ($count++ == 1?'':',') . "'" . $k . "':'" . ($k=='ctime'|| $k=='mtime'?date(DATE_TIME_FORMAT, $v):$v)  . "'";

		}
		echo "};\n";
		$fileList = array_slice($fileList, $pagination->getPageOffset(), $pagination->getLimit());
		echo 'numRows = ' . sizeof($fileList) . ";\n";
		echo "files = {\n";
		$count = 1;
		
		
		foreach($fileList as $file)
		{
			echo (($count > 1)?",":'').$count++ . ":{";
			$j = 1;
			foreach($file as $k=>$v)
			{
				
				if($k  == 'ctime' || $k == 'mtime')
				{
					$v = @date(DATE_TIME_FORMAT, $v);
				}	
				if($k == 'size')
				{
					$v = transformFileSize($v);
				}
				echo (($j++ > 1)?",":'') . "'" . $k . "':'" . $v . "'";
			}
			echo (($j++ > 1)?",":'') . "'url':'" . getFileUrl($file['path']) . "'";
			echo "}\n";				
		}
		echo  "};</script>\n";
	if(!empty($_GET['view']))
	{
		switch($_GET['view'])
		{
			case 'detail':
			case 'thumbnail':
			case 'text':	
				$view = $_GET['view'];
				break;
			default:
				$view = CONFIG_DEFAULT_VIEW;
		}
	}else 
	{
		$view = CONFIG_DEFAULT_VIEW;
	}	
	switch($view)
	{
		case 'text':
			//list file name only
			include_once(DIR_AJAX_ROOT . '_ajax_get_text_listing.php');
			break;
		case 'thumbnail':
			//list file with thumbnail
			include_once(DIR_AJAX_ROOT . '_ajax_get_thumbnail_listing.php');
			break;
		case 'detail':
		default:
			include_once(DIR_AJAX_ROOT . '_ajax_get_details_listing.php');
	}

	

?>
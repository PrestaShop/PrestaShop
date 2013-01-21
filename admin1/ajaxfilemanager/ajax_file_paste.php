<?
	include_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . "inc" . DIRECTORY_SEPARATOR . "config.php");		
	if (!defined('_PS_ADMIN_DIR_'))
		define('_PS_ADMIN_DIR_', getcwd());
	require_once('../../config/config.inc.php');
	require_once('../init.php');
	$error = '';
	$fileMoved = array();
	$unmovedDocDueToSamePath = array();
	if(CONFIG_SYS_VIEW_ONLY || (!CONFIG_OPTIONS_CUT && !CONFIG_OPTIONS_COPY))
	{
		$error = SYS_DISABLED;
	}
	elseif(empty($_GET['current_folder_path']))
		{
			$error = ERR_NOT_DEST_FOLDER_SPECIFIED;
		}elseif(!file_exists($_GET['current_folder_path']) || !is_dir($_GET['current_folder_path']))
		{
			$error = ERR_DEST_FOLDER_NOT_FOUND;
		}elseif(!isUnderRoot($_GET['current_folder_path']))
		{
			$error = ERR_DEST_FOLDER_NOT_ALLOWED;
		}else 
		{
			
			include_once(CLASS_MANAGER);
			include_once(CLASS_SESSION_ACTION);
			$sessionAction = new SessionAction();
			include_once(DIR_AJAX_INC . "class.manager.php");	
			$manager = new manager();
			$manager->setSessionAction($sessionAction);
			$selectedDocuments = $sessionAction->get();
			
			$destFolderPath = addTrailingSlash(backslashToSlash($_GET['current_folder_path']));
			
			
			if(sizeof($selectedDocuments))
			{
				//get all files within the destination folder
				$allDocs = array();
				if(($fh = @opendir($_GET['current_folder_path'])))
				{
					while(($file = readdir($fh)) && $file != '.' && $file != '..')
					{
						$allDocs[] = getRealPath($destFolderPath . $file);
					}
				}
				closedir($fh);
				include_once(CLASS_FILE);
				$file = new file();
				//check if all files are allowed to cut or copy

				foreach($selectedDocuments as $doc)
				{
					if(file_exists($doc) && isUnderRoot($doc) )
					{
						
						if( array_search(getRealPath($doc), $allDocs) === false || CONFIG_OVERWRITTEN)
						{
							if(CONFIG_OVERWRITTEN)
							{
								$file->delete($doc);
							}
							if($file->copyTo($doc, $_GET['current_folder_path']))
							{
								
								$finalPath = $destFolderPath . basename($doc);
								$objFile = new file($finalPath);
								$tem = $objFile->getFileInfo();
								$obj = new manager($finalPath, false);			
													
								$fileType = $obj->getFileType($finalPath, (is_dir($finalPath)?true:false));
								
								foreach($fileType as $k=>$v)
								{
									$tem[$k] = $v;
								}
								
/*								foreach ($folderInfo as $k=>$v)
								{
									$tem['i_' . $k] = $v;
								}
								if($folderInfo['type'] == 'folder' && empty($folderInfo['subdir']) &&  empty($folderInfo['file']))
								{
									$tem['cssClass'] = 'folderEmpty';
								}*/
								
								$tem['final_path'] = $finalPath;
								$tem['path'] = backslashToSlash($finalPath);		
								$tem['type'] = (is_dir($finalPath)?'folder':'file');
								$tem['size'] = @transformFileSize($tem['size']);
								$tem['ctime'] = date(DATE_TIME_FORMAT, $tem['ctime']);
								$tem['mtime'] = date(DATE_TIME_FORMAT, $tem['mtime']);
								$tem['flag'] = 'noFlag';
								$tem['url'] = getFileUrl($doc);
		
								$manager = null;
								if($sessionAction->getAction() == "cut")
								{
									$file->delete($doc);
								}
								$fileMoved[sizeof($fileMoved)] = $tem;
								$tem = null;
							}							
						}else 
						{
							$unmovedDocDueToSamePath[] = $doc;
						}
							
					}
				}

				$sessionAction->set(array());
			}
			if(sizeof($unmovedDocDueToSamePath) == sizeof($selectedDocuments))
			{
				$error = ERR_DEST_FOLDER_NOT_ALLOWED;
			}elseif(sizeof($unmovedDocDueToSamePath)) 
			{
				foreach($unmovedDocDueToSamePath as $v)
				{
					$error .=  sprintf(ERR_UNABLE_TO_MOVE_TO_SAME_DEST, $v) . "\r\n";
				}
			}
		}
		
		echo "{'error':'" . $error . "', 'unmoved_files':" . sizeof($unmovedDocDueToSamePath) . ", 'files':{";
		foreach($fileMoved as  $i=>$file)
		{
			
			echo ($i>0?', ':' ') . $i . ": { ";
			$j = 0;
			foreach($file as $k=>$v)
			{
				echo ($j++ > 0? ", ":'') . "'" . $k . "':'" . $v . "'"; 
				
			}
			echo "} ";
		}
		echo "} }";
	
?>

<?php
	include_once(CLASS_FILE);
	require_once(CLASS_SESSION_ACTION);
	require_once(CLASS_MANAGER);
	class Search
	{
		var $rootFolder = '';
		var $files = array();
		var $rootFolderInfo = array();
		var $searchkeywords = array(
			'mtime_from'=>'',
			'mtime_to'=>'',
			'name'=>'',
			'size_from'=>'',
			'size_to'=>'',
			'recursive'=>'0',
			
		);
		var $sessionAction = null;
		/**
		 * constructor
		 *
		 * @param string $rootFolder
		 */
		function __construct($rootFolder)
		{
			$this->rootFolder = $rootFolder;
			$this->sessionAction = new SessionAction();
			$objRootFolder = new file($this->rootFolder);
			$tem = $objRootFolder->getFileInfo();
			$obj = new manager($this->rootFolder, false);			
			$obj->setSessionAction($this->sessionAction);
			$selectedDocuments = $this->sessionAction->get();					
			$fileType = $obj->getFolderInfo($this->rootFolder);
			
			foreach($fileType as $k=>$v)
			{
				$tem[$k] = $v;
			}
			
			$tem['path'] = backslashToSlash($this->rootFolder);		
			$tem['type'] = (is_dir($this->rootFolder)?'folder':'file');
			$tem['size'] = (is_dir($this->rootFolder)?'':transformFileSize(@filesize($this->rootFolder)));
			//$tem['ctime'] = date(DATE_TIME_FORMAT, $tem['ctime']);
			//$tem['mtime'] = date(DATE_TIME_FORMAT, $tem['mtime']);
			$tem['flag'] = (array_search($tem['path'], $selectedDocuments) !== false?($this->sessionAction->getAction() == "copy"?'copyFlag':'cutFlag'):'noFlag');
			$tem['url'] = getFileUrl($this->rootFolder);
			$tem['friendly_path'] = transformFilePath($this->rootFolder);
			$tem['file'] = 0;
			$tem['subdir'] = 0;
			$manager = null;
			$this->rootFolderInfo = $tem;
			$tem = null;			
		}
		
		
		
		/**
		 * constructor
		 *
		 * @param string $rootFolder
		 */
		function Search($rootFolder)
		{
			$this->__construct($rootFolder);
		}

		/**
		 * change the search keyword individually
		 *
		 * @param string $key
		 * @param string $value
		 */
		function addSearchKeyword($key, $value)
		{
			$this->searchkeywords[$key] = $value;
		}
		/**
		 * change the search keywords 
		 *
		 * @param array $keywords
		 */
		function addSearchKeywords($keywords)
		{
			foreach($this->searchkeywords as $k=>$v)
			{
				if(array_key_exists($k, $keywords) !== false)
				{
					$this->searchkeywords[$k] = $keywords[$k];
				}
			}
		}
		/**
		 * get the file according to the search keywords
		 *
		 */
		function doSearch($baseFolderPath = null)
		{
			
			$baseFolderPath = addTrailingSlash(backslashToSlash((is_null($baseFolderPath)?$this->rootFolder:$baseFolderPath)));
			
			$dirHandler = @opendir($baseFolderPath);
			if($dirHandler)
			{
				while(false !== ($file = readdir($dirHandler)))
				{
					if($file != '.' && $file != '..')
					{
						$path = $baseFolderPath . $file;
						if(is_file($path))
						{
							$isValid = true;

							$fileTime = @filemtime($path);
							$fileSize = @filesize($path);	
							if($this->searchkeywords['name'] !== ''  && @eregi($this->searchkeywords['name'], $file) === false)
							{
								$isValid = false;
							}
							if($this->searchkeywords['mtime_from'] != '' && $fileTime < @strtotime($this->searchkeywords['mtime_from']))
							{
								$isValid = false;
							}
							if($this->searchkeywords['mtime_to'] != '' && $fileTime > @strtotime($this->searchkeywords['mtime_to']))
							{
								$isValid = false;
							}							
							if($this->searchkeywords['size_from'] != '' && $fileSize < @strtotime($this->searchkeywords['size_from']))
							{
								$isValid = false;
							}
							if($this->searchkeywords['size_to'] != '' && $fileSize > @strtotime($this->searchkeywords['size_to']))
							{
								$isValid = false;
							}			
							if($isValid && isListingDocument($path))
							{
								$finalPath = $path;
								$objFile = new file($finalPath);
								$tem = $objFile->getFileInfo();
								$obj = new manager($finalPath, false);			
								$obj->setSessionAction($this->sessionAction);
								$selectedDocuments = $this->sessionAction->get();													
								$fileType = $obj->getFileType($finalPath);
								
								foreach($fileType as $k=>$v)
								{
									$tem[$k] = $v;
								}
								
								$tem['path'] = backslashToSlash($finalPath);		
								$tem['type'] = (is_dir($finalPath)?'folder':'file');
								$tem['size'] = transformFileSize($tem['size']);
								$tem['ctime'] = date(DATE_TIME_FORMAT, $tem['ctime']);
								$tem['mtime'] = date(DATE_TIME_FORMAT, $tem['mtime']);
								$tem['flag'] = (array_search($tem['path'], $selectedDocuments) !== false?($this->sessionAction->getAction() == "copy"?'copyFlag':'cutFlag'):'noFlag');
								$tem['url'] = getFileUrl($tem['path']);
								$this->rootFolderInfo['file']++;
								$manager = null;
								$this->files[] = $tem;
								$tem = null;								
							}
						}elseif(is_dir($path) && $this->searchkeywords['recursive'])
						{
							$this->Search($baseFolderPath);
						}
					}
				}
			}
			closedir($dirHandler);
		}
		
		function getFoundFiles()
		{
			return $this->files;
		}
		
		function getRootFolderInfo()
		{

			return $this->rootFolderInfo;
		}
	}
?>

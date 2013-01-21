<?php
/**
	 * file listing
	 * @author Logan Cai (cailongqun [at] yahoo [dot] com [dot] cn)
	 * @link www.phpletter.com
	 * @since 22/April/2007
	 *
	 */
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . "class.file.php");
class manager
{
	var $currentFolderPath;
	var $sessionAction = null; //object to session action
	var $flags = array('no'=>'noFlag', 'cut'=>'cutFlag', 'copy'=>'copyFlag');
	var $forceFolderOnTop = false; //forced to have folder shown on the top of the list
	var $currentFolderInfo = array(
	'name'=>'',
	'subdir'=>0,
	'file'=>0,
	'ctime'=>'',
	'mtime'=>'',
	'is_readable'=>'',
	'is_writable'=>'',
	'size'=>0,
	'path'=>'',
	'type'=>'folder',
	'flag'=>'noFlag',
	'friendly_path'=>'',
	);
	
	var $lastVisitedFolderPathIndex = 'ajax_last_visited_folder';
	var $folderPathIndex = "path";
	var $calculateSubdir = true;
	var $fileTypes = array(
			array(array("exe", "com"), "fileExe", SEARCH_TYPE_EXE, 0),
			array(array("gif", "jpg", "png", "bmp", "tif"), "filePicture", SEARCH_TYPE_IMG, 1),
			array(array("zip", "sit", "rar", "gz", "tar"), "fileZip", SEARCH_TYPE_ARCHIVE, 0),
			array(array("htm", "html", "php", "jsp", "asp", 'js', 'css'), "fileCode", SEARCH_TYPE_HTML, 1),
			array(array("mov", "ram", "rm", "asx", "dcr", "wmv"), "fileVideo", SEARCH_TYPE_VIDEO, 1),
			array(array("mpg", "avi", "asf", "mpeg"), "fileVideo", SEARCH_TYPE_MOVIE, 1),
			array(array("aif", "aiff", "wav", "mp3", "wma"), "fileMusic", SEARCH_TYPE_MUSIC, 1),
			array(array("swf", 'flv'), "fileFlash", SEARCH_TYPE_FLASH, 1),
			array(array("ppt"), "filePPT", SEARCH_TYPE_PPT, 0),
			array(array("rtf"), "fileRTF", SEARCH_TYPE_DOC, 0),
			array(array("doc"), "fileWord", SEARCH_TYPE_WORD, 0),
			array(array("pdf"), "fileAcrobat", SEARCH_TYPE_PDF, 0),
			array(array("xls", "csv"), "fileExcel", SEARCH_TYPE_EXCEL, 0),
			array(array("txt"), "fileText", SEARCH_TYPE_TEXT, 1),
			array(array("xml", "xsl", "dtd"), "fileXml", SEARCH_TYPE_XML, 1)
	);
	
	/**
		 * constructor
		 * @path the path to a folder
		 * @calculateSubdir force to get the subdirectories information
		 */		
	function __construct($path = null, $calculateSubdir=true)
	{

		$this->calculateSubdir = $calculateSubdir;
		if(defined('CONFIG_SYS_FOLDER_SHOWN_ON_TOP'))
		{
			$this->forceFolderOnTop = CONFIG_SYS_FOLDER_SHOWN_ON_TOP;
		}
		if(!is_null($path))
		{
			$this->currentFolderPath = $path;

		}elseif(isset($_GET[$this->folderPathIndex]) && file_exists($_GET[$this->folderPathIndex]) && !is_file($_GET[$this->folderPathIndex]) )
		{
			$this->currentFolderPath = $_GET[$this->folderPathIndex];
		}
		elseif(isset($_SESSION[$this->lastVisitedFolderPathIndex]) && file_exists($_SESSION[$this->lastVisitedFolderPathIndex]) && !is_file($_SESSION[$this->lastVisitedFolderPathIndex]))
		{
			$this->currentFolderPath = $_SESSION[$this->lastVisitedFolderPathIndex];
		}else
		{
			$this->currentFolderPath = CONFIG_SYS_DEFAULT_PATH;
		}
		
		$this->currentFolderPath = (isUnderRoot($this->currentFolderPath)?backslashToSlash((addTrailingSlash($this->currentFolderPath))):CONFIG_SYS_DEFAULT_PATH);
		
		if($this->calculateSubdir)
		{// keep track of this folder path in session 
			$_SESSION[$this->lastVisitedFolderPathIndex] = $this->currentFolderPath;
		}
		if(is_dir($this->currentFolderPath))
		{
			$file = new file($this->currentFolderPath);
			$folderInfo = $file->getFileInfo();
			if(sizeof($folderInfo))
			{
				$this->currentFolderInfo['name']=basename($this->currentFolderPath);
				$this->currentFolderInfo['subdir']=0;
				$this->currentFolderInfo['file']=0;
				$this->currentFolderInfo['ctime']=$folderInfo['ctime'];
				$this->currentFolderInfo['mtime']=$folderInfo['mtime'];
				$this->currentFolderInfo['is_readable']=$folderInfo['is_readable'];
				$this->currentFolderInfo['is_writable']=$folderInfo['is_writable'];	
				$this->currentFolderInfo['path']  = $this->currentFolderPath;
				$this->currentFolderInfo['friendly_path'] = transformFilePath($this->currentFolderPath);
				$this->currentFolderInfo['type'] = "folder";
				$this->currentFolderInfo['cssClass']='folder';
				
				//$this->currentFolderInfo['flag'] = $folderInfo['flag'];
			}			
		}
		if($calculateSubdir && !file_exists($this->currentFolderPath))
		{
			die(ERR_FOLDER_NOT_FOUND . $this->currentFolderPath);
		}


	
	}
	
	function setSessionAction(&$session)
	{
		$this->sessionAction = $session;	
	}
	/**
		 * constructor
		 */
	function manager($path = null, $calculateSubdir=true)
	{
		$this->__construct($path, $calculateSubdir);
	}
	/**
		 * get current folder path
		 * @return  string
		 */
	function getCurrentFolderPath()
	{
		return $this->currentFolderPath;
	}
	/**
		 * get the list of files and folders under this current fold
		 *	@return array
		 */
	function getFileList()
	{
		$outputs = array();
		$files = array();
		$folders = array();
		$tem = array();
		$dirHandler = @opendir($this->currentFolderPath);
		if($dirHandler)
		{
			while(false !== ($file = readdir($dirHandler)))
			{
				if($file != '.' && $file != '..')
				{
					$flag = $this->flags['no'];
				
					if($this->sessionAction->getFolder() == $this->currentFolderPath)
					{//check if any flag associated with this folder or file
						$folder = addTrailingSlash(backslashToSlash($this->currentFolderPath));
						if(in_array($folder . $file, $this->sessionAction->get()))
						{
							if($this->sessionAction->getAction() == "copy")
							{
								$flag = $this->flags['copy'];
							}else 
							{
								$flag = $this->flags['cut'];
							}
						}
					}					
					$path=$this->currentFolderPath.$file;
					if(is_dir($path) && isListingDocument($path) )
					{
						$this->currentFolderInfo['subdir']++;
						if(!$this->calculateSubdir)
						{			
						}else 
						{
							
								$folder = $this->getFolderInfo($path);
								$folder['flag'] = $flag;
								$folders[$file] = $folder;
								$outputs[$file] = $folders[$file];							
						}

						
					}elseif(is_file($path) && isListingDocument($path))
					{

							$obj = new file($path);
							$tem = $obj->getFileInfo();
							if(sizeof($tem))
							{
								$fileType = $this->getFileType($file);
								foreach($fileType as $k=>$v)
								{
									$tem[$k] = $v;
								}
								$this->currentFolderInfo['size'] += $tem['size'];
								$this->currentFolderInfo['file']++;		
								$tem['path'] = backslashToSlash($path);		
								$tem['type'] = "file";
								$tem['flag'] = $flag;
								$files[$file] = $tem;
								$outputs[$file] = $tem;
								$tem = array();
								$obj->close();
								
							}							

				
					}
					
				}
			}
			if($this->forceFolderOnTop)
			{
				uksort($folders, "strnatcasecmp");
				uksort($files, "strnatcasecmp");
				$outputs = array();
				foreach($folders as $v)
				{
					$outputs[] = $v;
				}
				foreach ($files as $v)
				{
					$outputs[] = $v;
				}
			}else 
			{
				uksort($outputs, "strnatcasecmp");
			}
			
			@closedir($dirHandler);
		}else
		{
			trigger_error('Unable to locate the folder ' . $this->currentFolderPath, E_NOTICE);
		}
		return $outputs;
	}


	/**
	 * get current or the specified dir information
	 *
	 * @param string $path
	 * @return array
	 */
	function getFolderInfo($path=null)
	{
		if(is_null($path))
		{
			return $this->currentFolderInfo;
		}else 
		{
			$obj = new manager($path, false);
			$obj->setSessionAction($this->sessionAction);
			$obj->getFileList();
			return $obj->getFolderInfo();			
		}

	}

		/**
		 * return the file type of a file.
		 *
		 * @param string file name
		 * @return array
		 */
		function getFileType($fileName, $checkIfDir = false) 
		{
			
			$ext = strtolower($this->_getExtension($fileName, $checkIfDir));
			
			foreach ($this->fileTypes as $fileType) 
			{
				if(in_array($ext, $fileType[0]))
				{
					return array("cssClass" => $fileType[1], "fileType" => $fileType[2], "preview" => $fileType[3], 'test'=>5);
				}
			}
			if(!empty($fileName))
			{//this is folder
				if(empty($ext))
				{
					if(is_dir($fileName))
					{

						return array("cssClass" => ($checkIfDir && $this->isDirEmpty($fileName)?'folderEmpty':"folder") , "fileType" => "Folder", "preview" => 0, 'test'=>1);
					}else 
					{
						return array("cssClass" => "fileUnknown", "fileType" => SEARCH_TYPE_UNKNOWN, "preview" => 0, 'test'=>2);
					}
				}else 
				{
					return array("cssClass" => "fileUnknown", "fileType" => SEARCH_TYPE_UNKNOWN, "preview" => 0, 'test'=>3, 'ext'=>$ext , 'filename'=>$fileName);
				}
				
			}else
			{//this is unknown file
				return array("cssClass" => "fileUnknown", "fileType" => SEARCH_TYPE_UNKNOWN, "preview" => 0, 'test'=>4);
			}
		
		
		}

	/**
		 * return the predefined file types
		 *
		 * @return arrray
		 */
	function getFileTypes()
	{
		return $this->fileTypes;
	}
	/**
		 * print out the file types
		 *
		 */
	function printFileTypes()
	{
		foreach($fileTypes as $fileType)
		{
			if(isset($fileType[0]) && is_array($fileType[0]))
			{
				foreach($fileType[0] as $type)
				{
					echo $type. ",";
				}
			}
		}
	}

    /**
	 * Get the extension of a file name
	 * 
	 * @param  string $file
 	 * @return string
     * @copyright this function originally come from Andy's php 
	 */
    function _getExtension($file, $checkIfDir = false)
    {
    	if($checkIfDir && file_exists($file) && is_dir($file))
    	{
    		return '';
    	}else 
    	{
    		return @substr(@strrchr($file, "."), 1);
    	}
    	
    	
    }	

	function isDirEmpty($path)
	{
		$dirHandler = @opendir($path);
		if($dirHandler)
		{
			while(false !== ($file = readdir($dirHandler)))
			{
				if($file != '.' && $file != '..')
				{
					@closedir($dirHandler);
					return false;
					
				}
			}
			
			@closedir($dirHandler);
				
		}	
		return true;	
	}
}
?>
<?php
	/**
	 * file modification
	 * @author Logan Cai (cailongqun [at] yahoo [dot] com [dot] cn)
	 * @link www.phpletter.com
	 * @since 22/April/2007
	 *
	 */
	class file
	{
		var $fileInfo = "";
		var $filePath = "";
		var $fileStat = "";
		var $mask = '0775';
		var $debug = false;
		var $errors = array();
		/**
		 * constructor
		 *
		 * @param string $path the path to a file or folder
		 */
		function __construct($path = null)
		{
			if(!is_null($path))
			{
			if(file_exists($path))
			{
				$this->filePath = $path;
				if(is_file($this->filePath))
				{
					$this->fileStat = @stat($path);
					$this->fileInfo['size'] = $this->fileStat[7];
					$this->fileInfo['atime'] = $this->fileStat[8];
					$this->fileInfo['ctime'] = $this->fileStat[10];	
					$this->fileInfo['mtime'] = $this->fileStat[9];
					$this->fileInfo['path'] = $path;
					$this->fileInfo['name'] = basename($path);	
					$this->fileInfo['is_writable'] = $this->isWritable();
					$this->fileInfo['is_readable'] = $this->isReadable();
				}elseif(is_dir($this->filePath))
				{
					$this->fileStat = @stat($path);
					$this->fileInfo['name'] = basename($path);
					$this->fileInfo['path'] = $path;
					$this->fileInfo['atime'] = $this->fileStat[8];
					$this->fileInfo['ctime'] = $this->fileStat[10];	
					$this->fileInfo['mtime'] = $this->fileStat[9];
					$this->fileInfo['is_writable'] = $this->isWritable();
					$this->fileInfo['is_readable'] = $this->isReadable();					
				}
			}else 
			{
				trigger_error('No such file exists. ' . $path, E_USER_NOTICE);	
			}				
			}


			
		}
		/**
		 * contructor
		 *
		 * @param string $path
		 */
		function file($path=null)
		{
			$this->__construct($path);
		}
		
		
		/**
		 * check if a file or folder writable
		 *
		 * @param file path $path
		 * @return boolean
		 */
	function isWritable($path=null)
	{
		$path = (is_null($path)?$this->filePath:$path);		
		if (DIRECTORY_SEPARATOR == "\\")
		{
			$path = slashToBackslash($path);
			if(is_file($path))
			{
				$fp = @fopen($path,'ab');
				if($fp)
				{
					@fclose($fp);
					return true;
				}
			}elseif(is_dir($path))
			{
					$path = addTrailingSlash($path);
					$tmp = uniqid(time());
					if (@touch($path . $tmp)) 
					{
						@unlink($path . $tmp);
						return true;
					}			
			}
			return false;			
		}else 
		{
			return @is_writable(slashToBackslash($path));
		}

	}
	/**
	 * Returns true if the files is readable.
	 *
	 * @return boolean true if the files is readable.
	 */
	function isReadable($path =null) 
	{
		$path = is_null($path)?$this->filePath:$path;
		return @is_readable(slashToBackslash($path));
	}		
	/**
	 * change the modified time
	 *
	 * @param string $path
	 * @param string $time
	 * @return boolean
	 */
	function setLastModified($path=null, $time) 
	{
		$path = is_null($path)?$this->filePath:$path;
		$time = is_null($time)?time():$time;
		return @touch(slashToBackslash($path), $time);
	}

		/**
		 * create a new folder
		 *
		 * @path the path for the new folder
		 * @mask
		 * @dirOwner
		 * @return boolean
		 */
		function mkdir($path = null, $mask=null, $dirOwner='') 
		{
			$path = is_null($path)?$this->filePath:$path;
			if(!file_exists($path))
			{
				$mask = is_null($mask)?$this->mask:$mask;				
				$status = @mkdir(slashToBackslash($path));			
				if ($mask)
				{
					@chmod(slashToBackslash($path), intval($mask, 8));
				}					
				if($dirOwner)
				{
					$this->chown(slashToBackslash($path), $dirOwner);
				}
				return $status;				
			}
			return true;

		}	
	/**
	 * change the own of a file or folder
	 *
	 * @param the file path $path
	 * @param  $owner
	 */
	function chown($path, $owner) 
	{
		if(!empty($owner))
		{
			$owners = explode(":", $owner);
			if(!empty($owners[0]))
				@chown($path, $owners[0]);
			if(!empty($owners[1]))
				@chgrp($path, $owner[1]);
		}
	}	

    /**
         * Copy a file, or recursively copy a folder and its contents
         * @author      Aidan Lister <aidan@php.net>
         * @author      Paul Scott
         * @version     1.0.1
         * @param       string   $source    Source path
         * @param       string   $dest      Destination path
         * @return      bool     Returns TRUE on success, FALSE on failure
         */
    function copyTo($source, $dest)
    {
    	$source = removeTrailingSlash(backslashToSlash($source));
    	$dest = removeTrailingSlash(backslashToSlash($dest));
	 		if(!file_exists($dest) || !is_dir($dest))
			{
				if(!$this->mkdir($dest))
				{
					$this->_debug('Unable to create folder (' . $dest . ")");
					return false;
				}					
			}
					// Copy in to your self?
				if (getAbsPath($source) ==  getAbsPath($dest))
				{
					$this->_debug('Unable to copy itself. source: ' . getAbsPath($source) . "; dest: " . getAbsPath($dest));
					return false;		
				}
        // Simple copy for a file
        if (is_file($source))
        {        	
        		$dest = addTrailingSlash($dest) . (basename($source));
        	if(file_exists($dest))
        	{
        		return false;
        	}else {
        		
        		return copy($source, $dest);
        	}
            
            
        }elseif(is_dir($source))
        {
	        // Loop through the folder
	           if(file_exists(addTrailingSlash($dest) . basename($source)))
	           {
	           	return false;
	           }else 
	           {
			 		if(!file_exists(addTrailingSlash($dest) . basename($source)) || !is_dir(addTrailingSlash($dest) . basename($source)))
					{
						if(!$this->mkdir(addTrailingSlash($dest) . basename($source)))
						{
							$this->_debug('Unable to create folder (' . addTrailingSlash($dest) . basename($source) . ")");
							return false;
						}					
					}	        	
		        $handle = opendir($source);
		        while(false !== ($readdir = readdir($handle)))
		        {
		            if($readdir != '.' && $readdir != '..')
		            {	  
		            	$path = addTrailingSlash($source).'/'.$readdir;    
		            	$this->copyTo($path, addTrailingSlash($dest) . basename($source));
		            }
		        }
		         closedir($handle);
		        return true;	           	
	           }
	
        }		   
        return false;
    }	
    /**
     * get next available file name
     *
     * @param string $fileToMove the path of the file will be moved to
     * @param string $destFolder the path of destination folder
     * @return string
     */
    function getNextAvailableFileName($fileToMove, $destFolder)
    {
    	
    	$folderPath = addslashes(backslashToSlash(getParentPath($fileToMove)));
    	$destFolder = addslashes(backslashToSlash(getParentPath($destFolder)));
    	$finalPath = $destFolder . basename($fileToMove);
    	if(file_exists($fileToMove))
    	{
    		if(is_file())
    		{
    			$fileExt = getFileExt($fileToMove);
    			$fileBaseName = basename($fileToMove, '.' . $fileExt);
    			$count = 1;
    			while(file_exists($destFolder . $fileBaseName . $count . "." . $fileExt))
    			{
    				$count++;
    			}
    			$filePath = $destFolder . $fileBaseName . $count . "." . $fileExt;
    		}elseif(is_dir())
    		{
    			$folderName = basename($fileToMove);
     			$count = 1;
    			while(file_exists($destFolder . $folderName . $count))
    			{
    				$count++;
    			}
    			$filePath = $destFolder . $fileBaseName . $count;   			
    		}
    		
    	}
		return $finalPath;
    }
    /**
     * get file information
     *
     * @return array
     */
    function getFileInfo()
    {
    	return $this->fileInfo;
    }
    /**
     * close 
     *
     */
    function close()
    {
    	$this->fileInfo = null;
    	$this->fileStat = null;
    }
 	/**
	 * delete a file or a folder and all contents within that folder
	 *
	 * @param string $path
	 * @return boolean
	 */
	function delete($path = null)
	{
		$path = is_null($path)?$this->filePath:$path;
		if(file_exists($path))
		{
			if(is_file($path))
			{
				return @unlink($path);
			}elseif(is_dir($path))
			{
				return $this->__recursive_remove_directory($path);
			}
			
		}
		return false;
	}
	/**
	 * empty a folder
	 *
	 * @param string $path
	 * @return boolean
	 */
	function emptyFolder($path)
	{
		$path = is_null($path)?$this->filePath:"";
		if(file_exists($path) && is_dir($path))
		{
			return $this->__recursive_remove_directory($path, true);
		}
		return false;
	}
	
	function _debug($info)
	{
		if($this->debug)
		{
			echo $info . "<br>\n";
		}else 
		{
			$this->errors[] = $info;
		}
	}
/**
 * recursive_remove_directory( directory to delete, empty )
 * expects path to directory and optional TRUE / FALSE to empty
 * of course PHP has to have the rights to delete the directory
 * you specify and all files and folders inside the directory
 * 
 * to use this function to totally remove a directory, write:
 * recursive_remove_directory('path/to/directory/to/delete');
 * to use this function to empty a directory, write:
 *	recursive_remove_directory('path/to/full_directory',TRUE);
 * @param string $directory
 * @param boolean $empty
 * @return boolean
 */
 function __recursive_remove_directory($directory, $empty=FALSE)
 {
     // if the path has a slash at the end we remove it here
     if(substr($directory,-1) == '/')
     {
         $directory = substr($directory,0,-1);
     }
  
     // if the path is not valid or is not a directory ...
     if(!file_exists($directory) || !is_dir($directory))
     {
         // ... we return false and exit the function
         return FALSE;
  
     // ... if the path is not readable
     }elseif(!is_readable($directory))
     {
         // ... we return false and exit the function
         return FALSE;
  
     // ... else if the path is readable
     }else{
  
         // we open the directory
         $handle = @opendir($directory);
  
         // and scan through the items inside
         while (FALSE !== ($item = @readdir($handle)))
         {
             // if the filepointer is not the current directory
             // or the parent directory
             if($item != '.' && $item != '..')
             {
                 // we build the new path to delete
                 $path = $directory.'/'.$item;
  
                 // if the new path is a directory
                 if(is_dir($path))                  {
                     // we call this function with the new path
                     $this->__recursive_remove_directory($path);
  
                 // if the new path is a file
                 }else{
                    // we remove the file
                    @unlink($path);
                 }
             }
         }
         // close the directory
         @closedir($handle);
  
        // if the option to empty is not set to true
         if($empty == FALSE)
         {
             // try to delete the now empty directory
             if(!@rmdir($directory))
             {
                 // return false if not possible
                 return FALSE;
             }
         }
         // return success
         return TRUE;
     }
 }   		
	}

?>

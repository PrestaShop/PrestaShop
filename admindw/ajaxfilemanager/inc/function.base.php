<?php
	/**
	 * function avaialble to the file manager
	 * @author Logan Cai (cailongqun [at] yahoo [dot] com [dot] cn)
	 * @link www.phpletter.com
	 * @since 22/April/2007
	 *
	 */
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . "config.php");
/**
 * force to ensure existence of stripos
 */
if (!function_exists("stripos")) 
{
  function stripos($str,$needle,$offset=0)
  {
      return @strpos(strtolower($str),strtolower($needle),$offset);
  }
}
	/**
	 * get the current Url but not the query string specified in $excls
	 *
	 * @param array $excls specify those unwanted query string
	 * @return string
	 */
	function getCurrentUrl($excls=array())
	{
		$output = $_SERVER['PHP_SELF'];
		$count = 1;
		foreach($_GET as $k=>$v)
		{
			if(array_search($k, $excls) ===false)
			{
				$strAppend = "&";
				if($count == 1)
				{
					$strAppend = "?";
					$count++;
				}
				$output .= $strAppend . $k . "=" . $v;
			}
		}
		return htmlspecialchars($output);
	}

/**
 * print out an array
 *
 * @param array $array
 */
function displayArray($array, $comments="")
{
	echo "<pre>";
	echo $comments;
	print_r($array);
	echo $comments;
	echo "</pre>";
}



	/**
	 * check if a file extension is permitted
	 *
	 * @param string $filePath
	 * @param array $validExts
	 * @param array $invalidExts
	 * @return boolean
	 */
	function isValidExt($filePath, $validExts, $invalidExts=array())
	{
		$tem = array();

		if(sizeof($validExts))
		{
			foreach($validExts as $k=>$v)
			{
				$tem[$k] = strtolower(trim($v));
			}
		}
		$validExts = $tem;
		$tem = array();
		if(sizeof($invalidExts))
		{
			foreach($invalidExts as $k=>$v)
			{
				$tem[$k] = strtolower(trim($v));
			}
		}
		$invalidExts = $tem;
		if(sizeof($validExts) && sizeof($invalidExts))
		{
			foreach($validExts as  $k=>$ext)
			{
				if(array_search($ext, $invalidExts) !== false)
				{
					unset($validExts[$k]);
				}
			}
		}
		if(sizeof($validExts))
		{
			if(array_search(strtolower(getFileExt($filePath)), $validExts) !== false)
			{
				return true;
			}else 
			{
				return false;
			}
		}elseif(array_search(strtolower(getFileExt($filePath)), $invalidExts) === false)
		{
			return true;
		}else 
		{
			return false;
		}
	}





/**
 *  transform file relative path to absolute path
 * @param  string $value the path to the file
 * @return string 
 */
function relToAbs($value) 
{
	return backslashToSlash(preg_replace("/(\\\\)/","\\", getRealPath($value)));

}

	function getRelativeFileUrl($value, $relativeTo)
	{
		$output = '';
		$wwwroot = removeTrailingSlash(backslashToSlash(getRootPath()));
		$urlprefix = "";
		$urlsuffix = "";
		$value = backslashToSlash(getRealPath($value));
		$pos = strpos($value, $wwwroot);
		if ($pos !== false && $pos == 0)
		{
			$output  = $urlprefix . substr($value, strlen($wwwroot)) . $urlsuffix;
		}
	}
/**
 * replace slash with backslash
 *
 * @param string $value the path to the file
 * @return string
 */
function slashToBackslash($value) {
	return str_replace("/", DIRECTORY_SEPARATOR, $value);
}

/**
 * replace backslash with slash
 *
 * @param string $value the path to the file
 * @return string
 */
function backslashToSlash($value) {
	return str_replace(DIRECTORY_SEPARATOR, "/", $value);
}

/**
 * removes the trailing slash
 *
 * @param string $value
 * @return string
 */
function removeTrailingSlash($value) {
	if(preg_match('@^.+/$@i', $value))
	{
		$value = substr($value, 0, strlen($value)-1);
	}
	return $value;
}

/**
 * append a trailing slash
 *
 * @param string $value 
 * @return string
 */
function addTrailingSlash($value) 
{
	if(preg_match('@^.*[^/]{1}$@i', $value))
	{
		$value .= '/';
	}
	return $value;
}

/**
 * transform a file path to user friendly
 *
 * @param string $value
 * @return string
 */
function transformFilePath($value) {
	$rootPath = addTrailingSlash(backslashToSlash(getRealPath(CONFIG_SYS_ROOT_PATH)));
	$value = addTrailingSlash(backslashToSlash(getRealPath($value)));
	if(!empty($rootPath) && ($i = strpos($value, $rootPath)) !== false)
	{
		$value = ($i == 0?substr($value, strlen($rootPath)):"/");		
	}
	$value = prependSlash($value);
	return $value;
}
/**
 * prepend slash 
 *
 * @param string $value
 * @return string
 */
function prependSlash($value)
{
		if (($value && $value[0] != '/') || !$value )
		{
			$value = "/" . $value;
		}			
		return $value;	
}


	function writeInfo($data, $die = false)
	{
		$fp = @fopen(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'data.php', 'w+');
		@fwrite($fp, $data);
		@fwrite($fp, "\n\n" . date('d/M/Y H:i:s') );
		@fclose($fp);
		if($die)
		{
			die();
		}
		
	}

/**
 * no cachable header
 */
function addNoCacheHeaders() {
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
	header("Cache-Control: no-store, no-cache, must-revalidate");
	header("Cache-Control: post-check=0, pre-check=0", false);
	header("Pragma: no-cache");
}
	/**
	 * add extra query stiring to a url
	 * @param string $baseUrl
	 * @param string $extra the query string added to the base url
	 */
	function appendQueryString($baseUrl, $extra)
	{
		$output = $baseUrl;
		if(!empty($extra))
		{
			if(strpos($baseUrl, "?") !== false)
			{
				$output .= "&" . $extra;
			}else
			{
				$output .= "?" . $extra;
			}			
		}

		return $output;
	}
	/**
	 * make the query strin from $_GET, but excluding those specified by $excluded
	 *
	 * @param array $excluded
	 * @return string
	 */
	function makeQueryString($excluded=array())
	{
		$output = '';
		$count = 1;
		foreach($_GET as $k=>$v)
		{
			if(array_search($k, $excluded) === false)
			{
				$output .= ($count>1?'&':'') . ($k . "=" . $v);
				$count++;
			}
		}
		return $output;
	}
	/**
	 * get parent path from specific path
	 *
	 * @param string $value
	 * @return string
	 */
	function getParentPath($value)
	{
		$value = removeTrailingSlash(backslashToSlash($value));
		if(false !== ($index = strrpos($value, "/")) )
		{
			return substr($value, 0, $index);
		}

	}


	/**
	 * check if the file/folder is sit under the root
	 *
	 * @param string $value
	 * @return  boolean
	 */
	function isUnderRoot($value)
	{
		$roorPath = strtolower(addTrailingSlash(backslashToSlash(getRealPath(CONFIG_SYS_ROOT_PATH))));
		if(file_exists($value) && @strpos(strtolower(addTrailingSlash(backslashToSlash(getRealPath($value)))), $roorPath) === 0 )
		{
			return true;
		}
		return false;
	}
	/**
	 * check if a file under the session folder
	 *
	 * @param string $value
	 * @return boolean
	 */
	function isUnderSession($value)
	{
		global $session;
		$sessionPath = strtolower(addTrailingSlash(backslashToSlash(getRealPath($session->getSessionDir()))));
		if(file_exists($value) && @strpos(strtolower(addTrailingSlash(backslashToSlash(getRealPath($value)))), $sessionPath) === 0 )
		{
			return true;
		}
		return false;		
	}
	
	
	/**
	 * get thumbnail width and height
	 *
	 * @param integer $originaleImageWidth
	 * @param integer $originalImageHeight
	 * @param integer $thumbnailWidth
	 * @param integer $thumbnailHeight
	 * @return array()
	 */
	function getThumbWidthHeight( $originaleImageWidth, $originalImageHeight, $thumbnailWidth, $thumbnailHeight)
	{
		$outputs = array( "width"=>0, "height"=>0);
		$thumbnailWidth	= (int)($thumbnailWidth);
		$thumbnailHeight = (int)($thumbnailHeight);
		if(!empty($originaleImageWidth) && !empty($originalImageHeight))
		{
			//start to get the thumbnail width & height
        	if(($thumbnailWidth < 1 && $thumbnailHeight < 1) || ($thumbnailWidth > $originaleImageWidth && $thumbnailHeight > $originalImageHeight ))
        	{
        		$thumbnailWidth =$originaleImageWidth;
        		$thumbnailHeight = $originalImageHeight;
        	}elseif($thumbnailWidth < 1)
        	{
        		$thumbnailWidth = floor($thumbnailHeight / $originalImageHeight * $originaleImageWidth);

        	}elseif($thumbnailHeight < 1)
        	{
        		$thumbnailHeight = floor($thumbnailWidth / $originaleImageWidth * $originalImageHeight);
        	}else
        	{
        		$scale = min($thumbnailWidth/$originaleImageWidth, $thumbnailHeight/$originalImageHeight);
				$thumbnailWidth = floor($scale*$originaleImageWidth);
				$thumbnailHeight = floor($scale*$originalImageHeight);
        	}
			$outputs['width'] = $thumbnailWidth;
			$outputs['height'] = $thumbnailHeight;
		}
		return $outputs;

	}
/**
 * turn to absolute path from relative path
 *
 * @param string $value
 * @return string
 */
function getAbsPath($value) {
	if (substr($value, 0, 1) == "/")
		return slashToBackslash(DIR_AJAX_ROOT . $value);

	return slashToBackslash(dirname(__FILE__) . "/" . $value);
}

	/**
	 * get file/folder base name
	 *
	 * @param string $value
	 * @return string
	 */
	function getBaseName($value)
	{
		$value = removeTrailingSlash(backslashToSlash($value));

		if(false !== ($index = strrpos($value, "/")) )
		{
			return substr($value, $index + 1);
		}else
		{
			return $value;
		}
	}

function myRealPath($path) {

		if(strpos($path, ':/') !== false)
		{
			return $path;
		}
    // check if path begins with "/" ie. is absolute
    // if it isnt concat with script path
    
    if (strpos($path,"/") !== 0 ) {
        $base=dirname($_SERVER['SCRIPT_FILENAME']);
        $path=$base."/".$path;
    }
 
    // canonicalize
    $path=explode('/', $path);
    $newpath=array();
    for ($i=0; $i<sizeof($path); $i++) {
        if ($path[$i]==='' || $path[$i]==='.') continue;
           if ($path[$i]==='..') {
              array_pop($newpath);
              continue;
        }
        array_push($newpath, $path[$i]);
    }
    $finalpath="/".implode('/', $newpath);

    clearstatcache();
    // check then return valid path or filename
    if (file_exists($finalpath)) {
        return ($finalpath);
    }
    else return FALSE;
}
	/**
	 * calcuate realpath for a relative path
	 *
	 * @param string $value a relative path
	 * @return string absolute path of the input
	 */
 function getRealPath($value)
 {
 		$output = '';
 	 if(($path = realpath($value)) && $path != $value)
 	 {
 	 	$output = $path;
 	 }else 
 	 {
 	 	$output = myRealPath($value);
 	 }
 	 return $output;
 	
 }
	/**
	 * get file url
	 *
	 * @param string $value
	 * @return string
	 */
	function getFileUrl($value)
	{
		$output = '';
		$wwwroot = removeTrailingSlash(backslashToSlash(getRootPath()));

		$urlprefix = "";
		$urlsuffix = "";

	$value = backslashToSlash(getRealPath($value));
		

		$pos = stripos($value, $wwwroot);
		if ($pos !== false && $pos == 0)
		{
			$output  = $urlprefix . substr($value, strlen($wwwroot)) . $urlsuffix;
		}else 
		{
			$output = $value;
		}
		return "http://" .  addTrailingSlash(backslashToSlash($_SERVER['HTTP_HOST'])) . removeBeginingSlash(backslashToSlash($output));
	}
	
/**
 * 
 *	transfer file size number to human friendly string
 * @param integer $size.
 * @return String
 */
function transformFileSize($size) {

	if ($size > 1048576)
	{
		return round($size / 1048576, 1) . " MB";
	}elseif ($size > 1024)
	{
		return round($size / 1024, 1) . " KB";
	}elseif($size == '')
	{
		return $size;
	}else
	{
		return $size . " b";
	}	
}
	
	/**
	 * remove beginging slash
	 *
	 * @param string $value
	 * @return string
	 */
	function removeBeginingSlash($value)
	{
		$value = backslashToSlash($value);
		if(strpos($value, "/") === 0)
		{
			$value = substr($value, 1);
		}
		return $value;
	}
	
/**
 * get site root path
 *
 * @return String.
 */
function getRootPath() {
		$output = "";
		if (defined('CONFIG_WEBSITE_DOCUMENT_ROOT') && CONFIG_WEBSITE_DOCUMENT_ROOT)
		{
			return slashToBackslash(CONFIG_WEBSITE_DOCUMENT_ROOT);
		}
		if(isset($_SERVER['DOCUMENT_ROOT']) && ($output = relToAbs($_SERVER['DOCUMENT_ROOT'])) != '' )
		{
			return $output;
		}elseif(isset($_SERVER["SCRIPT_NAME"]) && isset($_SERVER["SCRIPT_FILENAME"]) && ($output = str_replace(backslashToSlash($_SERVER["SCRIPT_NAME"]), "", backslashToSlash($_SERVER["SCRIPT_FILENAME"]))) && is_dir($output))
		{
			return slashToBackslash($output);
		}elseif(isset($_SERVER["SCRIPT_NAME"]) && isset($_SERVER["PATH_TRANSLATED"]) && ($output = str_replace(backslashToSlash($_SERVER["SCRIPT_NAME"]), "", str_replace("//", "/", backslashToSlash($_SERVER["PATH_TRANSLATED"])))) && is_dir($output))
		{
			return $output;
		}else 
		{
			return '';
		}	

	return null;
}

	
	/**
	 * add beginging slash
	 *
	 * @param string $value
	 * @return string
	 */	
	function addBeginingSlash($value)
	{
		if(strpos($value, "/") !== 0 && !empty($value))
		{
			$value .= "/" . $value;
		}
		return $value;		
	}


	

	
	/**
	 * get a file extension
	 *
	 * @param string $fileName the path to a file or just the file name
	 */	
	function getFileExt($filePath)
	{
		return @substr(@strrchr($filePath, "."), 1);
	}
	
		/**
		 * reuturn the relative path between two url
		 *
		 * @param string $start_dir
		 * @param string $final_dir
		 * @return string
		 */
    function getRelativePath($start_dir, $final_dir){
      //
      $firstPathParts = explode(DIRECTORY_SEPARATOR, $start_dir);
      $secondPathParts = explode(DIRECTORY_SEPARATOR, $final_dir);
      //
      $sameCounter = 0;
      for($i = 0; $i < min( count($firstPathParts), count($secondPathParts) ); $i++) {
          if( strtolower($firstPathParts[$i]) !== strtolower($secondPathParts[$i]) ) {
              break;
          }
          $sameCounter++;
      }
      if( $sameCounter == 0 ) {
          return $final_dir;
      }
      //
      $newPath = '';
      for($i = $sameCounter; $i < count($firstPathParts); $i++) {
          if( $i > $sameCounter ) {
              $newPath .= DIRECTORY_SEPARATOR;
          }
          $newPath .= "..";
      }
      if( count($newPath) == 0 ) {
          $newPath = ".";
      }
      for($i = $sameCounter; $i < count($secondPathParts); $i++) {
          $newPath .= DIRECTORY_SEPARATOR;
          $newPath .= $secondPathParts[$i];
      }
      //
      return $newPath;
  }
  /**
   * get the php server memory limit
   * @return integer
   *
   */
  function getMemoryLimit()
  {
    $output = @ini_get('memory_limit') or $output = -1 ;
    if((int)($output) < 0)
    {//unlimited
    	$output = 999999999999999999;
    }
    elseif(strpos('g', strtolower($output)) !== false)
    {
    	$output = (int)($output) * 1024 * 1024 * 1024;
    }elseif(strpos('k', strtolower($output)) !== false)
    {
    	$output = (int)($output) * 1024 ;
    }else
    {
    	$output = (int)($output) * 1024 * 1024;
    }
    
    return $output;  	
  }
	/**
	 * get file content
	 *
	 * @param string $path
	 */
  function getFileContent($path)
  {
  	return @file_get_contents($path);
  	//return str_replace(array("\r", "\n", '"', "\t"), array("", "\\n", '\"', "\\t"), @file_get_contents($path));
  }
         /**
          * get the list of folder under a specified folder
          * which will be used for drop-down menu
          * @param string $path the path of the specified folder
          * @param array $outputs
          * @param string $indexNumber
          * @param string $prefixNumber the prefix before the index number
          * @param string $prefixName the prefix before the folder name
          * @return array
          */
         function getFolderListing($path,$indexNumber=null, $prefixNumber =' ', $prefixName =' - ',  $outputs=array())
         {
                   $path = removeTrailingSlash(backslashToSlash($path));
                   if(is_null($indexNumber))
                   {
                   	$outputs[IMG_LBL_ROOT_FOLDER] = removeTrailingSlash(backslashToSlash($path));
                   }
                   $fh = @opendir($path);
                   if($fh)
                   {
                            $count = 1;                          
                            while($file = @readdir($fh))
                            {
                                     $newPath = removeTrailingSlash(backslashToSlash($path . "/" . $file));
                                     if(isListingDocument($newPath) && $file != '.' && $file != '..' && is_dir($newPath))
                                     {                                          
                                               if(!empty($indexNumber))
                                               {//this is not root folder
                                               					
                                                        $outputs[$prefixNumber . $indexNumber . "." . $count . $prefixName . $file] = $newPath;
                                                        getFolderListing($newPath,  $prefixNumber . $indexNumber . "." . $count , $prefixNumber, $prefixName, $outputs);                                                 
                                               }else 
                                               {//this is root folder

                                                        $outputs[$count . $prefixName . $file] = $newPath;
                                                        getFolderListing($newPath, $count, $prefixNumber, $prefixName, $outputs);
                                               }
                                               $count++;
                                     }                                    
                            }
                            @closedir($fh);
                   }
                   return $outputs;
         }

         
         /**
          * get the valid text editor extension 
          * which is calcualte from the CONFIG_EDITABALE_VALID_EXTS 
          * exclude those specified in CONFIG_UPLOAD_INVALID_EXTS
          * and those are not specified in CONFIG_UPLOAD_VALID_EXTS
          *
          * @return array
          */
         function getValidTextEditorExts()
         {
         	$validEditorExts = explode(',', CONFIG_EDITABLE_VALID_EXTS);
         	if(CONFIG_UPLOAD_VALID_EXTS)
         	{//exclude those exts not shown on CONFIG_UPLOAD_VALID_EXTS
         		$validUploadExts = explode(',', CONFIG_UPLOAD_VALID_EXTS);
         		foreach($validEditorExts as $k=>$v)
         		{
         			if(array_search($v, $validUploadExts) === false)
         			{
         				unset($validEditorExts[$k]);
         			}
         		}        		
         	}
         	if(CONFIG_UPLOAD_INVALID_EXTS)
         	{//exlcude those exists in CONFIG_UPLOAD_INVALID_EXTS
         		$invalidUploadExts = explode(',', CONFIG_UPLOAD_INVALID_EXTS);
         		foreach($validEditorExts as $k=>$v)
         		{
         			if(array_search($v, $invalidUploadExts) !== false)
         			{
         				unset($validEditorExts[$k]);
         			}
         		}
         	}
         	return $validEditorExts;        	
         	
         }
    /**
     * check if file name or folder name is valid against a regular expression 
     *
     * @param string $pattern regular expression, separated by , if multiple
     * @param string $string
     * @return booolean
     */
        function isValidPattern( $pattern, $string)
        {
            if(($pattern)=== '')
            {
                return true;
            }
            else if (strpos($pattern,",")!==false)
            {
                $regExps = explode(',', $pattern);
                foreach ($regExps as $regExp => $value)
                {
                    if(eregi($value, $string))
                    {
                        return true;
                    }
                }               
            }
            else if(eregi($pattern, $string))
            {
                return true;
            }
            return false;
           
        }       

		
    /**
     * check if file name or folder name is invalid against a regular expression 
     *
     * @param string $pattern regular expression, separated by , if multiple
     * @param string $string
     * @return booolean
     */
        function isInvalidPattern( $pattern, $string)
        {
            if(($pattern)=== '')
            {
                return false;
            }
            else if (strpos($pattern,",")!==false)
            {
                $regExps = explode(',', $pattern);
                foreach ($regExps as $regExp => $value)
                {
                    if(eregi($value, $string))
                    {
                        return true;
                    }
                }               
            }
            else if(eregi($pattern, $string))
            {
                return true;
            }
            return false;
           
        }  
   			

		/**
		 * cut the file down to fit the list page
		 *
		 * @param string $fileName
		 */
		function shortenFileName($fileName, $maxLeng=17, $indicate = '...')
		{
			if(strlen($fileName) > $maxLeng)
			{
				$fileName = substr($fileName, 0, $maxLeng - strlen($indicate)) . $indicate;
			}
			return $fileName;
			
		}
		if (!function_exists('mime_content_type')) 
		{
		   function mime_content_type ( $f )
		   {
		       return trim ( @exec ('file -bi ' . escapeshellarg ( $f ) ) ) ;
		   }
		}		
		
         /**
          * check if such document is allowed to shown on the list
          *
          * @param string $path the path to the document
          * @return boolean
          */
         function isListingDocument($path)
         {
         	$file = basename($path);
         	if(CONFIG_SYS_PATTERN_FORMAT == 'list')
         	{// comma delimited vague file/folder name



			    		
      			if(is_dir($path))
      			{
 				$includeDir = trimlrm(CONFIG_SYS_INC_DIR_PATTERN);
				$excludeDir = trimlrm(CONFIG_SYS_EXC_DIR_PATTERN);     				
				$found_includeDir = strpos($includeDir, $file);
				$found_excludeDir = strpos($excludeDir, $file);      				
      				if((!CONFIG_SYS_INC_DIR_PATTERN || (!($found_includeDir === FALSE))) && (!CONFIG_SYS_EXC_DIR_PATTERN || (($found_excludeDir === FALSE))))
      				{
      					return true;
      				}else 
      				{
      					return false;
      				}
      			}elseif(is_file($path))
      			{
				$includeFile = trimlrm(CONFIG_SYS_INC_FILE_PATTERN);
				$excludeFile = trimlrm(CONFIG_SYS_EXC_FILE_PATTERN);            				
				$found_includeFile = strpos($includeFile, $file);
				$found_excludeFile = strpos($excludeFile, $file);	      				
      				if((!CONFIG_SYS_INC_FILE_PATTERN || (!($found_includeFile === FALSE))) && (!CONFIG_SYS_EXC_FILE_PATTERN ||   (($found_excludeFile === FALSE))))
      				{
      					return true;
      				}else 
      				{
      					return false;
      				}
      			}
         	}elseif(CONFIG_SYS_PATTERN_FORMAT == 'csv')
         	{//comma delimited file/folder name
         		
         		if(is_dir($path))
         		{
         		
	 				$includeDir = trimlrm(CONFIG_SYS_INC_DIR_PATTERN);
					$excludeDir = trimlrm(CONFIG_SYS_EXC_DIR_PATTERN);
					        
					if(!empty($includeDir) && !empty($excludeDir))
					{
						
						$validDir = explode(',', $includeDir);
						
						$invalidDir = explode(",", $excludeDir);

						if(array_search(basename($path), $validDir) !== false && array_search(basename($path), $invalidDir) === false)
						{
							return true;
						}else 
						{
							return false;
						}
					}elseif(!empty($includeDir))
					{
						$validDir = explode(',', $includeDir);
						if(array_search(basename($path), $validDir) !== false)
						{
							return true;
						}else 
						{
							return false;
						}
						
					}elseif(!empty($excludeFile))
					{
						$invalidDir = explode(",", $excludeDir);
						if(array_search(basename($path), $invalidDir) === false)
						{
							return true;
						}else 
						{
							return false;
						}
					}
					return true;
					
         		}elseif(is_file($path))
         		{
				$includeFile = trimlrm(CONFIG_SYS_INC_FILE_PATTERN);
				$excludeFile = trimlrm(CONFIG_SYS_EXC_FILE_PATTERN);   
				if(!empty($includeFile) && !empty($excludeFile))
				{
					$validFile = explode(',', $includeFile);
					$invalidFile = explode(',', $excludeFile);
					if(array_search(basename($path), $validFile) !== false && array_search(basename($path), $invalidFile) === false)
					{
						return true;
					}else 
					{
						return false;
					}
				}elseif(!empty($includeFile))
				{
					$validFile = explode(',', $includeFile);
					if(array_search(basename($path), $validFile) !== false)
					{
						return true;
					}else 
					{
						return false;
					}
				}elseif(!empty($excludeFile))
				{
					$invalidFile = explode(',', $excludeFile);
					if(array_search(basename($path), $invalidFile) === false)
					{
						return true;
					}else 
					{
						return false;
					}
				}
				return true;
         		}
         	}
         	else 
         	{//regular expression
	          	if(is_dir($path) )
	         	{
	         		if(isValidPattern(CONFIG_SYS_INC_DIR_PATTERN, $path) && !isInvalidPattern(CONFIG_SYS_EXC_DIR_PATTERN, $path))
	         		{
	         			 return true;	
	         		}else 
	         		{
	         			return false;
	         		}
	         	
	         	}elseif(is_file($path))
	         	{
	         		if(isValidPattern(CONFIG_SYS_INC_FILE_PATTERN, $path) && !isInvalidPattern(CONFIG_SYS_EXC_FILE_PATTERN, $path)  )
	         		{
	         			return true;
	         		}else 
	         		{
	         			return false;
	         		}
	         	}
         	}
         	return false;

         }		
		
		/**
		 * force to down the specified file
		 *
		 * @param string $path
		 * 
		 */
		function downloadFile($path, $newFileName=null)
		{
				if(file_exists($path) && is_file($path))
				{	
					$mimeContentType = 'application/octet-stream';
					if(function_exists('finfo_open'))
					{
						if(($fp = @finfo_open($path)))
						{
							$mimeContentType = @finfo_file($fp, basename($path));
							@finfo_close($fp);
						}
						
					}elseif(($temMimeContentType = @mime_content_type($path)) && !empty($temMimeContentType))
					{
						$mimeContentType = $temMimeContentType;
					}
					
 					
					
						

			// START ANDRE SILVA DOWNLOAD CODE
			// required for IE, otherwise Content-disposition is ignored
			if(ini_get('zlib.output_compression'))
			  ini_set('zlib.output_compression', 'Off');
			header("Pragma: public"); // required
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Cache-Control: private",false); // required for certain browsers 
			header("Content-Type: " . $mimeContentType );
			// change, added quotes to allow spaces in filenames, by Rajkumar Singh
			header("Content-Disposition: attachment; filename=\"".(is_null($newFileName)?basename($path):$newFileName)."\";" );
			header("Content-Transfer-Encoding: binary");
			header("Content-Length: ".filesize($path));
		
			readfile($path);
			exit();
			// END ANDRE SILVA DOWNLOAD CODE												
				}
		
		}

  /**
   * remove all white spaces
   *
   * @param string $hayStack 
   * @param string $whiteSpaceChars
   * @return string
   */
  function trimlrm ($hayStack, $whiteSpaceChars="\t\n\r\0\x0B")
  {
  	return str_replace($whiteSpaceChars, '', trim($hayStack));
  }		
  
  /**
   * get the parent path of the specified path
   *
   * @param string $path
   * @return string 
   */
  function getParentFolderPath($path)
  {
  	$realPath = addTrailingSlash(backslashToSlash(getRealPath($path)));
  	$parentRealPath =  addTrailingSlash(backslashToSlash(dirname($realPath)));
  	$differentPath = addTrailingSlash(substr($realPath, strlen($parentRealPath)));
  	$parentPath = substr($path, 0, strlen(addTrailingSlash(backslashToSlash($path))) - strlen($differentPath));
  	if(isUnderRoot($parentPath))
  	{
  		return $parentPath;
  	}else 
  	{
  		return CONFIG_SYS_DEFAULT_PATH;
  	}
  }
  
  function getCurrentFolderPath()
  {
  		$folderPathIndex = 'path';
  		$lastVisitedFolderPathIndex = 'ajax_last_visited_folder';
		if(isset($_GET[$folderPathIndex]) && file_exists($_GET[$folderPathIndex]) && !is_file($_GET[$folderPathIndex]) )
		{
			$currentFolderPath = $_GET[$folderPathIndex];
		}
		elseif(isset($_SESSION[$lastVisitedFolderPathIndex]) && file_exists($_SESSION[$lastVisitedFolderPathIndex]) && !is_file($_SESSION[$lastVisitedFolderPathIndex]))
		{
			$currentFolderPath = $_SESSION[$lastVisitedFolderPathIndex];
		}else
		{
			$currentFolderPath = CONFIG_SYS_DEFAULT_PATH;
		}
		
		$currentFolderPath = (isUnderRoot($currentFolderPath)?backslashToSlash((addTrailingSlash($currentFolderPath))):CONFIG_SYS_DEFAULT_PATH);
		
		//keep track of this folder path in session 
		$_SESSION[$lastVisitedFolderPathIndex] = $currentFolderPath;
		

		if(!file_exists($currentFolderPath))
		{
			die(ERR_FOLDER_NOT_FOUND . $currentFolderPath);
		}  	
  }
  
       if(!function_exists("imagerotate"))
        {
            function imagerotate($src_img, $angle, $bicubic=false)
            {
    // convert degrees to radians
    
    $angle =  (360 - $angle) + 180;
    $angle = deg2rad($angle);
   
    $src_x = imagesx($src_img);
    $src_y = imagesy($src_img);
   
    $center_x = floor($src_x/2);
    $center_y = floor($src_y/2);
   
    $rotate = imagecreatetruecolor($src_x, $src_y);
    imagealphablending($rotate, false);
    imagesavealpha($rotate, true);

    $cosangle = cos($angle);
    $sinangle = sin($angle);
   
    for ($y = 0; $y < $src_y; $y++) {
      for ($x = 0; $x < $src_x; $x++) {
    // rotate...
    $old_x = (($center_x-$x) * $cosangle + ($center_y-$y) * $sinangle)
      + $center_x;
    $old_y = (($center_y-$y) * $cosangle - ($center_x-$x) * $sinangle)
      + $center_y;
   
    if ( $old_x >= 0 && $old_x < $src_x
         && $old_y >= 0 && $old_y < $src_y ) {
      if ($bicubic == true) {
        $sY  = $old_y + 1;
        $siY  = $old_y;
        $siY2 = $old_y - 1;
        $sX  = $old_x + 1;
        $siX  = $old_x;
        $siX2 = $old_x - 1;
       
        $c1 = imagecolorsforindex($src_img, imagecolorat($src_img, $siX, $siY2));
        $c2 = imagecolorsforindex($src_img, imagecolorat($src_img, $siX, $siY));
        $c3 = imagecolorsforindex($src_img, imagecolorat($src_img, $siX2, $siY2));
        $c4 = imagecolorsforindex($src_img, imagecolorat($src_img, $siX2, $siY));
       
        $r = ($c1['red']  + $c2['red']  + $c3['red']  + $c4['red']  ) << 14;
        $g = ($c1['green'] + $c2['green'] + $c3['green'] + $c4['green']) << 6;
        $b = ($c1['blue']  + $c2['blue']  + $c3['blue']  + $c4['blue'] ) >> 2;
        $a = ($c1['alpha']  + $c2['alpha']  + $c3['alpha']  + $c4['alpha'] ) >> 2;
        $color = imagecolorallocatealpha($src_img, $r,$g,$b,$a);
      } else {
        $color = imagecolorat($src_img, $old_x, $old_y);
      }
    } else {
          // this line sets the background colour
      $color = imagecolorallocatealpha($src_img, 255, 255, 255, 127);
    }
    imagesetpixel($rotate, $x, $y, $color);
      }
    }
    return $rotate;          	
/*                $src_x = @imagesx($src_img);
                $src_y = @imagesy($src_img);
                if ($angle == 180)
                {
                    $dest_x = $src_x;
                    $dest_y = $src_y;
                }
                elseif ($src_x <= $src_y)
                {
                    $dest_x = $src_y;
                    $dest_y = $src_x;
                }
                elseif ($src_x >= $src_y) 
                {
                    $dest_x = $src_y;
                    $dest_y = $src_x;
                }     
		 		if(function_exists('ImageCreateTrueColor'))
		 		{
					$rotate = @ImageCreateTrueColor($dst_w,$dst_h);
				} else {
					$rotate = @ImageCreate($dst_w,$dst_h);
				}                   
                @imagealphablending($rotate, false);
               
                switch ($angle)
                {
                    case 270:
                        for ($y = 0; $y < ($src_y); $y++)
                        {
                            for ($x = 0; $x < ($src_x); $x++)
                            {
                                $color = imagecolorat($src_img, $x, $y);
                                imagesetpixel($rotate, $dest_x - $y - 1, $x, $color);
                            }
                        }
                        break;
                    case 90:
                        for ($y = 0; $y < ($src_y); $y++)
                        {
                            for ($x = 0; $x < ($src_x); $x++)
                            {
                                $color = imagecolorat($src_img, $x, $y);
                                imagesetpixel($rotate, $y, $dest_y - $x - 1, $color);
                            }
                        }
                        break;
                    case 180:
                        for ($y = 0; $y < ($src_y); $y++)
                        {
                            for ($x = 0; $x < ($src_x); $x++)
                            {
                                $color = imagecolorat($src_img, $x, $y);
                                imagesetpixel($rotate, $dest_x - $x - 1, $dest_y - $y - 1, $color);
                            }
                        }
                        break;
                    default: $rotate = $src_img;
                };
                return $rotate;*/
            }
        }  
?>
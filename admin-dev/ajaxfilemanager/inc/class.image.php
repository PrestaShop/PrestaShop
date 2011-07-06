<?php
	/**
	 * this class provide functions to edit an image, e.g. resize, rotate, flip, crop
	 * @author Logan Cai cailongqun [at] yahoo [dot] com [dot] cn
	 * @link  www.phpletter.com
	 * @version 0.9
	 * @since 14/May/2007
	 * @name Image
	 * 
	 */
	
	
	
	
	class Image
	{
		var $_debug = false; 
		var $_errors = array();
		var $gdInfo = array(); //keep all information of GD extension
		var $_imgOrig = null; //the hanlder of original image
		var $_imgFinal = null; //the handler of final image
		var $imageFile  = null;  
    var $transparentColorRed = null;
    var $transparentColorGreen = null;
    var $transparentColorBlue = null;		 
    var $chmod = 0755;
    var $_imgInfoOrig = array(
    	'name'=>'',
    	'ext'=>'',
    	'size'=>'',
    	'width'=>'',
    	'height'=>'',
    	'type'=>'',
    	'path'=>'',
    );    
    var $_imgInfoFinal = array(
    	'name'=>'',
    	'ext'=>'',
    	'size'=>'',
    	'width'=>'',
    	'height'=>'', 
    	'type'=>'',   
    	'path'=>'',
    );		
		var $_imgQuality = 90;
		/**
		 * constructor
		 *
		 * @param boolean $debug
		 * @return Image
		 */
		
		function __construct($debug = false)
		{
			$this->enableDebug($debug);
			$this->gdInfo = $this->getGDInfo();			
		}
		function Image($debug = false)
		{
			$this->__construct($debug);
		}
		/**
		 * enable to debug
		 *
		 * @param boolean $value
		 */
		function enableDebug($value)
		{
			$this->_debug = ($value?true:false);
		}
		/**
		 * check if debug enable
		 * @return boolean
		 */
		function _isDebugEnable()
		{
			return $this->_debug;
		}

	    /**
		 * append to errors array and shown the each error when the debug turned on  
		 * 
		 * @param  string $string
		 * @return void
     * @access private
     * @copyright this function originally come from Andy's php 
	 */
    function _debug($value)
    {
    		$this->_errors[] = $value;
        if ($this->_debug) 
        {
            echo $value . "<br />\n";
        }
    }		
    /**
     * show erros
     *
     */
    function showErrors()
    {
    	if(sizeof($this->_errors))
    	{
    		foreach($this->_errors as $error)
    		{
    			echo $error . "<br />\n";
    		}
    	}
    }
    /**
     * Load an image from the file system.
     * 
     * @param  string $filename
     * @return bool 
     * @access public
     * @copyright this function originally come from Andy's php 
     */
    function loadImage($filename)
    {
        $ext  = strtolower($this->_getExtension($filename));
        $func = 'imagecreatefrom' . ($ext == 'jpg' ? 'jpeg' : $ext);
        if (!$this->_isSupported($filename, $ext, $func, false)) {
            return false;
        }
        if($ext == "gif")
        {
             // the following part gets the transparency color for a gif file
            // this code is from the PHP manual and is written by
            // fred at webblake dot net and webmaster at webnetwizard dotco dotuk, thanks!
            $fp = @fopen($filename, "rb");
            $result = @fread($fp, 13);
            $colorFlag = ord(substr($result,10,1)) >> 7;
            $background = ord(substr($result,11));
            if ($colorFlag) {
                $tableSizeNeeded = ($background + 1) * 3;
                $result = @fread($fp, $tableSizeNeeded);
                $this->transparentColorRed = ord(substr($result, $background * 3, 1));
                $this->transparentColorGreen = ord(substr($result, $background * 3 + 1, 1));
                $this->transparentColorBlue = ord(substr($result, $background * 3 + 2, 1));
            }
            fclose($fp);
            // -- here ends the code related to transparency handling   	
        }
        $this->_imgOrig = @$func($filename);
        if ($this->_imgOrig == null) {
            $this->_debug("The image could not be created from the '$filename' file using the '$func' function.");
            return false;
        }else 
        {
        	$this->imageFile = $filename;
			    $this->_imgInfoOrig = array(
			    	'name'=>basename($filename),
			    	'ext'=>$ext,
			    	'size'=>filesize($filename),
			    	'path'=>$filename,
			    );        	
			    $imgInfo = $this->_getImageInfo($filename);
			    if(sizeof($imgInfo))
			    {
			    	foreach($imgInfo as $k=>$v)
			    	{
			    		$this->_imgInfoOrig[$k] = $v;
			    		$this->_imgInfoFinal[$k] = $v;
			    	}
			    }
			    
        }
        return true;
    }

    /**
     * Load an image from a string (eg. from a database table)
     * 
     * @param  string $string
     * @return bool 
     * @access public
     * @copyright this function originally come from Andy's php 
     */
    function loadImageFromString($string)
    {
    		$this->imageFile = $filename;
        $this->_imgOrig = imagecreatefromstring($string);
        if (!$this->_imgOrig) {
            $this->_debug('The image (supplied as a string) could not be created.');
            return false;
        }
        return true;
    }		
    

    /**
     * Save the modified image
     * 
     * @param  string $filename 
     * @param  int    $quality 
     * @param  string $forcetype 
     * @return bool 
     * @access public
     * @copyright this function originally come from Andy's php 
     */
    function saveImage($filename, $quality = 90, $forcetype = '')
    {
        if ($this->_imgFinal == null) {
            $this->_debug('No changes intend to be made.');
            return false;
        }

        $ext  = ($forcetype == '') ? $this->_getExtension($filename) : strtolower($forcetype);
        $func = 'image' . ($ext == 'jpg' ? 'jpeg' : $ext);
        if (!$this->_isSupported($filename, $ext, $func, true)) 
        {
            return false;
        }
        $saved = false;
        switch($ext) 
        {
            case 'gif':
                if ($this->gdInfo['Truecolor Support'] && imageistruecolor($this->_imgFinal)) 
                {
                    imagetruecolortopalette($this->_imgFinal, false, 255);
                }
            case 'png':
                $saved = $func($this->_imgFinal, $filename);
                break;
            case 'jpg':
                $saved = $func($this->_imgFinal, $filename, $quality);
                break;
        }

        if ($saved === false) 
        {
            $this->_debug("The image could not be saved to the '$filename' file as the file type '$ext' using the '$func' function.");
            return false;
        }else 
        {
        	$this->_imgInfoFinal['size'] = @filesize($filename);
        	@chmod($filename, intval($this->chmod, 8));
        }

        return true;
    }    
    /**
     * Shows the masked image without any saving
     * 
     * @param  string $type 
     * @param  int    $quality 
     * @return bool 
     * @access public
     * @copyright this function originally come from Andy's php 
     */
    function showImage($type = '', $quality = '')
    {
        if ($this->_imgFinal == null) {
            $this->_debug('There is no cropped image to show.');
            return false;
        }
        $type = (!empty($type)?$type:$this->_imgInfoOrig['ext']);
        $quality = (!empty($quality)?$quality:$this->_imgQuality);
				
        $type = strtolower($type);
        $func = 'image' . ($type == 'jpg' ? 'jpeg' : $type);
        $head = 'image/' . ($type == 'jpg' ? 'jpeg' : $type);
        
        if (!$this->_isSupported('[showing file]', $type, $func, false)) {
            return false;
        }

        header("Content-type: $head");
        switch($type) 
        {
            case 'gif':
                if ($this->gdInfo['Truecolor Support'] && imageistruecolor($this->_imgFinal)) 
                {
                    @imagetruecolortopalette($this->_imgFinal, false, 255);
                }
            case 'png':
                $func($this->_imgFinal);
                break;
            case 'jpg':
                $func($this->_imgFinal, '', $quality);
                break;
        }
        return true;
    }    
    
    /**
	 * Used for cropping image
	 * 
	 * @param  int $dst_x
	 * @param  int $dst_y
	 * @param  int $dst_w
	 * @param  int $dst_h
	 * @return bool
     * @access public
     * @copyright this function originally come from Andy's php 
	 */  
    function crop($dst_x, $dst_y, $dst_w, $dst_h)
    {
        if ($this->_imgOrig == null) {
            $this->_debug('The original image has not been loaded.');
            return false;
        }
        if (($dst_w <= 0) || ($dst_h <= 0)) {
            $this->_debug('The image could not be cropped because the size given is not valid.');
            return false;
        }
        if (($dst_w > imagesx($this->_imgOrig)) || ($dst_h > imagesy($this->_imgOrig))) {
            $this->_debug('The image could not be cropped because the size given is larger than the original image.');
            return false;
        }
        $this->_createFinalImageHandler($dst_w, $dst_h);
        if ($this->gdInfo['Truecolor Support']) 
        {
            	if(!@imagecopyresampled($this->_imgFinal, $this->_imgOrig, 0, 0, $dst_x, $dst_y, $dst_w, $dst_h, $dst_w, $dst_h))
            	{
            		$this->_debug('Unable crop the image.');
            		return false;
            	}            
        } else 
        {
          	if(!@imagecopyresized($this->_imgFinal, $this->_imgOrig, 0, 0, $dst_x, $dst_y, $dst_w, $dst_h, $dst_w, $dst_h))
          	{
           		$this->_debug('Unable crop the image.');
          		return false;           		
          	}
            
        }
        $this->_imgInfoFinal['width'] = $dst_w;
        $this->_imgInfoFinal['height'] = $dst_h;   
        return true; 	
    }
  
    
	/**
     * Resize the Image in the X and/or Y direction
     * If either is 0 it will be scaled proportionally
     *
     * @access public
     *
     * @param mixed $new_x 
     * @param mixed $new_y 
     * @param boolean $constraint keep to resize the image proportionally
     * @param boolean $unchangeIfsmaller keep the orignial size if the orignial smaller than the new size
     * 
     *
     * @return mixed none or PEAR_error
     */
	function resize( $new_x, $new_y, $constraint= false, $unchangeIfsmaller=false)
	{
		if(!$this->_imgOrig)
		{
			$this->_debug('No image fould.');
			return false;
		}		
		
		$new_x = (int)($new_x);
		$new_y = (int)($new_y);
		if($new_x <=0 || $new_y <= 0)
		{
			$this->_debug('either of new width or height can be zeor or less.');
		}else 
		{
		
			if($constraint)
			{
				if($new_x < 1 && $new_y < 1)
				{
					$new_x = $this->_imgInfoOrig['width'];
					$new_y = $this->_imgInfoOrig['height'];
				}elseif($new_x < 1)
				{
					$new_x = floor($new_y / $this->_imgInfoOrig['height'] * $this->_imgInfoOrig['width']);
	
				}elseif($new_y < 1)
				{
					$new_y = floor($new_x / $this->_imgInfoOrig['width'] * $this->_imgInfoOrig['height']);
				}else
				{
					$scale = min($new_x/$this->_imgInfoOrig['width'], $new_y/$this->_imgInfoOrig['height']) ;
					$new_x = floor($scale*$this->_imgInfoOrig['width']);
					$new_y = floor($scale*$this->_imgInfoOrig['height']);
				}						
			}
			if($unchangeIfsmaller)
			{
				if($this->_imgInfoOrig['width'] < $new_x && $this->_imgInfoOrig['height'] < $new_y )
				{
					$new_x = $this->_imgInfoOrig['width'];
					$new_y = $this->_imgInfoOrig['height'];
				}
			}
		
			
			
			if(is_null($this->_imgOrig))
			{
				$this->loadImage($filePath);
			}
			if(sizeof($this->_errors) == 0)
			{
				return $this->_resize($new_x, $new_y);
			}			
		}

		return false;
		
	} // End resize    
 	/**
     * resize the image and return the thumbnail image  details array("width"=>, "height"=>, "name")
     *
     * @param string $fileName 
     * @param int $new_x the thumbnail width
     * @param int $new_y the thumbnail height
     * @param string $mode can be save, view and both
     * @return unknown
     */
	function _resize( $new_x, $new_y) 
	{
		$this->_createFinalImageHandler($new_x, $new_y);
    // hacks fot transparency of png24 files
    if ($this->_imgInfoOrig['type'] == 'png') 
    {    
        @imagealphablending($this->_imgFinal, false);
				if(function_exists('ImageCopyResampled'))
				{
					@ImageCopyResampled($this->_imgFinal, $this->_imgOrig, 0, 0, 0, 0, $new_x, $new_y, $this->_imgInfoOrig['width'], $this->_imgInfoOrig['height']);
				} else {
					@ImageCopyResized($this->_imgFinal, $this->_imgOrig, 0, 0, 0, 0, $new_x, $new_y, $this->_imgInfoOrig['width'], $this->_imgInfoOrig['height']);
				} 
        @imagesavealpha($this->_imgFinal, true);

    }else 
    {//for the rest image
			if(function_exists('ImageCopyResampled'))
			{
				@ImageCopyResampled($this->_imgFinal, $this->_imgOrig, 0, 0, 0, 0, $new_x, $new_y, $this->_imgInfoOrig['width'], $this->_imgInfoOrig['height']);
			} else {
				@ImageCopyResized($this->_imgFinal, $this->_imgOrig, 0, 0, 0, 0, $new_x, $new_y, $this->_imgInfoOrig['width'], $this->_imgInfoOrig['height']);
			}    	
    }

		
		$this->_imgInfoFinal['width'] = $new_x;
		$this->_imgInfoFinal['height'] = $new_y;
		$this->_imgInfoFinal['name'] = basename($this->_imgInfoOrig['name']);
		$this->_imgInfoFinal['path'] = $this->_imgInfoOrig['path'];		
		if($this->_imgFinal)
		{
			return true;
		}else 
		{			
			$this->_debug('Unable to resize the image on the fly.');
			return false;
							
		}

	}   
    /**
	 * Get the extension of a file name
	 * 
	 * @param  string $file
 	 * @return string
     * @copyright this function originally come from Andy's php 
	 */
    function _getExtension($file)
    {
        $ext = '';
        if (strrpos($file, '.')) {
            $ext = strtolower(substr($file, (strrpos($file, '.') ? strrpos($file, '.') + 1 : strlen($file)), strlen($file)));
        }
        return $ext;
    }

	    /**
		 * Validate whether image reading/writing routines are valid.
		 * 
		 * @param  string $filename
		 * @param  string $extension
		 * @param  string $function
		 * @param  bool   $write
		 * @return bool
     * @access private
     * @copyright this function originally come from Andy's php 
	 */
    function _isSupported($filename, $extension, $function, $write = false)
    {

       $giftype = ($write) ? ' Create Support' : ' Read Support';
        $support = strtoupper($extension) . ($extension == 'gif' ? $giftype : ' Support');

        if (!isset($this->gdInfo[$support]) || $this->gdInfo[$support] == false) {
            $request = ($write) ? 'saving' : 'reading';
            $this->_debug("Support for $request the file type '$extension' cannot be found.");
            return false;
        }
        if (!function_exists($function)) {
            $request = ($write) ? 'save' : 'read';
            $this->_debug("The '$function' function required to $request the '$filename' file cannot be found.");
            return false;
        }

        return true;
    }
    /**
     * flip image horizotally or vertically
     *
     * @param string $direction
     * @return boolean
     */
    function flip($direction="horizontal")
    {
				$this->_createFinalImageHandler($this->_imgInfoOrig['width'], $this->_imgInfoOrig['height']);
			if($direction != "vertical")
			{
				$dst_x = 0;
				$dst_y = 0;
				$src_x = $this->_imgInfoOrig['width'] -1;
				$src_y = 0;
				$dst_w = $this->_imgInfoOrig['width'];
				$dst_h = $this->_imgInfoOrig['height'];
				$src_w = 0 - $this->_imgInfoOrig['width'];
				$src_h = $this->_imgInfoOrig['height'];
				
			}else 
			{
				$dst_x = 0;
				$dst_y = 0;
				$src_x = 0;
				$src_y = $this->_imgInfoOrig['height'] - 1;
				$dst_w = $this->_imgInfoOrig['width'];
				$dst_h = $this->_imgInfoOrig['height'];
				$src_w = $this->_imgInfoOrig['width'];
				$src_h = 0 - $this->_imgInfoOrig['height'];				
			}			
				if(function_exists('ImageCopyResampled')){
					ImageCopyResampled($this->_imgFinal, $this->_imgOrig, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h);
				} else {
					ImageCopyResized($this->_imgFinal, $this->_imgOrig, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h);
				}
				$this->_imgInfoFinal['width'] = $dst_w;
				$this->_imgInfoFinal['height'] = $dst_h;
				$this->_imgInfoFinal['name'] = basename($this->imageFile);
				$this->_imgInfoFinal['path'] = $this->imageFile;		
				if($this->_imgFinal)
				{
					return true;
				}else 
				{			
					$this->_debug('Unable to resize the image on the fly.');	
					return false;
								
				}   	
    }
    /**
     * flip vertically
     *
     * @return boolean
     */
    function flipVertical()
    {
    	return $this->flip('vertical');
    }
    /**
     * flip horizontal
     *
     * @return string
     */
    function flipHorizontal()
    {
    	return $this->flip('horizontal');
    }


    /**
     * get the GD version information
     *
     * @param  bool $versionOnly
     * @return array
     * @access private
     * @copyright this function originally come from Andy's php 
     */
    function getGDInfo($versionOnly = false)
    {
        $outputs = array();
        if (function_exists('gd_info')) 
        {
            $outputs = gd_info();
        } else 
        {
            $gd = array(
                    'GD Version'         => '',
                    'GIF Read Support'   => false,
                    'GIF Create Support' => false,
                    'JPG Support'        => false,
                    'PNG Support'        => false,
                    'FreeType Support'   => false,
                    'FreeType Linkage'   => '',
                    'T1Lib Support'      => false,
                    'WBMP Support'       => false,
                    'XBM Support'        => false       
                    );
            ob_start();
            phpinfo();
            $buffer = ob_get_contents();
            ob_end_clean();
            foreach (explode("\n", $buffer) as $line) {
                $line = array_map('trim', (explode('|', strip_tags(str_replace('</td>', '|', $line)))));
                if (isset($gd[$line[0]])) {
                    if (strtolower($line[1]) == 'enabled') {
                        $gd[$line[0]] = true;
                    } else {
                        $gd[$line[0]] = $line[1];
                    }
                }
            }
            $outputs = $gd;
        }

        if (isset($outputs['JIS-mapped Japanese Font Support'])) {
            unset($outputs['JIS-mapped Japanese Font Support']);
        }
        if (function_exists('imagecreatefromgd')) {
            $outputs['GD Support'] = true;
        }
        if (function_exists('imagecreatefromgd2')) {
            $outputs['GD2 Support'] = true;
        }
        if (preg_match('/^(bundled|2)/', $outputs['GD Version'])) {
            $outputs['Truecolor Support'] = true;
        } else {
            $outputs['Truecolor Support'] = false;
        }
        if ($outputs['GD Version'] != '') {
            $match = array();
            if (preg_match('/([0-9\.]+)/', $outputs['GD Version'], $match)) {
                $foo = explode('.', $match[0]);
                $outputs['Version'] = array('major' => isset($foo[0])?$foo[0]:'', 'minor' => isset($foo[1])?$foo[1]:'', 'patch' => isset($foo[2])?$foo:"");
            }
        }

        return ($versionOnly) ? $outputs['Version'] : $outputs;
    }    
    
    /**
	 * Destroy the resources used by the images.
	 * 
	 * @param  bool $original
	 * @return void
     * @access public
     * @copyright this function originally come from Andy's php 
	 */
    function DestroyImages($original = true)
    {
    		if(!is_null($this->_imgFinal))
    		{
    			@imagedestroy($this->_imgFinal);
    		}        
        $this->_imgFinal = null;
        if ($original && !is_null($this->_imgOrig)) {
            @imagedestroy($this->_imgOrig);
            $this->_imgOrig = null;
        }
    } 
    
	function getImageInfo($imagePath)
	{
		return $this->_getImageInfo($imagePath);
	}
	/**
     * get image information, e.g. width, height, type
     * @access public
     * @return array
     */
	function _getImageInfo($imagePath)
	{
		$outputs = array();
		$imageInfo = @GetImageSize($imagePath);
		if ($imageInfo && is_array($imageInfo))
		{
			switch($imageInfo[2]){
				case 1:
					$type = 'gif';
					break;
				case 2:
					$type = 'jpeg';
					break;
				case 3:
					$type = 'png';
					break;
				case 4:
					$type = 'swf';
					break;
				case 5:
					$type = 'psd';
				case 6:
					$type = 'bmp';
				case 7:
				case 8:
					$type = 'tiff';
				default:
					$type = '';
			}
			$outputs['width'] = $imageInfo[0];
			$outputs['height'] = $imageInfo[1];
			$outputs['type'] = $type;
			$outputs['ext'] = $this->_getExtension($imagePath);
		} else {
			$this->_debug('Unable locate the image or read images information.');
		}
		return $outputs;
		
	}
	  function rotate($angle, $bgColor=0)
    {
    	$angle = (int)($angle) -360;
    		while($angle <0)
    		{
    			$angle += 360;
    		}
 
		
         if($this->_imgFinal = imagerotate($this->_imgOrig, $angle))
         {
         	return true;
         }else 
         {
         	return false;
         }
 
       
    }
	/**
	 * get the original image info
	 *
	 * @return array
	 */
	function getOriginalImageInfo()
	{
		return $this->_imgInfoOrig;
	}
	/**
	 * return the final image info
	 *
	 * @return array
	 */
	function getFinalImageInfo()
	{
		if($this->_imgInfoFinal['width'] == '')
		{
			if(is_null($this->_imgFinal))
			{
				$this->_imgInfoFinal = $this->_imgInfoOrig;
			}else 
			{
				$this->_imgInfoFinal['width'] = @imagesx($this->_imgFinal);
				$this->_imgInfoFinal['height'] = @imagesy($this->_imgFinal);
			}
		}
		return $this->_imgInfoFinal;
	}
	
    /**
     *  create final image handler
     *
     *  @access private
     *  @param $dst_w width
     * 	@param $dst_h height
     * 	@return boolean
     * 	@copyright original from noname at nivelzero dot ro
     */
    function _createFinalImageHandler($dst_w, $dst_h)
    {
		 		if(function_exists('ImageCreateTrueColor'))
		 		{
					$this->_imgFinal = @ImageCreateTrueColor($dst_w,$dst_h);
				} else {
					$this->_imgFinal = @ImageCreate($dst_w,$dst_h);
				}   
        if (!is_null($this->transparentColorRed) && !is_null($this->transparentColorGreen) && !is_null($this->transparentColorBlue)) {
        
            $transparent = @imagecolorallocate($targetImageIdentifier, $this->transparentColorRed, $this->transparentColorGreen, $this->transparentColorBlue);
            @imagefilledrectangle($this->_imgFinal, 0, 0, $dst_w, $dst_h, $transparent);
            @imagecolortransparent($this->_imgFinal, $transparent);            
        }
        
    }	
	}
	
?>

<?php
	/**
	 * This class provide all file upload functionalities
	 * @author Logan Cai (cailongqun [at] yahoo [dot] com [dot] cn)
	 * @link www.phpletter.com
	 * @since 22/April/2007
	 *
	 */
class Upload
{
	var $fileType = ""; //the file type
	var $originalFileName = "";
	var $fileName = ""; //the file final name
	var $fileExtension = "";
	var $img_x = 0;
	var $img_y = 0;
	var $img_new_x = 0;
	var $img_new_y = 0;
	var $imgHandler = null;
	var $fileBaseName = ""; //file name without the file extension and .
	var $filePath = ""; //the file path which the file uploaded to
	var $fileSize = 0;
	var $validImageExts = array("gif", "jpg", "png");
	var $errors = array();
	var $_value  = null;  //an array holding the uploaded file details
	var $dirPath = "";
	var $invalidFileExt = array(); //var $invalidFileExt = array('php,inc,asp,aspx');
	var $errCode = "";
	var $safeMode;
	var $uploadFileMode = 0755;
	var $errorCodes = array(
		0=>'the file uploaded with success',
		1=>'The uploaded file exceeds the upload_max_filesize directive in php.ini',
		2=>'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form',
		3=>'The uploaded file was only partially uploaded',
		4=>'No file was uploaded.',
		6=>'Missing a temporary folder',
		7=>'Failed to write file to disk',
		8=>'File upload stopped by extension',
		999=>'No error code avaiable',
	);
	

	function Upload()
	{
		//doing nothing
	}
	
	function isFileUploaded($indexInPost="file")
	{
		
		$this->errCode = isset($_FILES[$indexInPost]['error'])?$_FILES[$indexInPost]['error']:999;
		if((isset($_FILES[$indexInPost]['error']) && $_FILES[$indexInPost] == 0) ||
		(!empty($_FILES[$indexInPost]['tmp_name']) && $_FILES[$indexInPost]['tmp_name'] != 'none')
		)
		{
			$this->_value = $_FILES[$indexInPost];
			$this->fileSize = @filesize($this->_value['tmp_name']);
			$this->originalFileName = $this->_value['name'];
			$this->fileType = $this->_value['type'];
			
			return true;
		}else 
		{
			
			array_push($this->errors, 'Unable to upload file');
			return false;
		}		
	}
	
	function getErrorCodeMsg()
	{
		return (isset($this->errorCodes[$this->errCode])?$this->errorCodes[$this->errCode]:"");
	}
	/**
	 * check if the uploaded file extension is allowed against the validFile Extension
	 * or against the invalid extension list when the list of valid file extension is not set
	 *
	 * @param array $validFileExt
	 * @return boolean
	 */
	function isPermittedFileExt($validFileExt = array())
	{
		$tem = array();

		if(sizeof($validFileExt))
		{
			foreach($validFileExt as $k=>$v)
			{
				$tem[$k] = strtolower(trim($v));
			}
		}
		$validFileExt = $tem;

		if(sizeof($validFileExt) && sizeof($this->invalidFileExt))
		{
			foreach($validFileExt as  $k=>$ext)
			{
				if(array_search(strtolower($ext), $this->invalidFileExt) !== false)
				{
					unset($validFileExt[$k]);
				}
			}
		}
		
	

		
		if(sizeof($validFileExt))
		{
			if(array_search(strtolower($this->getFileExt()), $validFileExt) !== false)
			{
				return true;
			}
		}elseif(array_search(strtolower($this->getFileExt()), $this->invalidFileExt) === false)
		{
			return true;
		}

		
		$this->deleteUploadedFile();
		return false;
		
	}
	/**
	 * check if the uploaded file size is too big
	 *
	 * @param integer $maxSize
	 */
	function isSizeTooBig($maxSize="")
	{
		if($this->fileSize > $maxSize)
		{
			$this->deleteUploadedFile();
			return true;
		}else 
		{
			return false;
		}
	}
	/**
	 * set the invali file extensions
	 *
	 * @param array $invalidFileExt
	 */
	function setInvalidFileExt($invalidFileExt=array())
	{
		$tem = array();
		if(sizeof($invalidFileExt))
		{
			foreach($invalidFileExt as $k=>$v)
			{
				$tem[$k]= strtolower(trim($v));
			}
		}
		
		$this->invalidFileExt = $tem;
	}
	/**
	 * get file type
	 *
	 * @return string
	 */
	function getFileType()
	{
		return $this->fileType;
	}
	/**
	 * get a file extension
	 *
	 * @param string $fileName the path to a file or just the file name
	 */	
	function getFileExt()
	{
		//return strtolower(substr(strrchr($this->fileName, "."), 1));
		return substr(strrchr($this->originalFileName, "."), 1);
	}
	/**
		 * move the uploaded file to a specific location
		 *
		 * @param string $dest  the path to the directory which the uploaded file will be moved to
		 * @param string $fileBaseName the base name which the uploaded file will be renamed to
		 * @param unknown_type $overwrite
		 * @return unknown
		 */
	function moveUploadedFile($dest, $fileBaseName = '', $overwrite=false)
	{

		//ensure the directory path ending with /
		if ($dest != ''  && substr($dest, -1) != '/') {
			$dest .= '/';
		}
		$this->dirPath = $dest;
		$fileName =  basename($this->_value['name']);

		$dotIndex = strrpos($fileName, '.');
		$this->fileExtension = '';
		if(is_int($dotIndex))
		{
			$this->fileExtension = substr($fileName, $dotIndex);
			$this->fileBaseName = substr($fileName, 0, $dotIndex);
		}
		if(!empty($fileBaseName))
		{
			$this->fileBaseName = $fileBaseName;
		}
		$fileName = $this->fileBaseName . $this->fileExtension;
		$filePath = $dest . $fileName;

		if(!$overwrite && file_exists($filePath) && is_file($filePath))
		{//rename

			$counter = 0;
			while(file_exists($dest.$fileName) && is_file($dest .$fileName))
			{
				$counter++;
				$fileName = $this->fileBaseName.'_'.$counter.$this->fileExtension;
			}
			$this->fileBaseName .= "_" . $counter;

		}
		if (@move_uploaded_file($this->_value['tmp_name'], $dest . $fileName)) {
			@chmod($dest . $fileName, $this->uploadFileMode);
			$this->fileName = $fileName;
			$this->filePath = $dest . $fileName;
			return true;
		} else {
			return false;
		}
	}


	/**
		 * check if the uploaded is permitted to upload
		 *
		 * @param mixed $invalidImageExts invalid image extension
		 * @param bool $delete force to delete the uploaded file
		 */	
	function isImage($invalidImageExts = array(), $delete = true)
	{
		if(!is_array($invalidImageExts) && !empty($invalidImageExts))
		{
			$invalidImageExts = explode(",", $invalidImageExts);
		}
		foreach ($invalidImageExts as $k=>$v)
		{
			$invalidImageExts[$k] = strtolower(trim($v));
		}
		foreach ($this->validImageExts as $k=>$v)
		{
			$ValidImageExts[$k] = strtolower(trim($v));
		}
		if(sizeof($invalidImageExts))
		{
			foreach ($ValidImageExts as $k=>$v)
			{
				if(array_search(strtolower($v), $invalidImageExts) !== false)
				{
					unset($ValidImageExts[$k]);
				}
			}
		}
		if(array_search(strtolower($this->getFileExt()), $ValidImageExts)!==false)
		{
			$this->_get_image_details($this->filePath);
			if(!empty($this->fileType))
			{
				return true;
			}
		}else
		{
			if($delete)
			{
				$this->deleteUploadedFile();
			}
		}

		array($this->errors, "This file is not a image type file.");
		return false;
	}

	/**
     * Resize the Image in the X and/or Y direction
     * If either is 0 it will be scaled proportionally
     *
     * @access public
     *
     * @param mixed $new_x 
     * @param mixed $new_y 
     * @param string $thumb_suffix
     *
     * @return mixed none or PEAR_error
     */
	function resize($filePath, $thumb_suffix="", $new_x = 0, $new_y = 0)
	{
		
		if(empty($filePath))
		{
			$filePath = $this->dirPath . $this->fileBaseName . $thumb_suffix  . $this->fileExtension;
		}
		// 0 means keep original size
		if ($this->img_x > $this->img_y)
		$new_y = (int)($new_y/$this->img_x*$this->img_y);
		else if ($this->img_y > $this->img_x)
		$new_x = (int)($new_x/$this->img_y*$this->img_x);
		// Now do the library specific resizing.
		return $this->_resize($filePath,$new_x, $new_y);
	} // End resize

	/**
     * resize the image and return the thumbnail image  details array("width"=>, "height"=>, "name")
     *
     * @param string $fileName 
     * @param int $new_x the thumbnail width
     * @param int $new_y the thumbnail height
     * @return unknown
     */
	function _resize($fileName, $new_x, $new_y) {
		$functionName = 'ImageCreateFrom' . $this->fileType;


		if(function_exists($functionName))
		{
			$this->imgHandler = $functionName($this->filePath);
		}else
		{
			array_push($this->errors, $functionName . " function is unavailable");
			return false;
		}

		if(function_exists('ImageCreateTrueColor')){
			$new_img =ImageCreateTrueColor($new_x,$new_y);
		} else {
			$new_img =ImageCreate($new_x,$new_y);
		}
		if(function_exists('ImageCopyResampled')){
			ImageCopyResampled($new_img, $this->imgHandler, 0, 0, 0, 0, $new_x, $new_y, $this->img_x, $this->img_y);
		} else {
			ImageCopyResized($new_img, $this->imgHandler, 0, 0, 0, 0, $new_x, $new_y, $this->img_x, $this->img_y);
		}
		if($this->_imageSave($new_img, $fileName, 80))
		{
			return array("width"=>$new_x, "height"=>$new_y, "name"=>basename($fileName));
		}else
		{

			array_push($this->errors, "Unable to resize the image");
			return false;
		}

	}
	/**
		 * save the thumbnail file and destroy the opened image
		 *
		 * @param resource $newImageHandler
		 * @param string $fileName
		 * @param int $quality
		 * @return boolean
		 */
	function _imageSave($newImageHandler, $fileName, $quality = 90)
	{
		$functionName = 'image' . $this->fileType;
		if($functionName($newImageHandler, $fileName, $quality))
		{
			imagedestroy($newImageHandler);
			return true;
		}else
		{
			imagedestroy($newImageHandler);
			array_push($this->errors, "Unable to save the thumbnail file.");
			return false;
		}

	}
	/**
     *
     * @access public
     * @return void
     */
	function _get_image_details($image)
	{

		//echo $image;
		$data = @GetImageSize($image);
		#1 = GIF, 2 = JPG, 3 = PNG, 4 = SWF, 5 = PSD, 6 = BMP, 7 = TIFF(intel byte order), 8 = TIFF(motorola byte order,
		# 9 = JPC, 10 = JP2, 11 = JPX, 12 = JB2, 13 = SWC
		if (is_array($data)){
			switch($data[2]){
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
					array_push($this->errors, "We do not recognize this image format");

			}
			$this->img_x = $data[0];
			$this->img_y = $data[1];
			$this->fileType = $type;

			return true;
		} else {
			array_push($this->errors, "Cannot fetch image or images details.");
			return null;
		}
	}
	/**
	 * caculate the thumbnail details from the original image file 
	 *
	 * @param string $originalImageName  
	 * @param int $originaleImageWidth
	 * @param int $originalImageHeight
	 * @param string $thumbnailSuffix
	 * @param int $thumbnailWidth
	 * @param int $thumbnailHeight
	 * @return array array("name"=>"image name", "width"=>"image width", "height"=>"image height")
	 */
	function getThumbInfo($originalImageName, $originaleImageWidth, $originalImageHeight, $thumbnailSuffix, $thumbnailWidth, $thumbnailHeight)
	{
		$outputs = array("name"=>"", "width"=>0, "height"=>0);
		$thumbnailWidth	= (int)($thumbnailWidth);
		$thumbnailHeight = (int)($thumbnailHeight);
		if(!empty($originalImageName) && !empty($originaleImageWidth) && !empty($originalImageHeight))
		{
			$dotIndex = strrpos($originalImageName, '.');
			//begin to get the thumbnail image name
			$fileExtension = '';
			$fileBaseName = '';
			if(is_int($dotIndex))
			{
				$fileExtension = substr($originalImageName, $dotIndex);
				$fileBaseName = substr($originalImageName, 0, $dotIndex);
			}
			$outputs['name'] = $fileBaseName . $thumbnailSuffix . $fileExtension;
			//start to get the thumbnail width & height
			if($thumbnailWidth < 1 && $thumbnailHeight < 1)
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
     * get the uploaded file
     */
	function deleteUploadedFile()
	{
		@unlink($this->filePath);
	}
	/**
	 * destroy the tmp file
	 *
	 */
	function finish()
	{
		@unlink($this->_value['tmp_name']);
	}
	
	function displayError()
	{
		if(sizeof($this->errors))
		{
			echo "<pre>";
			print_r($this->errors);
			echo "</pre>";
		}
	}
	/**
	 * get the path which the file uploaded to
	 *
	 */
	function getFilePath()
	{
		return $this->filePath;
	}
	/**
	 * return the directory path witch the file uploaded to
	 *
	 * @return unknown
	 */
	function getDirPath()
	{
		return $this->dirPath;
	}
	
	function getFileBaseName()
	{
		return $this->fileBaseName;
	}
	
	function getFileName()
	{
		return $this->fileName;
	}
	/**
	 * get image width
	 *
	 * @return integer
	 */
	function getImageWidth()
	{
		return $this->img_x;
	}
	/**
	 * get image height
	 *
	 * @return integer
	 */
	function getImageHeight()
	{
		return $this->img_y;
	}
	/**
	 * get uploaded file size
	 *
	 * @return string
	 */
	function getFileSize()
	{
		return $this->fileSize;
	}
	/**
	 * delete the uploaded image file & associated thumnails
	 *
	 * @param string $dirPath
	 * @param string $originalImageName
	 * @param string $arrayThumbnailSuffix
	 */
	function deleteFileAndThumbs($dirPath, $originalImageName, $arrayThumbnailSuffix)
	{
		//ensure the directory path ending with /
		if ($dirPath != ''  && substr($dirPath, -1) != '/') {
			$dirPath .= '/';
		}			
		if(!empty($originalImageName) && file_exists($dirPath . $originalImageName) && is_file($dirPath . $originalImageName))
		{
			@unlink($dirPath . $originalImageName);
			foreach($arrayThumbnailSuffix as $v)
			{
				$dotIndex = strrpos($originalImageName, '.');
				//begin to get the thumbnail image name
				$fileExtension = '';
				$fileBaseName = '';
				if(is_int($dotIndex))
				{
					$fileExtension = substr($originalImageName, $dotIndex);
					$fileBaseName = substr($originalImageName, 0, $dotIndex);
				}
				@unlink($dirPath . $fileBaseName . $v . $fileExtension);			
			}			
		}
	

	}
}
?>
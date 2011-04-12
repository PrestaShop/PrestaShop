<?php
/*
* 2007-2011 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2011 PrestaShop SA
*  @version  Release: $Revision: 1.4 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

/**
  * Generate a cached thumbnail for object lists (eg. carrier, order states...etc)
  *
  * @param string $image Real image filename
  * @param string $cacheImage Cached filename
  * @param integer $size Desired size
  */
function cacheImage($image, $cacheImage, $size, $imageType = 'jpg')
{
	if (file_exists($image))
	{
		if (!file_exists(_PS_TMP_IMG_DIR_.$cacheImage))
		{
			$imageGd = ($imageType == 'gif' ? imagecreatefromgif($image) : imagecreatefromjpeg($image));
			$x = imagesx($imageGd);
			$y = imagesy($imageGd);
			$max_x = ((int)$size)*3;
			/* Size is already ok */
			if ($y < $size && $x <= $max_x )
				copy($image, _PS_TMP_IMG_DIR_.$cacheImage);

			/* We need to resize */
			else
			{
				$ratioX = $x / ($y / $size);
				if($ratioX > $max_x)
				{
				    $ratioX = $max_x;
				    $size = $y / ($x / $max_x);
				}
				$newImage = ($imageType == 'gif' ? imagecreate($ratioX, $size) : imagecreatetruecolor($ratioX, $size));

				/* Allow to keep nice look even if resized */
				$white = imagecolorallocate($newImage, 255, 255, 255);
				imagefill($newImage, 0, 0, $white);
				imagecopyresampled($newImage, $imageGd, 0, 0, 0, 0, $ratioX, $size, $x, $y);
				imagecolortransparent($newImage, $white);

				/* Quality alteration and image creation */
				if ($imageType == 'gif')
					imagegif($newImage, _PS_TMP_IMG_DIR_.$cacheImage);
				else
					imagejpeg($newImage, _PS_TMP_IMG_DIR_.$cacheImage, 86);
			}
		}
		return '<img src="../img/tmp/'.$cacheImage.'" alt="" class="imgm" />';
	}
	return '';
}

/**
  * Check image upload
  *
  * @param array $file Upload $_FILE value
  * @param integer $maxFileSize Maximum upload size
  */
function checkImage($file, $maxFileSize)
{
	if ($file['size'] > $maxFileSize)
		return Tools::displayError('Image is too large').' ('.($file['size'] / 1000).Tools::displayError('KB').'). '.Tools::displayError('Maximum allowed:').' '.($maxFileSize / 1000).Tools::displayError('KB');
	if (!isPicture($file))
		return Tools::displayError('Image format not recognized, allowed formats are: .gif, .jpg, .png');
	if ($file['error'])
		return Tools::displayError('Error while uploading image; please change your server\'s settings.').'('.Tools::displayError('Error code: ').$file['error'].')';
	return false;
}



function checkImageUploadError($file)
{
	if ($file['error'])
	{
		switch ($file['error'])
		{
			case 1:
				return Tools::displayError('The file is too large.');
				break;

         case 2:
				return Tools::displayError('The file is too large.');
				break;

			case 3:
				return Tools::displayError('The file was partialy uploaded');
				break;

			case 4:
				return Tools::displayError('The file is empty');
				break;
		}
	}
}

/**
  * Check image MIME type
  *
  * @param string $file $_FILE of the current file
  * @param array $types Allowed MIME types
  */
function isPicture($file, $types = NULL)
{
    /* Detect mime content type */
    $mimeType = false;
    if (!$types)
        $types = array('image/gif', 'image/jpg', 'image/jpeg', 'image/pjpeg', 'image/png', 'image/x-png');

    /* Try 4 different methods to determine the mime type */
    if (function_exists('finfo_open'))
    {
        $const = defined('FILEINFO_MIME_TYPE') ? FILEINFO_MIME_TYPE : FILEINFO_MIME;
        $finfo = finfo_open($const);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
    }
    elseif (function_exists('mime_content_type'))
        $mimeType = mime_content_type($file['tmp_name']);
    elseif (function_exists('exec'))
    {
        $mimeType = trim(exec('file -b --mime-type '.escapeshellarg($file['tmp_name'])));
        if (!$mimeType)
            $mimeType = trim(exec('file --mime '.escapeshellarg($file['tmp_name'])));
        if (!$mimeType)
            $mimeType = trim(exec('file -bi '.escapeshellarg($file['tmp_name'])));
    }
    if (empty($mimeType) OR $mimeType == 'regular file')
        $mimeType = $file['type'];

    /* For each allowed MIME type, we are looking for it inside the current MIME type */
    foreach ($types AS $type)
        if (strstr($mimeType, $type))
            return true;

    return false;
}

/**
  * Check icon upload
  *
  * @param array $file Upload $_FILE value
  * @param integer $maxFileSize Maximum upload size
  */
function checkIco($file, $maxFileSize)
{
	if ($file['size'] > $maxFileSize)
		return Tools::displayError('Image is too large').' ('.($file['size'] / 1000).'ko). '.Tools::displayError('Maximum allowed:').' '.($maxFileSize / 1000).'ko';
	if (substr($file['name'], -4) != '.ico')
		return Tools::displayError('Image format not recognized, allowed formats are: .ico');
	if ($file['error'])
		return Tools::displayError('Error while uploading image; please change your server\'s settings.');
	return false;
}

/**
  * Resize, cut and optimize image
  *
  * @param array $sourceFile Image object from $_FILE
  * @param string $destFile Destination filename
  * @param integer $destWidth Desired width (optional)
  * @param integer $destHeight Desired height (optional)
  *
  * @return boolean Operation result
  */
function imageResize($sourceFile, $destFile, $destWidth = NULL, $destHeight = NULL, $fileType = 'jpg')
{
	list($sourceWidth, $sourceHeight, $type, $attr) = getimagesize($sourceFile);
	if (!$sourceWidth)
		return false;
	if ($destWidth == NULL) $destWidth = $sourceWidth;
	if ($destHeight == NULL) $destHeight = $sourceHeight;

	$sourceImage = createSrcImage($type, $sourceFile);

	$widthDiff = $destWidth / $sourceWidth;
	$heightDiff = $destHeight / $sourceHeight;

	if ($widthDiff > 1 AND $heightDiff > 1)
	{
		$nextWidth = $sourceWidth;
		$nextHeight = $sourceHeight;
	}
	else
	{
		if (Configuration::get('PS_IMAGE_GENERATION_METHOD') == 2 OR (!Configuration::get('PS_IMAGE_GENERATION_METHOD') AND $widthDiff > $heightDiff))
		{
			$nextHeight = $destHeight;
			$nextWidth = round(($sourceWidth * $nextHeight) / $sourceHeight);
			$destWidth = (int)(!Configuration::get('PS_IMAGE_GENERATION_METHOD') ? $destWidth : $nextWidth);
		}
		else
		{
			$nextWidth = $destWidth;
			$nextHeight = round($sourceHeight * $destWidth / $sourceWidth);
			$destHeight = (int)(!Configuration::get('PS_IMAGE_GENERATION_METHOD') ? $destHeight : $nextHeight);
		}
	}

	$destImage = imagecreatetruecolor($destWidth, $destHeight);

	$white = imagecolorallocate($destImage, 255, 255, 255);
	imagefilledrectangle ($destImage, 0, 0, $destWidth, $destHeight, $white);

	imagecopyresampled($destImage, $sourceImage, (int)(($destWidth - $nextWidth) / 2), (int)(($destHeight - $nextHeight) / 2), 0, 0, $nextWidth, $nextHeight, $sourceWidth, $sourceHeight);
	imagecolortransparent($destImage, $white);
	return (returnDestImage($fileType, $destImage, $destFile));
}

/**
  * Cut image
  *
  * @param array $srcFile Image object from $_FILE
  * @param string $destFile Destination filename
  * @param integer $destWidth Desired width (optional)
  * @param integer $destHeight Desired height (optional)
  *
  * @return boolean Operation result
  */
function imageCut($srcFile, $destFile, $destWidth = NULL, $destHeight = NULL, $fileType = 'jpg', $destX = 0, $destY = 0)
{
	if (!isset($srcFile['tmp_name']) OR !file_exists($srcFile['tmp_name']))
		return false;

	// Source infos
	$srcInfos = getimagesize($srcFile['tmp_name']);
	$src['width'] = $srcInfos[0];
	$src['height'] = $srcInfos[1];
	$src['ressource'] = createSrcImage($srcInfos[2], $srcFile['tmp_name']);

	// Destination infos
	$dest['x'] = $destX;
	$dest['y'] = $destY;
	$dest['width'] = $destWidth != NULL ? $destWidth : $src['width'];
	$dest['height'] = $destHeight != NULL ? $destHeight : $src['height'];
	$dest['ressource'] = createDestImage($dest['width'], $dest['height']);

	$white = imagecolorallocate($dest['ressource'], 255, 255, 255);
	imagecopyresampled($dest['ressource'], $src['ressource'], 0, 0, $dest['x'], $dest['y'], $dest['width'], $dest['height'], $dest['width'], $dest['height']);
	imagecolortransparent($dest['ressource'], $white);
	$return = returnDestImage($fileType, $dest['ressource'], $destFile);
	return	($return);
}

function createSrcImage($type, $filename)
{
	switch ($type)
	{
		case 1:
			return imagecreatefromgif($filename);
			break;
		case 3:
			return imagecreatefrompng($filename);
			break;
		case 2:
		default:
			return imagecreatefromjpeg($filename);
			break;
	}
}

function createDestImage($width, $height)
{
	$image = imagecreatetruecolor($width, $height);
	$white = imagecolorallocate($image, 255, 255, 255);
	imagefill($image, 0, 0, $white);
	return $image;
}

function returnDestImage($type, $ressource, $filename)
{
	$flag = false;
	switch ($type)
	{
		case 'gif':
			$flag = imagegif($ressource, $filename);
			break;
		case 'png':
			$flag = imagepng($ressource, $filename, 7);
			break;
		case 'jpeg':
		default:
			$flag = imagejpeg($ressource, $filename, 90);
			break;
	}
	imagedestroy($ressource);
	return $flag;
}

/**
  * Delete product or category image
  *
  * @param integer $id_item Product or category id
  * @param integer $id_image Image id
  */
function deleteImage($id_item, $id_image = NULL)
{
	$path = ($id_image) ? _PS_PROD_IMG_DIR_ : _PS_CAT_IMG_DIR_;
	$table = ($id_image) ? 'product' : 'category';

	if (file_exists(_PS_TMP_IMG_DIR_.$table.'_'.$id_item.'.jpg'))
		unlink(_PS_TMP_IMG_DIR_.$table.'_'.$id_item.'.jpg');

	if ($id_image AND file_exists($path.$id_item.'-'.$id_image.'.jpg'))
		unlink($path.$id_item.'-'.$id_image.'.jpg');
	elseif (!$id_image AND file_exists($path.$id_item.'.jpg'))
		unlink($path.$id_item.'.jpg');
	/* Auto-generated images */
	$imagesTypes = ImageType::getImagesTypes();
	foreach ($imagesTypes AS $k => $imagesType)
		if ($id_image AND file_exists($path.$id_item.'-'.$id_image.'-'.$imagesType['name'].'.jpg'))
			unlink($path.$id_item.'-'.$id_image.'-'.$imagesType['name'].'.jpg');
		elseif (!$id_image AND file_exists($path.$id_item.'-'.$imagesType['name'].'.jpg'))
			unlink($path.$id_item.'-'.$imagesType['name'].'.jpg');
	/* BO "mini" image */
	if (file_exists(_PS_TMP_IMG_DIR_.$table.'_mini_'.$id_item.'.jpg'))
		unlink(_PS_TMP_IMG_DIR_.$table.'_mini_'.$id_item.'.jpg');
	return true;
}


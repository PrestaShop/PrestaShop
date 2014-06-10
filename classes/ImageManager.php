<?php
/*
* 2007-2014 PrestaShop
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
*  @copyright  2007-2014 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

/**
 * This class includes functions for image manipulation
 *
 * @since 1.5.0
 */
class ImageManagerCore
{
	const ERROR_FILE_NOT_EXIST = 1;
	const ERROR_FILE_WIDTH     = 2;
	const ERROR_MEMORY_LIMIT   = 3;

	/**
	 * Generate a cached thumbnail for object lists (eg. carrier, order statuses...etc)
	 *
	 * @param string $image Real image filename
	 * @param string $cache_image Cached filename
	 * @param int $size Desired size
	 * @param string $image_type Image type
	 * @param bool $disable_cache When turned on a timestamp will be added to the image URI to disable the HTTP cache
	 * @param bool $regenerate When turned on and the file already exist, the file will be regenerated
	 * @return string
	 */
	public static function thumbnail($image, $cache_image, $size, $image_type = 'jpg', $disable_cache = true, $regenerate = false)
	{
		if (!file_exists($image))
			return '';

		if (file_exists(_PS_TMP_IMG_DIR_.$cache_image) && $regenerate)
			@unlink(_PS_TMP_IMG_DIR_.$cache_image);

		if ($regenerate || !file_exists(_PS_TMP_IMG_DIR_.$cache_image))
		{
			$infos = getimagesize($image);

			// Evaluate the memory required to resize the image: if it's too much, you can't resize it.
			if (!ImageManager::checkImageMemoryLimit($image))
				return false;

			$x = $infos[0];
			$y = $infos[1];
			$max_x = $size * 3;

			// Size is already ok
			if ($y < $size && $x <= $max_x)
				copy($image, _PS_TMP_IMG_DIR_.$cache_image);
			// We need to resize */
			else
			{
				$ratio_x = $x / ($y / $size);
				if ($ratio_x > $max_x)
				{
					$ratio_x = $max_x;
					$size = $y / ($x / $max_x);
				}

				ImageManager::resize($image, _PS_TMP_IMG_DIR_.$cache_image, $ratio_x, $size, $image_type);
			}
		}
		// Relative link will always work, whatever the base uri set in the admin
		if (Context::getContext()->controller->controller_type == 'admin')
			return '<img src="../img/tmp/'.$cache_image.($disable_cache ? '?time='.time() : '').'" alt="" class="imgm img-thumbnail" />';
		else
			return '<img src="'._PS_TMP_IMG_.$cache_image.($disable_cache ? '?time='.time() : '').'" alt="" class="imgm img-thumbnail" />';
	}

	/**
	 * Check if memory limit is too long or not
	 *
	 * @static
	 * @param $image
	 * @return bool
	 */
	public static function checkImageMemoryLimit($image)
	{
		$infos = @getimagesize($image);

		$memory_limit = Tools::getMemoryLimit();
		// memory_limit == -1 => unlimited memory
		if (function_exists('memory_get_usage') && (int)$memory_limit != -1)
		{
			$current_memory = memory_get_usage();
			$channel = isset($infos['channels']) ? ($infos['channels'] / 8) : 1;

			// Evaluate the memory required to resize the image: if it's too much, you can't resize it.
			if (($infos[0] * $infos[1] * $infos['bits'] * $channel + pow(2, 16)) * 1.8 + $current_memory > $memory_limit - 1024 * 1024)
				return false;
		}

		return true;
	}

	/**
	 * Resize, cut and optimize image
	 *
	 * @param string $src_file Image object from $_FILE
	 * @param string $dst_file Destination filename
	 * @param integer $dst_width Desired width (optional)
	 * @param integer $dst_height Desired height (optional)
	 * @param string $file_type
	 * @return boolean Operation result
	 */
	public static function resize($src_file, $dst_file, $dst_width = null, $dst_height = null, $file_type = 'jpg', $force_type = false, &$error = 0)
	{
		if (PHP_VERSION_ID < 50300)
			clearstatcache();
		else
			clearstatcache(true, $src_file);
		
		if (!file_exists($src_file) || !filesize($src_file))
			return !($error = self::ERROR_FILE_NOT_EXIST);

		list($src_width, $src_height, $type) = getimagesize($src_file);

		// If PS_IMAGE_QUALITY is activated, the generated image will be a PNG with .jpg as a file extension.
		// This allow for higher quality and for transparency. JPG source files will also benefit from a higher quality
		// because JPG reencoding by GD, even with max quality setting, degrades the image.
		if (Configuration::get('PS_IMAGE_QUALITY') == 'png_all'
			|| (Configuration::get('PS_IMAGE_QUALITY') == 'png' && $type == IMAGETYPE_PNG) && !$force_type)
			$file_type = 'png';

		if (!$src_width)
			return !($error = self::ERROR_FILE_WIDTH);
		if (!$dst_width)
			$dst_width = $src_width;
		if (!$dst_height)
			$dst_height = $src_height;

		$src_image = ImageManager::create($type, $src_file);

		$width_diff = $dst_width / $src_width;
		$height_diff = $dst_height / $src_height;

		if ($width_diff > 1 && $height_diff > 1)
		{
			$next_width = $src_width;
			$next_height = $src_height;
		}
		else
		{
			if (Configuration::get('PS_IMAGE_GENERATION_METHOD') == 2 || (!Configuration::get('PS_IMAGE_GENERATION_METHOD') && $width_diff > $height_diff))
			{
				$next_height = $dst_height;
				$next_width = round(($src_width * $next_height) / $src_height);
				$dst_width = (int)(!Configuration::get('PS_IMAGE_GENERATION_METHOD') ? $dst_width : $next_width);
			}
			else
			{
				$next_width = $dst_width;
				$next_height = round($src_height * $dst_width / $src_width);
				$dst_height = (int)(!Configuration::get('PS_IMAGE_GENERATION_METHOD') ? $dst_height : $next_height);
			}
		}

		if (!ImageManager::checkImageMemoryLimit($src_file))
			return !($error = self::ERROR_MEMORY_LIMIT);
		
		$dest_image = imagecreatetruecolor($dst_width, $dst_height);

		// If image is a PNG and the output is PNG, fill with transparency. Else fill with white background.
		if ($file_type == 'png' && $type == IMAGETYPE_PNG)
		{
			imagealphablending($dest_image, false);
			imagesavealpha($dest_image, true);
			$transparent = imagecolorallocatealpha($dest_image, 255, 255, 255, 127);
			imagefilledrectangle($dest_image, 0, 0, $dst_width, $dst_height, $transparent);
		}
		else
		{
			$white = imagecolorallocate($dest_image, 255, 255, 255);
			imagefilledrectangle ($dest_image, 0, 0, $dst_width, $dst_height, $white);
		}

		imagecopyresampled($dest_image, $src_image, (int)(($dst_width - $next_width) / 2), (int)(($dst_height - $next_height) / 2), 0, 0, $next_width, $next_height, $src_width, $src_height);
		return (ImageManager::write($file_type, $dest_image, $dst_file));
	}

	/**
	 * Check if file is a real image
	 *
	 * @param string $filename File path to check
	 * @param string $file_mime_type File known mime type (generally from $_FILES)
	 * @param array $mime_type_list Allowed MIME types
	 * @return bool
	 */
	public static function isRealImage($filename, $file_mime_type = null, $mime_type_list = null)
	{
		// Detect mime content type
		$mime_type = false;
		if (!$mime_type_list)
			$mime_type_list = array('image/gif', 'image/jpg', 'image/jpeg', 'image/pjpeg', 'image/png', 'image/x-png');

		// Try 4 different methods to determine the mime type
		if (function_exists('getimagesize'))
		{
			$image_info = @getimagesize($filename);

			if ($image_info)
				$mime_type = $image_info['mime'];
			else
				$file_mime_type = false;
		}
		else if (function_exists('finfo_open'))
		{
			$const = defined('FILEINFO_MIME_TYPE') ? FILEINFO_MIME_TYPE : FILEINFO_MIME;
			$finfo = finfo_open($const);
			$mime_type = finfo_file($finfo, $filename);
			finfo_close($finfo);
		}
		elseif (function_exists('mime_content_type'))
			$mime_type = mime_content_type($filename);
		elseif (function_exists('exec'))
		{
			$mime_type = trim(exec('file -b --mime-type '.escapeshellarg($filename)));
			if (!$mime_type)
				$mime_type = trim(exec('file --mime '.escapeshellarg($filename)));
			if (!$mime_type)
				$mime_type = trim(exec('file -bi '.escapeshellarg($filename)));
		}

		if ($file_mime_type && (empty($mime_type) || $mime_type == 'regular file' || $mime_type == 'text/plain'))
			$mime_type = $file_mime_type;

		// For each allowed MIME type, we are looking for it inside the current MIME type
		foreach ($mime_type_list as $type)
			if (strstr($mime_type, $type))
				return true;

		return false;
	}

	/**
	 * Check if image file extension is correct
	 *
	 * @static
	 * @param $filename real filename
	 * @return bool true if it's correct
	 */
	public static function isCorrectImageFileExt($filename, $authorized_extensions = null)
	{
		// Filter on file extension
		if ($authorized_extensions === null)
			$authorized_extensions = array('gif', 'jpg', 'jpeg', 'jpe', 'png');
		$name_explode = explode('.', $filename);
		if (count($name_explode) >= 2)
		{
			$current_extension = strtolower($name_explode[count($name_explode) - 1]);
			if (!in_array($current_extension, $authorized_extensions))
				return false;
		}
		else
			return false;

		return true;
	}

	/**
	 * Validate image upload (check image type and weight)
	 *
	 * @param array $file Upload $_FILE value
	 * @param integer $max_file_size Maximum upload size
	 * @return bool|string Return false if no error encountered
	 */
	public static function validateUpload($file, $max_file_size = 0, $types = null)
	{
		if ((int)$max_file_size > 0 && $file['size'] > (int)$max_file_size)
			return sprintf(Tools::displayError('Image is too large (%1$d kB). Maximum allowed: %2$d kB'), $file['size'] / 1024, $max_file_size / 1024);
		if (!ImageManager::isRealImage($file['tmp_name'], $file['type']) || !ImageManager::isCorrectImageFileExt($file['name'], $types) || preg_match('/\%00/', $file['name']))
			return Tools::displayError('Image format not recognized, allowed formats are: .gif, .jpg, .png');
		if ($file['error'])
			return sprintf(Tools::displayError('Error while uploading image; please change your server\'s settings. (Error code: %s)'), $file['error']);
		return false;
	}

	/**
	 * Validate icon upload
	 *
	 * @param array $file Upload $_FILE value
	 * @param int $max_file_size Maximum upload size
	 * @return bool|string Return false if no error encountered
	 */
	public static function validateIconUpload($file, $max_file_size = 0)
	{
		if ((int)$max_file_size > 0 && $file['size'] > $max_file_size)
			return sprintf(
				Tools::displayError('Image is too large (%1$d kB). Maximum allowed: %2$d kB'),
				$file['size'] / 1000,
				$max_file_size / 1000
			);
		if (substr($file['name'], -4) != '.ico')
			return Tools::displayError('Image format not recognized, allowed formats are: .ico');
		if ($file['error'])
			return Tools::displayError('Error while uploading image; please change your server\'s settings.');
		return false;
	}

	/**
	 * Cut image
	 *
	 * @param array $src_file Origin filename
	 * @param string $dst_file Destination filename
	 * @param integer $dst_width Desired width
	 * @param integer $dst_height Desired height
	 * @param string $file_type
	 * @param int $dst_x
	 * @param int $dst_y
	 *
	 * @return bool Operation result
	 */
	public static function cut($src_file, $dst_file, $dst_width = null, $dst_height = null, $file_type = 'jpg', $dst_x = 0, $dst_y = 0)
	{
		if (!file_exists($src_file))
			return false;

		// Source information
		$src_info = getimagesize($src_file);
		$src = array(
			'width' => $src_info[0],
			'height' => $src_info[1],
			'ressource' => ImageManager::create($src_info[2], $src_file),
		);

		// Destination information
		$dest = array();
		$dest['x'] = $dst_x;
		$dest['y'] = $dst_y;
		$dest['width'] = !is_null($dst_width) ? $dst_width : $src['width'];
		$dest['height'] = !is_null($dst_height) ? $dst_height : $src['height'];
		$dest['ressource'] = ImageManager::createWhiteImage($dest['width'], $dest['height']);

		$white = imagecolorallocate($dest['ressource'], 255, 255, 255);
		imagecopyresampled($dest['ressource'], $src['ressource'], 0, 0, $dest['x'], $dest['y'], $dest['width'], $dest['height'], $dest['width'], $dest['height']);
		imagecolortransparent($dest['ressource'], $white);
		$return = ImageManager::write($file_type, $dest['ressource'], $dst_file);
		return	$return;
	}

	/**
	 * Create an image with GD extension from a given type
	 *
	 * @param string $type
	 * @param string $filename
	 * @return resource
	 */
	public static function create($type, $filename)
	{
		switch ($type)
		{
			case IMAGETYPE_GIF :
				return imagecreatefromgif($filename);
			break;

			case IMAGETYPE_PNG :
				return imagecreatefrompng($filename);
			break;

			case IMAGETYPE_JPEG :
			default:
				return imagecreatefromjpeg($filename);
			break;
		}
	}

	/**
	 * Create an empty image with white background
	 *
	 * @param int $width
	 * @param int $height
	 * @return resource
	 */
	public static function createWhiteImage($width, $height)
	{
		$image = imagecreatetruecolor($width, $height);
		$white = imagecolorallocate($image, 255, 255, 255);
		imagefill($image, 0, 0, $white);
		return $image;
	}

	/**
	 * Generate and write image
	 *
	 * @param string $type
	 * @param resource $resource
	 * @param string $filename
	 * @return bool
	 */
	public static function write($type, $resource, $filename)
	{
		switch ($type)
		{
			case 'gif':
				$success = imagegif($resource, $filename);
			break;

			case 'png':
				$quality = (Configuration::get('PS_PNG_QUALITY') === false ? 7 : Configuration::get('PS_PNG_QUALITY'));
				$success = imagepng($resource, $filename, (int)$quality);
			break;

			case 'jpg':
			case 'jpeg':
			default:
				$quality = (Configuration::get('PS_JPEG_QUALITY') === false ? 90 : Configuration::get('PS_JPEG_QUALITY'));
				imageinterlace($resource,1); /// make it PROGRESSIVE
				$success = imagejpeg($resource, $filename, (int)$quality);
			break;
		}
		imagedestroy($resource);
		@chmod($filename, 0664);
		return $success;
	}

	/**
	 * Return the mime type by the file extension
	 *
	 * @param string $file_name
	 * @return string
	 */
	public static function getMimeTypeByExtension($file_name)
	{
		$types = array(
						'image/gif' => array('gif'),
						'image/jpeg' => array('jpg', 'jpeg'),
						'image/png' => array('png')
					);	
		$extension = substr($file_name, strrpos($file_name, '.') + 1);

		$mime_type = null;
		foreach ($types as $mime => $exts)
			if (in_array($extension, $exts))
			{
				$mime_type = $mime;
				break;
			}

		if ($mime_type === null)
			$mime_type = 'image/jpeg';

		return $mime_type;
	}
}
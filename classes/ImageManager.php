<?php
/**
 * 2007-2019 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

/**
 * Class ImageManagerCore.
 *
 * This class includes functions for image manipulation
 *
 * @since 1.5.0
 */
class ImageManagerCore
{
    const ERROR_FILE_NOT_EXIST = 1;
    const ERROR_FILE_WIDTH = 2;
    const ERROR_MEMORY_LIMIT = 3;

    /**
     * Generate a cached thumbnail for object lists (eg. carrier, order statuses...etc).
     *
     * @param string $image Real image filename
     * @param string $cacheImage Cached filename
     * @param int $size Desired size
     * @param string $imageType Image type
     * @param bool $disableCache When turned on a timestamp will be added to the image URI to disable the HTTP cache
     * @param bool $regenerate When turned on and the file already exist, the file will be regenerated
     *
     *@return string
     */
    public static function thumbnail($image, $cacheImage, $size, $imageType = 'jpg', $disableCache = true, $regenerate = false)
    {
        if (!file_exists($image)) {
            return '';
        }

        if (file_exists(_PS_TMP_IMG_DIR_ . $cacheImage) && $regenerate) {
            @unlink(_PS_TMP_IMG_DIR_ . $cacheImage);
        }

        if ($regenerate || !file_exists(_PS_TMP_IMG_DIR_ . $cacheImage)) {
            $infos = getimagesize($image);

            // Evaluate the memory required to resize the image: if it's too much, you can't resize it.
            if (!ImageManager::checkImageMemoryLimit($image)) {
                return false;
            }

            $x = $infos[0];
            $y = $infos[1];
            $maxX = $size * 3;

            // Size is already ok
            if ($y < $size && $x <= $maxX) {
                copy($image, _PS_TMP_IMG_DIR_ . $cacheImage);
            } else {
                // We need to resize */
                $ratioX = $x / ($y / $size);
                if ($ratioX > $maxX) {
                    $ratioX = $maxX;
                    $size = $y / ($x / $maxX);
                }

                ImageManager::resize($image, _PS_TMP_IMG_DIR_ . $cacheImage, $ratioX, $size, $imageType);
            }
        }

        return '<img src="' . self::getThumbnailPath($cacheImage, $disableCache) . '" alt="" class="imgm img-thumbnail" />';
    }

    /**
     * @param $cacheImage
     * @param $disableCache
     *
     * @return string
     */
    public static function getThumbnailPath($cacheImage, $disableCache)
    {
        $cacheParam = $disableCache ? '?time=' . time() : '';

        if (Context::getContext()->controller->controller_type == 'admin') {
            return '../img/tmp/' . $cacheImage . $cacheParam;
        }

        return _PS_TMP_IMG_ . $cacheImage . $cacheParam;
    }

    /**
     * Check if memory limit is too long or not.
     *
     * @param mixed $image
     *
     * @return bool
     */
    public static function checkImageMemoryLimit($image)
    {
        $infos = @getimagesize($image);

        if (!is_array($infos) || !isset($infos['bits'])) {
            return true;
        }

        $memoryLimit = Tools::getMemoryLimit();
        // memory_limit == -1 => unlimited memory
        if (isset($infos['bits']) && function_exists('memory_get_usage') && (int) $memoryLimit != -1) {
            $currentMemory = memory_get_usage();

            $bits = $infos['bits'] / 8;
            $channel = isset($infos['channels']) ? $infos['channels'] : 1;

            // Evaluate the memory required to resize the image: if it's too much, you can't resize it.
            // For perfs, avoid computing static maths formulas in the code. pow(2, 16) = 65536 ; 1024 * 1024 = 1048576
            if (($infos[0] * $infos[1] * $bits * $channel + 65536) * 1.8 + $currentMemory > $memoryLimit - 1048576) {
                return false;
            }
        }

        return true;
    }

    /**
     * Resize, cut and optimize image.
     *
     * @param string $sourceFile Image object from $_FILE
     * @param string $destinationFile Destination filename
     * @param int $destinationWidth Desired width (optional)
     * @param int $destinationHeight Desired height (optional)
     * @param string $fileType Desired file_type (may be override by PS_IMAGE_QUALITY)
     * @param bool $forceType Don't override $file_type
     * @param int $error Out error code
     * @param int $targetWidth Needed by AdminImportController to speed up the import process
     * @param int $targetHeight Needed by AdminImportController to speed up the import process
     * @param int $quality Needed by AdminImportController to speed up the import process
     * @param int $sourceWidth Needed by AdminImportController to speed up the import process
     * @param int $sourceHeight Needed by AdminImportController to speed up the import process
     *
     *@return bool Operation result
     */
    public static function resize(
        $sourceFile,
        $destinationFile,
        $destinationWidth = null,
        $destinationHeight = null,
        $fileType = 'jpg',
        $forceType = false,
        &$error = 0,
        &$targetWidth = null,
        &$targetHeight = null,
        $quality = 5,
        &$sourceWidth = null,
        &$sourceHeight = null
    ) {
        clearstatcache(true, $sourceFile);

        if (!file_exists($sourceFile) || !filesize($sourceFile)) {
            return !($error = self::ERROR_FILE_NOT_EXIST);
        }

        list($tmpWidth, $tmpHeight, $type) = getimagesize($sourceFile);
        $rotate = 0;
        if (function_exists('exif_read_data') && function_exists('mb_strtolower')) {
            $exif = @exif_read_data($sourceFile);

            if ($exif && isset($exif['Orientation'])) {
                switch ($exif['Orientation']) {
                    case 3:
                        $sourceWidth = $tmpWidth;
                        $sourceHeight = $tmpHeight;
                        $rotate = 180;

                        break;

                    case 6:
                        $sourceWidth = $tmpHeight;
                        $sourceHeight = $tmpWidth;
                        $rotate = -90;

                        break;

                    case 8:
                        $sourceWidth = $tmpHeight;
                        $sourceHeight = $tmpWidth;
                        $rotate = 90;

                        break;

                    default:
                        $sourceWidth = $tmpWidth;
                        $sourceHeight = $tmpHeight;
                }
            } else {
                $sourceWidth = $tmpWidth;
                $sourceHeight = $tmpHeight;
            }
        } else {
            $sourceWidth = $tmpWidth;
            $sourceHeight = $tmpHeight;
        }

        // If PS_IMAGE_QUALITY is activated, the generated image will be a PNG with .jpg as a file extension.
        // This allow for higher quality and for transparency. JPG source files will also benefit from a higher quality
        // because JPG reencoding by GD, even with max quality setting, degrades the image.
        if (Configuration::get('PS_IMAGE_QUALITY') == 'png_all'
            || (Configuration::get('PS_IMAGE_QUALITY') == 'png' && $type == IMAGETYPE_PNG) && !$forceType) {
            $fileType = 'png';
        }

        if (!$sourceWidth) {
            return !($error = self::ERROR_FILE_WIDTH);
        }
        if (!$destinationWidth) {
            $destinationWidth = $sourceWidth;
        }
        if (!$destinationHeight) {
            $destinationHeight = $sourceHeight;
        }

        $widthDiff = $destinationWidth / $sourceWidth;
        $heightDiff = $destinationHeight / $sourceHeight;

        $psImageGenerationMethod = Configuration::get('PS_IMAGE_GENERATION_METHOD');
        if ($widthDiff > 1 && $heightDiff > 1) {
            $nextWidth = $sourceWidth;
            $nextHeight = $sourceHeight;
        } else {
            if ($psImageGenerationMethod == 2 || (!$psImageGenerationMethod && $widthDiff > $heightDiff)) {
                $nextHeight = $destinationHeight;
                $nextWidth = round(($sourceWidth * $nextHeight) / $sourceHeight);
                $destinationWidth = (int) (!$psImageGenerationMethod ? $destinationWidth : $nextWidth);
            } else {
                $nextWidth = $destinationWidth;
                $nextHeight = round($sourceHeight * $destinationWidth / $sourceWidth);
                $destinationHeight = (int) (!$psImageGenerationMethod ? $destinationHeight : $nextHeight);
            }
        }

        if (!ImageManager::checkImageMemoryLimit($sourceFile)) {
            return !($error = self::ERROR_MEMORY_LIMIT);
        }

        $targetWidth = $destinationWidth;
        $targetHeight = $destinationHeight;

        $destImage = imagecreatetruecolor($destinationWidth, $destinationHeight);

        // If image is a PNG and the output is PNG, fill with transparency. Else fill with white background.
        if ($fileType == 'png' && $type == IMAGETYPE_PNG) {
            imagealphablending($destImage, false);
            imagesavealpha($destImage, true);
            $transparent = imagecolorallocatealpha($destImage, 255, 255, 255, 127);
            imagefilledrectangle($destImage, 0, 0, $destinationWidth, $destinationHeight, $transparent);
        } else {
            $white = imagecolorallocate($destImage, 255, 255, 255);
            imagefilledrectangle($destImage, 0, 0, $destinationWidth, $destinationHeight, $white);
        }

        $srcImage = ImageManager::create($type, $sourceFile);
        if ($rotate) {
            $srcImage = imagerotate($srcImage, $rotate, 0);
        }

        if ($destinationWidth >= $sourceWidth && $destinationHeight >= $sourceHeight) {
            imagecopyresized($destImage, $srcImage, (int) (($destinationWidth - $nextWidth) / 2), (int) (($destinationHeight - $nextHeight) / 2), 0, 0, $nextWidth, $nextHeight, $sourceWidth, $sourceHeight);
        } else {
            ImageManager::imagecopyresampled($destImage, $srcImage, (int) (($destinationWidth - $nextWidth) / 2), (int) (($destinationHeight - $nextHeight) / 2), 0, 0, $nextWidth, $nextHeight, $sourceWidth, $sourceHeight, $quality);
        }
        $writeFile = ImageManager::write($fileType, $destImage, $destinationFile);
        Hook::exec('actionOnImageResizeAfter', array('dst_file' => $destinationFile, 'file_type' => $fileType));
        @imagedestroy($srcImage);

        file_put_contents(
            dirname($destinationFile) . DIRECTORY_SEPARATOR . 'fileType',
            $fileType
        );

        return $writeFile;
    }

    /**
     * @param $dstImage
     * @param $srcImage
     * @param $dstX
     * @param $dstY
     * @param $srcX
     * @param $srcY
     * @param $dstW
     * @param $dstH
     * @param $srcW
     * @param $srcH
     * @param int $quality
     *
     * @return bool
     */
    public static function imagecopyresampled(
        &$dstImage,
        $srcImage,
        $dstX,
        $dstY,
        $srcX,
        $srcY,
        $dstW,
        $dstH,
        $srcW,
        $srcH,
        $quality = 3
    ) {
        // Plug-and-Play fastimagecopyresampled function replaces much slower imagecopyresampled.
        // Just include this function and change all "imagecopyresampled" references to "fastimagecopyresampled".
        // Typically from 30 to 60 times faster when reducing high resolution images down to thumbnail size using the default quality setting.
        // Author: Tim Eckel - Date: 09/07/07 - Version: 1.1 - Project: FreeRingers.net - Freely distributable - These comments must remain.
        //
        // Optional "quality" parameter (defaults is 3). Fractional values are allowed, for example 1.5. Must be greater than zero.
        // Between 0 and 1 = Fast, but mosaic results, closer to 0 increases the mosaic effect.
        // 1 = Up to 350 times faster. Poor results, looks very similar to imagecopyresized.
        // 2 = Up to 95 times faster.  Images appear a little sharp, some prefer this over a quality of 3.
        // 3 = Up to 60 times faster.  Will give high quality smooth results very close to imagecopyresampled, just faster.
        // 4 = Up to 25 times faster.  Almost identical to imagecopyresampled for most images.
        // 5 = No speedup. Just uses imagecopyresampled, no advantage over imagecopyresampled.

        if (empty($srcImage) || empty($dstImage) || $quality <= 0) {
            return false;
        }
        if ($quality < 5 && (($dstW * $quality) < $srcW || ($dstH * $quality) < $srcH)) {
            $temp = imagecreatetruecolor($dstW * $quality + 1, $dstH * $quality + 1);
            imagecopyresized($temp, $srcImage, 0, 0, $srcX, $srcY, $dstW * $quality + 1, $dstH * $quality + 1, $srcW, $srcH);
            imagecopyresampled($dstImage, $temp, $dstX, $dstY, 0, 0, $dstW, $dstH, $dstW * $quality, $dstH * $quality);
            imagedestroy($temp);
        } else {
            imagecopyresampled($dstImage, $srcImage, $dstX, $dstY, $srcX, $srcY, $dstW, $dstH, $srcW, $srcH);
        }

        return true;
    }

    /**
     * Check if file is a real image.
     *
     * @param string $filename File path to check
     * @param string $fileMimeType File known mime type (generally from $_FILES)
     * @param array $mimeTypeList Allowed MIME types
     *
     * @return bool
     */
    public static function isRealImage($filename, $fileMimeType = null, $mimeTypeList = null)
    {
        // Detect mime content type
        $mimeType = false;
        if (!$mimeTypeList) {
            $mimeTypeList = array('image/gif', 'image/jpg', 'image/jpeg', 'image/pjpeg', 'image/png', 'image/x-png');
        }

        // Try 4 different methods to determine the mime type
        if (function_exists('getimagesize')) {
            $imageInfo = @getimagesize($filename);

            if ($imageInfo) {
                $mimeType = $imageInfo['mime'];
            } else {
                $fileMimeType = false;
            }
        } elseif (function_exists('finfo_open')) {
            $const = defined('FILEINFO_MIME_TYPE') ? FILEINFO_MIME_TYPE : FILEINFO_MIME;
            $finfo = finfo_open($const);
            $mimeType = finfo_file($finfo, $filename);
            finfo_close($finfo);
        } elseif (function_exists('mime_content_type')) {
            $mimeType = mime_content_type($filename);
        } elseif (function_exists('exec')) {
            $mimeType = trim(exec('file -b --mime-type ' . escapeshellarg($filename)));
            if (!$mimeType) {
                $mimeType = trim(exec('file --mime ' . escapeshellarg($filename)));
            }
            if (!$mimeType) {
                $mimeType = trim(exec('file -bi ' . escapeshellarg($filename)));
            }
        }

        if ($fileMimeType && (empty($mimeType) || $mimeType == 'regular file' || $mimeType == 'text/plain')) {
            $mimeType = $fileMimeType;
        }

        // For each allowed MIME type, we are looking for it inside the current MIME type
        foreach ($mimeTypeList as $type) {
            if (strstr($mimeType, $type)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if image file extension is correct.
     *
     * @param string $filename Real filename
     * @param array|null $authorizedExtensions
     *
     * @return bool True if it's correct
     */
    public static function isCorrectImageFileExt($filename, $authorizedExtensions = null)
    {
        // Filter on file extension
        if ($authorizedExtensions === null) {
            $authorizedExtensions = array('gif', 'jpg', 'jpeg', 'jpe', 'png');
        }
        $nameExplode = explode('.', $filename);
        if (count($nameExplode) >= 2) {
            $currentExtension = strtolower($nameExplode[count($nameExplode) - 1]);
            if (!in_array($currentExtension, $authorizedExtensions)) {
                return false;
            }
        } else {
            return false;
        }

        return true;
    }

    /**
     * Validate image upload (check image type and weight).
     *
     * @param array $file Upload $_FILE value
     * @param int $maxFileSize Maximum upload size
     *
     * @return bool|string Return false if no error encountered
     */
    public static function validateUpload($file, $maxFileSize = 0, $types = null)
    {
        if ((int) $maxFileSize > 0 && $file['size'] > (int) $maxFileSize) {
            return Context::getContext()->getTranslator()->trans('Image is too large (%1$d kB). Maximum allowed: %2$d kB', array($file['size'] / 1024, $maxFileSize / 1024), 'Admin.Notifications.Error');
        }
        if (!ImageManager::isRealImage($file['tmp_name'], $file['type']) || !ImageManager::isCorrectImageFileExt($file['name'], $types) || preg_match('/\%00/', $file['name'])) {
            return Context::getContext()->getTranslator()->trans('Image format not recognized, allowed formats are: .gif, .jpg, .png', array(), 'Admin.Notifications.Error');
        }
        if ($file['error']) {
            return Context::getContext()->getTranslator()->trans('Error while uploading image; please change your server\'s settings. (Error code: %s)', array($file['error']), 'Admin.Notifications.Error');
        }

        return false;
    }

    /**
     * Validate icon upload.
     *
     * @param array $file Upload $_FILE value
     * @param int $maxFileSize Maximum upload size
     *
     * @return bool|string Return false if no error encountered
     */
    public static function validateIconUpload($file, $maxFileSize = 0)
    {
        if ((int) $maxFileSize > 0 && $file['size'] > $maxFileSize) {
            return Context::getContext()->getTranslator()->trans('Image is too large (%1$d kB). Maximum allowed: %2$d kB', array($file['size'] / 1000, $maxFileSize / 1000), 'Admin.Notifications.Error');
        }
        if (substr($file['name'], -4) != '.ico') {
            return Context::getContext()->getTranslator()->trans('Image format not recognized, allowed formats are: .ico', array(), 'Admin.Notifications.Error');
        }
        if ($file['error']) {
            return Context::getContext()->getTranslator()->trans('Error while uploading image; please change your server\'s settings.', array(), 'Admin.Notifications.Error');
        }

        return false;
    }

    /**
     * Cut image.
     *
     * @param array $srcFile Origin filename
     * @param string $dstFile Destination filename
     * @param int $dstWidth Desired width
     * @param int $dstHeight Desired height
     * @param string $fileType
     * @param int $dstX
     * @param int $dstY
     *
     * @return bool Operation result
     */
    public static function cut($srcFile, $dstFile, $dstWidth = null, $dstHeight = null, $fileType = 'jpg', $dstX = 0, $dstY = 0)
    {
        if (!file_exists($srcFile)) {
            return false;
        }

        // Source information
        $srcInfo = getimagesize($srcFile);
        $src = array(
            'width' => $srcInfo[0],
            'height' => $srcInfo[1],
            'ressource' => ImageManager::create($srcInfo[2], $srcFile),
        );

        // Destination information
        $dest = array();
        $dest['x'] = $dstX;
        $dest['y'] = $dstY;
        $dest['width'] = null !== $dstWidth ? $dstWidth : $src['width'];
        $dest['height'] = null !== $dstHeight ? $dstHeight : $src['height'];
        $dest['ressource'] = ImageManager::createWhiteImage($dest['width'], $dest['height']);

        $white = imagecolorallocate($dest['ressource'], 255, 255, 255);
        imagecopyresampled($dest['ressource'], $src['ressource'], 0, 0, $dest['x'], $dest['y'], $dest['width'], $dest['height'], $dest['width'], $dest['height']);
        imagecolortransparent($dest['ressource'], $white);
        $return = ImageManager::write($fileType, $dest['ressource'], $dstFile);
        Hook::exec('actionOnImageCutAfter', array('dst_file' => $dstFile, 'file_type' => $fileType));
        @imagedestroy($src['ressource']);

        return $return;
    }

    /**
     * Create an image with GD extension from a given type.
     *
     * @param string $type
     * @param string $filename
     *
     * @return resource
     */
    public static function create($type, $filename)
    {
        switch ($type) {
            case IMAGETYPE_GIF:
                return imagecreatefromgif($filename);

                break;

            case IMAGETYPE_PNG:
                return imagecreatefrompng($filename);

                break;

            case IMAGETYPE_JPEG:
            default:
                return imagecreatefromjpeg($filename);

                break;
        }
    }

    /**
     * Create an empty image with white background.
     *
     * @param int $width
     * @param int $height
     *
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
     * Generate and write image.
     *
     * @param string $type
     * @param resource $resource
     * @param string $filename
     *
     * @return bool
     */
    public static function write($type, $resource, $filename)
    {
        static $psPngQuality = null;
        static $psJpegQuality = null;

        if ($psPngQuality === null) {
            $psPngQuality = Configuration::get('PS_PNG_QUALITY');
        }

        if ($psJpegQuality === null) {
            $psJpegQuality = Configuration::get('PS_JPEG_QUALITY');
        }

        switch ($type) {
            case 'gif':
                $success = imagegif($resource, $filename);

                break;

            case 'png':
                $quality = ($psPngQuality === false ? 7 : $psPngQuality);
                $success = imagepng($resource, $filename, (int) $quality);

                break;

            case 'jpg':
            case 'jpeg':
            default:
                $quality = ($psJpegQuality === false ? 90 : $psJpegQuality);
                imageinterlace($resource, 1); /// make it PROGRESSIVE
                $success = imagejpeg($resource, $filename, (int) $quality);

                break;
        }
        imagedestroy($resource);
        @chmod($filename, 0664);

        return $success;
    }

    /**
     * Return the mime type by the file extension.
     *
     * @param string $fileName
     *
     * @return string
     */
    public static function getMimeTypeByExtension($fileName)
    {
        $types = array(
            'image/gif' => array('gif'),
            'image/jpeg' => array('jpg', 'jpeg'),
            'image/png' => array('png'),
        );
        $extension = substr($fileName, strrpos($fileName, '.') + 1);

        $mimeType = null;
        foreach ($types as $mime => $exts) {
            if (in_array($extension, $exts)) {
                $mimeType = $mime;

                break;
            }
        }

        if ($mimeType === null) {
            $mimeType = 'image/jpeg';
        }

        return $mimeType;
    }
}

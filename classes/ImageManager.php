<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
    public const ERROR_FILE_NOT_EXIST = 1;
    public const ERROR_FILE_WIDTH = 2;
    public const ERROR_MEMORY_LIMIT = 3;

    public const MIME_TYPE_SUPPORTED = [
        'image/gif',
        'image/jpg',
        'image/jpeg',
        'image/pjpeg',
        'image/png',
        'image/x-png',
        'image/webp',
        'image/svg+xml',
        'image/svg',
    ];

    public const EXTENSIONS_SUPPORTED = [
        'gif',
        'jpg',
        'jpeg',
        'jpe',
        'png',
        'webp',
    ];

    /**
     * @var array - a list of svg mime types
     */
    protected const SVG_MIMETYPES = ['image/svg+xml', 'image/svg'];

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
     * @return string|bool
     */
    public static function thumbnail($image, $cacheImage, $size, $imageType = 'jpg', $disableCache = true, $regenerate = false)
    {
        if (!file_exists($image)) {
            return '';
        }

        if ($regenerate && file_exists(_PS_TMP_IMG_DIR_ . $cacheImage)) {
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

                ImageManager::resize($image, _PS_TMP_IMG_DIR_ . $cacheImage, (int) $ratioX, (int) $size, $imageType);
            }
        }

        return '<img src="' . self::getThumbnailPath($cacheImage, $disableCache) . '" alt="" class="imgm img-thumbnail" />';
    }

    /**
     * @param string $cacheImage
     * @param bool $disableCache
     *
     * @return string
     */
    public static function getThumbnailPath($cacheImage, $disableCache)
    {
        $cacheParam = $disableCache ? '?time=' . time() : '';

        $controller = Context::getContext()->controller;
        if (isset($controller->controller_type) && $controller->controller_type == 'admin') {
            return __PS_BASE_URI__ . 'img/tmp/' . $cacheImage . $cacheParam;
        }

        return _PS_TMP_IMG_ . $cacheImage . $cacheParam;
    }

    /**
     * Check if memory limit is too long or not.
     *
     * @param string $image
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
        if (function_exists('memory_get_usage') && (int) $memoryLimit != -1) {
            $currentMemory = memory_get_usage();

            $bits = $infos['bits'] / 8;
            $channel = $infos['channels'] ?? 1;

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
     * @param string $destinationFileType Desired file_type (may be override by PS_IMAGE_QUALITY)
     * @param bool $forceType Don't override $file_type by PS_IMAGE_QUALITY, used when generating webp and avif files
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
        $destinationFileType = 'jpg',
        $forceType = false,
        &$error = 0,
        &$targetWidth = null,
        &$targetHeight = null,
        $quality = 5,
        &$sourceWidth = null,
        &$sourceHeight = null
    ) {
        clearstatcache(true, $sourceFile);

        // Check if original file exists
        if (!file_exists($sourceFile) || !filesize($sourceFile)) {
            $error = self::ERROR_FILE_NOT_EXIST;

            return false;
        }

        list($tmpWidth, $tmpHeight, $sourceFileType) = getimagesize($sourceFile);
        $rotate = 0;
        if (function_exists('exif_read_data')) {
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

        // If the filetype is not forced and we are requesting a JPG file, we must
        // adjust the format inside according to PS_IMAGE_QUALITY in some cases.
        if (!$forceType && $destinationFileType === 'jpg') {
            if (Configuration::get('PS_IMAGE_QUALITY') == 'png_all'
                || (Configuration::get('PS_IMAGE_QUALITY') == 'png' && $sourceFileType == IMAGETYPE_PNG)) {
                $destinationFileType = 'png';
            }

            if (Configuration::get('PS_IMAGE_QUALITY') == 'webp_all'
                || (Configuration::get('PS_IMAGE_QUALITY') == 'webp' && $sourceFileType == IMAGETYPE_WEBP)) {
                $destinationFileType = 'webp';
            }
        }

        if (!$sourceWidth) {
            $error = self::ERROR_FILE_WIDTH;

            return false;
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
            $error = self::ERROR_MEMORY_LIMIT;

            return false;
        }

        $targetWidth = $destinationWidth;
        $targetHeight = $destinationHeight;

        $destImage = imagecreatetruecolor($destinationWidth, $destinationHeight);

        // If the output is PNG, fill with transparency. Else fill with white background.
        if ($destinationFileType == 'png' || $destinationFileType == 'webp' || $destinationFileType == 'avif') {
            // if png color type is 3, the file is paletted (256 colors). Change palette to reduce file size
            if (self::getPNGColorType($sourceFile) == 3) {
                imagetruecolortopalette($destImage, false, 255);
            } else {
                imagealphablending($destImage, false);
            }
            imagesavealpha($destImage, true);
            $transparent = imagecolorallocatealpha($destImage, 255, 255, 255, 127);
            imagefilledrectangle($destImage, 0, 0, $destinationWidth, $destinationHeight, $transparent);
        } else {
            $white = imagecolorallocate($destImage, 255, 255, 255);
            imagefilledrectangle($destImage, 0, 0, $destinationWidth, $destinationHeight, $white);
        }

        $srcImage = ImageManager::create($sourceFileType, $sourceFile);
        if ($rotate) {
            /** @phpstan-ignore-next-line */
            $srcImage = imagerotate($srcImage, $rotate, 0);
        }

        if ($destinationWidth >= $sourceWidth && $destinationHeight >= $sourceHeight) {
            imagecopyresized($destImage, $srcImage, (int) (($destinationWidth - $nextWidth) / 2), (int) (($destinationHeight - $nextHeight) / 2), 0, 0, $nextWidth, $nextHeight, $sourceWidth, $sourceHeight);
        } else {
            ImageManager::imagecopyresampled($destImage, $srcImage, (int) (($destinationWidth - $nextWidth) / 2), (int) (($destinationHeight - $nextHeight) / 2), 0, 0, $nextWidth, $nextHeight, $sourceWidth, $sourceHeight, $quality);
        }
        $writeFile = ImageManager::write($destinationFileType, $destImage, $destinationFile);
        Hook::exec('actionOnImageResizeAfter', ['dst_file' => $destinationFile, 'file_type' => $destinationFileType]);
        @imagedestroy($srcImage);

        file_put_contents(
            dirname($destinationFile) . DIRECTORY_SEPARATOR . 'fileType',
            $destinationFileType
        );

        return $writeFile;
    }

    /**
     * @param resource|GdImage $dstImage
     * @param resource|GdImage $srcImage
     * @param int $dstX
     * @param int $dstY
     * @param int $srcX
     * @param int $srcY
     * @param int $dstW
     * @param int $dstH
     * @param int $srcW
     * @param int $srcH
     * @param int $quality
     *
     * @return bool
     */
    public static function imagecopyresampled(
        // @phpstan-ignore-next-line
        &$dstImage,
        // @phpstan-ignore-next-line
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

        if ($quality <= 0) {
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
     * @param string $filename
     *
     * @return string|bool
     */
    public static function getMimeType(string $filename)
    {
        $mimeType = false;
        // Try with GD
        if (function_exists('getimagesize')) {
            $imageInfo = @getimagesize($filename);
            if ($imageInfo) {
                $mimeType = $imageInfo['mime'];
            }
        }
        // Try with FileInfo
        if (!$mimeType && function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $filename);
            finfo_close($finfo);
        }
        // Try with Mime
        if (!$mimeType && function_exists('mime_content_type')) {
            $mimeType = mime_content_type($filename);
        }
        // Try with exec command and file binary
        if (!$mimeType && function_exists('exec')) {
            $mimeType = trim(exec('file -b --mime-type ' . escapeshellarg($filename)));
            if (!$mimeType) {
                $mimeType = trim(exec('file --mime ' . escapeshellarg($filename)));
            }
            if (!$mimeType) {
                $mimeType = trim(exec('file -bi ' . escapeshellarg($filename)));
            }
        }

        return $mimeType;
    }

    /**
     * Check if file is a real image.
     *
     * @param string $filename File path to check
     * @param string $fileMimeType File known mime type (generally from $_FILES)
     * @param array<string>|null $mimeTypeList Allowed MIME types
     *
     * @return bool
     */
    public static function isRealImage($filename, $fileMimeType = null, $mimeTypeList = null)
    {
        if (!$mimeTypeList) {
            $mimeTypeList = static::MIME_TYPE_SUPPORTED;
        }

        $mimeType = static::getMimeType($filename);

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
     * @param array<string>|null $authorizedExtensions
     *
     * @return bool True if it's correct
     */
    public static function isCorrectImageFileExt($filename, $authorizedExtensions = null)
    {
        // Filter on file extension
        if (empty($authorizedExtensions)) {
            $authorizedExtensions = static::EXTENSIONS_SUPPORTED;
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
     * @param array<string>|null $types Authorized extensions
     * @param array<string>|null $mimeTypeList Authorized mimetypes
     *
     * @return bool|string Return false if no error encountered
     */
    public static function validateUpload($file, $maxFileSize = 0, $types = null, $mimeTypeList = null)
    {
        if ((int) $maxFileSize > 0 && $file['size'] > (int) $maxFileSize) {
            return Context::getContext()->getTranslator()->trans('Image is too large (%1$d kB). Maximum allowed: %2$d kB', [$file['size'] / 1024, $maxFileSize / 1024], 'Admin.Notifications.Error');
        }
        if (!ImageManager::isRealImage($file['tmp_name'], $file['type'], $mimeTypeList)
            || !ImageManager::isCorrectImageFileExt($file['name'], $types)
            || preg_match('/\%00/', $file['name'])
        ) {
            return Context::getContext()->getTranslator()->trans(
                'Image format not recognized, allowed formats are: %s',
                [
                    implode(', ', is_null($types) ? static::EXTENSIONS_SUPPORTED : $types),
                ],
                'Admin.Notifications.Error'
            );
        }
        if ($file['error']) {
            return Context::getContext()->getTranslator()->trans('Error while uploading image; please change your server\'s settings. (Error code: %s)', [$file['error']], 'Admin.Notifications.Error');
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
            return Context::getContext()->getTranslator()->trans('Image is too large (%1$d kB). Maximum allowed: %2$d kB', [$file['size'] / 1000, $maxFileSize / 1000], 'Admin.Notifications.Error');
        }
        if (substr($file['name'], -4) != '.ico') {
            return Context::getContext()->getTranslator()->trans('Image format not recognized, allowed formats are: .ico', [], 'Admin.Notifications.Error');
        }
        if ($file['error']) {
            return Context::getContext()->getTranslator()->trans('Error while uploading image; please change your server\'s settings.', [], 'Admin.Notifications.Error');
        }

        return false;
    }

    /**
     * Cut image.
     *
     * @param string $srcFile Origin filename
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
        $src = [
            'width' => $srcInfo[0],
            'height' => $srcInfo[1],
            'ressource' => ImageManager::create($srcInfo[2], $srcFile),
        ];

        // Destination information
        $dest = [];
        $dest['x'] = $dstX;
        $dest['y'] = $dstY;
        $dest['width'] = null !== $dstWidth ? $dstWidth : $src['width'];
        $dest['height'] = null !== $dstHeight ? $dstHeight : $src['height'];
        $dest['ressource'] = ImageManager::createWhiteImage($dest['width'], $dest['height']);

        $white = imagecolorallocate($dest['ressource'], 255, 255, 255);
        // @phpstan-ignore-next-line
        imagecopyresampled($dest['ressource'], $src['ressource'], 0, 0, $dest['x'], $dest['y'], $dest['width'], $dest['height'], $dest['width'], $dest['height']);
        imagecolortransparent($dest['ressource'], $white);
        $return = ImageManager::write($fileType, $dest['ressource'], $dstFile);
        Hook::exec('actionOnImageCutAfter', ['dst_file' => $dstFile, 'file_type' => $fileType]);
        // @phpstan-ignore-next-line
        @imagedestroy($src['ressource']);

        return $return;
    }

    /**
     * Create an image with GD extension from a given type.
     *
     * @param string $type
     * @param string $filename
     *
     * @return false|resource
     */
    public static function create($type, $filename)
    {
        switch ($type) {
            case IMAGETYPE_GIF:
                return imagecreatefromgif($filename);
            case IMAGETYPE_PNG:
                return imagecreatefrompng($filename);
            case IMAGETYPE_WEBP:
                return imagecreatefromwebp($filename);
            case IMAGETYPE_JPEG:
            default:
                return imagecreatefromjpeg($filename);
        }
    }

    /**
     * Create an empty image with white background.
     *
     * @param int $width
     * @param int $height
     *
     * @phpstan-ignore-next-line
     *
     * @return resource|GdImage
     */
    public static function createWhiteImage($width, $height)
    {
        $image = imagecreatetruecolor($width, $height);
        $white = imagecolorallocate($image, 255, 255, 255);
        imagefill($image, 0, 0, $white);

        // @phpstan-ignore-next-line
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
        static $psWebpQuality = null;
        static $psAvifQuality = null;

        if ($psPngQuality === null) {
            $psPngQuality = Configuration::get('PS_PNG_QUALITY');
        }

        if ($psJpegQuality === null) {
            $psJpegQuality = Configuration::get('PS_JPEG_QUALITY');
        }

        if ($psWebpQuality === null) {
            $psWebpQuality = Configuration::get('PS_WEBP_QUALITY');
        }

        if ($psAvifQuality === null) {
            $psAvifQuality = Configuration::get('PS_AVIF_QUALITY');
        }

        $success = false;
        switch ($type) {
            case 'gif':
                // @phpstan-ignore-next-line
                $success = imagegif($resource, $filename);

                break;

            case 'png':
                $quality = ($psPngQuality === false ? 7 : $psPngQuality);
                // @phpstan-ignore-next-line
                $success = imagepng($resource, $filename, (int) $quality);

                break;

            case 'webp':
                $quality = ($psWebpQuality === false ? 80 : $psWebpQuality);
                // @phpstan-ignore-next-line
                $success = imagewebp($resource, $filename, (int) $quality);

                break;

            case 'avif':
                $quality = ($psAvifQuality === false ? 80 : $psAvifQuality);
                // @phpstan-ignore-next-line
                $success = imageavif($resource, $filename, $quality);

                break;

            case 'jpg':
            case 'jpeg':
            default:
                $quality = ($psJpegQuality === false ? 90 : $psJpegQuality);
                // @phpstan-ignore-next-line
                imageinterlace($resource, true); /// make it PROGRESSIVE
                // @phpstan-ignore-next-line
                $success = imagejpeg($resource, $filename, (int) $quality);

                break;
        }
        // @phpstan-ignore-next-line
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
        $types = [
            'image/gif' => ['gif'],
            'image/jpeg' => ['jpg', 'jpeg'],
            'image/png' => ['png'],
            'image/webp' => ['webp'],
            'image/svg+xml' => ['svg'],
            'image/avif' => ['avif'],
        ];
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

    public static function isSvgMimeType(string $mimeType): bool
    {
        return in_array($mimeType, self::SVG_MIMETYPES);
    }

    /**
     * copyImg copy an image located in $url and save it in a path
     * according to $entity->$id_entity .
     * $id_image is used if we need to add a watermark.
     *
     * @param int $id_entity id of product or category (set in entity)
     * @param int $id_image (default null) id of the image if watermark enabled
     * @param string $url path or url to use
     * @param string $entity 'products' or 'categories'
     * @param bool $regenerate
     *
     * @return bool
     */
    public static function copyImg($id_entity, $id_image = null, $url = '', $entity = 'products', $regenerate = true)
    {
        $tmpfile = tempnam(_PS_TMP_IMG_DIR_, 'ps_import');
        $watermark_types = explode(',', Configuration::get('WATERMARK_TYPES'));

        switch ($entity) {
            default:
            case 'products':
                $image_obj = new Image($id_image);
                $path = $image_obj->getPathForCreation();

                break;
            case 'categories':
                $path = _PS_CAT_IMG_DIR_ . (int) $id_entity;

                break;
            case 'manufacturers':
                $path = _PS_MANU_IMG_DIR_ . (int) $id_entity;

                break;
            case 'suppliers':
                $path = _PS_SUPP_IMG_DIR_ . (int) $id_entity;

                break;
            case 'stores':
                $path = _PS_STORE_IMG_DIR_ . (int) $id_entity;

                break;
        }

        $url = urldecode(trim($url));
        $parced_url = parse_url($url);

        if (isset($parced_url['path'])) {
            $uri = ltrim($parced_url['path'], '/');
            $parts = explode('/', $uri);
            foreach ($parts as &$part) {
                $part = rawurlencode($part);
            }
            unset($part);
            $parced_url['path'] = '/' . implode('/', $parts);
        }

        if (isset($parced_url['query'])) {
            $query_parts = [];
            parse_str($parced_url['query'], $query_parts);
            $parced_url['query'] = http_build_query($query_parts);
        }

        $url = http_build_url('', $parced_url);

        $orig_tmpfile = $tmpfile;

        if (Tools::copy($url, $tmpfile)) {
            // Evaluate the memory required to resize the image: if it's too much, you can't resize it.
            if (!ImageManager::checkImageMemoryLimit($tmpfile)) {
                @unlink($tmpfile);

                return false;
            }

            $tgt_width = $tgt_height = 0;
            $src_width = $src_height = 0;
            $error = 0;
            ImageManager::resize($tmpfile, $path . '.jpg', null, null, 'jpg', false, $error, $tgt_width, $tgt_height, 5, $src_width, $src_height);
            $images_types = ImageType::getImagesTypes($entity, true);

            if ($regenerate) {
                $path_infos = [];
                $path_infos[] = [$tgt_width, $tgt_height, $path . '.jpg'];
                foreach ($images_types as $image_type) {
                    $tmpfile = self::get_best_path($image_type['width'], $image_type['height'], $path_infos);

                    if (ImageManager::resize(
                        $tmpfile,
                        $path . '-' . stripslashes($image_type['name']) . '.jpg',
                        $image_type['width'],
                        $image_type['height'],
                        'jpg',
                        false,
                        $error,
                        $tgt_width,
                        $tgt_height,
                        5,
                        $src_width,
                        $src_height
                    )) {
                        // the last image should not be added in the candidate list if it's bigger than the original image
                        if ($tgt_width <= $src_width && $tgt_height <= $src_height) {
                            $path_infos[] = [$tgt_width, $tgt_height, $path . '-' . stripslashes($image_type['name']) . '.jpg'];
                        }
                        if ($entity == 'products') {
                            if (is_file(_PS_TMP_IMG_DIR_ . 'product_mini_' . (int) $id_entity . '.jpg')) {
                                unlink(_PS_TMP_IMG_DIR_ . 'product_mini_' . (int) $id_entity . '.jpg');
                            }
                            if (is_file(_PS_TMP_IMG_DIR_ . 'product_mini_' . (int) $id_entity . '_' . (int) Context::getContext()->shop->id . '.jpg')) {
                                unlink(_PS_TMP_IMG_DIR_ . 'product_mini_' . (int) $id_entity . '_' . (int) Context::getContext()->shop->id . '.jpg');
                            }
                        }
                    }
                }

                Hook::exec('actionWatermark', ['id_image' => $id_image, 'id_product' => $id_entity]);
            }
        } else {
            @unlink($orig_tmpfile);

            return false;
        }
        unlink($orig_tmpfile);

        return true;
    }

    public static function get_best_path($tgt_width, $tgt_height, $path_infos)
    {
        $path_infos = array_reverse($path_infos);
        $path = '';
        foreach ($path_infos as $path_info) {
            list($width, $height, $path) = $path_info;
            if ($width >= $tgt_width && $height >= $tgt_height) {
                return $path;
            }
        }

        return $path;
    }

    /**
     * The function `getPNGColorType` returns the color type byte from a PNG file
     *
     * @param string $fileName
     *
     * @return int|bool
     */
    public static function getPNGColorType($fileName)
    {
        $handle = fopen($fileName, 'r');
        if (false === $handle) {
            return false;
        }

        // set pointer to the color type byte and read it
        fseek($handle, 25);
        $colorTypeByte = fread($handle, 1);
        fclose($handle);

        return ord($colorTypeByte);
    }
}

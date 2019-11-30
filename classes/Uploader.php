<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
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
 * Class UploaderCore.
 */
class UploaderCore
{
    const DEFAULT_MAX_SIZE = 10485760;

    private $_check_file_size;
    private $_accept_types;
    private $_files;
    private $_max_size;
    private $_name;
    private $_save_path;

    /**
     * UploaderCore constructor.
     *
     * @param null $name
     */
    public function __construct($name = null)
    {
        $this->setName($name);
        $this->setCheckFileSize(true);
        $this->files = array();
    }

    /**
     * @param $value
     *
     * @return $this
     */
    public function setAcceptTypes($value)
    {
        $this->_accept_types = $value;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getAcceptTypes()
    {
        return $this->_accept_types;
    }

    /**
     * @param $value
     *
     * @return $this
     */
    public function setCheckFileSize($value)
    {
        $this->_check_file_size = $value;

        return $this;
    }

    /**
     * @param string|null $fileName
     *
     * @return string
     */
    public function getFilePath($fileName = null)
    {
        if (!isset($fileName)) {
            return tempnam($this->getSavePath(), $this->getUniqueFileName());
        }

        $pathInfo = pathinfo($fileName);
        if (isset($pathInfo['extension'])) {
            $fileName = $pathInfo['filename'] . '.' . Tools::strtolower($pathInfo['extension']);
        }

        return $this->getSavePath() . $fileName;
    }

    /**
     * @return array
     */
    public function getFiles()
    {
        if (!isset($this->_files)) {
            $this->_files = array();
        }

        return $this->_files;
    }

    /**
     * @param $value
     *
     * @return $this
     */
    public function setMaxSize($value)
    {
        $this->_max_size = (int) $value;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getMaxSize()
    {
        if (!isset($this->_max_size) || empty($this->_max_size)) {
            $this->setMaxSize(self::DEFAULT_MAX_SIZE);
        }

        return $this->_max_size;
    }

    /**
     * @param $value
     *
     * @return $this
     */
    public function setName($value)
    {
        $this->_name = $value;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * @param $value
     *
     * @return $this
     */
    public function setSavePath($value)
    {
        $this->_save_path = $value;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getPostMaxSizeBytes()
    {
        $postMaxSize = ini_get('post_max_size');
        $bytes = (int) trim($postMaxSize);
        $last = strtolower($postMaxSize[strlen($postMaxSize) - 1]);

        switch ($last) {
            case 'g':
                $bytes *= 1024;
                // no break
            case 'm':
                $bytes *= 1024;
                // no break
            case 'k':
                $bytes *= 1024;
        }

        if ($bytes == '') {
            $bytes = null;
        }

        return $bytes;
    }

    /**
     * @return string
     */
    public function getSavePath()
    {
        if (!isset($this->_save_path)) {
            $this->setSavePath(_PS_UPLOAD_DIR_);
        }

        return $this->_normalizeDirectory($this->_save_path);
    }

    /**
     * @param string $prefix
     *
     * @return string
     */
    public function getUniqueFileName($prefix = 'PS')
    {
        return uniqid($prefix, true);
    }

    /**
     * @return bool
     */
    public function checkFileSize()
    {
        return isset($this->_check_file_size) && $this->_check_file_size;
    }

    /**
     * @param null $dest
     *
     * @return array
     */
    public function process($dest = null)
    {
        if (isset($_FILES[$this->getName()])) {
            $upload = $_FILES[$this->getName()];

            if (is_array($upload['tmp_name'])) {
                $tmp = array();
                foreach ($upload['tmp_name'] as $index => $value) {
                    $tmp[$index] = array(
                        'tmp_name' => $upload['tmp_name'][$index],
                        'name' => $upload['name'][$index],
                        'size' => $upload['size'][$index],
                        'type' => $upload['type'][$index],
                        'error' => $upload['error'][$index],
                    );

                    $this->files[] = $this->upload($tmp[$index], $dest);
                }
            } else {
                $this->files[] = $this->upload($upload, $dest);
            }
        }

        return $this->files;
    }

    /**
     * @param $file
     * @param null $dest
     *
     * @return mixed
     */
    public function upload($file, $dest = null)
    {
        if ($this->validate($file)) {
            if (isset($dest) && is_dir($dest)) {
                $filePath = $dest;
            } else {
                $filePath = $this->getFilePath(isset($dest) ? $dest : $file['name']);
            }

            if (static::isUploadedFile($file['tmp_name'], -2)) {
                static::moveUploadedFile($file['tmp_name'], $file_path, -2);
            } else {
                // Non-multipart uploads (PUT method support)
                file_put_contents($filePath, fopen('php://input', 'rb'));
            }

            $fileSize = $this->_getFileSize($filePath, true);

            if ($fileSize === $file['size']) {
                $file['save_path'] = $filePath;
            } else {
                $file['size'] = $fileSize;
                unlink($filePath);
                $file['error'] = Context::getContext()->getTranslator()->trans('Server file size is different from local file size', array(), 'Admin.Notifications.Error');
            }
        }

        return $file;
    }

    /**
     * @param $error_code
     *
     * @return array|int|mixed|string
     */
    protected function checkUploadError($error_code)
    {
        $error = UPLOAD_ERR_OK;
        switch ($error_code) {
            case UPLOAD_ERR_INI_SIZE:
                $error = Context::getContext()->getTranslator()->trans('The uploaded file exceeds %s', array(ini_get('upload_max_filesize')), 'Admin.Notifications.Error');

                break;
            case UPLOAD_ERR_FORM_SIZE:
                $error = Context::getContext()->getTranslator()->trans('The uploaded file exceeds %s', array(ini_get('post_max_size')), 'Admin.Notifications.Error');

                break;
            case UPLOAD_ERR_PARTIAL:
                $error = Context::getContext()->getTranslator()->trans('The uploaded file was only partially uploaded', array(), 'Admin.Notifications.Error');

                break;
            case UPLOAD_ERR_NO_FILE:
                $error = Context::getContext()->getTranslator()->trans('No file was uploaded', array(), 'Admin.Notifications.Error');

                break;
            case UPLOAD_ERR_NO_TMP_DIR:
                $error = Context::getContext()->getTranslator()->trans('Missing temporary folder', array(), 'Admin.Notifications.Error');

                break;
            case UPLOAD_ERR_CANT_WRITE:
                $error = Context::getContext()->getTranslator()->trans('Failed to write file to disk', array(), 'Admin.Notifications.Error');

                break;
            case UPLOAD_ERR_EXTENSION:
                $error = Context::getContext()->getTranslator()->trans('A PHP extension stopped the file upload', array(), 'Admin.Notifications.Error');

                break;
            default:
                break;
        }

        return $error;
    }

    /**
     * @param $file
     *
     * @return bool
     */
    protected function validate(&$file)
    {
        $file['error'] = $this->checkUploadError($file['error']);
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return false;
        }

        $postMaxSize = $this->getPostMaxSizeBytes();
        if ($postMaxSize && ($this->_getServerVars('CONTENT_LENGTH') > $postMaxSize)) {
            $file['error'] = Context::getContext()->getTranslator()->trans('The uploaded file exceeds the post_max_size directive in php.ini', array(), 'Admin.Notifications.Error');

            return false;
        }

        if (preg_match('/\%00/', $file['name'])) {
            $file['error'] = Context::getContext()->getTranslator()->trans('Invalid file name', array(), 'Admin.Notifications.Error');

            return false;
        }

        //TODO check mime type.
        $types = $this->getAcceptTypes();
        if (isset($types) && !in_array(Tools::strtolower(pathinfo($file['name'], PATHINFO_EXTENSION)), $types)) {
            $file['error'] = Context::getContext()->getTranslator()->trans('Filetype not allowed', array(), 'Admin.Notifications.Error');

            return false;
        }

        if ($this->checkFileSize() && $file['size'] > $this->getMaxSize()) {
            $file['error'] = Context::getContext()->getTranslator()->trans('File is too big. Current size is %1s, maximum size is %2s.', array($file['size'], $this->getMaxSize()), 'Admin.Notifications.Error');

            return false;
        }

        return true;
    }

    /**
     * @param string $filePath
     * @param bool $clearStatCache
     *
     * @return int
     *
     * @deprecated 1.7.0
     */
    protected function _getFileSize($filePath, $clearStatCache = false)
    {
        return $this->getFileSize($filePath, $clearStatCache);
    }

    /**
     * @param string $filePath
     * @param bool $clearStatCache
     *
     * @return int
     *
     * @since 1.7.0
     */
    protected function getFileSize($filePath, $clearStatCache = false)
    {
        if ($clearStatCache) {
            clearstatcache(true, $filePath);
        }

        return filesize($filePath);
    }

    /**
     * @param $var
     *
     * @return string
     *
     * @deprecated 1.7.0
     */
    protected function _getServerVars($var)
    {
        return $this->getServerVars($var);
    }

    /**
     * @param $var
     *
     * @return string
     *
     * @since 1.7.0
     */
    protected function getServerVars($var)
    {
        return isset($_SERVER[$var]) ? $_SERVER[$var] : '';
    }

    /**
     * @param $directory
     *
     * @return string
     *
     * @deprecated 1.7.0
     */
    protected function _normalizeDirectory($directory)
    {
        return $this->normalizeDirectory($directory);
    }

    /**
     * @param $directory
     *
     * @return string
     *
     * @since 1.7.0
     */
    protected function normalizeDirectory($directory)
    {
        $last = $directory[strlen($directory) - 1];

        if (in_array($last, array('/', '\\'))) {
            $directory[strlen($directory) - 1] = DIRECTORY_SEPARATOR;

            return $directory;
        }

        $directory .= DIRECTORY_SEPARATOR;

        return $directory;
    }

    /**
     * Check if the file was uploaded via HTTP POST, move it to a temporary directory
     * and return the new path.
     *
     * This method is needed if the file needs to be accessed before it is moved
     * to the final destination and the "upload_tmp_dir" in not within the "open_basedir".
     *
     * @param string $uploadName POST name or PHP "tmp_name" if $uploadIndex === -2
     *
     * @return string|false
     */
    public static function getUploadedFilePath(string $uploadName, int $uploadIndex = -1)
    {
        if ($uploadIndex === -2) {
            $found = false;
            foreach ($_FILES as $k => $fArr) {
                if (!empty($fArr['tmp_name'])) {
                    if ($fArr['tmp_name'] === $uploadName) {
                        $uploadName = $k;
                        $uploadIndex = -1;
                        $found = true;
                    } else {
                        foreach ($fArr['tmp_name'] as $k2 => $v) {
                            if ($v === $uploadName) {
                                $uploadName = $k;
                                $uploadIndex = $k2;
                                $found = true;
                            }
                        }
                    }
                }
            }
            if (!$found) {
                return false;
            }
        }

        if (empty($_FILES[$uploadName]) || empty($_FILES[$uploadName]['tmp_name'])) {
            return false;
        } elseif ($uploadIndex === -1) {
            if (!is_string($_FILES[$uploadName]['tmp_name'])) {
                return false;
            } else {
                $uploadFileArr = $_FILES[$uploadName];
            }
        } else {
            if (empty($_FILES[$uploadName]['tmp_name'][$uploadIndex]) || !is_string($_FILES[$uploadName]['tmp_name'][$uploadIndex])) {
                return false;
            } else {
                $uploadFileArr = [];
                foreach ($_FILES[$uploadName] as $k => $vArr) {
                    if (isset($vArr[$uploadIndex])) {
                        $uploadFileArr[$k] = $vArr[$uploadIndex];
                    }
                }
            }
        }

        if (!isset($uploadFileArr['ps_upload__tmp_name'])) {
            if ($uploadIndex === -1) {
                $uploadFileArr['ps_upload__tmp_name'] = &$_FILES[$uploadName]['ps_upload__tmp_name'];
            } else {
                if (!isset($_FILES[$uploadName]['ps_upload__tmp_name'])) {
                    $_FILES[$uploadName]['ps_upload__tmp_name'] = [];
                }
                $uploadFileArr['ps_upload__tmp_name'] = &$_FILES[$uploadName]['ps_upload__tmp_name'][$uploadIndex];
            }

            $uploadFileArr['ps_upload__tmp_name'] = false;
            if (!is_uploaded_file($uploadFileArr['tmp_name'])) {
                return false;
            }

            $tempFilePath = Tools::createTempFilePath();
            if (!move_uploaded_file($uploadFileArr['tmp_name'], $tempFilePath)
                    || !isset($uploadFileArr['name']) || preg_match('~^\s*(?:|\.{1,2})\s*$|[/\\\\\x00-\x1f\x7f"\'*:<>?|]~s', $uploadFileArr['name'])
                    || !isset($uploadFileArr['error']) || $uploadFileArr['error'] !== UPLOAD_ERR_OK) {
                Tools::unlinkTempFile($tempFilePath);
            } else {
                chmod($tempFilePath, 0600);
                $uploadFileArr['ps_upload__tmp_name'] = $tempFilePath;
            }
        }

        if ($uploadFileArr['ps_upload__tmp_name'] !== false && is_file($uploadFileArr['ps_upload__tmp_name'])) {
            return $uploadFileArr['ps_upload__tmp_name'];
        } else {
            return false;
        }
    }

    /**
     * Check if the file was uploaded via HTTP POST and move it to a temporary directory.
     *
     * This method is needed if the file needs to be accessed before it is moved
     * to the final destination and the "upload_tmp_dir" in not within the "open_basedir".
     *
     * @param string $uploadName POST name or PHP "tmp_name" if $uploadIndex === -2
     */
    public static function isUploadedFile(string $uploadName, int $uploadIndex = -1): bool
    {
        return static::getUploadedFilePath($uploadName, $uploadIndex) !== false;
    }

    /**
     * Check if the file was uploaded via HTTP POST and move it to the destination path.
     *
     * This method is needed if the file needs to be accessed before it is moved
     * to the final destination and the "upload_tmp_dir" in not within the "open_basedir".
     *
     * @param string $uploadName POST name or PHP "tmp_name" if $uploadIndex === -2
     */
    public static function moveUploadedFile(string $uploadName, string $destinationPath, int $uploadIndex = -1): bool
    {
        $tempFilePath = static::getUploadedFilePath($uploadName, $uploadIndex);
        if ($tempFilePath !== false && $destinationPath !== '') {
            $res = rename($tempFilePath, $destinationPath);
            static::unlinkUploadedFile($uploadName, $uploadIndex);

            return $res;
        } else {
            return false;
        }
    }

    /**
     * Check if the file was uploaded via HTTP POST and delete it.
     *
     * This method is needed if the file needs to be accessed before it is moved
     * to the final destination and the "upload_tmp_dir" in not within the "open_basedir".
     *
     * @param string $uploadName POST name or PHP "tmp_name" if $uploadIndex === -2
     */
    public static function unlinkUploadedFile(string $uploadName, int $uploadIndex = -1): bool
    {
        $tempFilePath = static::getUploadedFilePath($uploadName, $uploadIndex);
        if ($tempFilePath !== false) {
            return @Tools::unlinkTempFile($tempFilePath);
        } else {
            return false;
        }
    }
}

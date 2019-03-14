<?php
/**
 * 2007-2018 PrestaShop.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
class HelperImageUploaderCore extends HelperUploader
{
    public function getMaxSize()
    {
        return (int) Tools::getMaxUploadSize();
    }

    public function getSavePath()
    {
        return $this->_normalizeDirectory(_PS_TMP_IMG_DIR_);
    }

    public function getFilePath($file_name = null)
    {
        //Force file path
        return tempnam($this->getSavePath(), $this->getUniqueFileName());
    }

    protected function validate(&$file)
    {
        $file['error'] = $this->checkUploadError($file['error']);

        $post_max_size = Tools::convertBytes(ini_get('post_max_size'));

        $upload_max_filesize = Tools::convertBytes(ini_get('upload_max_filesize'));

        if ($post_max_size && ($this->_getServerVars('CONTENT_LENGTH') > $post_max_size)) {
            $file['error'] = Context::getContext()->getTranslator()->trans('The uploaded file exceeds the post_max_size directive in php.ini', array(), 'Admin.Notifications.Error');

            return false;
        }

        if ($upload_max_filesize && ($this->_getServerVars('CONTENT_LENGTH') > $upload_max_filesize)) {
            $file['error'] = Context::getContext()->getTranslator()->trans('The uploaded file exceeds the upload_max_filesize directive in php.ini', array(), 'Admin.Notifications.Error');

            return false;
        }

        if ($error = ImageManager::validateUpload($file, Tools::getMaxUploadSize($this->getMaxSize()), $this->getAcceptTypes())) {
            $file['error'] = $error;

            return false;
        }

        if ($file['size'] > $this->getMaxSize()) {
            $file['error'] = Context::getContext()->getTranslator()->trans('File is too big. Current size is %1s, maximum size is %2s.', array($file['size'], $this->getMaxSize()), 'Admin.Notifications.Error');

            return false;
        }

        return true;
    }
}

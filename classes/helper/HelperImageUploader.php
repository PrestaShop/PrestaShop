<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
class HelperImageUploaderCore extends HelperUploader
{
    public function getMaxSize()
    {
        return (int) Tools::getMaxUploadSize();
    }

    public function getSavePath()
    {
        return $this->normalizeDirectory(_PS_TMP_IMG_DIR_);
    }

    public function getFilePath($file_name = null)
    {
        // Force file path
        return tempnam($this->getSavePath(), $this->getUniqueFileName());
    }

    protected function validate(&$file)
    {
        $file['error'] = $this->checkUploadError($file['error']);

        $post_max_size = Tools::convertBytes(ini_get('post_max_size'));

        $upload_max_filesize = Tools::convertBytes(ini_get('upload_max_filesize'));

        if ($post_max_size && ($this->getServerVars('CONTENT_LENGTH') > $post_max_size)) {
            $file['error'] = Context::getContext()->getTranslator()->trans('The uploaded file exceeds the post_max_size directive in php.ini', [], 'Admin.Notifications.Error');

            return false;
        }

        if ($upload_max_filesize && ($this->getServerVars('CONTENT_LENGTH') > $upload_max_filesize)) {
            $file['error'] = Context::getContext()->getTranslator()->trans('The uploaded file exceeds the upload_max_filesize directive in php.ini', [], 'Admin.Notifications.Error');

            return false;
        }

        if ($error = ImageManager::validateUpload($file, Tools::getMaxUploadSize($this->getMaxSize()), $this->getAcceptTypes())) {
            $file['error'] = $error;

            return false;
        }

        if ($file['size'] > $this->getMaxSize()) {
            $file['error'] = Context::getContext()->getTranslator()->trans('File is too big. Current size is %1s, maximum size is %2s.', [$file['size'], $this->getMaxSize()], 'Admin.Notifications.Error');

            return false;
        }

        return true;
    }
}

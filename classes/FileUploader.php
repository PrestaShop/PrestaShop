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
class FileUploaderCore
{
    protected $allowedExtensions = array();

    /** @var false|QqUploadedFileForm|QqUploadedFileXhr */
    protected $file;
    protected $sizeLimit;

    public function __construct(array $allowedExtensions = array(), $sizeLimit = 10485760)
    {
        $allowedExtensions = array_map('strtolower', $allowedExtensions);

        $this->allowedExtensions = $allowedExtensions;
        $this->sizeLimit = $sizeLimit;

        if (isset($_GET['qqfile'])) {
            $this->file = new QqUploadedFileXhr();
        } elseif (isset($_FILES['qqfile'])) {
            $this->file = new QqUploadedFileForm();
        } else {
            $this->file = false;
        }
    }

    protected function toBytes($str)
    {
        $val = trim($str);
        $last = mb_strtolower($str[mb_strlen($str) - 1]);
        switch ($last) {
            case 'g':
                $val *= 1024;
                // no break
            case 'm':
                $val *= 1024;
                // no break
            case 'k':
                $val *= 1024;
        }

        return $val;
    }

    /**
     * Returns array('success'=>true) or array('error'=>'error message').
     */
    public function handleUpload()
    {
        if (!$this->file) {
            return array('error' => Context::getContext()->getTranslator()->trans('No files were uploaded.', array(), 'Admin.Notifications.Error'));
        }

        $size = $this->file->getSize();

        if ($size == 0) {
            return array('error' => Context::getContext()->getTranslator()->trans('Source file does not exist or is empty.', array(), 'Admin.Notifications.Error'));
        }
        if ($size > $this->sizeLimit) {
            return array('error' => Context::getContext()->getTranslator()->trans('The uploaded file is too large.', array(), 'Admin.Notifications.Error'));
        }

        $pathinfo = pathinfo($this->file->getName());
        $these = implode(', ', $this->allowedExtensions);
        if (!isset($pathinfo['extension'])) {
            return array('error' => Context::getContext()->getTranslator()->trans('File has an invalid extension, it should be one of these: %s.', array($these), 'Admin.Notifications.Error'));
        }
        $ext = $pathinfo['extension'];
        if ($this->allowedExtensions && !in_array(mb_strtolower($ext), $this->allowedExtensions, true)) {
            return array('error' => Context::getContext()->getTranslator()->trans('File has an invalid extension, it should be one of these: %s.', array($these), 'Admin.Notifications.Error'));
        }

        return $this->file->save();
    }
}

<?php
/**
 * 2007-2016 PrestaShop
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
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2016 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

/**
 * Class FileUploaderCore
 */
class FileUploaderCore
{
    protected $allowedExtensions = array();

    /** @var QqUploadedFileXhr|QqUploadedFileForm|QqUploadedFileXhrCore|QqUploadedFileFormCore|false */
    protected $file;
    protected $sizeLimit;

    /**
     * FileUploaderCore constructor.
     *
     * @param array $allowedExtensions
     * @param int   $sizeLimit
     */
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

    /**
     * @param string $str
     *
     * @return int|string
     */
    protected function toBytes($str)
    {
        $val = trim($str);
        $last = strtolower($str[strlen($str) - 1]);
        switch ($last) {
            case 'g': $val *= 1024;
            case 'm': $val *= 1024;
            case 'k': $val *= 1024;
        }

        return $val;
    }

    /**
     * Returns array('success'=>true) or array('error'=>'error message')
     */
    public function handleUpload()
    {
        if (!$this->file) {
            return array('error' => Tools::displayError('No files were uploaded.'));
        }

        $size = $this->file->getSize();

        if ($size == 0) {
            return array('error' => Tools::displayError('File is empty'));
        }
        if ($size > $this->sizeLimit) {
            return array('error' => Tools::displayError('File is too large'));
        }

        $pathinfo = pathinfo($this->file->getName());
        $these = implode(', ', $this->allowedExtensions);
        if (!isset($pathinfo['extension'])) {
            return array('error' => sprintf(Tools::displayError('File has an invalid extension, it should be one of these: %s.'), $these));
        }
        $ext = $pathinfo['extension'];
        if ($this->allowedExtensions && !in_array(strtolower($ext), $this->allowedExtensions)) {
            return array('error' => sprintf(Tools::displayError('File has an invalid extension, it should be one of these: %s.'), $these));
        }

        return $this->file->save();
    }
}

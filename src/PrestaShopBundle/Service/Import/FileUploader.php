<?php
/**
 * 2007-2018 PrestaShop
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

namespace PrestaShopBundle\Service\Import;

use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * FileUploader is responsible for uploading import files to import directory
 */
class FileUploader
{
    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @param ConfigurationInterface $configuration
     * @param TranslatorInterface $translator
     */
    public function __construct(ConfigurationInterface $configuration, TranslatorInterface $translator)
    {
        $this->configuration = $configuration;
        $this->translator = $translator;
    }

    /**
     * Handle import file uploading to admin import/ directory
     *
     * @param UploadedFile $uploadedFile
     *
     * @return File
     *
     * @throws \RuntimeException
     */
    public function upload(UploadedFile $uploadedFile)
    {
        $this->validateUploadedFile($uploadedFile);

        $uploadedFileName = sprintf('%s-%s', date('YmdHis'), $uploadedFile->getClientOriginalName());

        $file = $uploadedFile->move(
            $this->getImporDir(),
            $uploadedFileName
        );

        return $file;
    }

    /**
     * Returns import files directory
     *
     * @return string
     */
    protected function getImporDir()
    {
        return $this->configuration->get('_PS_ADMIN_DIR_').DIRECTORY_SEPARATOR.'import';
    }

    /**
     * Check if uploaded file is valid
     *
     * @param UploadedFile $uploadedFile
     *
     * @throws \RuntimeException
     */
    protected function validateUploadedFile(UploadedFile $uploadedFile)
    {
        $message = false;

        switch ($uploadedFile->getError()) {
            case UPLOAD_ERR_INI_SIZE:
                $message = $this->translator->trans('The uploaded file exceeds the upload_max_filesize directive in php.ini. If your server configuration allows it, you may add a directive in your .htaccess.', [], 'Admin.Advparameters.Notification');
                break;
            case UPLOAD_ERR_FORM_SIZE:
                $message = $this->translator->trans('The uploaded file exceeds the post_max_size directive in php.ini. If your server configuration allows it, you may add a directive in your .htaccess, for example:', [], 'Admin.Advparameters.Notification')
                    .'<br/><a href="'.$this->context->link->getAdminLink('AdminMeta').'" >
					<code>php_value post_max_size 20M</code> '.
                    $this->translator->trans('(click to open "Generators" page)', [], 'Admin.Advparameters.Notification').'</a>';
                break;
            case UPLOAD_ERR_PARTIAL:
                $message = $this->translator->trans('The uploaded file was only partially uploaded.', [], 'Admin.Advparameters.Notification');
                break;
            case UPLOAD_ERR_NO_FILE:
                $message = $this->translator->trans('No file was uploaded.', [], 'Admin.Advparameters.Notification');
                break;
        }

        if ($message) {
            throw new \RuntimeException($message);
        }

        if (!preg_match('#([^\.]*?)\.(csv|xls[xt]?|o[dt]s)$#is', $uploadedFile->getClientOriginalName())) {
            $message = $this->translator->trans('The extension of your file should be .csv.', [], 'Admin.Advparameters.Notification');
        }

        if ($message) {
            throw new \RuntimeException($message);
        }
    }
}

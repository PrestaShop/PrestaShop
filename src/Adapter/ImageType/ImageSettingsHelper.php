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

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\ImageType;

use Image;
use PrestaShop\PrestaShop\Core\ImageType\ImageSettingsHelperInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Helper functions for Image Settings page.
 */
class ImageSettingsHelper implements ImageSettingsHelperInterface
{
    /**
     * @var int
     */
    protected $max_execution_time = 7200;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function moveImagesToNewFileSystem(): array
    {
        $errors = [];

        if (!Image::testFileSystem()) {
            $errors[] = $this->translator->trans(
                'Error: Your server configuration is not compatible with the new image system. No images were moved.',
                [],
                'Admin.Design.Notification'
            );
        } else {
            ini_set('max_execution_time', (string) $this->max_execution_time);
            $this->max_execution_time = (int) ini_get('max_execution_time');
            $result = Image::moveToNewFileSystem($this->max_execution_time);

            if ($result === 'timeout') {
                $errors[] = $this->translator->trans(
                    'Not all images have been moved. The server timed out before finishing. Click on "%move_images_label%" again to resume the moving process.',
                    [
                        '%move_images_label%' => $this->translator->trans('Move images', [], 'Admin.Design.Feature'),
                    ],
                    'Admin.Design.Notification'
                );
            } elseif ($result === false) {
                $errors[] = $this->translator->trans('Error: Some -- or all -- images cannot be moved.', [], 'Admin.Design.Notification');
            }
        }

        return $errors;
    }

    /**
     * {@inheritdoc}
     */
    public function showDuplicatesAlert(): bool
    {
        return file_exists(_PS_PROD_IMG_DIR_ . 'duplicates/');
    }

    /**
     * {@inheritdoc}
     */
    public function showMovingForm(): bool
    {
        $dir = _PS_PROD_IMG_DIR_;
        $displayMove = false;

        if (is_dir($dir)) {
            if ($dh = opendir($dir)) {
                while (($file = readdir($dh)) !== false && $displayMove === false) {
                    if (!is_dir($dir . DIRECTORY_SEPARATOR . $file) && $file[0] != '.' && is_numeric($file[0])) {
                        $displayMove = true;
                    }
                }
                closedir($dh);
            }
        }

        return $displayMove;
    }

    /**
     * {@inheritdoc}
     */
    public function getProductImagesDir(): string
    {
        return _PS_PROD_IMG_DIR_;
    }
}

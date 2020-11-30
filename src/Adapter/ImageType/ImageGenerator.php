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

use Configuration;
use Db;
use Image;
use ImageManager;
use ImageType;
use Language;
use Module;
use PrestaShop\PrestaShop\Core\ImageType\ImageGeneratorInterface;
use Symfony\Component\Translation\TranslatorInterface;

class ImageGenerator implements ImageGeneratorInterface
{
    const PROCESS_DIRS = [
        ['type' => 'categories', 'dir' => _PS_CAT_IMG_DIR_],
        ['type' => 'manufacturers', 'dir' => _PS_MANU_IMG_DIR_],
        ['type' => 'suppliers', 'dir' => _PS_SUPP_IMG_DIR_],
        ['type' => 'products', 'dir' => _PS_PROD_IMG_DIR_],
        ['type' => 'stores', 'dir' => _PS_STORE_IMG_DIR_],
    ];

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var int
     */
    private $startTime = 0;

    /**
     * @var int
     */
    private $maxExecutionTime = 7200;

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
    public function deleteOldImages(string $dir, array $type, bool $product = false)
    {
        if (!is_dir($dir)) {
            return;
        }

        foreach (scandir($dir, SCANDIR_SORT_NONE) as $d) {
            foreach ($type as $imageType) {
                if (
                    preg_match('/^[0-9]+\-' . ($product ? '[0-9]+\-' : '') . $imageType['name'] . '\.jpg$/', $d)
                    || (count($type) > 1 && preg_match('/^[0-9]+\-[_a-zA-Z0-9-]*\.jpg$/', $d))
                    || preg_match('/^([[:lower:]]{2})\-default\-' . $imageType['name'] . '\.jpg$/', $d)
                ) {
                    if (file_exists($dir . $d)) {
                        unlink($dir . $d);
                    }
                }
            }
        }

        if ($product) {
            $productsImages = Image::getAllImages();
            foreach ($productsImages as $image) {
                $imageObj = new Image($image['id_image']);
                $imageObj->id_product = $image['id_product'];

                if (file_exists($dir . $imageObj->getImgFolder())) {
                    foreach (scandir($dir . $imageObj->getImgFolder(), SCANDIR_SORT_NONE) as $d) {
                        foreach ($type as $imageType) {
                            if (
                                preg_match('/^[0-9]+\-' . $imageType['name'] . '\.jpg$/', $d)
                                || (count($type) > 1 && preg_match('/^[0-9]+\-[_a-zA-Z0-9-]*\.jpg$/', $d))
                            ) {
                                if (file_exists($dir . $imageObj->getImgFolder() . $d)) {
                                    unlink($dir . $imageObj->getImgFolder() . $d);
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function regenerateNewImages(string $dir, array $type, bool $productsImages = false)
    {
        if (!is_dir($dir)) {
            return false;
        }

        $generateHightDpiImages = (bool) Configuration::get('PS_HIGHT_DPI');
        $errors = [];

        if (!$productsImages) {
            $formated_medium = ImageType::getFormattedName('medium');
            foreach (scandir($dir, SCANDIR_SORT_NONE) as $image) {
                if (preg_match('/^[0-9]*\.jpg$/', $image)) {
                    foreach ($type as $k => $imageType) {
                        $newDir = $dir;
                        if (!file_exists($newDir)) {
                            continue;
                        }

                        if (
                            ($dir == _PS_CAT_IMG_DIR_)
                            && ($imageType['name'] == $formated_medium)
                            && is_file(_PS_CAT_IMG_DIR_ . str_replace('.', '_thumb.', $image))
                        ) {
                            $image = str_replace('.', '_thumb.', $image);
                        }

                        if (!file_exists($newDir . substr($image, 0, -4) . '-' . stripslashes($imageType['name']) . '.jpg')) {
                            if (!file_exists($dir . $image) || !filesize($dir . $image)) {
                                $errors[] = $this->translator->trans('Source file does not exist or is empty (%filepath%)', ['%filepath%' => $dir . $image], 'Admin.Design.Notification');
                            } elseif (!ImageManager::resize($dir . $image, $newDir . substr(str_replace('_thumb.', '.', $image), 0, -4) . '-' . stripslashes($imageType['name']) . '.jpg', (int) $imageType['width'], (int) $imageType['height'])) {
                                $errors[] = $this->translator->trans('Failed to resize image file (%filepath%)', ['%filepath%' => $dir . $image], 'Admin.Design.Notification');
                            }

                            if ($generateHightDpiImages) {
                                if (!ImageManager::resize($dir . $image, $newDir . substr($image, 0, -4) . '-' . stripslashes($imageType['name']) . '2x.jpg', (int) $imageType['width'] * 2, (int) $imageType['height'] * 2)) {
                                    $errors[] = $this->translator->trans('Failed to resize image file to high resolution (%filepath%)', ['%filepath%' => $dir . $image], 'Admin.Design.Notification');
                                }
                            }
                        }

                        // stop 4 seconds before the timeout, just enough time to process the end of the page on a slow server
                        if (time() - $this->startTime > $this->maxExecutionTime - 4) {
                            return 'timeout';
                        }
                    }
                }
            }
        } else {
            foreach (Image::getAllImages() as $image) {
                $imageObj = new Image($image['id_image']);
                $existing_img = $dir . $imageObj->getExistingImgPath() . '.jpg';
                if (file_exists($existing_img) && filesize($existing_img)) {
                    foreach ($type as $imageType) {
                        if (!file_exists($dir . $imageObj->getExistingImgPath() . '-' . stripslashes($imageType['name']) . '.jpg')) {
                            if (!ImageManager::resize($existing_img, $dir . $imageObj->getExistingImgPath() . '-' . stripslashes($imageType['name']) . '.jpg', (int) $imageType['width'], (int) $imageType['height'])) {
                                $errors[] = $this->translator->trans(
                                    'Original image is corrupt (%filename%) for product ID %id% or bad permission on folder.',
                                    [
                                        '%filename%' => $existing_img,
                                        '%id%' => (int) $imageObj->id_product,
                                    ],
                                    'Admin.Design.Notification'
                                );
                            }
                        }
                        if ($generateHightDpiImages) {
                            if (!file_exists($dir . $imageObj->getExistingImgPath() . '-' . stripslashes($imageType['name']) . '2x.jpg')) {
                                if (!ImageManager::resize($existing_img, $dir . $imageObj->getExistingImgPath() . '-' . stripslashes($imageType['name']) . '2x.jpg', (int) $imageType['width'] * 2, (int) $imageType['height'] * 2)) {
                                    $errors[] = $this->translator->trans(
                                        'Original image is corrupt (%filename%) for product ID %id% or bad permission on folder.',
                                        [
                                            '%filename%' => $existing_img,
                                            '%id%' => (int) $imageObj->id_product,
                                        ],
                                        'Admin.Design.Notification'
                                    );
                                }
                            }
                        }
                    }
                } else {
                    $errors[] = $this->translator->trans(
                        'Original image is missing or empty (%filename%) for product ID %id%',
                        [
                            '%filename%' => $existing_img,
                            '%id%' => (int) $imageObj->id_product,
                        ],
                        'Admin.Design.Notification'
                    );
                }
                if (time() - $this->startTime > $this->maxExecutionTime - 4) { // stop 4 seconds before the tiemout, just enough time to process the end of the page on a slow server
                    return 'timeout';
                }
            }
        }

        return $errors;
    }

    /**
     * {@inheritdoc}
     */
    public function regenerateNoPictureImages(string $dir, array $type, array $languages)
    {
        $errors = false;
        $generate_hight_dpi_images = (bool) Configuration::get('PS_HIGHT_DPI');

        foreach ($type as $image_type) {
            foreach ($languages as $language) {
                $file = $dir . $language['iso_code'] . '.jpg';
                if (!file_exists($file)) {
                    $file = _PS_PROD_IMG_DIR_ . Language::getIsoById((int) Configuration::get('PS_LANG_DEFAULT')) . '.jpg';
                }
                if (!file_exists($dir . $language['iso_code'] . '-default-' . stripslashes($image_type['name']) . '.jpg')) {
                    if (!ImageManager::resize($file, $dir . $language['iso_code'] . '-default-' . stripslashes($image_type['name']) . '.jpg', (int) $image_type['width'], (int) $image_type['height'])) {
                        $errors = true;
                    }

                    if ($generate_hight_dpi_images) {
                        if (!ImageManager::resize($file, $dir . $language['iso_code'] . '-default-' . stripslashes($image_type['name']) . '2x.jpg', (int) $image_type['width'] * 2, (int) $image_type['height'] * 2)) {
                            $errors = true;
                        }
                    }
                }
            }
        }

        return $errors;
    }

    /**
     * {@inheritdoc}
     */
    public function regenerateWatermark(string $dir, ?array $type = null)
    {
        $result = Db::getInstance()->executeS('
		SELECT m.`name` FROM `' . _DB_PREFIX_ . 'module` m
		LEFT JOIN `' . _DB_PREFIX_ . 'hook_module` hm ON hm.`id_module` = m.`id_module`
		LEFT JOIN `' . _DB_PREFIX_ . 'hook` h ON hm.`id_hook` = h.`id_hook`
		WHERE h.`name` = \'actionWatermark\' AND m.`active` = 1');

        if ($result && count($result)) {
            $productsImages = Image::getAllImages();
            foreach ($productsImages as $image) {
                $imageObj = new Image($image['id_image']);
                if (file_exists($dir . $imageObj->getExistingImgPath() . '.jpg')) {
                    foreach ($result as $module) {
                        $moduleInstance = Module::getInstanceByName($module['name']);
                        if ($moduleInstance && is_callable([$moduleInstance, 'hookActionWatermark'])) {
                            call_user_func([$moduleInstance, 'hookActionWatermark'], ['id_image' => $imageObj->id, 'id_product' => $imageObj->id_product, 'image_type' => $type]);
                        }

                        if (time() - $this->startTime > $this->maxExecutionTime - 4) { // stop 4 seconds before the tiemout, just enough time to process the end of the page on a slow server
                            return 'timeout';
                        }
                    }
                }
            }
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function regenerateThumbnails(array $data): array
    {
        $this->startTime = time();
        ini_set('max_execution_time', (string) $this->maxExecutionTime);
        $this->maxExecutionTime = (int) ini_get('max_execution_time');
        $languages = Language::getLanguages(false);
        $errors = [];
        $type = $data['image_category'];
        $deleteOldImages = $data['erase_previous_images'];

        foreach (self::PROCESS_DIRS as $proc) {
            if ($type != 'all' && $type != $proc['type']) {
                continue;
            }

            // Getting format generation
            $formats = ImageType::getImagesTypes($proc['type']);
            if ($type != 'all') {
                $format = (string) $data['format_' . $type];
                if ($format != 'all') {
                    foreach ($formats as $k => $form) {
                        if ($form['id_image_type'] != $format) {
                            unset($formats[$k]);
                        }
                    }
                }
            }

            if ($deleteOldImages) {
                $this->deleteOldImages($proc['dir'], $formats, $proc['type'] == 'products');
            }
            if (($return = $this->regenerateNewImages($proc['dir'], $formats, $proc['type'] == 'products')) === true) {
                if (!count($errors)) {
                    $errors[] = $this->translator->trans('Cannot write images for this type: %1$s. Please check the %2$s folder\'s writing permissions.', [$proc['type'], $proc['dir']], 'Admin.Design.Notification');
                }
            } elseif ($return == 'timeout') {
                $errors[] = $this->translator->trans('Only part of the images have been regenerated. The server timed out before finishing.', [], 'Admin.Design.Notification');
            } else {
                if ($proc['type'] == 'products') {
                    if ($this->regenerateWatermark($proc['dir'], $formats) == 'timeout') {
                        $errors[] = $this->translator->trans('Server timed out. The watermark may not have been applied to all images.', [], 'Admin.Design.Notification');
                    }
                }
                if (!count($errors)) {
                    if ($this->regenerateNoPictureImages($proc['dir'], $formats, $languages)) {
                        $errors[] = $this->translator->trans('Cannot write "No picture" image to %s images folder. Please check the folder\'s writing permissions.', [$proc['type']], 'Admin.Design.Notification');
                    }
                }
            }
        }

        return $errors;
    }
}

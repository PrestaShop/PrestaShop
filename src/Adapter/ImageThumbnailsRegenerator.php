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

namespace PrestaShop\PrestaShop\Adapter;

use Db;
use Image;
use ImageManager as LegacyImageManager;
use ImageType as LegacyImageType;
use Module as LegacyModule;
use PrestaShop\PrestaShop\Adapter\Product\Image\Repository\ProductImageRepository;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\ImageSettings\Exception\ImageNotDeletedException;
use PrestaShop\PrestaShop\Core\Domain\ImageSettings\Exception\ImageTypeException;
use PrestaShop\PrestaShop\Core\Image\ImageFormatConfiguration;
use PrestaShop\PrestaShop\Core\Language\LanguageRepositoryInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class responsible for regenerating images by image type.
 */
class ImageThumbnailsRegenerator
{
    private int $maxExecutionTime = 7200;
    private int $startTime = 0;

    public function __construct(
      private readonly ProductImageRepository $productImageRepository,
      private readonly ImageFormatConfiguration $imageFormatConfiguration,
      private readonly LanguageRepositoryInterface $langRepository,
      private readonly ConfigurationInterface $configuration,
      private readonly TranslatorInterface $translator,
    ) {
        // Save start time to calculate remaining time and to avoid timeout on long running processes
        $this->startTime = time();
        ini_set('max_execution_time', $this->maxExecutionTime); // ini_set may be disabled, we need the real value
        $this->maxExecutionTime = (int) ini_get('max_execution_time');
    }

    /**
     * Delete previous resized images.
     *
     * @param string $dir
     * @param array $types
     * @param bool $isProduct
     *
     * @return bool
     */
    public function deletePreviousImages(string $dir, array $types, bool $isProduct = false): bool
    {
        if (!is_dir($dir)) {
            return false;
        }
        $toDel = scandir($dir, SCANDIR_SORT_NONE);

        foreach ($toDel as $d) {
            foreach ($types as $imageType) {
                if (preg_match('/^[0-9]+\-' . ($isProduct ? '[0-9]+\-' : '') . $imageType->getName() . '(|2x)\.(jpg|png|webp|avif)$/', $d)
                    || (count($types) > 1 && preg_match('/^[0-9]+\-[_a-zA-Z0-9-]*\.(jpg|png|webp|avif)$/', $d))
                    || preg_match('/^([[:lower:]]{2})\-default\-' . $imageType->getName() . '(|2x)\.(jpg|png|webp|avif)$/', $d)) {
                    if (file_exists($dir . $d)) {
                        unlink($dir . $d);
                    }
                }
            }
        }

        // Delete product images using new filesystem.
        if ($isProduct) {
            $productsImages = $this->productImageRepository->getAllImages();
            foreach ($productsImages as $image) {
                if (file_exists($dir . $image->getImgFolder())) {
                    $toDel = scandir($dir . $image->getImgFolder(), SCANDIR_SORT_NONE);
                    foreach ($toDel as $d) {
                        foreach ($types as $imageType) {
                            if (preg_match('/^[0-9]+\-' . $imageType->getName() . '(|2x)\.(jpg|png|webp|avif)$/', $d)
                                || (count($types) > 1 && preg_match('/^[0-9]+\-[_a-zA-Z0-9-]*\.(jpg|png|webp|avif)$/', $d))) {
                                if (file_exists($dir . $image->getImgFolder() . $d)) {
                                    unlink($dir . $image->getImgFolder() . $d);
                                }
                            }
                        }
                    }
                }
            }
        }

        return true;
    }

    /**
     * Regenerate images.
     *
     * @param string $dir
     * @param array $type
     * @param bool $productsImages
     *
     * @return bool|array
     */
    public function regenerateNewImages(string $dir, array $type, bool $productsImages = false): bool|array
    {
        if (!is_dir($dir)) {
            return false;
        }

        $errors = [];

        /*
         * Let's resolve which formats we will use for image generation.
         *
         * In case of .jpg images, the actual format inside is decided by ImageManager.
         */
        $configuredImageFormats = $this->imageFormatConfiguration->getGenerationFormats();

        if (!$productsImages) {
            $formated_medium = LegacyImageType::getFormattedName('medium');
            foreach (scandir($dir, SCANDIR_SORT_NONE) as $image) {
                if (preg_match('/^[0-9]*\.jpg$/', $image)) {
                    foreach ($type as $k => $imageType) {
                        // Customizable writing dir
                        $newDir = $dir;
                        if (!file_exists($newDir)) {
                            continue;
                        }

                        if (($dir == _PS_CAT_IMG_DIR_) && ($imageType->getName() == $formated_medium) && is_file(_PS_CAT_IMG_DIR_ . str_replace('.', '_thumb.', $image))) {
                            $image = str_replace('.', '_thumb.', $image);
                        }

                        foreach ($configuredImageFormats as $imageFormat) {
                            // If thumbnail does not exist
                            if (!file_exists($newDir . substr($image, 0, -4) . '-' . stripslashes($imageType->getName()) . '.' . $imageFormat)) {
                                // Check if original image exists
                                if (!file_exists($dir . $image) || !filesize($dir . $image)) {
                                    $errors[] = $this->translator->trans('Source file does not exist or is empty (%filepath%)', ['%filepath%' => $dir . $image], 'Admin.Design.Notification');
                                } else {
                                    if (!LegacyImageManager::resize(
                                        $dir . $image,
                                        $newDir . substr(str_replace('_thumb.', '.', $image), 0, -4) . '-' . stripslashes($imageType->getName()) . '.' . $imageFormat,
                                        (int) $imageType->getWidth(),
                                        (int) $imageType->getHeight(),
                                        $imageFormat
                                        )) {
                                        $errors[] = $this->translator->trans('Failed to resize image file (%filepath%)', ['%filepath%' => $dir . $image], 'Admin.Design.Notification');
                                    }
                                }
                            }
                        }

                        // stop 4 seconds before the timeout, just enough time to process the end of the page on a slow server
                        if (time() - $this->startTime > $this->maxExecutionTime - 4) {
                            return ['timeout'];
                        }
                    }
                }
            }
        } else {
            foreach ($this->productImageRepository->getAllImages() as $imageObj) {
                $existing_img = $dir . $imageObj->getExistingImgPath() . '.jpg';
                if (file_exists($existing_img) && filesize($existing_img)) {
                    foreach ($type as $imageType) {
                        foreach ($configuredImageFormats as $imageFormat) {
                            if (!file_exists($dir . $imageObj->getExistingImgPath() . '-' . stripslashes($imageType->getName()) . '.' . $imageFormat)) {
                                if (!LegacyImageManager::resize(
                                    $existing_img,
                                    $dir . $imageObj->getExistingImgPath() . '-' . stripslashes($imageType->getName()) . '.' . $imageFormat,
                                    (int) $imageType->getWidth(),
                                    (int) $imageType->getHeight(),
                                    $imageFormat
                                )) {
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
                    return ['timeout'];
                }
            }
        }

        return $errors;
    }

    /* Hook watermark optimization */
    public function regenerateWatermark(string $dir, array $formats = null): bool|string
    {
        $result = Db::getInstance()->executeS('
		SELECT m.`name` FROM `' . _DB_PREFIX_ . 'module` m
		LEFT JOIN `' . _DB_PREFIX_ . 'hook_module` hm ON hm.`id_module` = m.`id_module`
		LEFT JOIN `' . _DB_PREFIX_ . 'hook` h ON hm.`id_hook` = h.`id_hook`
		WHERE h.`name` = \'actionWatermark\' AND m.`active` = 1');

        if ($result && count($result)) {
            $productsImages = $this->productImageRepository->getAllImages();
            foreach ($productsImages as $imageObj) {
                if (file_exists($dir . $imageObj->getExistingImgPath() . '.jpg')) {
                    foreach ($result as $module) {
                        $moduleInstance = LegacyModule::getInstanceByName($module['name']);
                        if ($moduleInstance && is_callable([$moduleInstance, 'hookActionWatermark'])) {
                            call_user_func([$moduleInstance, 'hookActionWatermark'], ['id_image' => $imageObj->id, 'id_product' => $imageObj->id_product, 'image_type' => $formats]);
                        }

                        if (time() - $this->startTime > $this->maxExecutionTime - 4) { // stop 4 seconds before the tiemout, just enough time to process the end of the page on a slow server
                            return 'timeout';
                        }
                    }
                }
            }
        }

        return true;
    }

    /**
     * Regenerate no-pictures images.
     *
     * @param string $dir
     * @param array $type
     * @param array $languages
     *
     * @return bool
     */
    public function regenerateNoPictureImages(string $dir, array $type, array $languages): bool
    {
        $defaultLang = $this->langRepository->findOneBy(['id' => (int) $this->configuration->get('PS_LANG_DEFAULT')]);
        $errors = false;

        /*
         * Let's resolve which formats we will use for image generation.
         *
         * In case of .jpg images, the actual format inside is decided by ImageManager.
         */
        $configuredImageFormats = $this->imageFormatConfiguration->getGenerationFormats();

        foreach ($type as $image_type) {
            foreach ($languages as $language) {
                $file = $dir . $language->getIsoCode() . '.jpg';
                if (!file_exists($file)) {
                    $file = _PS_PRODUCT_IMG_DIR_ . $defaultLang->getIsoCode() . '.jpg';
                }
                foreach ($configuredImageFormats as $imageFormat) {
                    if (!file_exists($dir . $language->getIsoCode() . '-default-' . stripslashes($image_type->getName()) . '.' . $imageFormat)) {
                        if (!LegacyImageManager::resize(
                            $file,
                            $dir . $language->getIsoCode() . '-default-' . stripslashes($image_type->getName()) . '.' . $imageFormat,
                            (int) $image_type->getWidth(),
                            (int) $image_type->getHeight(),
                            $imageFormat
                        )) {
                            $errors = true;
                        }
                    }
                }
            }
        }

        return $errors;
    }

    /**
     * Function aim to delete all images from defined image type
     *
     * @throws ImageTypeException
     */
    public function deleteImagesFromType($imageTypeName, $path): void
    {
        foreach (glob($path . '*', GLOB_BRACE) as $file) {
            if (is_dir($file)) {
                $this->deleteImagesFromType($imageTypeName, $file . '/');
            } else {
                if (
                    preg_match('/\/(\d+|\w{2}-default)-' . $imageTypeName . '\.(jpg|png|webp|avif)$/', $file)
                ) {
                    if (!unlink($file)) {
                        throw new ImageNotDeletedException(sprintf('Unable to delete image "%s"', $file));
                    }
                }
            }
        }
    }
}

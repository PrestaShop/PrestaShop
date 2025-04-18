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
use ImageManager as LegacyImageManager;
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

        // Prepare regular expression to use when deleting thumbnails
        $regexTypes = [];
        foreach ($types as $type) {
            $regexTypes[] = $type->getName();
        }
        $regexStandard = '/^[0-9]+(|_thumb)\-(' . implode('|', $regexTypes) . ')(|2x)\.(' . implode('|', ImageFormatConfiguration::SUPPORTED_FORMATS) . ')$/';
        $regexPlaceholders = '/^([[:lower:]]{2})\-default\-(' . implode('|', $regexTypes) . ')(|2x)\.(' . implode('|', ImageFormatConfiguration::SUPPORTED_FORMATS) . ')$/';
        $regexProducts = '/^[0-9]+\-(' . implode('|', $regexTypes) . ')(|2x)\.(' . implode('|', ImageFormatConfiguration::SUPPORTED_FORMATS) . ')$/';

        /*
         * Scan all files in the given folder.
         * We do this also in case of products, because it will take care of placeholder thumbnails, hence the second regex.
         */
        $filesToDelete = scandir($dir, SCANDIR_SORT_NONE);
        foreach ($filesToDelete as $file) {
            if ((preg_match($regexStandard, $file) || preg_match($regexPlaceholders, $file)) && file_exists($dir . $file)) {
                unlink($dir . $file);
            }
        }

        // Delete product images
        if ($isProduct) {
            // Get all product images in the shop
            $productsImages = $this->productImageRepository->getAllImages();
            foreach ($productsImages as $image) {
                // Get path to current image folder, example: /img/p/1/2/3
                $pathToImageFolder = $dir . $image->getImgFolder();
                if (file_exists($pathToImageFolder)) {
                    // Scan all files in the given folder
                    $filesToDelete = scandir($pathToImageFolder, SCANDIR_SORT_NONE);
                    foreach ($filesToDelete as $d) {
                        if (preg_match($regexProducts, $d) && file_exists($pathToImageFolder . $d)) {
                            unlink($pathToImageFolder . $d);
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
            foreach (scandir($dir, SCANDIR_SORT_NONE) as $originalImageName) {
                /*
                 * Let's find all original image files in this folder.
                 * They are either ID.jpg or ID_thumb.jpg in case of category thumbnails
                 */
                if (preg_match('/^[0-9]*(|_thumb)\.jpg$/', $originalImageName)) {
                    foreach ($type as $imageType) {
                        // Customizable writing dir
                        $newDir = $dir;
                        if (!file_exists($newDir)) {
                            continue;
                        }

                        foreach ($configuredImageFormats as $imageFormat) {
                            $thumbnailName = substr($originalImageName, 0, -4) . '-' . stripslashes($imageType->getName()) . '.' . $imageFormat;
                            // If thumbnail does not exist
                            if (!file_exists($newDir . $thumbnailName)) {
                                // Check if original image exists
                                if (!file_exists($dir . $originalImageName) || !filesize($dir . $originalImageName)) {
                                    $errors[] = $this->translator->trans('Source file does not exist or is empty (%filepath%)', ['%filepath%' => $dir . $originalImageName], 'Admin.Design.Notification');
                                } else {
                                    if (!LegacyImageManager::resize(
                                        $dir . $originalImageName,
                                        $newDir . $thumbnailName,
                                        (int) $imageType->getWidth(),
                                        (int) $imageType->getHeight(),
                                        $imageFormat
                                    )) {
                                        $errors[] = $this->translator->trans('Failed to resize image file (%filepath%)', ['%filepath%' => $dir . $originalImageName], 'Admin.Design.Notification');
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
                $originalImageName = $dir . $imageObj->getExistingImgPath() . '.jpg';
                if (file_exists($originalImageName) && filesize($originalImageName)) {
                    foreach ($type as $imageType) {
                        foreach ($configuredImageFormats as $imageFormat) {
                            $thumbnailName = $imageObj->getExistingImgPath() . '-' . stripslashes($imageType->getName()) . '.' . $imageFormat;

                            if (!file_exists($dir . $thumbnailName)) {
                                if (!LegacyImageManager::resize(
                                    $originalImageName,
                                    $dir . $thumbnailName,
                                    (int) $imageType->getWidth(),
                                    (int) $imageType->getHeight(),
                                    $imageFormat
                                )) {
                                    $errors[] = $this->translator->trans(
                                        'Original image is corrupt (%filename%) for product ID %id% or bad permission on folder.',
                                        [
                                            '%filename%' => $originalImageName,
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
                            '%filename%' => $originalImageName,
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
    public function regenerateWatermark(string $dir, ?array $formats = null): bool|string
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
                // We get the "no image available" in the folder of the object
                $file = $dir . $language->getIsoCode() . '.jpg';

                if (!file_exists($file)) {
                    // If it doesn't exist, we use an image for default language
                    $file = $dir . $defaultLang->getIsoCode() . '.jpg';

                    if (!file_exists($file)) {
                        // If it doesn't exist, we use a fallback one in the root of img directory
                        $file = _PS_IMG_DIR_ . 'noimageavailable.jpg';
                    }
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

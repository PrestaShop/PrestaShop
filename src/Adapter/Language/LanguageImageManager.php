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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\Language;

use ImageType;

/**
 * Handles language images (flag, "no image" placeholders)
 */
class LanguageImageManager
{
    /**
     * Path where images are saved to
     */
    const IMG_PATH = _PS_IMG_DIR_ . '/l/';

    /**
     * Path where flags are stored
     */
    const FLAGS_SOURCE = _PS_BO_ALL_THEMES_DIR_ . 'new-theme/static/flags/%s.jpg';

    /**
     * Path where flags are copied to
     */
    const FLAGS_DESTINATION = self::IMG_PATH . "%d.jpg";

    /**
     * Default flag
     */
    const FALLBACK_FLAG_SOURCE = self::IMG_PATH . 'none.jpg';

    const IMAGE_DIRECTORIES = [
        _PS_CAT_IMG_DIR_,
        _PS_MANU_IMG_DIR_,
        _PS_PROD_IMG_DIR_,
        _PS_SUPP_IMG_DIR_,
    ];

    const PLACEHOLDER_IMAGE_NAME_PATTERNS = [
        '%s.jpg',
        '%s-default-%s.jpg'
    ];

    const DEFAULT_LANGUAGE_CODE = 'en';

    /**
     * Sets up the language flag image for the given language
     *
     * @param string $localeCode IETF language tag
     * @param int $langId Language id
     * @param string|null $flagCode If provided, use this flag code. By default, auto-detect using locale code.
     */
    public function setupLanguageFlag(string $localeCode, int $langId, ?string $flagCode = null): void
    {
        $flagCode = $flagCode ?? $this->getFlagCountryCodeFromLocale($localeCode);

        $flagPath = $this->getFlagPath($flagCode);

        if (!file_exists($flagPath)) {
            $flagPath = static::FALLBACK_FLAG_SOURCE;
        }

        $destinationPath = $this->getFlagDestination($langId);

        $this->unlinkIfExists($destinationPath);

        copy($flagPath, $destinationPath);
    }

    /**
     * Creates default copies for the "no image" image
     *
     * @param string $isoCode 2-letter ISO code
     */
    public function setupDefaultImagePlaceholder(string $isoCode): void
    {
        $filesToCopy = [
            $this->getPlaceholderImageFilename(static::DEFAULT_LANGUAGE_CODE) => $this->getPlaceholderImageFilename($isoCode)
        ];

        $imageTypes = ImageType::getAll();
        if (!empty($imageTypes)) {
            foreach (array_keys($imageTypes) as $alias) {
                $formattedImageType = ImageType::getFormattedName($alias);
                $from = $this->getPlaceholderImageFilename(static::DEFAULT_LANGUAGE_CODE, $formattedImageType);
                $to = $this->getPlaceholderImageFilename($isoCode, $formattedImageType);
                $filesToCopy[$from] = $to;
            }
        }

        foreach (static::IMAGE_DIRECTORIES as $destinationDir) {
            foreach ($filesToCopy as $sourceFile => $newFile) {
                @copy(static::IMG_PATH . $sourceFile, $destinationDir . $newFile);
            }
        }
    }

    /**
     * Deletes images associated with the language
     *
     * @param int $langId
     * @param string $isoCode 2-letter ISO code
     */
    public function deleteImages(int $langId, string $isoCode): void
    {
        $images = array(
            $this->getPlaceholderImageFilename($isoCode),
            $this->getPlaceholderImageFilename($isoCode, ImageType::getFormattedName('thickbox')),
            $this->getPlaceholderImageFilename($isoCode, ImageType::getFormattedName('home')),
            $this->getPlaceholderImageFilename($isoCode, ImageType::getFormattedName('large')),
            $this->getPlaceholderImageFilename($isoCode, ImageType::getFormattedName('medium')),
            $this->getPlaceholderImageFilename($isoCode, ImageType::getFormattedName('small')),
        );
        foreach (static::IMAGE_DIRECTORIES as $directory) {
            foreach ($images as $image) {
                $this->unlinkIfExists($directory . $image);
                $this->unlinkIfExists(static::IMG_PATH . $langId . '.jpg');
            }
        }
    }

    /**
     * @param string $locale IETF language tag
     *
     * @return string
     */
    private function getFlagCountryCodeFromLocale(string $locale): string
    {
        return strtolower(explode('-', $locale)[1]);
    }

    /**
     * @param string $countryCode
     *
     * @return string
     */
    private function getFlagPath(string $countryCode): string
    {
        return sprintf(static::FLAGS_SOURCE, $countryCode);
    }

    /**
     * @param int $langId
     *
     * @return string
     */
    private function getFlagDestination(int $langId): string
    {
        return sprintf(static::FLAGS_DESTINATION, $langId);
    }

    /**
     * Removes a file if it exists
     *
     * @param string $file
     */
    private function unlinkIfExists(string $file): void
    {
        if (file_exists($file)) {
            unlink($file);
        }
    }

    /**
     * @param string $isoCode
     * @param string|null $imageTypeName
     *
     * @return string
     */
    private function getPlaceholderImageFilename(string $isoCode, string $imageTypeName = null): string
    {
        if (null !== $imageTypeName) {
            return sprintf(static::PLACEHOLDER_IMAGE_NAME_PATTERNS[1], $isoCode, $imageTypeName);
        }

        return sprintf(static::PLACEHOLDER_IMAGE_NAME_PATTERNS[0], $isoCode);
    }

}

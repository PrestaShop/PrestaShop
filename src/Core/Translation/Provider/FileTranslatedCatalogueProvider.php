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

namespace PrestaShop\PrestaShop\Core\Translation\Provider;

use PrestaShop\PrestaShop\Core\Exception\FileNotFoundException;
use PrestaShop\PrestaShop\Core\Translation\Finder\TranslationFinder;
use Symfony\Component\Translation\MessageCatalogue;

/**
 * Gets catalogue within the files filtered by name in the directory given.
 * The translation files are searched in the subdirectory with the language name.
 * For example, if the main directory is 'myTranslationsDir',
 * if you call getCatalogue('fr_FR'), the translations files will be searched in 'myTranslationsDir/fr_FR'
 */
class FileTranslatedCatalogueProvider extends AbstractCatalogueProvider
{
    /**
     * @var string Directory containing all the sub folders for each locales containing their own XLF files
     */
    private $translatedCatalogueDirectory;

    /**
     * @var array
     */
    private $filenameFilters;

    /**
     * @param string $directory
     * @param array $filenameFilters
     *
     * @throws FileNotFoundException
     */
    public function __construct(string $directory, array $filenameFilters)
    {
        if (!is_dir($directory) || !is_readable($directory)) {
            throw new FileNotFoundException(sprintf('Directory %s does not exist', $directory));
        }

        if (!$this->assertIsArrayOfString($filenameFilters)) {
            throw new \InvalidArgumentException('Given filename filters are invalid. An array of strings was expected.');
        }

        $this->translatedCatalogueDirectory = $directory;
        $this->filenameFilters = $filenameFilters;
    }

    /**
     * Returns the translation catalogue for the provided locale
     *
     * @param string $locale
     *
     * @return MessageCatalogue
     *
     * @throws FileNotFoundException
     */
    public function getCatalogue(string $locale): MessageCatalogue
    {
        $catalogue = new MessageCatalogue($locale);
        $translationFinder = new TranslationFinder();
        $localeResourceDirectory = rtrim($this->translatedCatalogueDirectory, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $locale;

        foreach ($this->filenameFilters as $filter) {
            $filteredCatalogue = $translationFinder->getCatalogueFromPaths(
                [$localeResourceDirectory],
                $locale,
                $filter
            );
            $catalogue->addCatalogue($filteredCatalogue);
        }

        return $catalogue;
    }
}

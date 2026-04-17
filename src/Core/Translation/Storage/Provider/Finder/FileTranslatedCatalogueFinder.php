<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Translation\Storage\Provider\Finder;

use InvalidArgumentException;
use PrestaShop\PrestaShop\Core\Translation\Exception\TranslationFilesNotFoundException;
use PrestaShop\PrestaShop\Core\Translation\Storage\Finder\TranslationFinder;
use Symfony\Component\Translation\MessageCatalogue;

/**
 * Gets catalogue within the files filtered by name in the directory given.
 * The translation files are searched in the subdirectory with the language name.
 * For example, if the main directory is 'myTranslationsDir',
 * if you call getCatalogue('fr_FR'), the translations files will be searched in 'myTranslationsDir/fr_FR'
 */
class FileTranslatedCatalogueFinder extends AbstractCatalogueFinder
{
    /**
     * @var string Directory containing all the sub folders for each locales containing their own XLF files
     */
    private $translatedCatalogueDirectory;

    /**
     * @var array<int, string>
     */
    private $filenameFilters;

    /**
     * @param string $translatedCatalogueDirectory
     * @param array $filenameFilters
     *
     * @throws TranslationFilesNotFoundException
     */
    public function __construct(string $translatedCatalogueDirectory, array $filenameFilters)
    {
        if (!is_dir($translatedCatalogueDirectory) || !is_readable($translatedCatalogueDirectory)) {
            throw new TranslationFilesNotFoundException(sprintf('Directory %s does not exist', $translatedCatalogueDirectory));
        }

        if (!$this->assertIsArrayOfString($filenameFilters)) {
            throw new InvalidArgumentException('Given filename filters are invalid. An array of strings was expected.');
        }

        $this->translatedCatalogueDirectory = $translatedCatalogueDirectory;
        $this->filenameFilters = $filenameFilters;
    }

    /**
     * Returns the translation catalogue for the provided locale
     *
     * @param string $locale
     *
     * @return MessageCatalogue
     *
     * @throws TranslationFilesNotFoundException
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

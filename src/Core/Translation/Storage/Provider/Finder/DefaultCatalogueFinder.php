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
 * The Default catalogue is the base wording, in english, and stored in filesystem or extracted from templates.
 */
class DefaultCatalogueFinder extends AbstractCatalogueFinder
{
    /**
     * @var string Directory containing the default locale XLF files
     */
    private $defaultCatalogueDirectory;

    /**
     * @var array<int, string>
     */
    private $filenameFilters;

    /**
     * @param string $defaultCatalogueDirectory Directory where to look files
     * @param string[] $filenameFilters Array of globs to use to match files
     *
     * @throws TranslationFilesNotFoundException
     */
    public function __construct(string $defaultCatalogueDirectory, array $filenameFilters)
    {
        if (!is_dir($defaultCatalogueDirectory) || !is_readable($defaultCatalogueDirectory)) {
            throw new TranslationFilesNotFoundException(sprintf('Directory %s does not exist', $defaultCatalogueDirectory));
        }

        if (!$this->assertIsArrayOfString($filenameFilters)) {
            throw new InvalidArgumentException('Given filename filters are invalid. An array of strings was expected.');
        }

        $this->defaultCatalogueDirectory = $defaultCatalogueDirectory;
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
        $defaultCatalogue = new MessageCatalogue($locale);
        $translationFinder = new TranslationFinder();

        foreach ($this->filenameFilters as $filter) {
            $filteredCatalogue = $translationFinder->getCatalogueFromPaths(
                [$this->defaultCatalogueDirectory],
                $locale,
                $filter
            );
            $defaultCatalogue->addCatalogue($filteredCatalogue);
        }

        $defaultCatalogue = $this->emptyCatalogue($defaultCatalogue);

        return $defaultCatalogue;
    }

    /**
     * Empties out the catalogue by removing translations but leaving keys
     *
     * @param MessageCatalogue $messageCatalogue
     *
     * @return MessageCatalogue Empty the catalogue
     */
    protected function emptyCatalogue(MessageCatalogue $messageCatalogue): MessageCatalogue
    {
        foreach ($messageCatalogue->all() as $domain => $messages) {
            foreach (array_keys($messages) as $translationKey) {
                $messageCatalogue->set((string) $translationKey, '', $domain);
            }
        }

        return $messageCatalogue;
    }
}

<?php

namespace PrestaShopBundle\Translation\Provider;

use PrestaShop\PrestaShop\Core\Exception\FileNotFoundException;
use Symfony\Component\Translation\MessageCatalogue;
use Symfony\Component\Translation\MessageCatalogueInterface;

class FilesystemCatalogueExtractor implements ExtractorInterface
{
    /**
     * @var array
     */
    private $filenameFilters;

    /**
     * @var string
     */
    private $locale;

    /**
     * @var string
     */
    private $resourceDirectory;

    /**
     * @param string $locale
     *
     * @return FilesystemCatalogueExtractor
     */
    public function setLocale(string $locale)
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * @param array $filenameFilters
     *
     * @return FilesystemCatalogueExtractor
     */
    public function setFilenameFilters(array $filenameFilters): FilesystemCatalogueExtractor
    {
        $this->filenameFilters = $filenameFilters;

        return $this;
    }

    /**
     * @param string $resourceDirectory
     *
     * @return FilesystemCatalogueExtractor
     */
    public function setResourceDirectory(string $resourceDirectory): FilesystemCatalogueExtractor
    {
        $this->resourceDirectory = $resourceDirectory;

        return $this;
    }

    /**
     * @param bool $empty
     *
     * @return MessageCatalogueInterface
     *
     * @throws FileNotFoundException
     */
    public function extract(): MessageCatalogueInterface
    {
        $xlfCatalogue = new MessageCatalogue($this->locale);
        $translationFinder = new TranslationFinder();
        $localeResourceDirectory = $this->resourceDirectory . DIRECTORY_SEPARATOR . $this->locale;

        foreach ($this->filenameFilters as $filter) {
            try {
                $filteredCatalogue = $translationFinder->getCatalogueFromPaths(
                    [$localeResourceDirectory],
                    $this->locale,
                    $filter
                );
                $xlfCatalogue->addCatalogue($filteredCatalogue);
            } catch (FileNotFoundException $e) {
                // there are no translation files, ignore them
            }
        }

        return $xlfCatalogue;
    }
}

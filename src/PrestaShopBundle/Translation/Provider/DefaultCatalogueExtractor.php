<?php

namespace PrestaShopBundle\Translation\Provider;

use PrestaShop\PrestaShop\Core\Exception\FileNotFoundException;
use Symfony\Component\Translation\MessageCatalogue;
use Symfony\Component\Translation\MessageCatalogueInterface;

class DefaultCatalogueExtractor implements ExtractorInterface
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
    private $defaultResourceDirectory;

    /**
     * @param string $locale
     *
     * @return DefaultCatalogueExtractor
     */
    public function setLocale(string $locale)
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * @param array $filenameFilters
     *
     * @return DefaultCatalogueExtractor
     */
    public function setFilenameFilters(array $filenameFilters): DefaultCatalogueExtractor
    {
        $this->filenameFilters = $filenameFilters;

        return $this;
    }

    /**
     * @param string $defaultResourceDirectory
     *
     * @return DefaultCatalogueExtractor
     */
    public function setDefaultResourceDirectory(string $defaultResourceDirectory): DefaultCatalogueExtractor
    {
        $this->defaultResourceDirectory = $defaultResourceDirectory;

        return $this;
    }

    /**
     * @param bool $empty
     *
     * @return MessageCatalogueInterface
     *
     * @throws FileNotFoundException
     */
    public function extract(bool $empty = true): MessageCatalogueInterface
    {
        $defaultCatalogue = new MessageCatalogue($this->locale);
        $translationFinder = new TranslationFinder();

        foreach ($this->filenameFilters as $filter) {
            $filteredCatalogue = $translationFinder->getCatalogueFromPaths(
                [$this->defaultResourceDirectory],
                $this->locale,
                $filter
            );
            $defaultCatalogue->addCatalogue($filteredCatalogue);
        }

        if ($empty && $this->locale !== AbstractProvider::DEFAULT_LOCALE) {
            $defaultCatalogue = $this->emptyCatalogue($defaultCatalogue);
        }

        return $defaultCatalogue;
    }

    /**
     * Empties out the catalogue by removing translations but leaving keys
     *
     * @param MessageCatalogueInterface $messageCatalogue
     *
     * @return MessageCatalogueInterface Empty the catalogue
     */
    protected function emptyCatalogue(MessageCatalogueInterface $messageCatalogue): MessageCatalogueInterface
    {
        foreach ($messageCatalogue->all() as $domain => $messages) {
            foreach (array_keys($messages) as $translationKey) {
                $messageCatalogue->set($translationKey, '', $domain);
            }
        }

        return $messageCatalogue;
    }
}

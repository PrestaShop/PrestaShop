<?php

namespace PrestaShopBundle\Translation\Provider;

use PrestaShop\PrestaShop\Core\Exception\FileNotFoundException;
use PrestaShopBundle\Translation\Loader\DatabaseTranslationLoader;
use Symfony\Component\Translation\MessageCatalogue;
use Symfony\Component\Translation\MessageCatalogueInterface;

class UserTranslatedCatalogueExtractor implements ExtractorInterface
{
    /**
     * @var DatabaseTranslationLoader
     */
    private $databaseLoader;

    /**
     * @var string
     */
    private $locale;

    /**
     * @var string
     */
    private $theme;

    /**
     * @var array
     */
    private $translationDomains = [''];

    public function __construct(DatabaseTranslationLoader $databaseLoader)
    {
        $this->databaseLoader = $databaseLoader;
    }

    /**
     * @param string $locale
     *
     * @return UserTranslatedCatalogueExtractor
     */
    public function setLocale(string $locale)
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * @param string|null $theme
     *
     * @return UserTranslatedCatalogueExtractor
     */
    public function setTheme(?string $theme)
    {
        $this->theme = $theme;

        return $this;
    }

    /**
     * Returns a list of patterns used to choose which wordings will be imported from database.
     * Patterns from this list will be run against translation domains.
     *
     * @return string[] List of Mysql compatible regexes (no regex delimiter)
     */
    protected function getTranslationDomains(): array
    {
        return $this->translationDomains;
    }

    /**
     * @param array $translationDomains
     *
     * @return UserTranslatedCatalogueExtractor
     */
    public function setTranslationDomains(array $translationDomains): UserTranslatedCatalogueExtractor
    {
        $this->translationDomains = $translationDomains;

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
        $databaseCatalogue = new MessageCatalogue($this->locale);

        foreach ($this->getTranslationDomains() as $translationDomain) {
            $domainCatalogue = $this->databaseLoader->load(
                null,
                $this->locale,
                $translationDomain,
                $this->theme
            );

            if ($domainCatalogue instanceof MessageCatalogue) {
                $databaseCatalogue->addCatalogue($domainCatalogue);
            }
        }

        return $databaseCatalogue;
    }
}

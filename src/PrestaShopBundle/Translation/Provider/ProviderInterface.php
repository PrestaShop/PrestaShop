<?php

namespace PrestaShopBundle\Translation\Provider;

use Symfony\Component\Translation\MessageCatalogue;
use Symfony\Component\Translation\MessageCatalogueInterface;

interface ProviderInterface
{
    /**
     * @param string $locale
     */
    public function setLocale(string $locale);

    /**
     * Identifier for the providers
     *
     * @return string
     */
    public function getIdentifier(): string;

    /**
     * @return MessageCatalogueInterface
     */
    public function getMessageCatalogue(): MessageCatalogueInterface;

    /**
     * Get the default (aka untranslated) catalogue
     *
     * @param bool $empty if true, empty the catalogue values (keep the keys)
     *
     * @return MessageCatalogueInterface Return a default catalogue with all keys
     */
    public function getDefaultCatalogue($empty = true);

    /**
     * @return MessageCatalogue
     */
    public function getXliffCatalogue();

    /**
     * @param string|null $themeName the Theme name
     *
     * @return MessageCatalogue
     */
    public function getDatabaseCatalogue($themeName = null);
}

<?php

namespace PrestaShop\PrestaShop\Adapter\Language;

use PrestaShop\PrestaShop\Adapter\LegacyContext;

/**
 * Class ContextLanguageDataProvider is responsible for getting languages related data from legacy content class
 */
class ContextLanguageDataProvider
{
    /**
     * @var LegacyContext
     */
    private $legacyContext;

    public function __construct(LegacyContext $legacyContext)
    {
        $this->legacyContext = $legacyContext;
    }

    /**
     * Returns all locales - active and inactive ones. The first one is the employee default one.
     *
     * @return array
     */
    public function getInstalledLocales()
    {
        return $this->legacyContext->getLanguages(false);
    }
}

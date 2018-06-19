<?php

namespace PrestaShopBundle\Form\Admin\Improve\International\Localization;

use PrestaShop\PrestaShop\Adapter\Language\LanguageDataProvider;

/**
 * Class LanguageChoiceProvider is responsible for providing language choices for ChoiceType form field
 */
class LanguageChoiceProvider
{
    /**
     * @var LanguageDataProvider
     */
    private $languageDataProvider;

    /**
     * @param LanguageDataProvider $languageDataProvider
     */
    public function __construct(LanguageDataProvider $languageDataProvider)
    {
        $this->languageDataProvider = $languageDataProvider;
    }

    /**
     * Get language choices for form
     *
     * @return array
     */
    public function getChoices()
    {
        $languages = $this->languageDataProvider->getLanguages();
        $choices = [];

        foreach ($languages as $language) {
            $choices[$language['name']] = $language['id_lang'];
        }

        return $choices;
    }
}

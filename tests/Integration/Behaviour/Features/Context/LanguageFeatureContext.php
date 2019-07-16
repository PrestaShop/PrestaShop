<?php

namespace Tests\Integration\Behaviour\Features\Context;

use Configuration;
use Language;
use RuntimeException;

class LanguageFeatureContext extends AbstractPrestaShopFeatureContext
{
    /**
    *  @Given /^language with iso code "([^"]*)" is the default one$/
    */
    public function languageWithIsoCodeIsTheDefaultOne($isoCode)
    {
        $languageId = Language::getIdByIso($isoCode);

        if (!$languageId) {
            throw new RuntimeException(
                sprintf(
                    'Iso code %s does not exist',
                    $isoCode
                )
            );
        }

        Configuration::updateValue('PS_LANG_DEFAULT', $languageId);

        SharedStorage::getStorage()->set('default_language_id', $languageId);
    }
}

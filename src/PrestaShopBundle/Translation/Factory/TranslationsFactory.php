<?php

/**
 * 2007-2016 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2016 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Translation\Factory;

use PrestaShopBundle\Translation\Provider\AbstractProvider;
use PrestaShopBundle\Translation\Provider\UseDefaultCatalogueInterface;
use Symfony\Component\Translation\MessageCatalogue;
use Symfony\Component\Translation\MessageCatalogueInterface;

/**
 * This class returns a collection of translations, using locale and identifier.
 *
 * Returns MessageCatalogue object or Translation tree array.
 */
class TranslationsFactory implements TranslationsFactoryInterface
{
    private $providers = array();

    /**
     * Generates extract of global Catalogue, using domain's identifiers.
     *
     * @param string $domainIdentifier Domain identifier
     * @param string $locale           Locale identifier
     *
     * @return MessageCatalogue A MessageCatalogue instance
     *
     * @throws ProviderNotFoundException
     */
    public function createCatalogue($domainIdentifier, $locale = 'en_US')
    {
        foreach ($this->providers as $provider) {
            if ($domainIdentifier === $provider->getIdentifier()) {
                return $provider->setLocale($locale)->getMessageCatalogue();
            }
        }

        throw new ProviderNotFoundException($domainIdentifier);
    }

    /**
     * Used to generate Translation tree in Back Office.
     *
     * @param string $domainIdentifier Domain identifier
     * @param string $locale           Locale identifier
     * @param string $theme            Theme name
     *
     * @return array Translation tree structure
     *
     * @throws ProviderNotFoundException
     */
    public function createTranslationsArray($domainIdentifier, $locale = self::DEFAULT_LOCALE, $theme = null)
    {
        foreach ($this->providers as $provider) {
            if ($domainIdentifier === $provider->getIdentifier()) {
                $provider->setLocale($locale);

                $catalogue = $provider->getXliffCatalogue();
                $catalogue = $this->addDefaultTranslations($provider, $catalogue);

                $translations = $catalogue->all();
                $databaseCatalogue = $provider->getDatabaseCatalogue($theme)->all();

                foreach ($translations as $domain => $messages) {
                    $databaseDomain = str_replace('.'.$locale, '', $domain);

                    $missingTranslations = 0;

                    foreach ($messages as $translationKey => $translationValue) {
                        $keyExists =
                            array_key_exists($databaseDomain, $databaseCatalogue) &&
                            array_key_exists($translationKey, $databaseCatalogue[$databaseDomain])
                        ;

                        $fallbackOnDefaultValue = $translationKey != $translationValue ||
                            $locale === str_replace('_', '-', self::DEFAULT_LOCALE);
                        ;
                        $translations[$domain][$translationKey] = array(
                            'xlf' =>  $fallbackOnDefaultValue ? $translations[$domain][$translationKey] : '',
                            'db' => $keyExists ? $databaseCatalogue[$databaseDomain][$translationKey] : '',
                        );

                        if (
                            empty($translations[$domain][$translationKey]['xlf']) &&
                            empty($translations[$domain][$translationKey]['db'])
                        ) {
                            $missingTranslations++;
                        }
                    }

                    $translations[$domain]['__metadata'] = array('missing_translations' => $missingTranslations);
                }

                ksort($translations);

                return $translations;
            }
        }

        throw new ProviderNotFoundException($domainIdentifier);
    }

    /**
     * @param AbstractProvider $provider
     * @param MessageCatalogueInterface $catalogue
     * @return MessageCatalogueInterface
     */
    private function addDefaultTranslations(AbstractProvider $provider, MessageCatalogueInterface $catalogue)
    {
        if (!$provider instanceof UseDefaultCatalogueInterface) {
            return $catalogue;
        }

        $catalogueWithDefault = $provider->getDefaultCatalogue();
        $catalogueWithDefault->addCatalogue($catalogue);

        return $catalogueWithDefault;
    }

    /**
     * @param AbstractProvider $provider
     */
    public function addProvider(AbstractProvider $provider)
    {
        $this->providers[] = $provider;
    }
}

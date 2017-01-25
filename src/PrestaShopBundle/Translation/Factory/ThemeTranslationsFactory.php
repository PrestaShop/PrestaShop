<?php

/**
 * 2007-2017 PrestaShop
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
 * @copyright 2007-2017 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Translation\Factory;

use PrestaShopBundle\Translation\Provider\ThemeProvider;

/**
 * This class returns a collection of translations, using locale and identifier.
 *
 * But in this particular case, the identifier is the theme name.
 *
 * Returns MessageCatalogue object or Translation tree array.
 */
class ThemeTranslationsFactory extends TranslationsFactory
{
    /**
     * @var ThemeProvider
     */
    private $themeProvider;

    public function __construct(ThemeProvider $themeProvider)
    {
        $this->themeProvider = $themeProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function createCatalogue($themeName, $locale = 'en_US')
    {
        return $this->themeProvider
            ->setThemeName($themeName)
            ->setLocale($locale)
            ->getMessageCatalogue()
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function createTranslationsArray($themeName, $locale = 'en_US')
    {
        $this->themeProvider
            ->setThemeName($themeName)
            ->setLocale($locale)
            ->synchronizeTheme();
        ;

        $translations = $this->getFrontTranslationsForThemeAndLocale($themeName, $locale);
        $translations = $this->pushThemeTranslations($translations, $locale);

        ksort($translations);

        return $translations;
    }

    /**
     * @param $translations
     * @param $locale
     * @return mixed
     */
    protected function pushThemeTranslations($translations, $locale)
    {
        $themeTranslations = $this->themeProvider->getXliffCatalogue()->all();
        $databaseCatalogue = $this->themeProvider->getDatabaseCatalogue()->all();

        foreach ($themeTranslations as $domain => $messages) {
            $databaseDomain = $this->removeLocaleFromDomain($locale, $domain);

            $missingTranslations = 0;

            foreach ($messages as $translationKey => $translationValue) {
                $keyExists = array_key_exists($databaseDomain, $databaseCatalogue) &&
                    array_key_exists($translationKey, $databaseCatalogue[$databaseDomain])
                ;

                $themeTranslations[$domain][$translationKey] = array(
                    'xlf' => $translationKey != $translationValue ? $themeTranslations[$domain][$translationKey] : '',
                    'db' => $keyExists ? $databaseCatalogue[$databaseDomain][$translationKey] : '',
                );

                if (
                    empty($themeTranslations[$domain][$translationKey]['xlf']) &&
                    empty($themeTranslations[$domain][$translationKey]['db'])
                ) {
                    $missingTranslations++;
                }
            }

            $translations[$domain]['__metadata'] = array('missing_translations' => $missingTranslations);
        }

        foreach ($translations as $domain => $messages) {
            if (!array_key_exists($domain, $themeTranslations)) {
                $themeTranslations[$domain] = $messages;

                continue;
            }

            foreach ($messages as $translationKey => $translationValues) {
                if (!array_key_exists($translationKey, $themeTranslations[$domain])) {
                    $themeTranslations[$domain][$translationKey] = $translationValues;
                }

            }
        }

        return $themeTranslations;
    }


    /**
     * @param $locale
     * @param $domain
     * @return mixed
     */
    protected function removeLocaleFromDomain($locale, $domain)
    {
        return str_replace('.' . $locale, '', $domain);
    }

    /**
     * @param $themeName
     * @param $locale
     * @return mixed
     */
    protected function getFrontTranslationsForThemeAndLocale($themeName, $locale)
    {
        return parent::createTranslationsArray('front', $locale, $themeName);
    }
}

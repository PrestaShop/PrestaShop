<?php

/**
 * 2007-2016 PrestaShop.
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

use PrestaShopBundle\Translation\Provider\ThemeProvider;

/**
 * This class returns a collection of translations, using locale and identifier.
 *
 * But in this particular case, the identifier is the theme name.
 *
 * Returns MessageCatalogue object or Translation tree array.
 */
class ThemeTranslationsFactory implements TranslationsFactoryInterface
{
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
            ->setLocale($locale);

        $translations = $this->themeProvider->getXliffCatalogue()->all();

        if ($this->themeProvider instanceof UseDefaultCatalogueInterface) {
            $translations = $this->getTranslationsWithSources($this->themeProvider);
        }

        $databaseCatalogue = $this->themeProvider->getDatabaseCatalogue()->all();

        foreach ($translations as $domain => $messages) {
            $databaseDomain = str_replace('.'.$locale, '', $domain);

            foreach ($messages as $translationKey => $translationValue) {
                $keyExists =
                    array_key_exists($databaseDomain, $databaseCatalogue) &&
                    array_key_exists($translationKey, $databaseCatalogue[$databaseDomain])
                ;

                $tree[$domain][$translationKey] = array(
                    'xlf' => $translations[$domain][$translationKey],
                    'db' => $keyExists ? $databaseCatalogue[$databaseDomain][$translationKey] : '',
                );
            }
        }

        ksort($translations);

        return $translations;
    }
}

<?php

/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
     * @var ThemeProvider the theme provider
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
            ->getMessageCatalogue();
    }

    /**
     * {@inheritdoc}
     */
    public function createTranslationsArray($themeName, $locale = 'en_US', $theme = null, $search = null)
    {
        $this->themeProvider
            ->setThemeName($themeName)
            ->setLocale($locale)
            ->synchronizeTheme();

        $translations = $this->getFrontTranslationsForThemeAndLocale($themeName, $locale, $search);

        ksort($translations);

        return $translations;
    }

    /**
     * @param string $locale the catalogue locale
     * @param string $domain the catalogue domain
     *
     * @return string
     */
    protected function removeLocaleFromDomain($locale, $domain)
    {
        return str_replace('.' . $locale, '', $domain);
    }

    /**
     * @param string $themeName the theme name
     * @param string $locale the catalogue locale
     * @param string|null $search
     *
     * @throws ProviderNotFoundException
     *
     * @return array
     */
    protected function getFrontTranslationsForThemeAndLocale($themeName, $locale, $search = null)
    {
        return parent::createTranslationsArray('theme', $locale, $themeName, $search);
    }
}

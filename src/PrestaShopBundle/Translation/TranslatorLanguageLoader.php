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

namespace PrestaShopBundle\Translation;

use PrestaShop\PrestaShop\Core\Addon\Theme\Theme;
use PrestaShopBundle\Translation\Loader\SqlTranslationLoader;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Translation\Loader\XliffFileLoader;
use Symfony\Component\Translation\TranslatorInterface;

class TranslatorLanguageLoader
{
    const TRANSLATION_DIR = _PS_ROOT_DIR_ . '/app/Resources/translations';
    /**
     * @var bool
     */
    private $isAdminContext;

    /**
     * TranslatorLanguageLoader constructor.
     *
     * @param $isAdminContext
     */
    public function __construct($isAdminContext)
    {
        $this->isAdminContext = $isAdminContext;
    }

    /**
     * Loads a language into a translator
     *
     * @param TranslatorInterface $translator Translator to modifiy
     * @param string $locale Locale code for the language to load
     * @param bool $withDB [default=true] Whether to load translations from the database or not
     * @param Theme|null $theme [default=false] Currently active theme (Front office only)
     */
    public function loadLanguage(TranslatorInterface $translator, $locale, $withDB = true, Theme $theme = null)
    {
        if (!$translator->isLanguageLoaded($locale)) {
            $translator->addLoader('xlf', new XliffFileLoader());

            if ($withDB) {
                $sqlTranslationLoader = new SqlTranslationLoader();
                if (null !== $theme) {
                    $sqlTranslationLoader->setTheme($theme);
                }
                $translator->addLoader('db', $sqlTranslationLoader);
            }

            $finder = Finder::create()
                ->files()
                ->name('*.' . $locale . '.xlf')
                ->notName($this->isAdminContext ? '^Shop*' : '^Admin*')
                ->in($this->getTranslationResourcesDirectories($theme));

            foreach ($finder as $file) {
                list($domain, $locale, $format) = explode('.', $file->getBasename(), 3);
                $translator->addResource($format, $file, $locale, $domain);
                if ($withDB) {
                    $translator->addResource('db', $domain . '.' . $locale . '.db', $locale, $domain);
                }
            }
        }
    }

    /**
     * @param Theme|null $theme
     *
     * @return array
     */
    protected function getTranslationResourcesDirectories(Theme $theme = null)
    {
        $locations = [self::TRANSLATION_DIR];

        if (null !== $theme) {
            $activeThemeLocation = $theme->getDirectory() . '/translations';
            if (is_dir($activeThemeLocation)) {
                $locations[] = $activeThemeLocation;
            }
        }

        return $locations;
    }
}

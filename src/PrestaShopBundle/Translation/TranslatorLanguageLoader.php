<?php

/**
 * 2007-2019 PrestaShop SA and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Translation;

use PrestaShop\PrestaShop\Core\Addon\Theme\Theme;
use PrestaShopBundle\Translation\Loader\SqlTranslationLoader;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Translation\Loader\XliffFileLoader;

class TranslatorLanguageLoader
{
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
     * @param PrestaShopTranslatorInterface $translator
     * @param string $locale
     * @param Theme|null $theme
     */
    public function loadLanguage(PrestaShopTranslatorInterface $translator, $locale, Theme $theme = null)
    {
        if ($translator->getLocale() !== $locale && !$translator->isCatalogLoaded($locale)) {
            $translator->addLoader('xlf', new XliffFileLoader());

            $sqlTranslationLoader = new SqlTranslationLoader();
            if (null !== $theme) {
                $sqlTranslationLoader->setTheme($theme);
            }
            $translator->addLoader('db', $sqlTranslationLoader);

            $finder = Finder::create()
                ->files()
                ->name('*.' . $locale . '.xlf')
                ->notName($this->isAdminContext ? '^Shop*' : '^Admin*')
                ->in($this->getTranslationResourcesDirectories($theme));

            foreach ($finder as $file) {
                list($domain, $locale, $format) = explode('.', $file->getBasename(), 3);
                $translator->addResource($format, $file, $locale, $domain);
                $translator->addResource('db', $domain . '.' . $locale . '.db', $locale, $domain);
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
        $locations = [_PS_ROOT_DIR_ . '/app/Resources/translations'];

        if (null !== $theme) {
            $activeThemeLocation = $theme->getDirectory() . '/translations';
            if (is_dir($activeThemeLocation)) {
                $locations[] = $activeThemeLocation;
            }
        }

        return $locations;
    }
}

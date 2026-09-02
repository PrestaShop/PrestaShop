<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
     * @return array
     *
     * @throws ProviderNotFoundException
     */
    protected function getFrontTranslationsForThemeAndLocale($themeName, $locale, $search = null)
    {
        return parent::createTranslationsArray('theme', $locale, $themeName, $search);
    }
}

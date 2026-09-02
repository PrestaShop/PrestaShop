<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Translation\Loader;

use Db;
use PrestaShop\PrestaShop\Core\Addon\Theme\Theme;
use PrestaShopException;
use Symfony\Component\Translation\Exception\NotFoundResourceException;
use Symfony\Component\Translation\Loader\LoaderInterface;
use Symfony\Component\Translation\MessageCatalogue;
use Symfony\Component\Translation\MessageCatalogueInterface;

class SqlTranslationLoader implements LoaderInterface
{
    /**
     * @var Theme the theme
     */
    protected $theme;

    /**
     * @param Theme $theme the theme
     *
     * @return $this
     */
    public function setTheme(Theme $theme)
    {
        $this->theme = $theme;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function load($resource, $locale, $domain = 'messages'): MessageCatalogue
    {
        static $localeResults = [];

        if (!array_key_exists($locale, $localeResults)) {
            try {
                $locale = Db::getInstance()->escape($locale, false, true);

                $localeResults[$locale] = Db::getInstance()->getRow(
                    'SELECT `id_lang`
                FROM `' . _DB_PREFIX_ . 'lang`
                WHERE `locale` = "' . $locale . '"'
                );
            } catch (PrestaShopException) {
                // When no DB is created there is nothing to fetch, so we return an empty catalog to avoid breaking process for
                // invalid reasons (like CLI commands before the shop is installed)
                return new MessageCatalogue($locale);
            }
        }

        if (empty($localeResults[$locale])) {
            throw new NotFoundResourceException(sprintf('Language not found in database: %s', $locale));
        }

        $selectTranslationsQuery = '
            SELECT `key`, `translation`, `domain`
            FROM `' . _DB_PREFIX_ . 'translation`
            WHERE `id_lang` = ' . $localeResults[$locale]['id_lang'] . '
            AND ' . $this->buildThemeCondition() . '
            ORDER BY theme IS NOT NULL';

        $translations = Db::getInstance()->executeS($selectTranslationsQuery) ?: [];

        $catalogue = new MessageCatalogue($locale);
        $this->addTranslationsToCatalogue($translations, $catalogue);

        return $catalogue;
    }

    /**
     * Builds the WHERE sub-condition that restricts which ps_translation rows are loaded.
     *
     * Always covers both core rows (theme IS NULL) and theme-specific rows for every
     * active shop. This is required because in PS9 the Symfony container is always active,
     * so getTranslator() never calls TranslatorLanguageLoader::loadLanguage() and setTheme()
     * is never invoked. A single loader instance must therefore handle both row types.
     *
     * In the PS8 legacy path, TranslatorLanguageLoader registers two separate instances
     * (a plain 'db' loader and a 'db.theme' loader with setTheme() called). With the
     * unified condition both instances load the same rows; the second pass is redundant
     * but harmless.
     *
     * ORDER BY theme IS NOT NULL in the caller ensures theme=NULL rows are processed first
     * inside addTranslationsToCatalogue(), so shop-specific overrides win on duplicate keys.
     *
     * The correct column is ps_shop.theme_name — ps_shop.theme has never existed; referencing
     * it causes MySQL/MariaDB to silently return an empty result set (issue #41232, Bug A).
     */
    protected function buildThemeCondition(): string
    {
        return '(theme IS NULL OR theme IN (SELECT `theme_name` FROM `' . _DB_PREFIX_ . 'shop` WHERE `active` = 1))';
    }

    /**
     * @param array $translations the list of translations
     * @param MessageCatalogueInterface $catalogue the Message Catalogue
     */
    protected function addTranslationsToCatalogue(array $translations, MessageCatalogueInterface $catalogue)
    {
        foreach ($translations as $translation) {
            $catalogue->set($translation['key'], $translation['translation'], $translation['domain']);
        }
    }
}

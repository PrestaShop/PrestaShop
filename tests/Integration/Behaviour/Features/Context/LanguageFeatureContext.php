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

namespace Tests\Integration\Behaviour\Features\Context;

use Configuration;
use Db;
use Language;
use PHPUnit\Framework\Assert;
use PrestaShopBundle\Install\DatabaseDump;
use RuntimeException;

class LanguageFeatureContext extends AbstractPrestaShopFeatureContext
{
    /**
     * @BeforeFeature @restore-languages-before-feature
     */
    public static function restoreLanguagesTablesBeforeFeature(): void
    {
        static::restoreLanguagesTables();
    }

    /**
     * @AfterFeature @restore-languages-after-feature
     */
    public static function restoreLanguagesTablesAfterFeature(): void
    {
        static::restoreLanguagesTables();
    }

    private static function restoreLanguagesTables(): void
    {
        // Removing Language manually includes cleaning all related lang tables, this cleaning is handled in
        // Language::delete in a more efficient way than relying on table restoration
        $langIds = Db::getInstance()->executeS(sprintf('SELECT id_lang FROM %slang;', _DB_PREFIX_));
        unset($langIds[0]);
        foreach ($langIds as $langId) {
            $lang = new Language($langId['id_lang']);
            $lang->delete();
        }

        // We still restore lang table to reset increment ID
        DatabaseDump::restoreTables(['lang', 'lang_shop']);

        // Reset default language
        Configuration::updateValue('PS_LANG_DEFAULT', 1);

        // Restore static cache
        Language::resetStaticCache();
    }

    /**
     *  @Given I restore languages tables
     */
    public function restoreLanguageTablesOnDemand(): void
    {
        static::restoreLanguagesTables();
    }

    /**
     *  @Given /^language with iso code "([^"]*)" is the default one$/
     */
    public function languageWithIsoCodeIsTheDefaultOne($isoCode)
    {
        $languageId = Language::getIdByIso($isoCode);

        if (!$languageId) {
            throw new RuntimeException(sprintf('Iso code %s does not exist', $isoCode));
        }

        Configuration::updateValue('PS_LANG_DEFAULT', (string) $languageId);

        SharedStorage::getStorage()->set('default_language_id', $languageId);
    }

    /**
     * @Given language :reference with locale :locale exists
     */
    public function createLanguageWithLocale($reference, $locale)
    {
        $languageId = Language::getIdByLocale($locale, true);

        if (false === $languageId) {
            $language = new Language();
            $language->locale = $locale;
            $language->active = true;
            $language->name = $locale;
            $language->is_rtl = false;
            $language->language_code = strtolower($locale);
            $language->iso_code = substr($locale, 0, strpos($locale, '-'));
            $language->add();
            // We need to reset the static cache, or it messes with multilang fields (because the
            // cache doesn't contain all the expected languages)
            Language::resetCache();
        } else {
            $language = new Language($languageId);
        }

        SharedStorage::getStorage()->set($reference, $language);
    }

    /**
     * @When I delete language :reference
     */
    public function deleteLanguage($reference): void
    {
        $language = SharedStorage::getStorage()->get($reference);
        $language->delete();
    }

    /**
     * @Then language :reference should be :locale
     */
    public function assertLanguageLocale($reference, $locale)
    {
        /** @var Language $language */
        $language = SharedStorage::getStorage()->get($reference);

        if ($language->locale !== $locale) {
            throw new RuntimeException(sprintf('Currency "%s" has "%s" iso code, but "%s" was expected.', $reference, $language->locale, $locale));
        }
    }

    /**
     *  @Given /^the robots.txt file has(n't|) a rule where the directory "([^"]*)" is allowed$/
     */
    public function robotsTxtAllowsDirectory(string $isAllowedString, string $directory): void
    {
        $isAllowed = $isAllowedString === '';
        $robotsTxtFile = file_get_contents(_PS_ROOT_DIR_ . '/robots.txt');

        Assert::assertSame(
            $isAllowed,
            strpos($robotsTxtFile, 'Disallow: ' . $directory . "\n") !== false
        );
    }
}

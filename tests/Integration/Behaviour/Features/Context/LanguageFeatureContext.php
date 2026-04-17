<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Integration\Behaviour\Features\Context;

use Configuration;
use Language;
use PHPUnit\Framework\Assert;
use PrestaShop\PrestaShop\Core\Context\LanguageContextBuilder;
use RuntimeException;
use Tests\Resources\Resetter\LanguageResetter;

class LanguageFeatureContext extends AbstractPrestaShopFeatureContext
{
    /**
     * @BeforeFeature @restore-languages-before-feature
     */
    public static function restoreLanguagesTablesBeforeFeature(): void
    {
        self::restoreLanguagesTables();
    }

    /**
     * @AfterFeature @restore-languages-after-feature
     */
    public static function restoreLanguagesTablesAfterFeature(): void
    {
        self::restoreLanguagesTables();
    }

    private static function restoreLanguagesTables(): void
    {
        LanguageResetter::resetLanguages();
    }

    /**
     *  @Given I restore languages tables
     */
    public function restoreLanguageTablesOnDemand(): void
    {
        self::restoreLanguagesTables();
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

        /** @var LanguageContextBuilder $languageBuilder */
        $languageBuilder = CommonFeatureContext::getContainer()->get('test_language_context_builder');
        $languageBuilder->setDefaultLanguageId($languageId);
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

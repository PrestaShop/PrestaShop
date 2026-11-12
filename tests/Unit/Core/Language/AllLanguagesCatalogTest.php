<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Unit\Core\Language;

use PHPUnit\Framework\TestCase;

/**
 * The catalogue shipped in app/Resources/all_languages.json is copied field by field into ps_lang by
 * Language::checkAndAddLanguage(), so every value has to survive that table and its validators.
 */
class AllLanguagesCatalogTest extends TestCase
{
    /**
     * Withdrawn ISO 3166-1 alpha-2 codes. A language tag built on one of these is deprecated in the
     * IANA registry, and it reaches the browser through <html lang> and the hreflang alternates.
     */
    private const WITHDRAWN_REGION_CODES = ['AN', 'BU', 'CS', 'DD', 'FX', 'NT', 'SU', 'TP', 'YU', 'ZR'];

    /**
     * @return array<string, array{string, array<string, string>}>
     */
    public static function provideLanguages(): array
    {
        $catalog = json_decode(
            file_get_contents(__DIR__ . '/../../../../app/Resources/all_languages.json'),
            true
        );

        $cases = [];
        foreach ($catalog as $key => $language) {
            $cases[$key] = [$key, $language];
        }

        return $cases;
    }

    /**
     * @dataProvider provideLanguages
     *
     * @param array<string, string> $language
     */
    public function testItFitsTheLangTableAndItsValidators(string $key, array $language): void
    {
        // ps_lang.iso_code is varchar(2), language_code and locale are varchar(5)
        $this->assertLessThanOrEqual(2, strlen($language['iso_code']), $key . ': iso_code');
        $this->assertLessThanOrEqual(5, strlen($language['language_code']), $key . ': language_code');
        $this->assertLessThanOrEqual(5, strlen($language['locale']), $key . ': locale');

        // Validate::isLanguageCode()
        $this->assertMatchesRegularExpression(
            '/^[a-zA-Z]{2}(-[a-zA-Z]{2})?$/',
            $language['language_code'],
            $key . ': language_code has to pass Validate::isLanguageCode()'
        );
    }

    /**
     * @dataProvider provideLanguages
     *
     * @param array<string, string> $language
     */
    public function testItsPublishedLanguageTagIsNotDeprecated(string $key, array $language): void
    {
        $region = explode('-', $language['language_code'])[1] ?? null;

        $this->assertNotContains(
            strtoupper((string) $region),
            self::WITHDRAWN_REGION_CODES,
            $key . ': language_code is published as hreflang, so its region has to be a current ISO 3166-1 code'
        );
    }
}

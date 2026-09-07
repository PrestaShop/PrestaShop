<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Unit\Core\Language;

use PHPUnit\Framework\TestCase;
use RuntimeException;

/**
 * The two language resource files are read by different layers: all_languages.json decides which
 * languages can be installed, legacy-to-standard-locales.json resolves an installed language's ISO
 * code to its locale in TranslationService::langToLocale(). The translation screen submits the ISO
 * code of an installed language, so an ISO the first file offers and the second one omits reaches an
 * unguarded array read and the screen cannot resolve a locale at all.
 */
class LanguageResourcesConsistencyTest extends TestCase
{
    private const ALL_LANGUAGES = _PS_ROOT_DIR_ . '/app/Resources/all_languages.json';
    private const LEGACY_TO_STANDARD_LOCALES = _PS_ROOT_DIR_ . '/app/Resources/legacy-to-standard-locales.json';

    /**
     * @dataProvider provideInstallableLanguages
     */
    public function testEveryInstallableLanguageResolvesToItsLocale(string $isoCode, string $locale): void
    {
        $legacyToStandardLocales = self::readJson(self::LEGACY_TO_STANDARD_LOCALES);

        $this->assertArrayHasKey(
            $isoCode,
            $legacyToStandardLocales,
            sprintf(
                'Language "%s" can be installed but legacy-to-standard-locales.json cannot resolve its locale.',
                $isoCode
            )
        );
        $this->assertSame($locale, $legacyToStandardLocales[$isoCode]);
    }

    public function testEveryInstallableLanguageDeclaresItsOwnIsoCode(): void
    {
        foreach (self::readJson(self::ALL_LANGUAGES) as $isoCode => $language) {
            $this->assertSame($isoCode, $language['iso_code']);
        }
    }

    public static function provideInstallableLanguages(): iterable
    {
        foreach (self::readJson(self::ALL_LANGUAGES) as $isoCode => $language) {
            yield $isoCode => [$isoCode, $language['locale']];
        }
    }

    private static function readJson(string $path): array
    {
        $contents = file_get_contents($path);
        if (false === $contents) {
            throw new RuntimeException(sprintf('%s is not readable', $path));
        }

        $decoded = json_decode($contents, true);
        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new RuntimeException(sprintf('%s is not valid JSON: %s', $path, json_last_error_msg()));
        }

        return $decoded;
    }
}

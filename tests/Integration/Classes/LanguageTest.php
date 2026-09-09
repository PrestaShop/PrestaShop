<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes;

use Language;
use PHPUnit\Framework\TestCase;
use Tests\Resources\DatabaseDump;

class LanguageTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        DatabaseDump::restoreAllTables();
    }

    public function testGetIdByIso()
    {
        $this->assertNull(Language::getIdByIso('zz', false));
        $this->assertNull(Language::getIdByIso('zz', true));

        $language = new Language();
        $language->name = 'zz';
        $language->iso_code = 'zz';
        $language->locale = 'zz-ZZ';
        $language->language_code = 'zz-ZZ';
        $language->add();

        $idByIso = Language::getIdByIso('zz', false);
        $this->assertNotEquals(0, $idByIso);
        $this->assertIsInt($idByIso);

        $idByIso = Language::getIdByIso('zz', true);
        $this->assertNotEquals(0, $idByIso);
        $this->assertIsInt($idByIso);
    }

    /**
     * The guards, which refuse before anything is copied or extracted. The installation itself is
     * not exercised here on purpose: installSfLanguagePack() clears the Symfony cache, which is not
     * something to do in the middle of a suite.
     *
     * @dataProvider provideRefusedImports
     */
    public function testImportSfLanguagePackRefusesWhatItCannotInstall(string $locale, string $archivePath): void
    {
        $errors = [];

        $this->assertFalse(Language::importSfLanguagePack($locale, $archivePath, $errors));
        $this->assertCount(1, $errors);
    }

    /**
     * @return array<string, array{string, string}>
     */
    public static function provideRefusedImports(): array
    {
        return [
            'a locale that is not one' => ['not a locale', __FILE__],
            'an iso code rather than a locale' => ['fr', __FILE__],
            'an archive that is not there' => ['fr-FR', __DIR__ . '/absent-language-pack.zip'],
        ];
    }
}

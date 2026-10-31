<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes;

use AdminImportControllerCore;
use Db;
use Language;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;
use ReflectionProperty;

/**
 * An imported image caption belongs to the language being imported. Writing it into every language is
 * what made a second import in another language look like it overwrote the first.
 */
class ImportImageCaptionLanguageTest extends TestCase
{
    /** @var mixed */
    private $previousIsoLang;

    /** @var int */
    private int $addedLanguageId = 0;

    protected function setUp(): void
    {
        parent::setUp();
        $this->previousIsoLang = $_GET['iso_lang'] ?? null;

        // The test shop ships a single language, and one language cannot show whether a caption stayed
        // out of the others. Add a second one for the duration of the test.
        if (count(Language::getIDs(false)) < 2) {
            Db::getInstance()->insert('lang', [
                'name' => 'Test language',
                'active' => 1,
                'iso_code' => 'zz',
                'language_code' => 'zz-zz',
                'locale' => 'zz-ZZ',
                'date_format_lite' => 'Y-m-d',
                'date_format_full' => 'Y-m-d H:i:s',
                'is_rtl' => 0,
            ]);
            $this->addedLanguageId = (int) Db::getInstance()->Insert_ID();
            Db::getInstance()->insert('lang_shop', ['id_lang' => $this->addedLanguageId, 'id_shop' => 1]);
            $this->forgetTheLanguageCache();
        }
    }

    protected function tearDown(): void
    {
        if ($this->previousIsoLang === null) {
            unset($_GET['iso_lang']);
        } else {
            $_GET['iso_lang'] = $this->previousIsoLang;
        }

        if ($this->addedLanguageId) {
            Db::getInstance()->delete('lang_shop', 'id_lang = ' . $this->addedLanguageId);
            Db::getInstance()->delete('lang', 'id_lang = ' . $this->addedLanguageId);
            $this->addedLanguageId = 0;
            $this->forgetTheLanguageCache();
        }

        parent::tearDown();
    }

    private function forgetTheLanguageCache(): void
    {
        $cache = new ReflectionProperty(Language::class, '_LANGUAGES');
        $cache->setAccessible(true);
        $cache->setValue(null, null);
    }

    public function testTheCaptionOnlyLandsInTheImportedLanguage(): void
    {
        $languages = Language::getIDs(false);

        $this->assertGreaterThan(1, count($languages), 'the setup must provide a second language');

        $idLang = (int) Language::getIdByIso('en');
        $this->assertNotEmpty($idLang, 'the shop is expected to have English installed');

        $_GET['iso_lang'] = 'en';

        $values = $this->buildCaption('A printed t-shirt');

        $this->assertSame('A printed t-shirt', $values[$idLang]);

        foreach ($values as $id => $value) {
            if ((int) $id !== $idLang) {
                $this->assertSame('', $value, 'a language that was not imported must be left empty');
            }
        }
    }

    public function testWithoutAChosenLanguageEveryLanguageIsStillFilled(): void
    {
        unset($_GET['iso_lang']);

        $values = $this->buildCaption('A printed t-shirt');

        $this->assertNotEmpty($values);

        foreach ($values as $value) {
            $this->assertSame('A printed t-shirt', $value, 'the previous behaviour is kept when no language is chosen');
        }
    }

    private function buildCaption(string $caption): array
    {
        $method = new ReflectionMethod(AdminImportControllerCore::class, 'createMultiLangFieldForImportedLanguage');
        $method->setAccessible(true);

        return $method->invoke(null, $caption);
    }
}

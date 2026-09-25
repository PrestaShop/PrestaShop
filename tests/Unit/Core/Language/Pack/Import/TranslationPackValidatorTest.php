<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Language\Pack\Import;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Language\Pack\Import\Exception\InvalidTranslationPackException;
use PrestaShop\PrestaShop\Core\Language\Pack\Import\TranslationPackValidator;
use Symfony\Component\Filesystem\Filesystem;
use ZipArchive;

class TranslationPackValidatorTest extends TestCase
{
    private string $workDir;
    private Filesystem $filesystem;

    protected function setUp(): void
    {
        parent::setUp();
        $this->filesystem = new Filesystem();
        $this->workDir = sys_get_temp_dir() . '/translation_pack_validator_' . uniqid('', true);
        $this->filesystem->mkdir($this->workDir);
    }

    protected function tearDown(): void
    {
        $this->filesystem->remove($this->workDir);
        parent::tearDown();
    }

    public function testItReturnsTheLocaleOfAWellFormedPack(): void
    {
        // The published packs look exactly like this: one directory entry named after the locale
        // and XLF catalogues beside it.
        $archive = $this->createArchive('pack.zip', [
            'fr-FR/' => null,
            'fr-FR/AdminActions.fr-FR.xlf' => '<xliff/>',
            'fr-FR/ShopThemeActions.fr-FR.xlf' => '<xliff/>',
        ]);

        $this->assertSame('fr-FR', (new TranslationPackValidator())->validate($archive));
    }

    public function testItAcceptsACatalogueWhateverTheCaseOfItsExtension(): void
    {
        $archive = $this->createArchive('upper.zip', ['en-US/AdminActions.en-US.XLF' => '<xliff/>']);

        $this->assertSame('en-US', (new TranslationPackValidator())->validate($archive));
    }

    /**
     * @dataProvider provideRefusedArchives
     *
     * @param array<string, string|null> $entries
     */
    public function testItRefusesAnArchiveThatIsNotATranslationPack(array $entries, int $expectedCode): void
    {
        $archive = $this->createArchive('refused.zip', $entries);

        $this->expectException(InvalidTranslationPackException::class);
        $this->expectExceptionCode($expectedCode);

        (new TranslationPackValidator())->validate($archive);
    }

    /**
     * @return array<string, array{array<string, string|null>, int}>
     */
    public static function provideRefusedArchives(): array
    {
        return [
            // The reason this check exists: nothing but a catalogue may reach the translations dir.
            'a script beside the catalogues' => [
                ['fr-FR/AdminActions.fr-FR.xlf' => '<xliff/>', 'fr-FR/shell.php' => '<?php'],
                InvalidTranslationPackException::UNEXPECTED_ENTRY,
            ],
            'a traversing entry' => [
                ['fr-FR/AdminActions.fr-FR.xlf' => '<xliff/>', '../escaped.xlf' => '<xliff/>'],
                InvalidTranslationPackException::UNEXPECTED_ENTRY,
            ],
            'a traversing entry deeper in' => [
                ['fr-FR/nested/../../escaped.xlf' => '<xliff/>'],
                InvalidTranslationPackException::UNEXPECTED_ENTRY,
            ],
            'an absolute entry' => [
                ['/etc/passwd.xlf' => 'x'],
                InvalidTranslationPackException::UNEXPECTED_ENTRY,
            ],
            'a catalogue outside any locale directory' => [
                ['AdminActions.fr-FR.xlf' => '<xliff/>'],
                InvalidTranslationPackException::UNEXPECTED_ENTRY,
            ],
            'a catalogue nested too deep' => [
                ['fr-FR/sub/AdminActions.fr-FR.xlf' => '<xliff/>'],
                InvalidTranslationPackException::UNEXPECTED_ENTRY,
            ],
            'two locales at once' => [
                ['fr-FR/AdminActions.fr-FR.xlf' => '<xliff/>', 'en-US/AdminActions.en-US.xlf' => '<xliff/>'],
                InvalidTranslationPackException::MIXED_LOCALES,
            ],
            'a directory that is not a locale' => [
                ['translations/AdminActions.xlf' => '<xliff/>'],
                InvalidTranslationPackException::MALFORMED_LOCALE,
            ],
            'nothing but a directory' => [
                ['fr-FR/' => null],
                InvalidTranslationPackException::EMPTY_ARCHIVE,
            ],
        ];
    }

    public function testItRefusesAnArchiveWithMoreEntriesThanAnyPackHas(): void
    {
        // The same guard also sums the declared uncompressed sizes; the entry count is the half of
        // it that can be provoked without writing hundreds of megabytes in a test.
        $entries = [];
        for ($i = 0; $i <= 5000; ++$i) {
            $entries[sprintf('fr-FR/Admin%d.fr-FR.xlf', $i)] = '<xliff/>';
        }
        $archive = $this->createArchive('many.zip', $entries);

        $this->expectException(InvalidTranslationPackException::class);
        $this->expectExceptionCode(InvalidTranslationPackException::TOO_LARGE);

        (new TranslationPackValidator())->validate($archive);
    }

    public function testItRefusesAFileThatIsNotAnArchive(): void
    {
        $notAnArchive = $this->workDir . '/plain.zip';
        $this->filesystem->dumpFile($notAnArchive, 'this is not a zip archive');

        $this->expectException(InvalidTranslationPackException::class);
        $this->expectExceptionCode(InvalidTranslationPackException::NOT_AN_ARCHIVE);

        (new TranslationPackValidator())->validate($notAnArchive);
    }

    public function testItRefusesAPathThatIsNotThere(): void
    {
        $this->expectException(InvalidTranslationPackException::class);
        $this->expectExceptionCode(InvalidTranslationPackException::NOT_READABLE);

        (new TranslationPackValidator())->validate($this->workDir . '/absent.zip');
    }

    /**
     * @param array<string, string|null> $entries a null value adds a directory entry
     */
    private function createArchive(string $name, array $entries): string
    {
        $path = $this->workDir . '/' . $name;
        $archive = new ZipArchive();
        $archive->open($path, ZipArchive::CREATE | ZipArchive::OVERWRITE);
        foreach ($entries as $entry => $content) {
            if (null === $content) {
                $archive->addEmptyDir(rtrim($entry, '/'));
                continue;
            }
            $archive->addFromString($entry, $content);
        }
        $archive->close();

        return $path;
    }
}

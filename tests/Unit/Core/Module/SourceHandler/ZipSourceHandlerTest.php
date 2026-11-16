<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Module\SourceHandler;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Module\Exception\ModuleErrorException;
use PrestaShop\PrestaShop\Core\Module\SourceHandler\ZipSourceHandler;
use PrestaShop\PrestaShop\Core\Security\Exception\ZipExtractionException;
use PrestaShop\PrestaShop\Core\Security\SafeZipExtractor;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tests\Resources\ResourceResetter;
use ZipArchive;

class ZipSourceHandlerTest extends TestCase
{
    private const UNHANDLABLE_SOURCE = 'unhandlablesource';
    private const INVALID_SOURCE = __DIR__ . '/../../../../Resources/dummyFile/invalid_module.zip';
    private const VALID_SOURCE = __DIR__ . '/../../../../Resources/dummyFile/valid_module.zip';

    /** @var ZipSourceHandler */
    private $zipSourceHandler;

    /** @var string[] */
    private $archives = [];

    /**
     * @var ResourceResetter : responsible to reset resources used for tests
     * */
    private $resourceResetter;

    public function setUp(): void
    {
        $this->resourceResetter = new ResourceResetter();
        $this->resourceResetter->backupTestModules();

        $translator = $this->createMock(TranslatorInterface::class);
        $translator->method('trans')->willReturnArgument(0);

        $this->zipSourceHandler = new ZipSourceHandler(
            $this->resourceResetter::TEST_MODULES_DIR,
            $translator
        );
    }

    public function tearDown(): void
    {
        $this->resourceResetter->resetTestModules();
        foreach ($this->archives as $archive) {
            @unlink($archive);
        }
    }

    public function testCanHandle(): void
    {
        $this->assertFalse($this->zipSourceHandler->canHandle(self::UNHANDLABLE_SOURCE));
        $this->assertTrue($this->zipSourceHandler->canHandle(self::INVALID_SOURCE));
        $this->assertTrue($this->zipSourceHandler->canHandle(self::VALID_SOURCE));
    }

    public function testGetNameUnexistingSource(): void
    {
        $this->expectException(ModuleErrorException::class);
        $this->zipSourceHandler->getModuleName(self::UNHANDLABLE_SOURCE);
    }

    public function testGetNameInvalidSource(): void
    {
        $this->expectException(ModuleErrorException::class);
        $this->zipSourceHandler->getModuleName(self::INVALID_SOURCE);
    }

    public function testGetNameValidSource(): void
    {
        $this->assertSame(
            'valid_module',
            $this->zipSourceHandler->getModuleName(self::VALID_SOURCE)
        );
    }

    public function testHandleUnhandlableSource(): void
    {
        $this->expectException(ModuleErrorException::class);
        $this->zipSourceHandler->handle(self::UNHANDLABLE_SOURCE);
    }

    public function testHandleValidSource(): void
    {
        $this->zipSourceHandler->handle(self::VALID_SOURCE);
        $this->assertFileExists($this->resourceResetter::TEST_MODULES_DIR . '/valid_module/valid_module.php');
    }

    public function testGetNameRejectsAnInvalidModuleName(): void
    {
        $this->expectException(ModuleErrorException::class);
        $this->zipSourceHandler->getModuleName($this->createArchive(['bad.name/bad.name.php' => '<?php']));
    }

    public function testHandleInstallsOnlyTheModuleFolder(): void
    {
        $this->zipSourceHandler->handle($this->createArchive([
            'archive_module/archive_module.php' => '<?php',
            'archive_module_extra.txt' => 'text',
        ]));

        $this->assertFileExists($this->resourceResetter::TEST_MODULES_DIR . 'archive_module/archive_module.php');
        $this->assertFalse(file_exists($this->resourceResetter::TEST_MODULES_DIR . 'archive_module_extra.txt'));
    }

    public function testHandleRejectsAnArchiveWithAnEntryOutsideTheModule(): void
    {
        $outsideFile = 'archive_module_outside_' . uniqid() . '.txt';
        $archive = $this->createArchive([
            'archive_module/archive_module.php' => '<?php',
            '../' . $outsideFile => 'text',
        ]);

        try {
            $this->zipSourceHandler->handle($archive);
            $this->fail('The archive should have been rejected');
        } catch (ModuleErrorException $exception) {
            $this->assertSame('This file does not seem to be a valid module zip', $exception->getMessage());
            $this->assertInstanceOf(ZipExtractionException::class, $exception->getPrevious());
        }

        $this->assertFalse(is_dir($this->resourceResetter::TEST_MODULES_DIR . 'archive_module'));
        // The handler extracts into the cache sandbox: a ../ entry would land next to its extraction folder
        $this->assertFalse(file_exists(_PS_CACHE_DIR_ . 'sandbox' . DIRECTORY_SEPARATOR . $outsideFile));
        $this->assertFalse(file_exists($this->resourceResetter::TEST_MODULES_DIR . $outsideFile));
    }

    public function testHandleLeavesNothingInTheSandbox(): void
    {
        $sandbox = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'ps-zip-sandbox-' . uniqid();
        $translator = $this->createMock(TranslatorInterface::class);
        $translator->method('trans')->willReturnArgument(0);
        $handler = new ZipSourceHandler($this->resourceResetter::TEST_MODULES_DIR, $translator, new SafeZipExtractor($sandbox));

        try {
            $handler->handle($this->createArchive(['archive_module/archive_module.php' => '<?php']));

            $this->assertFileExists($this->resourceResetter::TEST_MODULES_DIR . 'archive_module/archive_module.php');
            $this->assertSame([], array_values(array_diff(scandir($sandbox), ['.', '..'])));
        } finally {
            @rmdir($sandbox);
        }
    }

    /**
     * @param array<string, string> $entries
     */
    private function createArchive(array $entries): string
    {
        $archive = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'ps-zip-source-' . uniqid() . '.zip';
        $zip = new ZipArchive();
        $zip->open($archive, ZipArchive::CREATE);
        foreach ($entries as $name => $content) {
            $zip->addFromString($name, $content);
        }
        $zip->close();
        $this->archives[] = $archive;

        return $archive;
    }
}

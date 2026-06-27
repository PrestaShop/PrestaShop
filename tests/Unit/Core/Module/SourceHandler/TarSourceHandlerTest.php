<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Module\SourceHandler;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Module\Exception\ModuleErrorException;
use PrestaShop\PrestaShop\Core\Module\SourceHandler\TarSourceHandler;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tests\Resources\ResourceResetter;

class TarSourceHandlerTest extends TestCase
{
    private const UNHANDLABLE_SOURCE = 'unhandlablesource';
    private const VALID_SOURCE = __DIR__ . '/../../../../Resources/dummyFile/valid_module.tar.gz';

    /** @var TarSourceHandler */
    private $tarSourceHandler;

    /**
     * @var ResourceResetter : responsible to reset resources used for tests
     */
    private $resourceResetter;

    public function setUp(): void
    {
        $this->resourceResetter = new ResourceResetter();
        $this->resourceResetter->backupTestModules();

        $translator = $this->createMock(TranslatorInterface::class);
        $translator->method('trans')->willReturnArgument(0);

        $this->tarSourceHandler = new TarSourceHandler(
            $this->resourceResetter::TEST_MODULES_DIR,
            $translator
        );
    }

    public function tearDown(): void
    {
        $this->resourceResetter->resetTestModules();
    }

    public function testCanHandle(): void
    {
        $this->assertFalse($this->tarSourceHandler->canHandle(self::UNHANDLABLE_SOURCE));
        $this->assertTrue($this->tarSourceHandler->canHandle(self::VALID_SOURCE));
    }

    public function testGetNameUnexistingSource(): void
    {
        $this->expectException(ModuleErrorException::class);
        $this->tarSourceHandler->getModuleName(self::UNHANDLABLE_SOURCE);
    }

    public function testGetNameValidSource(): void
    {
        $this->assertSame(
            'valid_module',
            $this->tarSourceHandler->getModuleName(self::VALID_SOURCE)
        );
    }

    /**
     * Uploaded files are stored under an extension-less temporary name; the handler must still
     * resolve the module name from such a file (this is the case that originally failed).
     */
    public function testGetNameValidSourceWithoutExtension(): void
    {
        $extensionLessCopy = tempnam(sys_get_temp_dir(), 'ps_module_upload_');
        copy(self::VALID_SOURCE, $extensionLessCopy);

        try {
            $this->assertSame(
                'valid_module',
                $this->tarSourceHandler->getModuleName($extensionLessCopy)
            );
        } finally {
            @unlink($extensionLessCopy);
        }
    }

    public function testHandleUnhandlableSource(): void
    {
        $this->expectException(ModuleErrorException::class);
        $this->tarSourceHandler->handle(self::UNHANDLABLE_SOURCE);
    }

    public function testHandleValidSource(): void
    {
        $this->tarSourceHandler->handle(self::VALID_SOURCE);
        $this->assertFileExists($this->resourceResetter::TEST_MODULES_DIR . '/valid_module/valid_module.php');
    }
}

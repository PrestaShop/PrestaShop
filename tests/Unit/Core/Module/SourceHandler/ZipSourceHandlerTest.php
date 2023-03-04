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

declare(strict_types=1);

namespace Tests\Unit\Core\Module\SourceHandler;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Module\Exception\ModuleErrorException;
use PrestaShop\PrestaShop\Core\Module\SourceHandler\ZipSourceHandler;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tests\Resources\ResourceResetter;

class ZipSourceHandlerTest extends TestCase
{
    private const UNHANDLABLE_SOURCE = 'unhandlablesource';
    private const INVALID_SOURCE = __DIR__ . '/../../../../Resources/dummyFile/invalid_module.zip';
    private const VALID_SOURCE = __DIR__ . '/../../../../Resources/dummyFile/valid_module.zip';

    /** @var ZipSourceHandler */
    private $zipSourceHandler;

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
}

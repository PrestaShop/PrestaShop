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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Module\SourceHandler;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Module\SourceHandler\Exception\SourceNotHandledException;
use PrestaShop\PrestaShop\Core\Module\SourceHandler\RemoteZipSourceHandler;
use PrestaShop\PrestaShop\Core\Module\SourceHandler\ZipSourceHandler;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tests\Resources\ResourceResetter;

class RemoteZipSourceHandlerTest extends TestCase
{
    private const VALID_SOURCE_ZIP = __DIR__ . '/../../../../Resources/dummyFile/valid_module.zip';
    private const INVALID_SOURCE_ZIP = __DIR__ . '/../../../../Resources/dummyFile/invalid_module.zip';
    private const VALID_SOURCE_URL = 'http://valid-source.local/valid_module.zip';
    private const INVALID_SOURCE_URL = 'http://valid-source.local/not_a_zipfile.xml';

    /** @var ZipSourceHandler */
    private $zipSourceHandler;

    /** @var ResourceResetter responsible to reset resources used for tests */
    private $resourceResetter;

    public function setUp(): void
    {
        $this->resourceResetter = new ResourceResetter();
        $this->resourceResetter->backupTestModules();
        $this->resourceResetter->backupDownloads();

        $translator = $this->createMock(TranslatorInterface::class);
        $translator->method('trans')->willReturnArgument(0);

        $this->zipSourceHandler = new ZipSourceHandler($this->resourceResetter::TEST_MODULES_DIR, $translator);
    }

    private function getMockedZipSourceHandler(MockResponse ...$mockResponses): RemoteZipSourceHandler
    {
        return new RemoteZipSourceHandler(
            $this->zipSourceHandler,
            new MockHttpClient($mockResponses),
            _PS_DOWNLOAD_DIR_
        );
    }

    public function tearDown(): void
    {
        $this->resourceResetter->resetTestModules();
        $this->resourceResetter->resetDownloads();
    }

    public function testCanHandle(): void
    {
        $responses = [
            new MockResponse('', ['response_headers' => ['Content-Type' => 'application/zip']]),
            new MockResponse('', ['response_headers' => ['Content-Type' => 'application/xml']]),
            new MockResponse('', ['response_headers' => ['Content-Type' => 'application/zip']]),
            new MockResponse('', ['response_headers' => ['Content-Type' => 'application/zip', 'Content-Disposition' => 'attachment; filename="valid_module.zip"']]),
            new MockResponse('', ['http_code' => 401, 'response_headers' => ['Content-Type' => 'application/zip']]),
        ];
        $remoteZipSourceHandler = $this->getMockedZipSourceHandler(...$responses);

        $this->assertTrue($remoteZipSourceHandler->canHandle(self::VALID_SOURCE_URL));
        $this->assertFalse($remoteZipSourceHandler->canHandle(self::INVALID_SOURCE_URL));
        $this->assertFalse($remoteZipSourceHandler->canHandle(self::INVALID_SOURCE_URL));
        $this->assertTrue($remoteZipSourceHandler->canHandle(self::INVALID_SOURCE_URL));
        $this->assertFalse($remoteZipSourceHandler->canHandle(self::VALID_SOURCE_URL));
    }

    public function testGetModuleNameBeforeTestingHandling(): void
    {
        $this->expectException(SourceNotHandledException::class);
        $remoteZipSourceHandler = $this->getMockedZipSourceHandler();
        $remoteZipSourceHandler->getModuleName(self::INVALID_SOURCE_ZIP);
    }

    public function testGetModuleNameForAnotherSource(): void
    {
        $this->expectException(SourceNotHandledException::class);
        $response = new MockResponse('', ['response_headers' => ['Content-Type' => 'application/zip']]);
        $remoteZipSourceHandler = $this->getMockedZipSourceHandler($response);
        $this->assertTrue($remoteZipSourceHandler->canHandle(self::VALID_SOURCE_URL));
        $remoteZipSourceHandler->getModuleName(self::INVALID_SOURCE_URL);
    }

    public function testGetModuleNameForValidSource(): void
    {
        $responses = [
            new MockResponse('', ['response_headers' => ['Content-Type' => 'application/zip']]),
            new MockResponse('', ['response_headers' => ['Content-Type' => 'application/zip', 'Content-Disposition' => 'attachment; filename="custom_name.zip"']]),
        ];

        $remoteZipSourceHandler = $this->getMockedZipSourceHandler(...$responses);

        $this->assertTrue($remoteZipSourceHandler->canHandle(self::VALID_SOURCE_URL));
        $this->assertSame('valid_module', $remoteZipSourceHandler->getModuleName(self::VALID_SOURCE_URL));

        $this->assertTrue($remoteZipSourceHandler->canHandle(self::VALID_SOURCE_URL));
        $this->assertSame('custom_name', $remoteZipSourceHandler->getModuleName(self::VALID_SOURCE_URL));
    }

    public function testHandleBeforeTestingHandling(): void
    {
        $this->expectException(SourceNotHandledException::class);
        $remoteZipSourceHandler = $this->getMockedZipSourceHandler();
        $remoteZipSourceHandler->handle(self::INVALID_SOURCE_ZIP);
    }

    public function testHandleForAnotherSource(): void
    {
        $this->expectException(SourceNotHandledException::class);
        $response = new MockResponse('', ['response_headers' => ['Content-Type' => 'application/zip']]);
        $remoteZipSourceHandler = $this->getMockedZipSourceHandler($response);
        $this->assertTrue($remoteZipSourceHandler->canHandle(self::VALID_SOURCE_URL));
        $remoteZipSourceHandler->handle(self::INVALID_SOURCE_URL);
    }

    public function testHandleForValidSource(): void
    {
        $response = new MockResponse(
            file_get_contents(self::VALID_SOURCE_ZIP),
            ['response_headers' => ['Content-Type' => 'application/zip']]
        );
        $remoteZipSourceHandler = $this->getMockedZipSourceHandler(
            $response,
            $response
        );

        $this->assertTrue($remoteZipSourceHandler->canHandle(self::VALID_SOURCE_URL));
        $remoteZipSourceHandler->handle(self::VALID_SOURCE_URL);
        $this->assertFileExists($this->resourceResetter::TEST_MODULES_DIR . '/valid_module/valid_module.php');
    }
}

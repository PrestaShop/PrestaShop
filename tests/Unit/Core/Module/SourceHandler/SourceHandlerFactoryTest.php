<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Module\SourceHandler;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Module\SourceHandler\SourceHandlerFactory;
use PrestaShop\PrestaShop\Core\Module\SourceHandler\SourceHandlerInterface;
use PrestaShop\PrestaShop\Core\Module\SourceHandler\SourceHandlerNotFoundException;

class SourceHandlerFactoryTest extends TestCase
{
    /** @var SourceHandlerFactory */
    private $sourceHandlerFactory;

    public function setUp(): void
    {
        $this->sourceHandlerFactory = new SourceHandlerFactory();
    }

    public function testGetUnavailableHandler()
    {
        $this->expectException(SourceHandlerNotFoundException::class);
        $this->sourceHandlerFactory->getHandler('unhandlablesource');
    }

    public function testGetHandler()
    {
        $handlerMock = $this->createMock(SourceHandlerInterface::class);
        $handlerMock->method('canHandle')->willReturn(true);

        $this->sourceHandlerFactory->addHandler($handlerMock);

        $this->assertEquals($handlerMock, $this->sourceHandlerFactory->getHandler('handlablesource'));
    }
}

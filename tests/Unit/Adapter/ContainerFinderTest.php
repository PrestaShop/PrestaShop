<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Unit\Adapter;

use Context;
use Controller;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\ContainerFinder;
use PrestaShop\PrestaShop\Core\Exception\ContainerNotFoundException;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ContainerFinderTest extends TestCase
{
    public function testGetContainerFromAttribute()
    {
        $contextMock = $this->getMockContext();
        $contextMock->container = $this->getMockContainerInterface();

        $containerFinder = new ContainerFinder($contextMock);
        $this->assertInstanceOf(ContainerInterface::class, $containerFinder->getContainer());
    }

    public function testGetContainerFromController()
    {
        $contextMock = $this->getMockContext();
        $contextMock->controller = $this->getMockController();

        $containerFinder = new ContainerFinder($contextMock);
        $this->assertInstanceOf(ContainerInterface::class, $containerFinder->getContainer());
    }

    public function testGetContainerException()
    {
        $this->expectException(ContainerNotFoundException::class);
        $this->expectExceptionMessage('Kernel Container is not available');

        $contextMock = $this->getMockContext();

        $containerFinder = new ContainerFinder($contextMock);
        $containerFinder->getContainer();
    }

    /**
     * @return MockObject|Context
     */
    private function getMockContext()
    {
        return $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @return MockObject|ContainerInterface
     */
    private function getMockContainerInterface()
    {
        return $this->getMockBuilder(ContainerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @return MockObject|Controller
     */
    private function getMockController()
    {
        $mockController = $this->getMockBuilder(Controller::class)
            ->disableOriginalConstructor()
            ->getMock();
        $mockController->method('getContainer')
            ->willReturn($this->getMockContainerInterface());

        return $mockController;
    }
}

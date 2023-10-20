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

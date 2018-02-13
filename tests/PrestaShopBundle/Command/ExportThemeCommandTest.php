<?php
/**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace Tests\PrestaShopBundle\Command;

use PrestaShopBundle\Command\ExportThemeCommand;
use Symfony\Component\Console\Tester\CommandTester;
use PHPUnit\Framework\TestCase;

/**
 * @group sf
 */
class ExportThemeCommandTest extends TestCase
{
    public function testExecute()
    {
        $command = new ExportThemeCommand();
        $commandTester = new CommandTester($command);

        $containerMock = $this->mockContainer();
        $command->setContainer($containerMock);

        $helperSetMock = $this->mockHelperSet();
        $command->setHelperSet($helperSetMock);

        $this->assertEquals(0, $commandTester->execute(array('theme'  => 'classic')));
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function mockThemeRepository()
    {
        $themeMock = $this->getMockBuilder('\PrestaShop\PrestaShop\Core\Addon\Theme\Theme')
            ->disableOriginalConstructor()
            ->getMock();

        $themeRepositoryMock = $this->getMockBuilder('\PrestaShop\PrestaShop\Core\Addon\Theme\ThemeRepository')
            ->disableOriginalConstructor()
            ->getMock();

        $themeRepositoryMock->method('getInstanceByName')
            ->willReturn($themeMock);

        return $themeRepositoryMock;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function mockTranslator()
    {
        $translatorMock = $this->getMockBuilder('\Symfony\Component\Translation\Translator')
            ->disableOriginalConstructor()
            ->getMock();

        return $translatorMock;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function mockThemeExporter()
    {
        $themeExporterMock = $this->getMockBuilder('\PrestaShop\PrestaShop\Core\Addon\Theme\ThemeExporter')
            ->disableOriginalConstructor()
            ->getMock();

        return $themeExporterMock;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function mockContainer()
    {
        $containerMock = $this->getMockBuilder('\Symfony\Component\DependencyInjection\Container')
            ->disableOriginalConstructor()
            ->getMock();

        $themeRepositoryMock = $this->mockThemeRepository();
        $translatorMock = $this->mockTranslator();
        $themeExporterMock = $this->mockThemeExporter();

        $containerMock->method('get')
            ->will($this->returnCallback(function ($serviceId) use (
                $themeRepositoryMock,
                $translatorMock,
                $themeExporterMock
            ) {
                $services = array(
                    'prestashop.core.addon.theme.repository' => $themeRepositoryMock,
                    'translator' => $translatorMock,
                    'prestashop.core.addon.theme.exporter' => $themeExporterMock
                );

                return $services[$serviceId];
            }));

        return $containerMock;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function mockHelperSet()
    {
        $helperSetMock = $this->getMockBuilder('\Symfony\Component\Console\Helper\HelperSet')
            ->disableOriginalConstructor()
            ->getMock();

        $formatterHelperMock = $this->getMockBuilder('\Symfony\Component\Console\Helper\FormatterHelper')
            ->disableOriginalConstructor()
            ->getMock();

        $helperSetMock->method('get')
            ->with('formatter')
            ->willReturn($formatterHelperMock);
        return $helperSetMock;
    }
}

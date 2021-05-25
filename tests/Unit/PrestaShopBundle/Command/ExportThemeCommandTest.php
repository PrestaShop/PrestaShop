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

namespace Tests\Unit\PrestaShopBundle\Command;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Addon\Theme\Theme;
use PrestaShop\PrestaShop\Core\Addon\Theme\ThemeExporter;
use PrestaShop\PrestaShop\Core\Addon\Theme\ThemeRepository;
use PrestaShopBundle\Command\ExportThemeCommand;
use Symfony\Component\Console\Helper\FormatterHelper;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Translation\Translator;

class ExportThemeCommandTest extends TestCase
{
    public function testExecute(): void
    {
        $command = new ExportThemeCommand(
            $this->mockThemeRepository(),
            $this->mockThemeExporter(),
            $this->mockTranslator()
        );
        $commandTester = new CommandTester($command);

        $helperSetMock = $this->mockHelperSet();
        $command->setHelperSet($helperSetMock);

        $this->assertEquals(0, $commandTester->execute(['theme' => 'classic']));
    }

    protected function mockThemeRepository(): ThemeRepository
    {
        $themeMock = $this->getMockBuilder(Theme::class)
            ->disableOriginalConstructor()
            ->getMock();

        $themeRepositoryMock = $this->getMockBuilder(ThemeRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $themeRepositoryMock->method('getInstanceByName')
            ->willReturn($themeMock);

        return $themeRepositoryMock;
    }

    protected function mockTranslator(): Translator
    {
        return $this->getMockBuilder(Translator::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    protected function mockThemeExporter(): ThemeExporter
    {
        return $this->getMockBuilder(ThemeExporter::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    protected function mockContainer(): Container
    {
        $containerMock = $this->getMockBuilder(Container::class)
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
                $services = [
                    'prestashop.core.addon.theme.repository' => $themeRepositoryMock,
                    'translator' => $translatorMock,
                    'prestashop.core.addon.theme.exporter' => $themeExporterMock,
                ];

                return $services[$serviceId];
            }));

        return $containerMock;
    }

    protected function mockHelperSet(): HelperSet
    {
        $helperSetMock = $this->getMockBuilder(HelperSet::class)
            ->disableOriginalConstructor()
            ->getMock();

        $formatterHelperMock = $this->getMockBuilder(FormatterHelper::class)
            ->disableOriginalConstructor()
            ->getMock();

        $helperSetMock->method('get')
            ->with('formatter')
            ->willReturn($formatterHelperMock);

        return $helperSetMock;
    }
}

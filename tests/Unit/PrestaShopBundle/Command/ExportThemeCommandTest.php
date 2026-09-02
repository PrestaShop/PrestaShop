<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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

        $this->assertEquals(0, $commandTester->execute(['theme' => Theme::getDefaultTheme()]));
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

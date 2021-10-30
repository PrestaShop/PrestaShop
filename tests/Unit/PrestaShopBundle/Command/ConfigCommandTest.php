<?php

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\Command;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Adapter\Language\LanguageDataProvider;
use PrestaShop\PrestaShop\Adapter\LegacyContextLoader;
use PrestaShopBundle\Command\ConfigCommand;
use Symfony\Component\Console\Tester\CommandTester;

class ConfigCommandTest extends TestCase
{
    public function testGet(): void
    {
        $commandTester = $this->getCommandTester();

        $this->assertEquals(ConfigCommand::STATUS_OK, $commandTester->execute(['action' => 'get', 'key' => 'CONFIG_TEST']));
        $this->assertStringContainsString('CONFIG_TEST=', $commandTester->getDisplay());
    }

    public function testSet(): void
    {
        $commandTester = $this->getCommandTester();

        $this->assertEquals(ConfigCommand::STATUS_OK, $commandTester->execute(['action' => 'set', 'key' => 'CONFIG_TEST', '--value' => 'testing']));
        $this->assertStringContainsString('CONFIG_TEST=', $commandTester->getDisplay());
    }

    public function testGetLanguage(): void
    {
        $commandTester = $this->getCommandTester();
        $this->assertEquals(ConfigCommand::STATUS_OK, $commandTester->execute(['action' => 'get', 'key' => 'PS_INVOICE_PREFIX', '--lang' => 'en']));
        $this->assertEquals(ConfigCommand::STATUS_INVALID_OPTIONS, $commandTester->execute(['action' => 'get', 'key' => 'PS_INVOICE_PREFIX', '--lang' => 'fr']));
        $this->assertStringContainsString('Invalid language', $commandTester->getDisplay());
    }

    public function testInvalidAction(): void
    {
        $commandTester = $this->getCommandTester();
        $this->assertEquals(ConfigCommand::STATUS_INVALID_ACTION, $commandTester->execute(['action' => 'invalidaction', 'key' => 'CONFIG_TEST']));
        $this->assertStringContainsString('Unknown configuration action', $commandTester->getDisplay());
    }

    public function testNoKey(): void
    {
        $commandTester = $this->getCommandTester();
        $this->expectException(\RuntimeException::class);
        $commandTester->execute([]);
    }

    protected function getCommandTester(): CommandTester
    {
        $command = new ConfigCommand(
            $this->mockLegacyContextLoader(),
            $this->mockConfiguration(),
            $this->mockLanguageDataProvider()
        );

        return new CommandTester($command);
    }

    protected function mockLegacyContextLoader(): LegacyContextLoader
    {
        $legacyContextLoaderMock = $this->getMockBuilder(LegacyContextLoader::class)
            ->disableOriginalConstructor()
            ->getMock();

        return $legacyContextLoaderMock;
    }

    protected function mockConfiguration(): Configuration
    {
        $configurationMock = $this->getMockBuilder(Configuration::class)
            ->disableOriginalConstructor()
            ->getMock();

        return $configurationMock;
    }

    protected function mockLanguageDataProvider(): LanguageDataProvider
    {
        $languageDataProviderMock = $this->getMockBuilder(LanguageDataProvider::class)
            ->disableOriginalConstructor()
            ->getMock();

        $languageDataProviderMock->method('getLanguages')->willReturn($this->getMockLanguageData());

        return $languageDataProviderMock;
    }

    private function getMockLanguageData(): array
    {
        return [
            0 => [
                'id_lang' => '1',
                'name' => 'English (English)',
                'active' => '1',
                'iso_code' => 'en',
                'language_code' => 'en-us',
                'locale' => 'en-US',
                'date_format_lite' => 'm/d/Y',
                'date_format_full' => 'm/d/Y H:i:s',
                'is_rtl' => '0',
                'id_shop' => '1',
                'shops' => [
                    1 => true,
                ],
            ],
            1 => [
                'id_lang' => '2',
                'name' => 'Suomi (Finnish)',
                'active' => '1',
                'iso_code' => 'fi',
                'language_code' => 'fi-fi',
                'locale' => 'fi-FI',
                'date_format_lite' => 'Y-m-d',
                'date_format_full' => 'Y-m-d H:i:s',
                'is_rtl' => '0',
                'id_shop' => '1',
                'shops' => [
                    1 => true,
                ],
            ],
        ];
    }
}

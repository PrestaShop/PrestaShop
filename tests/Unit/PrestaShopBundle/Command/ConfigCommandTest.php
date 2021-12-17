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

        // language specific
        $this->assertEquals(ConfigCommand::STATUS_OK, $commandTester->execute(['action' => 'get', 'key' => 'PS_INVOICE_PREFIX', '--lang' => 'en']));
        $this->assertEquals(ConfigCommand::STATUS_INVALID_OPTIONS, $commandTester->execute(['action' => 'get', 'key' => 'PS_INVOICE_PREFIX', '--lang' => 'fr']));
        $this->assertStringContainsString('Invalid language', $commandTester->getDisplay());
        // lang with shop
        $this->assertEquals(ConfigCommand::STATUS_OK, $commandTester->execute(['action' => 'get', 'key' => 'PS_INVOICE_PREFIX', '--lang' => 'en', '--shopId' => '1']));

        // for one shop
        $this->assertEquals(ConfigCommand::STATUS_OK, $commandTester->execute(['action' => 'get', 'key' => 'CONFIG_TEST', '--shopId' => '1']));
        $this->assertStringContainsString('CONFIG_TEST=', $commandTester->getDisplay());

        // for shop group
        $this->assertEquals(ConfigCommand::STATUS_OK, $commandTester->execute(['action' => 'get', 'key' => 'CONFIG_TEST', '--shopGroupId' => '1']));
        $this->assertStringContainsString('CONFIG_TEST=', $commandTester->getDisplay());

        // invalid shop / shopgroup / both set
        $this->assertEquals(ConfigCommand::STATUS_FAILED_SHOPCONSTRAINT, $commandTester->execute(['action' => 'get', 'key' => 'CONFIG_TEST', '--shopId' => '-1']));
        $this->assertEquals(ConfigCommand::STATUS_FAILED_SHOPCONSTRAINT, $commandTester->execute(['action' => 'get', 'key' => 'CONFIG_TEST', '--shopGroupId' => '-1']));
        $this->assertEquals(ConfigCommand::STATUS_INVALID_OPTIONS, $commandTester->execute(['action' => 'get', 'key' => 'CONFIG_TEST', '--shopId' => '1', '--shopGroupId' => '1']));

        // invalid key
        $this->assertEquals(ConfigCommand::STATUS_INVALID_OPTIONS, $commandTester->execute(['action' => 'get', 'key' => 'INVALID KEY AS IT HAS SPACES']));
        $this->assertStringContainsString('is not a valid configuration key', $commandTester->getDisplay());
    }

    public function testSet(): void
    {
        $commandTester = $this->getCommandTester();

        $this->assertEquals(ConfigCommand::STATUS_OK, $commandTester->execute(['action' => 'set', 'key' => 'CONFIG_TEST', '--value' => 'testing']));
        $this->assertStringContainsString('CONFIG_TEST=', $commandTester->getDisplay());

        // with language
        $this->assertEquals(ConfigCommand::STATUS_OK, $commandTester->execute(['action' => 'set', 'key' => 'CONFIG_TEST', '--value' => 'testing', '--lang' => 'en']));
        $this->assertStringContainsString('CONFIG_TEST=', $commandTester->getDisplay());

        // missing value
        $this->assertEquals(ConfigCommand::STATUS_VALUE_REQUIRED, $commandTester->execute(['action' => 'set', 'key' => 'CONFIG_TEST']));
        $this->assertStringContainsString('Value required', $commandTester->getDisplay());

        // invalid key
        $this->assertEquals(ConfigCommand::STATUS_INVALID_OPTIONS, $commandTester->execute(['action' => 'set', 'key' => 'INVALID KEY']));
        $this->assertStringContainsString('is not a valid configuration key', $commandTester->getDisplay());
    }

    public function testRemove(): void
    {
        $commandTester = $this->getCommandTester();

        // remove before set
        $this->assertEquals(ConfigCommand::STATUS_OK, $commandTester->execute(['action' => 'remove', 'key' => 'CONFIG_TEST_TO_BE_REMOVED']));

        // set and remove
        $this->assertEquals(ConfigCommand::STATUS_OK, $commandTester->execute(['action' => 'set', 'key' => 'CONFIG_TEST_TO_BE_REMOVED', '--value' => 'testing']));
        $this->assertStringContainsString('CONFIG_TEST_TO_BE_REMOVED=', $commandTester->getDisplay());
        $this->assertEquals(ConfigCommand::STATUS_OK, $commandTester->execute(['action' => 'remove', 'key' => 'CONFIG_TEST_TO_BE_REMOVED']));
        $this->assertStringContainsString('OK', $commandTester->getDisplay());
    }

    public function testExceptionsInvalidAction(): void
    {
        $commandTester = $this->getCommandTester();
        $this->assertEquals(ConfigCommand::STATUS_INVALID_ACTION, $commandTester->execute(['action' => 'invalidaction', 'key' => 'CONFIG_TEST']));
        $this->assertStringContainsString('Unknown configuration action', $commandTester->getDisplay());
    }

    public function testExceptionsMissingKey(): void
    {
        $commandTester = $this->getCommandTester();
        $this->expectException(\RuntimeException::class);
        $this->assertEquals(ConfigCommand::STATUS_INVALID_ACTION, $commandTester->execute(['action' => 'get']));
    }

    public function testExceptionsMissingEverything(): void
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

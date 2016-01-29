<?php

namespace PrestaShop\PrestaShop\tests\Unit\Core\Module;

use Phake;
use PrestaShop\PrestaShop\Tests\TestCase\UnitTestCase;
use PrestaShop\PrestaShop\Core\Module\HookConfigurator;

class HookConfiguratorTest extends UnitTestCase
{
    private $hookConfigurator;
    private $hookRepository;

    public function setup()
    {
        $this->hookRepository = Phake::mock(
            'PrestaShop\PrestaShop\Core\Module\HookRepository'
        );

        $this->hookConfigurator = new HookConfigurator($this->hookRepository);
        parent::setup();
    }

    private function setCurrentDisplayHooksConfiguration(array $hookConfiguration)
    {
        Phake::when($this->hookRepository)
            ->getDisplayHooks()
            ->thenReturn($hookConfiguration)
        ;

        return $this;
    }

    public function test_single_module_appended_to_hook()
    {
        $this->setCurrentDisplayHooksConfiguration([
            "displayTop" => [
                "block_already_here"
            ]
        ]);

        $expected = [
            "displayTop" => [
                "block_already_here",
                "blocklanguages"
            ]
        ];

        $actual = $this->hookConfigurator->getThemeHooksConfiguration([
            "displayTop" => [
                null,
                "blocklanguages"
            ]
        ]);

        $this->assertEquals($expected, $actual);
    }

    public function test_multiple_modules_appended_to_hook()
    {
        $this->setCurrentDisplayHooksConfiguration([
            "displayTop" => [
                "block_already_here"
            ]
        ]);

        $expected = [
            "displayTop" => [
                "block_already_here",
                "blocklanguages",
                "blockcurrencies"
            ]
        ];

        $actual = $this->hookConfigurator->getThemeHooksConfiguration([
            "displayTop" => [
                null,
                "blocklanguages",
                "blockcurrencies"
            ]
        ]);

        $this->assertEquals($expected, $actual);
    }

    public function test_single_module_prepended_to_hook()
    {
        $this->setCurrentDisplayHooksConfiguration([
            "displayTop" => [
                "block_already_here"
            ]
        ]);

        $expected = [
            "displayTop" => [
                "blocklanguages",
                "block_already_here"
            ]
        ];

        $actual = $this->hookConfigurator->getThemeHooksConfiguration([
            "displayTop" => [
                "blocklanguages",
                null
            ]
        ]);

        $this->assertEquals($expected, $actual);
    }

    public function test_multiple_modules_prepended_to_hook()
    {
        $this->setCurrentDisplayHooksConfiguration([
            "displayTop" => [
                "block_already_here"
            ]
        ]);

        $expected = [
            "displayTop" => [
                "blocklanguages",
                "blockcurrencies",
                "block_already_here"
            ]
        ];

        $actual = $this->hookConfigurator->getThemeHooksConfiguration([
            "displayTop" => [
                "blocklanguages",
                "blockcurrencies",
                null
            ]
        ]);

        $this->assertEquals($expected, $actual);
    }

    public function test_modules_hooked_are_replaced()
    {
        $this->setCurrentDisplayHooksConfiguration([
            "displayTop" => [
                "block_already_here"
            ]
        ]);

        $expected = [
            "displayTop" => [
                "blocklanguages",
                "blockcurrencies"
            ]
        ];

        $actual = $this->hookConfigurator->getThemeHooksConfiguration([
            "displayTop" => [
                "blocklanguages",
                "blockcurrencies"
            ]
        ]);

        $this->assertEquals($expected, $actual);
    }

    public function test_when_a_module_is_hooked_it_is_unhooked_from_current_display_hooks()
    {
        $this->setCurrentDisplayHooksConfiguration([
            "displayTop" => [
                "blocklanguages"
            ],
            "displayNav" => [
                "block_already_here"
            ]
        ]);

        $expected = [
            "displayTop" => [
            ],
            "displayNav" => [
                "blocklanguages"
            ]
        ];

        $actual = $this->hookConfigurator->getThemeHooksConfiguration([
            "displayNav" => [
                "blocklanguages"
            ]
        ]);

        $this->assertEquals($expected, $actual);
    }
}

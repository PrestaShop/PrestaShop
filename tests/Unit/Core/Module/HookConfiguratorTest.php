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

namespace Tests\Unit\Core\Module;

use Phake;
use Tests\TestCase\UnitTestCase;
use PrestaShop\PrestaShop\Core\Module\HookConfigurator;

class HookConfiguratorTest extends UnitTestCase
{
    private $hookConfigurator;
    private $hookRepository;

    public function setUp()
    {
        $this->hookRepository = Phake::mock(
            'PrestaShop\PrestaShop\Core\Module\HookRepository'
        );

        $this->hookConfigurator = new HookConfigurator($this->hookRepository);
        parent::setUp();
    }

    private function setCurrentDisplayHooksConfiguration(array $hookConfiguration)
    {
        Phake::when($this->hookRepository)
            ->getDisplayHooksWithModules()
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

    public function test_single_module_appended_to_hook_with_exceptions()
    {
        $this->setCurrentDisplayHooksConfiguration([
            "displayTop" => [
                "block_already_here"
            ]
        ]);

        $expected = [
            "displayTop" => [
                "block_already_here",
                "blocklanguages" => [
                    "except_pages" => [
                        "category",
                        "product"
                    ]
                ]
            ]
        ];

        $actual = $this->hookConfigurator->getThemeHooksConfiguration([
            "displayTop" => [
                null,
                "blocklanguages" => [
                    "except_pages" => [
                        "category",
                        "product"
                    ]
                ]
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

    public function test_multiple_tilde_in_hook_module_list()
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
                null,
                "blockcurrencies",
                null,
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

    public function test_new_hook_is_created()
    {
        $config = [
            "displayTop" => [
                "blocklanguages"
            ]
        ];
        $this->setCurrentDisplayHooksConfiguration([]);

        $this->assertEquals(
            $config,
            $this
                ->hookConfigurator
                ->getThemeHooksConfiguration($config)
        );
    }
}

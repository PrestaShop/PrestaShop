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

namespace Tests\Unit\Core\Module;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Module\HookConfigurator;
use PrestaShop\PrestaShop\Core\Module\HookRepository;

class HookConfiguratorTest extends TestCase
{
    private $hookConfigurator;
    private $hookRepository;

    protected function setUp(): void
    {
        $this->hookRepository = $this->createMock(HookRepository::class);

        $this->hookConfigurator = new HookConfigurator($this->hookRepository);
        parent::setUp();
    }

    private function setCurrentDisplayHooksConfiguration(array $hookConfiguration)
    {
        $this->hookRepository->method('getDisplayHooksWithModules')->willReturn($hookConfiguration);

        return $this;
    }

    public function testSingleModuleAppendedToHook()
    {
        $this->setCurrentDisplayHooksConfiguration([
            'displayTop' => [
                'block_already_here',
            ],
        ]);

        $expected = [
            'displayTop' => [
                'block_already_here',
                'blocklanguages',
            ],
        ];

        $actual = $this->hookConfigurator->getThemeHooksConfiguration([
            'displayTop' => [
                null,
                'blocklanguages',
            ],
        ]);

        $this->assertEquals($expected, $actual);
    }

    public function testSingleModuleAppendedToHookWithExceptions()
    {
        $this->setCurrentDisplayHooksConfiguration([
            'displayTop' => [
                'block_already_here',
            ],
        ]);

        $expected = [
            'displayTop' => [
                'block_already_here',
                'blocklanguages' => [
                    'except_pages' => [
                        'category',
                        'product',
                    ],
                ],
            ],
        ];

        $actual = $this->hookConfigurator->getThemeHooksConfiguration([
            'displayTop' => [
                null,
                'blocklanguages' => [
                    'except_pages' => [
                        'category',
                        'product',
                    ],
                ],
            ],
        ]);

        $this->assertEquals($expected, $actual);
    }

    public function testMultipleModulesAppendedToHook()
    {
        $this->setCurrentDisplayHooksConfiguration([
            'displayTop' => [
                'block_already_here',
            ],
        ]);

        $expected = [
            'displayTop' => [
                'block_already_here',
                'blocklanguages',
                'blockcurrencies',
            ],
        ];

        $actual = $this->hookConfigurator->getThemeHooksConfiguration([
            'displayTop' => [
                null,
                'blocklanguages',
                'blockcurrencies',
            ],
        ]);

        $this->assertEquals($expected, $actual);
    }

    public function testMultipleTildeInHookModuleList()
    {
        $this->setCurrentDisplayHooksConfiguration([
            'displayTop' => [
                'block_already_here',
            ],
        ]);

        $expected = [
            'displayTop' => [
                'block_already_here',
                'blocklanguages',
                'blockcurrencies',
            ],
        ];

        $actual = $this->hookConfigurator->getThemeHooksConfiguration([
            'displayTop' => [
                null,
                'blocklanguages',
                null,
                'blockcurrencies',
                null,
            ],
        ]);

        $this->assertEquals($expected, $actual);
    }

    public function testSingleModulePrependedToHook()
    {
        $this->setCurrentDisplayHooksConfiguration([
            'displayTop' => [
                'block_already_here',
            ],
        ]);

        $expected = [
            'displayTop' => [
                'blocklanguages',
                'block_already_here',
            ],
        ];

        $actual = $this->hookConfigurator->getThemeHooksConfiguration([
            'displayTop' => [
                'blocklanguages',
                null,
            ],
        ]);

        $this->assertEquals($expected, $actual);
    }

    public function testMultipleModulesPrependedToHook()
    {
        $this->setCurrentDisplayHooksConfiguration([
            'displayTop' => [
                'block_already_here',
            ],
        ]);

        $expected = [
            'displayTop' => [
                'blocklanguages',
                'blockcurrencies',
                'block_already_here',
            ],
        ];

        $actual = $this->hookConfigurator->getThemeHooksConfiguration([
            'displayTop' => [
                'blocklanguages',
                'blockcurrencies',
                null,
            ],
        ]);

        $this->assertEquals($expected, $actual);
    }

    public function testModulesHookedAreReplaced()
    {
        $this->setCurrentDisplayHooksConfiguration([
            'displayTop' => [
                'block_already_here',
            ],
        ]);

        $expected = [
            'displayTop' => [
                'blocklanguages',
                'blockcurrencies',
            ],
        ];

        $actual = $this->hookConfigurator->getThemeHooksConfiguration([
            'displayTop' => [
                'blocklanguages',
                'blockcurrencies',
            ],
        ]);

        $this->assertEquals($expected, $actual);
    }

    public function testWhenAModuleIsHookedItIsUnhookedFromCurrentDisplayHooks()
    {
        $this->setCurrentDisplayHooksConfiguration([
            'displayTop' => [
                'blocklanguages',
            ],
            'displayNav' => [
                'block_already_here',
            ],
        ]);

        $expected = [
            'displayTop' => [
            ],
            'displayNav' => [
                'blocklanguages',
            ],
        ];

        $actual = $this->hookConfigurator->getThemeHooksConfiguration([
            'displayNav' => [
                'blocklanguages',
            ],
        ]);

        $this->assertEquals($expected, $actual);
    }

    public function testNewHookIsCreated()
    {
        $config = [
            'displayTop' => [
                'blocklanguages',
            ],
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

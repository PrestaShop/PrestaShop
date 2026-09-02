<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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

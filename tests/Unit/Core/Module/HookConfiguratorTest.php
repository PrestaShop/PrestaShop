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
use PrestaShop\PrestaShop\Core\Module\ModuleManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\Yaml\Yaml;

class HookConfiguratorTest extends TestCase
{
    private $hookConfigurator;
    private $hookRepository;
    private $moduleManager;

    protected function setUp(): void
    {
        $this->hookRepository = $this->createMock(HookRepository::class);
        $this->moduleManager = $this->createMock(ModuleManager::class);
        // Both service definitions inject the module manager, so the on-disk guard runs in
        // production. Injecting it here too is what exercises the module name lookup, and the
        // stub answers for a fixed set of names: anything else, a list position included, is
        // not a module on disk, which is exactly what the real manager reports.
        $this->moduleManager
            ->method('isOnDisk')
            ->willReturnCallback(static fn ($name): bool => in_array($name, [
                'block_already_here',
                'blockcurrencies',
                'blocklanguages',
            ], true));

        $this->hookConfigurator = new HookConfigurator($this->hookRepository, null, $this->moduleManager);
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

    /**
     * The module name as an array key is the older form, reachable only from a hand-built
     * array. It stays supported, and normalises to the same list entry as the YAML form.
     */
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
                [
                    'blocklanguages' => [
                        'except_pages' => [
                            'category',
                            'product',
                        ],
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

    public function testModuleWithExceptionsIsHookedFromAParsedThemeYaml()
    {
        $hooks = Yaml::parse(
            "displayTop:\n"
            . "  - ~\n"
            . "  - blocklanguages:\n"
            . "      except_pages:\n"
            . "        - category\n"
            . "        - product\n"
        );
        // A mapping cannot be written next to the `~` placeholder in one sequence, so the
        // module carrying settings arrives under a position and not under its own name.
        $this->assertSame([0, 1], array_keys($hooks['displayTop']));

        $this->setCurrentDisplayHooksConfiguration([
            'displayTop' => [
                'block_already_here',
            ],
        ]);

        $expected = [
            'displayTop' => [
                'block_already_here',
                [
                    'blocklanguages' => [
                        'except_pages' => [
                            'category',
                            'product',
                        ],
                    ],
                ],
            ],
        ];

        $this->assertEquals($expected, $this->hookConfigurator->getThemeHooksConfiguration($hooks));
    }

    public function testModuleWithExceptionsIsReadableByTheRepository()
    {
        // HookRepository::persistHooksConfiguration() takes the name with key() and the
        // settings with current(). The two halves used to disagree on where the name lives.
        $this->setCurrentDisplayHooksConfiguration([
            'displayTop' => [],
        ]);

        $configuration = $this->hookConfigurator->getThemeHooksConfiguration([
            'displayTop' => [
                [
                    'blocklanguages' => [
                        'except_pages' => [
                            'category',
                        ],
                    ],
                ],
            ],
        ]);

        $entry = $configuration['displayTop'][0];
        $this->assertSame('blocklanguages', key($entry));
        $this->assertSame(['category'], current($entry)['except_pages']);
    }

    public function testModuleWithExceptionsRemovedFromDiskIsSkipped()
    {
        $this->setCurrentDisplayHooksConfiguration([
            'displayTop' => [],
        ]);

        $logger = $this->createMock(LoggerInterface::class);
        // The warning has to name the module. Naming the list position instead is where the
        // "Module 1 was removed from disk" line in the reports comes from.
        $logger
            ->expects($this->once())
            ->method('warning')
            ->with('Module removed_from_disk was removed from disk, impossible to hook it');

        $hookConfigurator = new HookConfigurator($this->hookRepository, $logger, $this->moduleManager);

        $configuration = $hookConfigurator->getThemeHooksConfiguration([
            'displayTop' => [
                [
                    'removed_from_disk' => [
                        'except_pages' => [
                            'category',
                        ],
                    ],
                ],
                'blocklanguages',
            ],
        ]);

        $this->assertEquals(['displayTop' => ['blocklanguages']], $configuration);
    }

    public function testModuleWithExceptionsIsUnhookedFromItsCurrentHook()
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
            'displayTop' => [],
            'displayNav' => [
                'block_already_here',
                [
                    'blocklanguages' => [
                        'except_pages' => [
                            'category',
                        ],
                    ],
                ],
            ],
        ];

        $actual = $this->hookConfigurator->getThemeHooksConfiguration([
            'displayNav' => [
                null,
                [
                    'blocklanguages' => [
                        'except_pages' => [
                            'category',
                        ],
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

    public function testExistingModuleWithExceptionsIsKeptByThePlaceholder()
    {
        // A module already hooked with exceptions comes back from the repository keyed by its own
        // name. Taking the value as the name handed the module manager an array, which is fatal.
        $this->setCurrentDisplayHooksConfiguration([
            'displayTop' => [
                'blocklanguages' => [
                    'except_pages' => [
                        'category',
                    ],
                ],
            ],
        ]);

        $expected = [
            'displayTop' => [
                [
                    'blocklanguages' => [
                        'except_pages' => [
                            'category',
                        ],
                    ],
                ],
                'blockcurrencies',
            ],
        ];

        $actual = $this->hookConfigurator->getThemeHooksConfiguration([
            'displayTop' => [
                null,
                'blockcurrencies',
            ],
        ]);

        $this->assertEquals($expected, $actual);
    }

    public function testExistingModuleWithExceptionsIsUnhookedWhenTheThemeMovesIt()
    {
        $this->setCurrentDisplayHooksConfiguration([
            'displayTop' => [
                'blocklanguages' => [
                    'except_pages' => [
                        'category',
                    ],
                ],
            ],
        ]);

        $expected = [
            'displayTop' => [],
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

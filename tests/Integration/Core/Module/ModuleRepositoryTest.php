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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace Tests\Integration\Core\Module;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\HookManager;
use PrestaShop\PrestaShop\Adapter\Module\AdminModuleDataProvider;
use PrestaShop\PrestaShop\Adapter\Module\ModuleDataProvider;
use PrestaShop\PrestaShop\Core\Module\ModuleRepository;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Cache\DoctrineProvider;
use Symfony\Component\Translation\Translator;

class ModuleRepositoryTest extends TestCase
{
    /**
     * @var ModuleRepository
     */
    private $moduleRepository;

    protected function setUp(): void
    {
        $moduleDataProvider = $this->createMock(ModuleDataProvider::class);
        $moduleDataProvider->method('findByName')->willReturn([
            'installed' => 0,
            'active' => true,
        ]);

        $translator = $this->createMock(Translator::class);
        $translator->method('trans')->willReturnArgument(0);

        $hookManager = $this->createMock(HookManager::class);
        // cf. HookManager::exec() method signature
        $hookExecMethodMock = function (
            $hook_name,
            $hook_args,
            $id_module,
            $array_return,
            $check_exceptions,
            $use_push,
            $id_shop
            ) {
            // This mock represents a module that :
            // - overrides `dummy_payment` module `fullDescription` attributes
            // - adds `testAttribute` attributes to `dummy_payment` module
            // when 'actionListModules' hook called
            if ($hook_name === 'actionListModules') {
                return [
                    'ps_distributionapiclient' => [
                        [
                            'name' => 'dummy_payment',
                            'fullDescription' => 'overridden full description',
                            'testAttribute' => 'added value',
                        ],
                    ],
                ];
            } else {
                return [];
            }
        };

        $hookManager->method('exec')->willReturn(
            $this->returnCallback($hookExecMethodMock)
        );

        $this->moduleRepository = new ModuleRepository(
            $moduleDataProvider,
            $this->createMock(AdminModuleDataProvider::class),
            new DoctrineProvider(new ArrayAdapter()),
            $hookManager,
            dirname(__DIR__, 3) . '/Resources/modules/',
            1
        );
    }

    public function testGetListReturnsWellEnrichedModule(): void
    {
        $moduleList = iterator_to_array($this->moduleRepository->getList());
        $filteredModules = array_filter($moduleList, function ($module, $key) {
            return $module->get('name') === 'dummy_payment';
        }, ARRAY_FILTER_USE_BOTH);

        $this->assertEquals(1, count($filteredModules), 'Returned module list may contain at least "dummy_payment" module.');
        $dummy_module = array_shift($filteredModules);

        $this->assertEquals('overridden full description', $dummy_module->get('fullDescription'));
        $this->assertEquals('added value', $dummy_module->get('testAttribute'));
    }

    public function testGetModuleWellEnrichedByModules(): void
    {
        $dummy_module = $this->moduleRepository->getModule('dummy_payment');

        $this->assertEquals('overridden full description', $dummy_module->get('fullDescription'));
        $this->assertEquals('added value', $dummy_module->get('testAttribute'));
    }
}

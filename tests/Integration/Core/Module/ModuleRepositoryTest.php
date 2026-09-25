<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Core\Module;

use Doctrine\Common\Cache\CacheProvider;
use Doctrine\Common\Cache\Psr6\DoctrineProvider;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\HookManager;
use PrestaShop\PrestaShop\Adapter\Module\AdminModuleDataProvider;
use PrestaShop\PrestaShop\Adapter\Module\ModuleDataProvider;
use PrestaShop\PrestaShop\Core\Context\LanguageContext;
use PrestaShop\PrestaShop\Core\Localization\LocaleInterface;
use PrestaShop\PrestaShop\Core\Module\ModuleRepository;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Translation\Translator;

class ModuleRepositoryTest extends TestCase
{
    /**
     * @var ModuleRepository
     */
    private $moduleRepository;

    protected function setUp(): void
    {
        $mockModuleDataProvider = $this->createMock(ModuleDataProvider::class);
        $mockModuleDataProvider->method('findByName')->willReturn([
            'installed' => 0,
            'active' => true,
        ]);
        $mockModuleDataProvider->method('can')->willReturn(true);

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

        /** @var CacheProvider $cacheProvider */
        $cacheProvider = DoctrineProvider::wrap(new ArrayAdapter());

        $this->moduleRepository = new ModuleRepository(
            $mockModuleDataProvider,
            $this->createMock(AdminModuleDataProvider::class),
            $cacheProvider,
            $hookManager,
            dirname(__DIR__, 3) . '/Resources/modules/',
            new LanguageContext(
                1,
                'English',
                'en',
                'en-US',
                'en-us',
                false,
                'm/d/Y',
                'm/d/Y H:i:s',
                $this->createMock(LocaleInterface::class),
            ),
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

    public function testGetModuleIsRefreshedWhenTheMainClassIsEditedInPlace(): void
    {
        $moduleName = 'qamodulecachetest';
        $modulesDir = sys_get_temp_dir() . '/ps-module-cache-' . uniqid() . '/';
        $moduleDir = $modulesDir . $moduleName;
        $mainClass = $moduleDir . '/' . $moduleName . '.php';

        mkdir($moduleDir, 0777, true);
        file_put_contents($mainClass, "<?php\n// version 1.0.0\n");

        try {
            $repository = $this->createRepositoryFor($modulesDir);

            $initialFilemtime = $repository->getModule($moduleName)->getDiskAttributes()->get('filemtime');
            $directoryMtime = filemtime($moduleDir);

            // Editing a file that already exists does not touch the folder holding it, which is exactly
            // what happens when a developer bumps $this->version in the main class.
            file_put_contents($mainClass, "<?php\n// version 1.0.1\n");
            touch($mainClass, $initialFilemtime + 10);
            touch($moduleDir, $directoryMtime);
            clearstatcache();

            $this->assertSame(
                $directoryMtime,
                filemtime($moduleDir),
                'The folder mtime must stay put, otherwise this test is not reproducing the reported case.'
            );

            $this->assertSame(
                $initialFilemtime + 10,
                $repository->getModule($moduleName)->getDiskAttributes()->get('filemtime'),
                'The cached module must be rebuilt once its main class changes.'
            );
        } finally {
            @unlink($mainClass);
            @rmdir($moduleDir);
            @rmdir($modulesDir);
        }
    }

    private function createRepositoryFor(string $modulesDir): ModuleRepository
    {
        $moduleDataProvider = $this->createMock(ModuleDataProvider::class);
        $moduleDataProvider->method('can')->willReturn(true);

        $hookManager = $this->createMock(HookManager::class);
        $hookManager->method('exec')->willReturn([]);

        /** @var CacheProvider $cacheProvider */
        $cacheProvider = DoctrineProvider::wrap(new ArrayAdapter());

        return new ModuleRepository(
            $moduleDataProvider,
            $this->createMock(AdminModuleDataProvider::class),
            $cacheProvider,
            $hookManager,
            $modulesDir,
            new LanguageContext(
                1,
                'English',
                'en',
                'en-US',
                'en-us',
                false,
                'm/d/Y',
                'm/d/Y H:i:s',
                $this->createMock(LocaleInterface::class),
            ),
        );
    }
}

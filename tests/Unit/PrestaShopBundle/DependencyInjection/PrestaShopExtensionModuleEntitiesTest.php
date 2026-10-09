<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\DependencyInjection;

use PHPUnit\Framework\TestCase;
use PrestaShopBundle\DependencyInjection\PrestaShopExtension;
use ReflectionMethod;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Filesystem\Filesystem;

/**
 * ApiPlatform reads a mapping path by requiring every PHP file it finds there, and a module
 * index.php holds an `exit` statement. That used to be handled by deleting those files from the
 * module, which left a shop quietly modifying files it does not own - they then show up as missing
 * when updating. The mapping is built against a directory of links instead.
 */
class PrestaShopExtensionModuleEntitiesTest extends TestCase
{
    private string $workDir;
    private string $moduleDir;
    private string $cacheDir;

    protected function setUp(): void
    {
        parent::setUp();

        $this->workDir = sys_get_temp_dir() . '/ps_extension_test_' . uniqid();
        $this->moduleDir = $this->workDir . '/modules/';
        $this->cacheDir = $this->workDir . '/cache';

        $entityDir = $this->moduleDir . 'probemodule/src/Entity';
        $filesystem = new Filesystem();
        $filesystem->mkdir($entityDir);
        $filesystem->mkdir($this->cacheDir);

        file_put_contents($entityDir . '/ProbeEntity.php', "<?php\nnamespace Probe\\Entity;\nclass ProbeEntity {}\n");
        // The file the module ships to keep the folder from being listed, holding an exit statement.
        file_put_contents($entityDir . '/index.php', "<?php\nexit;\n");
    }

    protected function tearDown(): void
    {
        (new Filesystem())->remove($this->workDir);
        parent::tearDown();
    }

    public function testTheModuleKeepsItsIndexFile(): void
    {
        $this->prependApiConfig();

        self::assertFileExists(
            $this->moduleDir . 'probemodule/src/Entity/index.php',
            'the shop removed a file from a module directory it does not own'
        );
    }

    public function testTheMappingPointsAtLinksWithoutTheIndexFile(): void
    {
        $container = $this->prependApiConfig();

        $paths = $container->getExtensionConfig('api_platform')[0]['mapping']['paths'] ?? [];
        self::assertNotEmpty($paths, 'no mapping path was registered for the module entities');

        $mirror = null;
        foreach ($paths as $path) {
            if (str_contains($path, 'probemodule')) {
                $mirror = $path;
            }
        }
        self::assertNotNull($mirror, 'the module entities were not mapped at all');
        self::assertStringStartsWith($this->cacheDir, $mirror, 'the module folder itself is still mapped');

        self::assertFileExists($mirror . '/ProbeEntity.php', 'the entity is not reachable through the mapping');
        self::assertFileDoesNotExist($mirror . '/index.php', 'index.php would be required and would stop the build');
        self::assertSame(
            realpath($this->moduleDir . 'probemodule/src/Entity/ProbeEntity.php'),
            realpath($mirror . '/ProbeEntity.php'),
            'the entity must still resolve to its own file in the module'
        );
    }

    private function prependApiConfig(): ContainerBuilder
    {
        $container = new ContainerBuilder();
        $container->setParameter('prestashop.active_modules', ['probemodule']);
        $container->setParameter('prestashop.module_dir', $this->moduleDir);
        $container->setParameter('kernel.cache_dir', $this->cacheDir);

        $method = new ReflectionMethod(PrestaShopExtension::class, 'preprendApiConfig');
        $method->setAccessible(true);
        $method->invoke(new PrestaShopExtension(), $container);

        return $container;
    }
}

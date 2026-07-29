<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\DependencyInjection\Compiler;

use Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use PHPUnit\Framework\TestCase;
use PrestaShopBundle\DependencyInjection\Compiler\ModulesDoctrineCompilerPass;
use ReflectionMethod;
use ReflectionProperty;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\Filesystem\Filesystem;

class ModulesDoctrineCompilerPassTest extends TestCase
{
    private string $entityDir;

    private string $namespace;

    protected function setUp(): void
    {
        parent::setUp();

        $this->entityDir = sys_get_temp_dir() . '/ps_doctrine_pass_test_' . uniqid() . '/src/Entity';
        $this->namespace = 'Probe' . uniqid() . '\\Entity';
        (new Filesystem())->mkdir($this->entityDir . '/Sub');

        $this->writeEntity('ProbeEntity', $this->namespace, $this->entityDir);
        $this->writeEntity('NestedEntity', $this->namespace . '\\Sub', $this->entityDir . '/Sub');
        // A module index.php ends with exit, which would stop the build; throwing makes its inclusion visible here.
        $indexFile = "<?php\nthrow new \\LogicException('index.php was required');\n";
        file_put_contents($this->entityDir . '/index.php', $indexFile);
        file_put_contents($this->entityDir . '/Sub/index.php', $indexFile);
    }

    protected function tearDown(): void
    {
        (new Filesystem())->remove(dirname($this->entityDir, 2));
        parent::tearDown();
    }

    public function testTheIndexFilesOfEverySubFolderAreLeftOut(): void
    {
        $classNames = $this->createDriver()->getAllClassNames();
        sort($classNames);

        self::assertSame([
            $this->namespace . '\\ProbeEntity',
            $this->namespace . '\\Sub\\NestedEntity',
        ], $classNames);
    }

    private function createDriver(): AnnotationDriver
    {
        $method = new ReflectionMethod(ModulesDoctrineCompilerPass::class, 'createAnnotationMappingDriver');
        $method->setAccessible(true);
        /** @var DoctrineOrmMappingsPass $mappingsPass */
        $mappingsPass = $method->invoke(new ModulesDoctrineCompilerPass(), $this->namespace, $this->entityDir);

        $property = new ReflectionProperty($mappingsPass, 'driver');
        $property->setAccessible(true);
        /** @var Definition $definition */
        $definition = $property->getValue($mappingsPass);

        $driver = new AnnotationDriver(new AnnotationReader(), $definition->getArgument(1));
        foreach ($definition->getMethodCalls() as [$name, $arguments]) {
            $driver->{$name}(...$arguments);
        }

        return $driver;
    }

    private function writeEntity(string $className, string $namespace, string $directory): void
    {
        file_put_contents(
            $directory . '/' . $className . '.php',
            "<?php\nnamespace $namespace;\n\nuse Doctrine\\ORM\\Mapping as ORM;\n\n/**\n * @ORM\\Entity\n */\nclass $className\n{\n}\n"
        );
    }
}

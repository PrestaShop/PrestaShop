<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\DependencyInjection\Compiler;

use League\Flysystem\Filesystem;
use League\Flysystem\InMemory\InMemoryFilesystemAdapter;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Storage\FactoryStorageInterface;
use PrestaShopBundle\DependencyInjection\Compiler\StorageCollectorPass;
use PrestaShopBundle\Storage\FactoryStorage;
use PrestaShopBundle\Storage\FlysystemStorage;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;

class StorageCollectorPassTest extends TestCase
{
    public function testItRegistersTheTaggedStoragesIntoTheFactory(): void
    {
        $container = $this->createContainerWithFactory();
        $this->addTaggedStorage($container, 'prestashop.storage.images', ['storage_name' => 'images']);
        $this->addTaggedStorage($container, 'my_module.storage.invoices', ['storage_name' => 'invoices']);

        $container->compile();

        /** @var FactoryStorageInterface $factory */
        $factory = $container->get(FactoryStorageInterface::class);

        $this->assertSame(['images', 'invoices'], $factory->getNames());
        $this->assertInstanceOf(FlysystemStorage::class, $factory->get('invoices'));
    }

    public function testItThrowsAnExceptionForATaggedStorageWithoutName(): void
    {
        $container = $this->createContainerWithFactory();
        $this->addTaggedStorage($container, 'my_module.storage.invoices', []);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Service "my_module.storage.invoices" is tagged as "prestashop.storage" but has no "storage_name" attribute.');

        $container->compile();
    }

    public function testItDoesNothingWhenNoFactoryIsRegistered(): void
    {
        $container = new ContainerBuilder();
        $container->addCompilerPass(new StorageCollectorPass());
        $this->addTaggedStorage($container, 'my_module.storage.invoices', ['storage_name' => 'invoices']);

        $container->compile();

        $this->assertFalse($container->has(FactoryStorageInterface::class));
    }

    private function createContainerWithFactory(): ContainerBuilder
    {
        $container = new ContainerBuilder();
        $container->addCompilerPass(new StorageCollectorPass());
        $container
            ->register(FactoryStorageInterface::class, FactoryStorage::class)
            ->setArgument('$storages', null)
            ->setPublic(true)
        ;

        return $container;
    }

    private function addTaggedStorage(ContainerBuilder $container, string $serviceId, array $tagAttributes): void
    {
        $filesystem = new Definition(Filesystem::class, [new Definition(InMemoryFilesystemAdapter::class)]);

        $container
            ->register($serviceId, FlysystemStorage::class)
            ->setArguments([$filesystem])
            ->addTag(StorageCollectorPass::STORAGE_TAG, $tagAttributes)
        ;
    }
}

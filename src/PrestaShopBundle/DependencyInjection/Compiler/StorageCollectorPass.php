<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\DependencyInjection\Compiler;

use PrestaShop\PrestaShop\Core\Storage\FactoryStorageInterface;
use PrestaShopBundle\Storage\FactoryStorage;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Compiler\ServiceLocatorTagPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Registers every tagged storage into the storage factory.
 *
 * A module declares an additional storage by tagging its own service, which keeps it from
 * having to redefine the factory:
 *
 *   my_module.storage.invoices:
 *     class: PrestaShopBundle\Storage\FlysystemStorage
 *     arguments: [ '@my_module.filesystem.invoices' ]
 *     tags:
 *       - { name: prestashop.storage, storage_name: invoices }
 */
class StorageCollectorPass implements CompilerPassInterface
{
    public const STORAGE_TAG = 'prestashop.storage';

    public function process(ContainerBuilder $container): void
    {
        if (!$container->has(FactoryStorageInterface::class)) {
            return;
        }

        $storages = [];
        foreach ($container->findTaggedServiceIds(self::STORAGE_TAG) as $serviceId => $tags) {
            foreach ($tags as $tag) {
                if (empty($tag['storage_name'])) {
                    throw new InvalidArgumentException(sprintf(
                        'Service "%s" is tagged as "%s" but has no "storage_name" attribute.',
                        $serviceId,
                        self::STORAGE_TAG
                    ));
                }

                $storages[$tag['storage_name']] = new Reference($serviceId);
            }
        }

        $container
            ->findDefinition(FactoryStorageInterface::class)
            ->setArgument('$storages', ServiceLocatorTagPass::register($container, $storages, FactoryStorage::class))
        ;
    }
}

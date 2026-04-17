<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Doctrine;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Container\ContainerInterface;
use Throwable;

final class FrontDoctrineProxyWarmer
{
    /** @var ContainerInterface */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function run(): void
    {
        if (!$this->container->has('doctrine')) {
            return;
        }

        try {
            /** @var ManagerRegistry $doctrine */
            $doctrine = $this->container->get('doctrine');

            foreach ($doctrine->getManagers() as $entityManager) {
                if (!$entityManager instanceof EntityManagerInterface || !method_exists($entityManager, 'getProxyFactory')) {
                    continue;
                }

                $metadata = $entityManager->getMetadataFactory()->getAllMetadata();
                if (!$metadata) {
                    continue;
                }

                $entityManager->getProxyFactory()->generateProxyClasses($metadata, $entityManager->getConfiguration()->getProxyDir());
            }
        } catch (Throwable $exception) {
            if ($this->container->has('logger')) {
                $this->container->get('logger')->error('[Doctrine Proxy] Proxy generation error: ' . $exception->getMessage());
            }
        }
    }
}

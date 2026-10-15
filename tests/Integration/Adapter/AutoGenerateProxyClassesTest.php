<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Integration\Adapter;

use Doctrine\ORM\EntityManager;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\ContainerBuilder;
use PrestaShopBundle\Entity\Lang;

class AutoGenerateProxyClassesTest extends TestCase
{
    public function testDoctrineGeneratesProxyForLazyAssociation(): void
    {
        $container = ContainerBuilder::getContainer('front', false);

        /** @var EntityManager|null $entityManager */
        $entityManager = $container->get('doctrine.orm.entity_manager');
        $proxyDir = $container->getParameter('doctrine.orm.proxy_dir');
        $entityProxyFile = '__CG__PrestaShopBundleEntityLang.php';

        $this->assertNotNull(
            $proxyDir,
            'The "doctrine.orm.proxy_dir" parameter is not defined in the container. Check your test environment configuration.'
        );

        // Normalize path to prevent duplicate separators. File and directory may not exists.
        $proxyClassFile = realpath($proxyDir . DIRECTORY_SEPARATOR . $entityProxyFile);

        if ($proxyClassFile !== false && is_file($proxyClassFile)) {
            unlink($proxyClassFile);
        }

        $this->assertFileDoesNotExist($proxyClassFile);

        $entity = $entityManager->getReference(
            Lang::class,
            1
        );

        // Normalize path to prevent duplicate separators. File and directory may not exists.
        $proxyClassFile = realpath($proxyDir . DIRECTORY_SEPARATOR . $entityProxyFile);

        $this->assertNotFalse($proxyClassFile);

        $this->assertFileExists(
            (string) $proxyClassFile,
            'Doctrine should generate the proxy class for the Lang entity.'
        );

        $this->assertInstanceOf(\Doctrine\Persistence\Proxy::class, $entity);
        $this->assertNotEmpty($entity->getName());
    }
}

<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Integration\Adapter;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use PHPUnit\Framework\TestCase;
use PrestaShop\Module\Banner\Repository\FrontRepository;
use PrestaShop\PrestaShop\Adapter\ContainerBuilder;
use PrestaShopBundle\Exception\ServiceContainerException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

class ContainerBuilderTest extends TestCase
{
    public function testGetFrontContainer()
    {
        $container = ContainerBuilder::getContainer('front', true);
        $this->assertNotNull($container);
        $this->assertInstanceOf(ContainerInterface::class, $container);
    }

    public function testFrontContainerContainsAnEntityManager()
    {
        $container = ContainerBuilder::getContainer('front', true);
        $entityManager = $container->get('doctrine.orm.entity_manager');
        $this->assertNotNull($entityManager);
        $this->assertInstanceOf(EntityManagerInterface::class, $entityManager);
    }

    public function testContainerLoadsModuleAutoload()
    {
        ContainerBuilder::getContainer('front', true);
        $this->assertTrue(class_exists('\PrestaShop\Module\Banner\Entity\Banner'));
    }

    public function testDoctrineModuleMapping()
    {
        $container = ContainerBuilder::getContainer('front', true);
        /** @var EntityManagerInterface $entityManager */
        $entityManager = $container->get('doctrine.orm.entity_manager');
        /** @var ClassMetadata $classMetadata */
        $classMetadata = $entityManager->getClassMetadata('\PrestaShop\Module\Banner\Entity\Banner');
        $this->assertNotNull($classMetadata);
    }

    public function testDoctrineCoreMapping()
    {
        $container = ContainerBuilder::getContainer('front', true);
        /** @var EntityManagerInterface $entityManager */
        $entityManager = $container->get('doctrine.orm.entity_manager');
        /** @var ClassMetadata $classMetadata */
        $classMetadata = $entityManager->getClassMetadata('\PrestaShopBundle\Entity\Lang');
        $this->assertNotNull($classMetadata);
    }

    public function testFrontModuleServices()
    {
        $container = ContainerBuilder::getContainer('front', true);
        $frontRepository = $container->get('ps_banner.front_repository');
        $this->assertNotNull($frontRepository);
        /* @phpstan-ignore-next-line */
        $this->assertInstanceOf(FrontRepository::class, $frontRepository);
    }

    public function testNoAdminServicesInFront()
    {
        $this->expectException(ServiceNotFoundException::class);

        $container = ContainerBuilder::getContainer('front', true);
        $container->get('ps_banner.admin_repository');
    }

    public function testBuildContainerAdminThrowException()
    {
        $this->expectException(ServiceContainerException::class);
        ContainerBuilder::getContainer('admin', false);
    }
}

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
use PrestaShop\PrestaShop\Adapter\Csp\CspFeatureChecker;
use PrestaShop\PrestaShop\Adapter\Csp\CspHeaderBuilder;
use PrestaShop\PrestaShop\Adapter\Csp\CspViolationRecorder;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyDefinitionRepositoryInterface;
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

    /**
     * The extra property repository has mandatory collaborators (a logger and the constraint
     * encoder). The hand-built front-office container imports the extra_property services but not
     * adapter/services.yml, so it is the one that would break first if either were unresolvable.
     */
    public function testFrontContainerBuildsTheExtraPropertyRepository(): void
    {
        $container = ContainerBuilder::getContainer('front', true);

        $repository = $container->get(ExtraPropertyDefinitionRepositoryInterface::class);

        $this->assertInstanceOf(ExtraPropertyDefinitionRepositoryInterface::class, $repository);
    }

    /**
     * The legacy webservice (webservice/dispatcher.php) runs on its own hand-built container, built from
     * config/services/webservice/*.yml: everything the front container needs from config/services/common.yml
     * (the logger the extra property repository depends on, for instance) must resolve there too.
     */
    public function testWebserviceContainerBuildsTheExtraPropertyRepository(): void
    {
        $container = ContainerBuilder::getContainer('webservice', true);

        $repository = $container->get(ExtraPropertyDefinitionRepositoryInterface::class);

        $this->assertInstanceOf(ExtraPropertyDefinitionRepositoryInterface::class, $repository);
    }

    /**
     * The CSP collector and the storefront header both run on the front-office dispatch, so the
     * recorder, the feature checker and the header builder (with its policy provider, rule repository
     * and hook dispatcher) must all resolve in the hand-built front container, which imports
     * config/services/common.yml but not adapter/services.yml.
     */
    public function testFrontContainerBuildsTheCspCollectorServices(): void
    {
        $container = ContainerBuilder::getContainer('front', true);

        $this->assertInstanceOf(CspViolationRecorder::class, $container->get(CspViolationRecorder::class));
        $this->assertInstanceOf(CspFeatureChecker::class, $container->get(CspFeatureChecker::class));
        $this->assertInstanceOf(CspHeaderBuilder::class, $container->get(CspHeaderBuilder::class));
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

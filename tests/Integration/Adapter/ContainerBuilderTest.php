<?php
/**
 * 2007-2019 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace Tests\Integration\Adapter;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use PHPUnit\Framework\TestCase;
use PrestaShop\Module\Banner\Repository\AdminRepository;
use PrestaShop\Module\Banner\Repository\FrontRepository;
use PrestaShop\PrestaShop\Adapter\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;

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

    public function testGetAdminContainer()
    {
        $container = ContainerBuilder::getContainer('admin', true);
        $this->assertNotNull($container);
        $this->assertInstanceOf(ContainerInterface::class, $container);
    }

    public function testGetAdminContainerContainsAnEntityManager()
    {
        $container = ContainerBuilder::getContainer('admin', true);
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

    public function testFrontModuleServices()
    {
        $container = ContainerBuilder::getContainer('front', true);
        $frontRepository = $container->get('ps_banner.front_repository');
        $this->assertNotNull($frontRepository);
        $this->assertInstanceOf(FrontRepository::class, $frontRepository);
    }

    public function testAdminModuleServices()
    {
        $container = ContainerBuilder::getContainer('admin', true);
        $adminRepository = $container->get('ps_banner.admin_repository');
        $this->assertNotNull($adminRepository);
        $this->assertInstanceOf(AdminRepository::class, $adminRepository);
    }

    /**
     * @expectedException \Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException
     */
    public function testNoAdminServicesInFront()
    {
        $container = ContainerBuilder::getContainer('front', true);
        $container->get('ps_banner.admin_repository');
    }

    /**
     * @expectedException \Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException
     */
    public function testNoFrontServicesInAdmin()
    {
        $container = ContainerBuilder::getContainer('admin', true);
        $container->get('ps_banner.front_repository');
    }
}

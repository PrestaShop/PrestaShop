<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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

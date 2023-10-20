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

declare(strict_types=1);

namespace Tests\Integration\Core\Foundation\Entity;

use CMSRole;
use Db;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\CMS\CMSRoleRepository;
use PrestaShop\PrestaShop\Core\ContainerBuilder;
use PrestaShop\PrestaShop\Core\Foundation\Database\EntityManager;
use PrestaShop\PrestaShop\Core\Foundation\IoC\Container;
use Product;

class EntityManagerTest extends TestCase
{
    /**
     * @var Container
     */
    private $container;
    private $entityManager;

    protected function setUp(): void
    {
        parent::setUp();

        $containerBuilder = new ContainerBuilder();
        $this->container = $containerBuilder->build();
        $this->entityManager = $this->container->make(EntityManager::class);
    }

    public function testExplicitlyDefinedRepositoryIsFoundByEntitymanager(): void
    {
        $this->assertInstanceOf(
            CMSRoleRepository::class,
            $this->entityManager->getRepository(CMSRole::class)
        );
    }

    public function testFindImplicitlyDefinedRepository(): void
    {
        $repository = $this->entityManager->getRepository(Product::class);
        $product = $repository->findOne(1);
        $this->assertInstanceOf(Product::class, $product);
        $this->assertEquals(1, $product->id);
    }

    public function testSaveDataMapperStyle(): void
    {
        $repository = $this->entityManager->getRepository(CMSRole::class);

        $entity = new CMSRole();

        $name = 'Yo CMS Role ' . mt_rand(0, mt_getrandmax());

        $entity->name = $name;
        $entity->id_cms = 6666;

        $this->entityManager->save($entity);

        $this->assertGreaterThan(0, $repository->findOneByName($name)->id);

        // Clean DB !
        $this->entityManager->delete($entity);
    }
}

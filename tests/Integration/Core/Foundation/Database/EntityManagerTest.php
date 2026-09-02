<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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

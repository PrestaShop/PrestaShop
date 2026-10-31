<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\PrestaShopBundle\Utils\Database;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception as DBALException;
use Doctrine\ORM\EntityManagerInterface;
use PrestaShop\PrestaShop\Core\Util\Database\EntitySchemaManagerInterface;
use PrestaShopBundle\Entity\Lang;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Resources\Entity\TestEntityOne;
use Tests\Resources\Entity\TestEntityTwo;

final class EntitySchemaManagerTest extends KernelTestCase
{
    private const SERVICE_NAME = 'prestashop.util.database.entity_schema_manager';
    private ?Connection $connection;

    public function setUp(): void
    {
        parent::setUp();
        self::bootKernel();
        $this->connection = self::$kernel->getContainer()->get('doctrine.dbal.default_connection');
    }

    public function testService(): void
    {
        $entitySchemaManager = $this->getEntitySchemaManager();
        $this->assertNotNull($entitySchemaManager);
        $this->assertInstanceOf(EntitySchemaManagerInterface::class, $entitySchemaManager);
    }

    /**
     * @depends testService
     */
    public function testTableCreate(): void
    {
        $this->getEntitySchemaManager()->create(TestEntityOne::class);
        $this->assertTrue($this->checkIfTableExists('my_table_test_entity_one_for_pr_35527'));
    }

    /**
     * @depends testTableCreate
     */
    public function testTableUpdate(): void
    {
        $this->assertTrue($this->getEntitySchemaManager()->update(TestEntityOne::class));
        $this->assertTrue($this->checkIfTableExists('my_table_test_entity_one_for_pr_35527'));
    }

    /**
     * Registering an extra entity path must not override the core metadata drivers.
     *
     * @depends testService
     */
    public function testCoreEntityMappingIsPreservedAfterAddEntityPath(): void
    {
        $this->getEntitySchemaManager();

        /** @var EntityManagerInterface $entityManager */
        $entityManager = self::$kernel->getContainer()->get('doctrine.orm.default_entity_manager');
        $classMetadata = $entityManager->getClassMetadata(Lang::class);

        $this->assertSame(Lang::class, $classMetadata->getName());
    }

    /**
     * @depends testTableUpdate
     */
    public function testTableDrop(): void
    {
        $this->getEntitySchemaManager()->drop(TestEntityOne::class);
        $this->assertFalse($this->checkIfTableExists('my_table_test_entity_one_for_pr_35527'));
    }

    /**
     * @depends testTableDrop
     */
    public function testTableCreateMultiple(): void
    {
        $this->getEntitySchemaManager()->createMultiple([TestEntityOne::class, TestEntityTwo::class]);

        $this->assertTrue($this->checkIfTableExists('my_table_test_entity_one_for_pr_35527'));
        $this->assertTrue($this->checkIfTableExists('my_table_test_entity_two_for_pr_35527'));
    }

    /**
     * @depends testTableCreateMultiple
     */
    public function testTableDropMultiple(): void
    {
        $this->getEntitySchemaManager()->dropMultiple([TestEntityOne::class, TestEntityTwo::class]);

        $this->assertFalse($this->checkIfTableExists('my_table_test_entity_one_for_pr_35527'));
        $this->assertFalse($this->checkIfTableExists('my_table_test_entity_two_for_pr_35527'));
    }

    private function getEntitySchemaManager(): ?EntitySchemaManagerInterface
    {
        $service = self::$kernel->getContainer()->get(self::SERVICE_NAME);

        if (!$service instanceof EntitySchemaManagerInterface) {
            return null;
        }

        $service->addEntityPath(
            self::$kernel->getContainer()->getParameter('kernel.project_dir') . '/tests/Resources/Entity'
        );

        return $service;
    }

    private function checkIfTableExists(string $tableName): bool
    {
        try {
            $result = $this->connection->executeQuery('SHOW TABLES LIKE ?', [$tableName]);

            return $result->rowCount() > 0;
        } catch (DBALException $e) {
            return false;
        }
    }
}

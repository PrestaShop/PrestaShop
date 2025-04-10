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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace Tests\Integration\PrestaShopBundle\Utils\Database;

use Doctrine\DBAL\Connection;
use Exception;
use PrestaShop\PrestaShop\Core\Util\Database\EntitySchemaManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Resources\Entity\TestEntityOne;
use Tests\Resources\Entity\TestEntityTwo;

class EntitySchemaManagerTest extends KernelTestCase
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
        $service->addEntityPath('%kernel.project_dir%/tests/Resources/Entity');

        return $service;
    }

    private function checkIfTableExists(string $tableName): bool
    {
        try {
            $result = $this->connection->executeQuery('SHOW TABLES LIKE ?', [$tableName]);

            return $result->rowCount() > 0;
        } catch (Exception $e) {
            return false;
        }
    }
}

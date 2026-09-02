<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Foundation\Database;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Foundation\Database\DatabaseInterface;
use PrestaShop\PrestaShop\Core\Foundation\Database\EntityManager;
use PrestaShop\PrestaShop\Core\Foundation\Database\EntityMetaData;
use PrestaShop\PrestaShop\Core\Foundation\Database\EntityRepository;
use PrestaShop\PrestaShop\Core\Foundation\Database\Exception as DatabaseException;

class EntityRepositoryTest extends TestCase
{
    /**
     * @var EntityRepository
     */
    private $repository;

    protected function setUp(): void
    {
        $mockDb = $this->createMock(DatabaseInterface::class);
        $mockDb->method('select')->withAnyParameters()->willReturn([]);

        $mockEntityManager = $this->createMock(EntityManager::class);
        $mockEntityManager->method('getDatabase')->willReturn($mockDb);

        $this->repository = new EntityRepository(
            $mockEntityManager,
            'ps_',
            new EntityMetaData()
        );
    }

    public function testCallToInvalidMethodThrowsException(): void
    {
        $this->expectException(DatabaseException::class);

        /* @phpstan-ignore-next-line */
        $this->repository->thisDoesNotExist();
    }

    public function testCallToFindByDoesNotThrow(): void
    {
        /* @phpstan-ignore-next-line */
        $this->assertIsArray($this->repository->findByStuff('hey'));
    }

    public function testCallToFindOneByDoesNotThrow(): void
    {
        /* @phpstan-ignore-next-line */
        $this->assertNull($this->repository->findOneByStuff('hey'));
    }
}

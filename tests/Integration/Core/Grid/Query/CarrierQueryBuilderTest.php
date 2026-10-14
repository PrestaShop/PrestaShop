<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Core\Grid\Query;

use Doctrine\DBAL\Connection;
use PrestaShop\PrestaShop\Core\Grid\Query\CarrierQueryBuilder;
use PrestaShop\PrestaShop\Core\Grid\Query\DoctrineSearchCriteriaApplicator;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteria;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CarrierQueryBuilderTest extends KernelTestCase
{
    private const CONTEXT_LANG_ID = 1;
    private const CONTEXT_SHOP_ID = 1;

    private Connection $connection;
    private string $dbPrefix;
    private CarrierQueryBuilder $queryBuilder;

    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();

        $this->connection = self::getContainer()->get('doctrine.dbal.default_connection');
        $this->dbPrefix = self::getContainer()->getParameter('database_prefix');
        $this->queryBuilder = new CarrierQueryBuilder(
            $this->connection,
            $this->dbPrefix,
            new DoctrineSearchCriteriaApplicator(),
            (string) self::CONTEXT_LANG_ID,
            [self::CONTEXT_SHOP_ID]
        );
    }

    /**
     * id_carrier changes whenever an edited carrier already has orders, because the repository
     * keeps the old row and versions the carrier. id_reference survives that, which makes it the
     * identifier an ERP or a webservice client has to store, so the grid has to show it.
     */
    public function testTheGridExposesTheStableCarrierReference(): void
    {
        $rows = $this->fetchRows($this->createSearchCriteria([], 'id_carrier', 'ASC', 50, 0));

        self::assertNotEmpty($rows, 'No carrier came back, so this test could not discriminate.');

        $expected = $this->referencesByCarrierId();
        foreach ($rows as $row) {
            self::assertArrayHasKey(
                'id_reference',
                $row,
                'The carriers grid does not expose the carrier reference.'
            );
            self::assertSame(
                $expected[(int) $row['id_carrier']],
                (int) $row['id_reference'],
                sprintf('Wrong reference reported for carrier %s.', $row['id_carrier'])
            );
        }
    }

    /**
     * A column the merchant can see but not search is only half the answer, and the query builder
     * ignores any filter that is not declared allowed, silently returning every row.
     *
     * One row is the right expectation even on a shop with versioned carriers: each new version
     * marks its predecessor deleted, and the grid only selects `deleted = 0`, so a reference is
     * unique among the carriers this query returns.
     */
    public function testTheGridCanBeFilteredByTheStableCarrierReference(): void
    {
        $references = $this->referencesByCarrierId();
        self::assertGreaterThan(
            1,
            count($references),
            'Fewer than two carriers exist, so filtering could not be told apart from not filtering.'
        );

        $carrierId = array_key_first($references);
        $reference = $references[$carrierId];

        $rows = $this->fetchRows(
            $this->createSearchCriteria(['id_reference' => $reference], 'id_carrier', 'ASC', 50, 0)
        );

        self::assertCount(
            1,
            $rows,
            sprintf('Filtering on reference %d returned %d carriers.', $reference, count($rows))
        );
        self::assertSame($carrierId, (int) $rows[0]['id_carrier']);
        self::assertSame(
            $reference,
            (int) $rows[0]['id_reference'],
            'The filtered row does not carry the reference it was selected by.'
        );
    }

    /**
     * @return array<int, int> id_carrier => id_reference, for the carriers the grid shows
     */
    private function referencesByCarrierId(): array
    {
        $rows = $this->connection->createQueryBuilder()
            ->select('c.id_carrier, c.id_reference')
            ->from($this->dbPrefix . 'carrier', 'c')
            ->innerJoin('c', $this->dbPrefix . 'carrier_shop', 'cs', 'c.id_carrier = cs.id_carrier')
            ->where('c.deleted = 0')
            ->andWhere('cs.id_shop = :shopId')
            ->setParameter('shopId', self::CONTEXT_SHOP_ID)
            ->executeQuery()
            ->fetchAllAssociative();

        $references = [];
        foreach ($rows as $row) {
            $references[(int) $row['id_carrier']] = (int) $row['id_reference'];
        }

        return $references;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function fetchRows(SearchCriteriaInterface $criteria): array
    {
        return $this->queryBuilder->getSearchQueryBuilder($criteria)->executeQuery()->fetchAllAssociative();
    }

    /**
     * @param array<string, mixed> $filters
     */
    private function createSearchCriteria(array $filters, string $orderBy, string $orderWay, int $limit, int $offset): SearchCriteriaInterface
    {
        return new SearchCriteria($filters, $orderBy, $orderWay, $offset, $limit);
    }
}

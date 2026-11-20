<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Core\Grid\Query;

use Doctrine\DBAL\Connection;
use PrestaShop\PrestaShop\Core\Context\LanguageContext;
use PrestaShop\PrestaShop\Core\Grid\Query\AttachmentQueryBuilder;
use PrestaShop\PrestaShop\Core\Grid\Query\DoctrineSearchCriteriaApplicator;
use PrestaShop\PrestaShop\Core\Search\Filters;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class AttachmentQueryBuilderTest extends KernelTestCase
{
    private const LANG_ID = 1;

    private Connection $connection;
    private string $dbPrefix;
    private AttachmentQueryBuilder $queryBuilder;
    /** @var int[] */
    private array $attachmentIds = [];

    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();

        $this->connection = self::getContainer()->get('doctrine.dbal.default_connection');
        $this->dbPrefix = self::getContainer()->getParameter('database_prefix');

        $languageContext = $this->createMock(LanguageContext::class);
        $languageContext->method('getId')->willReturn(self::LANG_ID);
        $this->queryBuilder = new AttachmentQueryBuilder(
            $this->connection,
            $this->dbPrefix,
            new DoctrineSearchCriteriaApplicator(),
            $languageContext
        );
    }

    protected function tearDown(): void
    {
        foreach (['product_attachment', 'attachment_lang', 'attachment'] as $table) {
            $this->connection->executeStatement(
                'DELETE FROM ' . $this->dbPrefix . $table . ' WHERE id_attachment IN (:ids)',
                ['ids' => $this->attachmentIds],
                ['ids' => Connection::PARAM_INT_ARRAY]
            );
        }

        parent::tearDown();
    }

    public function testProductsFilterFindsTheFilesOfAProductByIdReferenceOrName(): void
    {
        $product = $this->connection->fetchAssociative(
            'SELECT p.id_product, p.reference, pl.name FROM ' . $this->dbPrefix . 'product p
             INNER JOIN ' . $this->dbPrefix . 'product_lang pl ON pl.id_product = p.id_product AND pl.id_lang = :lang
             WHERE p.id_product = 2',
            ['lang' => self::LANG_ID]
        );
        $fileOfProduct = $this->insertAttachment('Zzfile of the product', [2, 3]);
        $otherFile = $this->insertAttachment('Zzfile of other products', [1]);
        $fileWithoutProducts = $this->insertAttachment('Zzfile without products', []);

        $this->assertSame([$fileOfProduct], $this->fetchAttachmentIds('2'));
        $this->assertSame([$fileOfProduct], $this->fetchAttachmentIds($product['reference']));
        $this->assertSame([$fileOfProduct], $this->fetchAttachmentIds($product['name']));
        $this->assertSame([$otherFile], $this->fetchAttachmentIds('1'));
        // A file attached to several matching products is listed once
        $this->assertSame(1, $this->countAttachments('2'));
        // "0" keeps listing the files attached to no product
        $this->assertSame([$fileWithoutProducts], $this->fetchAttachmentIds('0'));
        // LIKE wildcards typed in the filter are searched literally
        $this->assertSame([], $this->fetchAttachmentIds('%'));
    }

    /**
     * @param int[] $productIds
     */
    private function insertAttachment(string $name, array $productIds): int
    {
        $this->connection->insert($this->dbPrefix . 'attachment', [
            'file' => sha1($name),
            'file_name' => $name . '.pdf',
            'file_size' => 1,
            'mime' => 'application/pdf',
        ]);
        $attachmentId = (int) $this->connection->lastInsertId();
        $this->attachmentIds[] = $attachmentId;

        $this->connection->insert($this->dbPrefix . 'attachment_lang', [
            'id_attachment' => $attachmentId,
            'id_lang' => self::LANG_ID,
            'name' => $name,
            'description' => '',
        ]);
        foreach ($productIds as $productId) {
            $this->connection->insert($this->dbPrefix . 'product_attachment', [
                'id_attachment' => $attachmentId,
                'id_product' => $productId,
            ]);
        }

        return $attachmentId;
    }

    /**
     * Only the files inserted by the test, the test data may hold others
     *
     * @return int[]
     */
    private function fetchAttachmentIds(string $productSearch): array
    {
        $rows = $this->queryBuilder
            ->getSearchQueryBuilder($this->createSearchCriteria($productSearch))
            ->executeQuery()
            ->fetchAllAssociative();

        return array_values(array_intersect(array_map('intval', array_column($rows, 'id_attachment')), $this->attachmentIds));
    }

    private function countAttachments(string $productSearch): int
    {
        $criteria = $this->createSearchCriteria($productSearch);
        $criteria->addFilter(['name' => 'Zzfile']);

        return (int) $this->queryBuilder
            ->getCountQueryBuilder($criteria)
            ->executeQuery()
            ->fetchOne();
    }

    private function createSearchCriteria(string $productSearch): Filters
    {
        return new Filters([
            'limit' => 100,
            'offset' => 0,
            'orderBy' => 'id_attachment',
            'sortOrder' => 'asc',
            'filters' => ['products' => $productSearch],
        ]);
    }
}

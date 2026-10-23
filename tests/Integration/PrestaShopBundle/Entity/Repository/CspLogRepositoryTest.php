<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\PrestaShopBundle\Entity\Repository;

use Doctrine\DBAL\Connection;
use PrestaShop\PrestaShop\Adapter\Csp\CspViolationRecorder;
use PrestaShop\PrestaShop\Core\Domain\Csp\ValueObject\CspContext;
use PrestaShopBundle\Entity\Repository\CspLogRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * The upsert is raw SQL (INSERT ... ON DUPLICATE KEY UPDATE), so its merge behaviour is exercised here
 * against a real database rather than mocked: a repeat report for a source already seen must keep the
 * example (sample / source file / line) captured earlier instead of nulling it, which only the database's
 * COALESCE can show.
 */
class CspLogRepositoryTest extends KernelTestCase
{
    private const SHOP_ID = 1;
    private const DIRECTIVE = 'script-src';
    private const SOURCE = 'https://cdn.example.com';
    private const PAGE = 'https://shop.test/some-page';

    private CspLogRepository $repository;

    private CspViolationRecorder $recorder;

    private Connection $connection;

    private string $prefix;

    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();

        $this->repository = self::getContainer()->get(CspLogRepository::class);
        $this->recorder = new CspViolationRecorder($this->repository, 1000);
        $this->connection = self::getContainer()->get('doctrine.dbal.default_connection');
        $this->prefix = (string) self::getContainer()->getParameter('database_prefix');

        $this->repository->deleteByShop(CspContext::FRONT, self::SHOP_ID);
    }

    protected function tearDown(): void
    {
        $this->repository->deleteByShop(CspContext::FRONT, self::SHOP_ID);
        parent::tearDown();
    }

    public function testARepeatReportWithoutASampleKeepsTheOneAlreadyStored(): void
    {
        // First report carries the offending code sample and where it lives.
        $this->recorder->record(CspContext::FRONT, self::SHOP_ID, self::DIRECTIVE, self::SOURCE, self::PAGE, 'alert(1)', 'https://shop.test/app.js', 42);
        // A later report for the same source on the same page carries none (a common case).
        $this->recorder->record(CspContext::FRONT, self::SHOP_ID, self::DIRECTIVE, self::SOURCE, self::PAGE, null, null, null);

        $row = $this->fetchRow();
        $this->assertSame('alert(1)', $row['sample'], 'A plain re-report must not wipe the captured sample');
        $this->assertSame('https://shop.test/app.js', $row['source_file']);
        $this->assertSame(42, (int) $row['line_number']);
        $this->assertSame(2, (int) $row['hits'], 'The repeat must still bump the hit counter');
    }

    public function testALaterReportWithItsOwnSampleReplacesTheStoredOne(): void
    {
        $this->recorder->record(CspContext::FRONT, self::SHOP_ID, self::DIRECTIVE, self::SOURCE, self::PAGE, 'old()', null, null);
        $this->recorder->record(CspContext::FRONT, self::SHOP_ID, self::DIRECTIVE, self::SOURCE, self::PAGE, 'new()', null, null);

        $this->assertSame('new()', $this->fetchRow()['sample']);
    }

    /**
     * @return array<string, mixed>
     */
    private function fetchRow(): array
    {
        $rows = $this->connection->fetchAllAssociative(
            'SELECT sample, source_file, line_number, hits FROM ' . $this->prefix . 'csp_log WHERE id_shop = :shop AND context = :context',
            ['shop' => self::SHOP_ID, 'context' => CspContext::FRONT->value]
        );

        $this->assertCount(1, $rows, 'The source should collapse to a single row per page');

        return $rows[0];
    }
}

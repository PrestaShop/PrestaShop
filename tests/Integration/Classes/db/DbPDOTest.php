<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes\Db;

use Db;
use DbPDO;
use PDO;
use PDOException;
use ReflectionProperty;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class DbPDOTest extends KernelTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        self::bootKernel();
    }

    public function testTheConnectionKeepsTheOptionsItWasBuiltWith(): void
    {
        $db = Db::getInstance();
        $this->assertInstanceOf(DbPDO::class, $db);

        $link = new ReflectionProperty(DbPDO::class, 'link');
        $pdo = $link->getValue($db);

        $this->assertSame(
            PDO::ERRMODE_EXCEPTION,
            $pdo->getAttribute(PDO::ATTR_ERRMODE),
            'The connection options were reindexed, so PDO::ATTR_ERRMODE never reached PDO.'
        );
    }

    /**
     * MYSQL_ATTR_MULTI_STATEMENTS is applied when the connection is opened and cannot be read back
     * with getAttribute(), so the only way to observe it is to send a chained statement and see
     * whether the server accepts it.
     */
    public function testTheConnectionRefusesChainedStatementsWhenTheyAreNotAllowed(): void
    {
        if (!defined('_PS_ALLOW_MULTI_STATEMENTS_QUERIES_') || _PS_ALLOW_MULTI_STATEMENTS_QUERIES_) {
            $this->markTestSkipped('This shop is configured to allow multi statement queries.');
        }

        $db = Db::getInstance();
        $this->assertInstanceOf(DbPDO::class, $db);

        $link = new ReflectionProperty(DbPDO::class, 'link');
        $pdo = $link->getValue($db);

        try {
            $pdo->query('SELECT 1; SELECT 2');
        } catch (PDOException $e) {
            $this->assertStringContainsString(
                'syntax',
                strtolower($e->getMessage()),
                'The chained statement was refused, but not by the SQL parser.'
            );

            return;
        }

        $this->fail(
            'The connection accepted "SELECT 1; SELECT 2" while _PS_ALLOW_MULTI_STATEMENTS_QUERIES_ is false, '
            . 'so MYSQL_ATTR_MULTI_STATEMENTS never reached PDO.'
        );
    }
}

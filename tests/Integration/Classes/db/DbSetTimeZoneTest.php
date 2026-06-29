<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes\db;

use DateTime;
use Db;
use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Ensures both database connection layers (legacy Db and Doctrine DBAL) align the
 * MySQL session time zone with PHP's current offset, so that NOW() / CURRENT_TIMESTAMP
 * match PHP date() regardless of the MySQL server time zone (see issue #30828).
 */
class DbSetTimeZoneTest extends KernelTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();
    }

    public function testLegacyConnectionUsesThePhpTimeZoneOffset(): void
    {
        $db = Db::getInstance();
        // Re-apply explicitly so the test does not depend on bootstrap ordering.
        $db->setTimeZone();

        $this->assertSame(
            (new DateTime())->format('P'),
            $db->getValue('SELECT @@session.time_zone')
        );
    }

    public function testLegacyNowMatchesPhpDate(): void
    {
        $db = Db::getInstance();
        $db->setTimeZone();

        $this->assertLessThanOrEqual(
            2,
            abs(strtotime((string) $db->getValue('SELECT NOW()')) - time()),
            'MySQL NOW() should match the PHP wall-clock time within 2 seconds'
        );
    }

    public function testDoctrineConnectionUsesThePhpTimeZoneOffset(): void
    {
        /** @var Connection $connection */
        $connection = self::getContainer()->get('doctrine.dbal.default_connection');

        $this->assertSame(
            (new DateTime())->format('P'),
            $connection->fetchOne('SELECT @@session.time_zone')
        );
    }
}

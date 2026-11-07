<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes\Order;

use Configuration;
use Db;
use Order;
use PDO;
use PDOException;
use PHPUnit\Framework\TestCase;

/**
 * Proves that the SELECT ... FOR UPDATE used by Order::setLastInvoiceNumber() actually
 * serializes concurrent readers, so two orders validated at the same time cannot read the
 * same MAX(number) and end up with the same invoice number.
 */
class SetLastInvoiceNumberTest extends TestCase
{
    private const READ_SQL = 'SELECT MAX(`number`) FROM `' . _DB_PREFIX_ . 'order_invoice` FOR UPDATE';

    /** @var PDO */
    private $connectionA;

    /** @var PDO */
    private $connectionB;

    /** @var int */
    private $seededInvoiceId;

    protected function setUp(): void
    {
        $this->connectionA = $this->newConnection();
        $this->connectionB = $this->newConnection();

        // FOR UPDATE only locks rows it scans, so a row must exist to be locked.
        $this->connectionA->exec(
            'INSERT INTO `' . _DB_PREFIX_ . 'order_invoice` (`id_order`, `number`, `delivery_number`, `shipping_tax_computation_method`, `date_add`)'
            . ' VALUES (0, 1, 0, 0, NOW())'
        );
        $this->seededInvoiceId = (int) $this->connectionA->lastInsertId();
    }

    protected function tearDown(): void
    {
        foreach ([$this->connectionA, $this->connectionB] as $connection) {
            try {
                $connection->exec('ROLLBACK');
            } catch (PDOException $e) {
            }
        }

        $this->newConnection()->exec(
            'DELETE FROM `' . _DB_PREFIX_ . 'order_invoice` WHERE `id_order_invoice` = ' . $this->seededInvoiceId
        );
    }

    public function testForUpdateBlocksConcurrentReader(): void
    {
        $this->connectionA->exec('START TRANSACTION');
        $this->connectionA->query(self::READ_SQL);

        $this->connectionB->exec('SET SESSION innodb_lock_wait_timeout = 1');
        $this->connectionB->exec('START TRANSACTION');

        try {
            $this->connectionB->query(self::READ_SQL);
            $this->fail('Second reader should have been blocked by the FOR UPDATE lock held by the first transaction.');
        } catch (PDOException $e) {
            // 1205 = lock wait timeout: the row range is held by connection A as expected.
            $this->assertSame('1205', (string) $e->errorInfo[1]);
        }

        $this->connectionA->exec('COMMIT');

        // Once A commits the lock is released and B can read again.
        $this->assertNotFalse($this->connectionB->query(self::READ_SQL));
    }

    /**
     * Runs the real method through the legacy Db layer to guard against the locked read being
     * built with getValue()/getRow() (which append "LIMIT 1" after FOR UPDATE -> SQL error).
     */
    public function testSetLastInvoiceNumberAssignsANumber(): void
    {
        Configuration::updateValue('PS_INVOICE_START_NUMBER', false, false, null, (int) Configuration::get('PS_SHOP_DEFAULT'));

        $assigned = Order::setLastInvoiceNumber($this->seededInvoiceId, (int) Configuration::get('PS_SHOP_DEFAULT'));

        $this->assertTrue($assigned);
        $this->assertGreaterThan(
            0,
            (int) Db::getInstance()->getValue(
                'SELECT `number` FROM `' . _DB_PREFIX_ . 'order_invoice` WHERE `id_order_invoice` = ' . $this->seededInvoiceId
            )
        );
    }

    private function newConnection(): PDO
    {
        [$host, $port] = array_pad(explode(':', _DB_SERVER_, 2), 2, null);

        $dsn = 'mysql:host=' . $host . ';dbname=' . _DB_NAME_;
        if ($port) {
            $dsn .= ';port=' . $port;
        }

        return new PDO($dsn, _DB_USER_, _DB_PASSWD_, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    }
}

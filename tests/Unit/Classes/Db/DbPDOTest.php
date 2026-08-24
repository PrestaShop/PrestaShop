<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Classes\Db;

use DbPDOCore;
use ErrorException;
use PDO;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class DbPDOTest extends TestCase
{
    /**
     * @return MockObject|PDO
     */
    private function getMockPDO()
    {
        return $this->getMockBuilder(PDO::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['exec', 'query', 'inTransaction'])
            ->getMock();
    }

    /**
     * Runs $callback with PHP warnings/notices promoted to ErrorException, so that reading a property
     * disconnect() removed would fail the test instead of only being reported as a PHP warning.
     */
    private function failOnPhpWarnings(callable $callback): void
    {
        set_error_handler(static function (int $severity, string $message, string $file, int $line): bool {
            throw new ErrorException($message, 0, $severity, $file, $line);
        }, E_WARNING | E_NOTICE);

        try {
            $callback();
        } finally {
            restore_error_handler();
        }
    }

    /**
     * disconnect() must leave $link readable (null), not remove the property: DbCore::query()'s
     * lost-connection recovery calls disconnect() then connect() on the same object, and setPDO() /
     * hasUncommittedTransaction() / connect() all read $link. An unset property makes every one of
     * those raise "Undefined property", which _PS_MODE_DEV_ promotes to a fatal ErrorException.
     */
    public function testTheConnectionLinkStaysReadableAfterDisconnect(): void
    {
        $db = new DbPDOCore('', '', '', '', false);
        $db->setPDO($this->getMockPDO());

        $db->disconnect();

        $this->failOnPhpWarnings(static function () use ($db) {
            self::assertFalse($db->hasUncommittedTransaction());
        });
    }

    public function testAConnectionCanBeSharedAgainAfterDisconnect(): void
    {
        $db = new DbPDOCore('', '', '', '', false);
        $db->setPDO($this->getMockPDO());
        $db->disconnect();

        $newLink = $this->getMockPDO();
        // Re-applied on the new link, since disconnect() dropped the previous one.
        $newLink->expects($this->once())->method('exec')->with('SET SESSION sql_mode = \'\'');

        $this->failOnPhpWarnings(static function () use ($db, $newLink) {
            $db->setPDO($newLink);
        });

        $this->assertFalse($db->hasUncommittedTransaction());
    }
}

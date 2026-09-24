<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Classes\Db;

use Db;
use PHPUnit\Framework\TestCase;
use PrestaShopException;

class DbLostConnectionTest extends TestCase
{
    public function testAConnectionClosedByTheServerIsRetriedOnceWhenTheDriverThrows(): void
    {
        // What PDO does since PHP 8: PDO::ERRMODE_EXCEPTION is its default, so a lost connection
        // arrives as an exception instead of a falsy result.
        $db = new ScriptedDb(['throw-gone-away', 'ok']);

        $this->assertTrue($db->query('SELECT 1'));
        $this->assertSame(1, $db->connectCalls);
        $this->assertSame(2, $db->queryCalls);
    }

    public function testAConnectionClosedByTheServerIsRetriedOnceWhenTheDriverReturnsFalse(): void
    {
        $db = new ScriptedDb(['false-gone-away', 'ok']);

        $this->assertTrue($db->query('SELECT 1'));
        $this->assertSame(1, $db->connectCalls);
        $this->assertSame(2, $db->queryCalls);
    }

    public function testAnyOtherErrorIsNotRetriedAndKeepsItsException(): void
    {
        $db = new ScriptedDb(['throw-syntax-error', 'ok']);

        try {
            $db->query('SELECT 1');
            $this->fail('the syntax error should have been rethrown');
        } catch (PrestaShopException $exception) {
            $this->assertStringContainsString('You have an error in your SQL syntax', $exception->getMessage());
        }

        $this->assertSame(0, $db->connectCalls);
        $this->assertSame(1, $db->queryCalls);
    }

    public function testASuccessfulQueryDoesNotReconnect(): void
    {
        $db = new ScriptedDb(['ok']);

        $this->assertTrue($db->query('SELECT 1'));
        $this->assertSame(0, $db->connectCalls);
        $this->assertSame(1, $db->queryCalls);
    }
}

/**
 * A driver whose answers are scripted, so the reconnection logic can be exercised without a server.
 */
class ScriptedDb extends Db
{
    /**
     * @var int
     */
    public $connectCalls = 0;

    /**
     * @var int
     */
    public $queryCalls = 0;

    /**
     * @var string[]
     */
    private $answers;

    /**
     * @var int
     */
    private $errorNumber = 0;

    /**
     * @var string
     */
    private $errorMessage = '';

    /**
     * @param string[] $answers
     */
    public function __construct(array $answers)
    {
        $this->answers = $answers;
    }

    public function connect()
    {
        $this->errorNumber = 0;
        $this->errorMessage = '';
        // A scripted driver has no link to hand back, and Db::query() does not read one.
        /* @phpstan-ignore return.missing */
        ++$this->connectCalls;
    }

    public function disconnect()
    {
    }

    protected function _query($sql)
    {
        ++$this->queryCalls;
        $answer = array_shift($this->answers);

        switch ($answer) {
            case 'throw-gone-away':
                $this->fail(2006, 'MySQL server has gone away');
                throw new PrestaShopException('SQLSTATE[HY000]: General error: 2006 MySQL server has gone away');
            case 'false-gone-away':
                $this->fail(2006, 'MySQL server has gone away');

                return false;
            case 'throw-syntax-error':
                $this->fail(1064, 'You have an error in your SQL syntax');
                throw new PrestaShopException('You have an error in your SQL syntax');
            default:
                $this->errorNumber = 0;
                $this->errorMessage = '';

                return true;
        }
    }

    public function getNumberError()
    {
        return $this->errorNumber;
    }

    public function getMsgError()
    {
        return $this->errorMessage;
    }

    private function fail(int $number, string $message): void
    {
        $this->errorNumber = $number;
        $this->errorMessage = $message;
    }

    protected function _numRows($result)
    {
        return 0;
    }

    public function Insert_ID()
    {
        return 0;
    }

    public function Affected_Rows()
    {
        return 0;
    }

    public function nextRow($result = false)
    {
        return false;
    }

    protected function getAll($result = false)
    {
        return [];
    }

    public function getVersion()
    {
        return '';
    }

    public function _escape($str)
    {
        return $str;
    }

    public function set_db($db_name)
    {
        return true;
    }

    public function getBestEngine()
    {
        return 'InnoDB';
    }
}

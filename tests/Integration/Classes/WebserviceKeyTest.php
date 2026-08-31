<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes;

use Db;
use ErrorException;
use PHPUnit\Framework\TestCase;
use Tests\Integration\Utility\ContextMockerTrait;
use WebserviceKey;

class WebserviceKeyTest extends TestCase
{
    use ContextMockerTrait;

    /**
     * @var WebserviceKey|null
     */
    private $webserviceKey;

    protected function setUp(): void
    {
        parent::setUp();
        self::mockContext();
        // The bug only reproduces when no employee is attached to the context,
        // e.g. a CLI/cron execution or an install script.
        static::getContext()->employee = null;
    }

    protected function tearDown(): void
    {
        if ($this->webserviceKey && $this->webserviceKey->id) {
            $this->webserviceKey->delete();
        }
        parent::tearDown();
    }

    /**
     * WebserviceKey::add() reads Context::getContext()->employee->id to log the creation.
     * When no employee is attached to the context this must not emit a PHP warning
     * ("Attempt to read property 'id' on null") and the log must be written with
     * id_employee = 0 instead.
     */
    public function testAddWithoutEmployeeInContextDoesNotTriggerWarning(): void
    {
        $this->webserviceKey = $this->buildWebserviceKey();

        $result = $this->withWarningsAsExceptions(function () {
            return $this->webserviceKey->add();
        });

        $this->assertTrue($result);
        $this->assertNotFalse($this->webserviceKey->id);
        $this->assertSame(0, $this->getLastLoggedEmployeeId());
    }

    /**
     * Same regression as above, but for WebserviceKey::delete().
     */
    public function testDeleteWithoutEmployeeInContextDoesNotTriggerWarning(): void
    {
        $this->webserviceKey = $this->buildWebserviceKey();
        $this->webserviceKey->add();

        $result = $this->withWarningsAsExceptions(function () {
            return $this->webserviceKey->delete();
        });

        $this->assertTrue($result);
        $this->assertSame(0, $this->getLastLoggedEmployeeId());
    }

    private function buildWebserviceKey(): WebserviceKey
    {
        $webserviceKey = new WebserviceKey();
        $webserviceKey->key = substr(sha1(uniqid('webservice_key_test', true)), 0, 32);
        $webserviceKey->description = 'Webservice key created without employee in context';
        $webserviceKey->active = true;

        return $webserviceKey;
    }

    private function withWarningsAsExceptions(callable $callback)
    {
        set_error_handler(static function (int $severity, string $message, string $file, int $line): bool {
            if (E_WARNING === $severity) {
                throw new ErrorException($message, 0, $severity, $file, $line);
            }

            return false;
        });

        try {
            return $callback();
        } finally {
            restore_error_handler();
        }
    }

    private function getLastLoggedEmployeeId(): int
    {
        return (int) Db::getInstance()->getValue('
            SELECT `id_employee`
            FROM `' . _DB_PREFIX_ . 'log`
            WHERE `object_type` = "WebserviceKey" AND `object_id` = ' . (int) $this->webserviceKey->id . '
            ORDER BY `id_log` DESC
        ');
    }
}

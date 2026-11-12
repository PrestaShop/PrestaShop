<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Tests\Integration\Adapter\SqlManager\QueryHandler;

use Db;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Command\AddSqlRequestCommand;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Exception\SqlRequestConstraintException;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Query\GetSqlRequestExecutionResult;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\SqlRequestExecutionResult;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\ValueObject\SqlRequestId;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Resources\DatabaseDump;

class GetSqlRequestExecutionResultHandlerTest extends KernelTestCase
{
    /**
     * @var CommandBusInterface
     */
    private $queryBus;

    /**
     * @var CommandBusInterface
     */
    private $commandBus;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        static::resetDatabase();
    }

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
        static::resetDatabase();
    }

    protected static function resetDatabase(): void
    {
        DatabaseDump::restoreTables([
            'request_sql',
            'employee',
            'employee_session',
            'api_client',
        ]);
    }

    protected function setUp(): void
    {
        self::bootKernel();

        $this->queryBus = self::getContainer()->get('prestashop.core.query_bus');
        $this->commandBus = self::getContainer()->get('prestashop.core.command_bus');
    }

    public function testSensitiveDataAreHidden(): void
    {
        /** @var SqlRequestId $sqlRequestId */
        $sqlRequestId = $this->commandBus->handle(new AddSqlRequestCommand('request1', 'SELECT e.email, e.lastname, e.firstname, e.passwd FROM ps_employee e;'));
        $query = new GetSqlRequestExecutionResult($sqlRequestId->getValue());
        /** @var SqlRequestExecutionResult $sqlRequestExecutionResult */
        $sqlRequestExecutionResult = $this->queryBus->handle($query);
        self::assertEquals('*******************', $sqlRequestExecutionResult->getRows()[0]['passwd']);

        /** @var SqlRequestId $sqlRequestId */
        $sqlRequestId = $this->commandBus->handle(new AddSqlRequestCommand('request1', 'SELECT e.email, e.lastname, e.firstname, e.passwd as "MyStrongPassword" FROM ps_employee e;'));
        $query = new GetSqlRequestExecutionResult($sqlRequestId->getValue());
        /** @var SqlRequestExecutionResult $sqlRequestExecutionResult */
        $sqlRequestExecutionResult = $this->queryBus->handle($query);
        self::assertEquals('*******************', $sqlRequestExecutionResult->getRows()[0]['MyStrongPassword']);

        /** @var SqlRequestId $sqlRequestId */
        $sqlRequestId = $this->commandBus->handle(new AddSqlRequestCommand('request1', 'SELECT e.email, e.lastname, e.firstname, e.passwd as  `MyStrongPassword` FROM ps_employee e;'));
        $query = new GetSqlRequestExecutionResult($sqlRequestId->getValue());
        /** @var SqlRequestExecutionResult $sqlRequestExecutionResult */
        $sqlRequestExecutionResult = $this->queryBus->handle($query);
        self::assertEquals('*******************', $sqlRequestExecutionResult->getRows()[0]['MyStrongPassword']);

        /** @var SqlRequestId $sqlRequestId */
        $sqlRequestId = $this->commandBus->handle(new AddSqlRequestCommand('request1', 'SELECT e.email, e.lastname, e.firstname, LOWER(LOWER(e.passwd)) as MyStrongPassword FROM ps_employee e;'));
        $query = new GetSqlRequestExecutionResult($sqlRequestId->getValue());
        /** @var SqlRequestExecutionResult $sqlRequestExecutionResult */
        $sqlRequestExecutionResult = $this->queryBus->handle($query);
        self::assertEquals('*******************', $sqlRequestExecutionResult->getRows()[0]['MyStrongPassword']);
    }

    /**
     * @dataProvider getSecretColumns
     */
    public function testEveryKnownSecretColumnIsHidden(string $column, string $sql, string $selected): void
    {
        // hideSensitiveData() skips a null value, so the column has to hold something to be a test
        Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . 'employee` SET `reset_password_token` = \'a-real-secret\' WHERE `id_employee` = 1');
        Db::getInstance()->insert('employee_session', ['id_employee' => 1, 'token' => 'a-real-secret', 'date_add' => date('Y-m-d H:i:s'), 'date_upd' => date('Y-m-d H:i:s')]);
        Db::getInstance()->insert('api_client', ['client_id' => 'secret-test', 'client_name' => 'secret-test', 'client_secret' => 'a-real-secret', 'enabled' => 1, 'scopes' => '[]']);

        /** @var SqlRequestId $sqlRequestId */
        $sqlRequestId = $this->commandBus->handle(new AddSqlRequestCommand('secret_request', $sql));
        /** @var SqlRequestExecutionResult $result */
        $result = $this->queryBus->handle(new GetSqlRequestExecutionResult($sqlRequestId->getValue()));

        self::assertEquals(
            '*******************',
            $result->getRows()[0][$selected],
            sprintf('%s (column %s) was returned in clear text', $selected, $column)
        );
    }

    public function getSecretColumns(): iterable
    {
        yield 'reset token' => [
            'reset_password_token',
            'SELECT e.id_employee, e.reset_password_token FROM ps_employee e WHERE e.id_employee = 1;',
            'reset_password_token',
        ];
        yield 'reset token behind an alias' => [
            'reset_password_token',
            'SELECT e.id_employee, e.reset_password_token AS session_token FROM ps_employee e WHERE e.id_employee = 1;',
            'session_token',
        ];
        yield 'employee session token' => [
            'token',
            'SELECT es.id_employee, es.token FROM ps_employee_session es;',
            'token',
        ];
        yield 'api client secret' => [
            'client_secret',
            'SELECT ac.client_id, ac.client_secret FROM ps_api_client ac;',
            'client_secret',
        ];
    }

    public function testUnauthorizedFunctionInSelect(): void
    {
        $this->expectException(SqlRequestConstraintException::class);
        $this->commandBus->handle(new AddSqlRequestCommand('request1', 'SELECT load_file(\'/etc/passwd\') FROM ps_zone;'));
    }
}

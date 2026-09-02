<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Tests\Integration\Adapter\SqlManager\QueryHandler;

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

    public function testUnauthorizedFunctionInSelect(): void
    {
        $this->expectException(SqlRequestConstraintException::class);
        $this->commandBus->handle(new AddSqlRequestCommand('request1', 'SELECT load_file(\'/etc/passwd\') FROM ps_zone;'));
    }
}

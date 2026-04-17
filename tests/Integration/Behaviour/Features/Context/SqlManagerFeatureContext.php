<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Integration\Behaviour\Features\Context;

use Behat\Gherkin\Node\TableNode;
use Db;
use Exception;
use PHPUnit\Framework\Assert;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Command\AddSqlRequestCommand;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Command\EditSqlRequestCommand;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\DatabaseTableFields;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Exception\SqlRequestConstraintException;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Query\GetDatabaseTableFieldsList;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\ValueObject\DatabaseTableField;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\ValueObject\SqlRequestId;
use RuntimeException;
use Tests\Integration\Behaviour\Features\Context\Domain\AbstractDomainFeatureContext;

/**
 * SqlManagerFeatureContext provides behat steps to perform actions related to prestashop SQL management
 * and validate returned outputs
 */
class SqlManagerFeatureContext extends AbstractDomainFeatureContext
{
    /**
     * "When" steps perform actions, and some of them store the latest result
     * in this variable so that "Then" action can check its content
     *
     * @var mixed
     */
    protected $latestResult;

    /**
     * @When I request the database fields from table :tableName
     */
    public function getDatabaseTableFieldsList($tableName)
    {
        $commandBus = CommonFeatureContext::getContainer()->get('prestashop.core.command_bus');

        $fullTableName = _DB_PREFIX_ . $tableName;
        $query = new GetDatabaseTableFieldsList($fullTableName);
        $result = $commandBus->handle($query);

        $this->latestResult = $result;
    }

    /**
     * @Then I should get a set of database fields that contain values:
     */
    public function assertSetOfDatabaseFieldsContain(TableNode $table)
    {
        $result = $this->latestResult;

        $this->assertInstanceOf(DatabaseTableFields::class, $result);

        foreach ($table->getRows() as $row) {
            $this->assertDatabaseFieldsContain($result, current($row));
        }
    }

    /**
     * @Given there is :count stored SQL requests
     */
    public function resetStoredSqlRequest($count)
    {
        $legacyDatabaseSingleton = Db::getInstance(_PS_USE_SQL_SLAVE_);
        $legacyDatabaseSingleton->delete('request_sql');
    }

    /**
     * @Then there should be :arg1 stored SQL request
     */
    public function assertStoredSqlRequestCount($count)
    {
        $legacyDatabaseSingleton = Db::getInstance(_PS_USE_SQL_SLAVE_);
        $realCountResults = $legacyDatabaseSingleton->executeS('SELECT COUNT(*) AS result FROM ' . _DB_PREFIX_ . 'request_sql');

        $realCount = current($realCountResults)['result'];

        if ((int) $realCount !== (int) $count) {
            throw new RuntimeException(sprintf('Expects %d sql stored requests, got %d instead', (int) $count, (int) $realCount));
        }
    }

    /**
     * @When I add the SQL request :sqlRequest named :name
     */
    public function addSqlRequest($sqlRequest, $name)
    {
        $commandBus = CommonFeatureContext::getContainer()->get('prestashop.core.command_bus');

        $command = new AddSqlRequestCommand($name, $sqlRequest);
        $result = $commandBus->handle($command);

        $this->latestResult = $result;
    }

    /**
     * @param string $expected
     * @param object $subject
     *
     * @todo: import phpunit asserts instead of re-writing them
     */
    private function assertInstanceOf($expected, $subject)
    {
        if (get_class($subject) !== $expected) {
            throw new RuntimeException(sprintf('Expects %s, got %s instead', $expected, get_class($subject)));
        }
    }

    /**
     * @param DatabaseTableFields $fields
     * @param string $expected
     */
    private function assertDatabaseFieldsContain(DatabaseTableFields $fields, $expected)
    {
        foreach ($fields->getFields() as $field) {
            $this->assertInstanceOf(DatabaseTableField::class, $field);
            if ($field->getName() === $expected) {
                return;
            }
        }

        throw new RuntimeException(sprintf('Expected database field %s in given set', $expected));
    }

    /**
     * @Given /^I add sql request "(.+)" with the following properties$/
     */
    public function addSqlRequestWithProperties(string $sqlQueryReference, TableNode $node): void
    {
        $data = $node->getRowsHash();

        try {
            /** @var SqlRequestId $sqlRequestId */
            $sqlRequestId = $this->getCommandBus()->handle(
                new AddSqlRequestCommand(
                    $data['name'],
                    $data['sql']
                )
            );
            SharedStorage::getStorage()->set($sqlQueryReference, $sqlRequestId->getValue());
        } catch (Exception $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @Given /^I edit sql request "(.+)" with the following properties$/
     */
    public function editSqlRequestWithProperties(string $sqlQueryReference, TableNode $node): void
    {
        $sqlRequestId = SharedStorage::getStorage()->get($sqlQueryReference);
        $data = $node->getRowsHash();

        try {
            /** @var SqlRequestId $sqlRequestId */
            $sqlRequestId = $this->getCommandBus()->handle(
                (new EditSqlRequestCommand(new SqlRequestId((int) $sqlRequestId)))
                    ->setName($data['name'])
                    ->setSql($data['sql'])
            );
        } catch (Exception $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @Then the sql request is valid
     */
    public function sqlRequestIsValid(): void
    {
        $this->assertLastErrorIsNull();
    }

    /**
     * @Then I should get an error that only the SELECT request is allowed
     */
    public function assertLastErrorIsOnlySelectRequest(): void
    {
        $lastError = $this->assertLastErrorIs(SqlRequestConstraintException::class);
        Assert::assertEquals(
            '"SELECT" does not exist.',
            $lastError->getMessage()
        );
    }

    /**
     * @Then I should get an error that the SQL request is malformed
     */
    public function assertLastErrorIsAMalformedSqlRequest(): void
    {
        $lastError = $this->assertLastErrorIs(SqlRequestConstraintException::class);
        Assert::assertEquals(
            'Bad SQL query',
            $lastError->getMessage()
        );
    }

    /**
     * @Then /^I should get an error that the table "(.+)" does not exists$/
     */
    public function assertLastErrorIsAnUnknownTable(string $tableName): void
    {
        $lastError = $this->assertLastErrorIs(SqlRequestConstraintException::class);
        Assert::assertEquals(
            sprintf('The "%s" table does not exist.', $tableName),
            $lastError->getMessage()
        );
    }
}

<?php

use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Query\GetDatabaseTableFieldsList;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\DatabaseTableFields;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\ValueObject\DatabaseTableField;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Command\AddSqlRequestCommand;
use Behat\Gherkin\Node\TableNode;

trait SqlManagerContextTrait
{
    /**
     * @When I request the database fields from table :tableName
     */
    public function getDatabaseTableFieldsList($tableName)
    {
        $commandBus = $this::getContainer()->get('prestashop.core.command_bus');

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
     * @Then there should be :arg1 stored SQL request
     */
    public function assertStoredSqlRequestCount($count)
    {
        // @todo: need improvement and decoupling
        $legacyDatabaseSingleton = \Db::getInstance(_PS_USE_SQL_SLAVE_);
        $realCountResults = $legacyDatabaseSingleton->executeS('SELECT COUNT(*) AS result FROM ps_request_sql');

        $realCount = current($realCountResults)['result'];

        if ((int) $realCount !== (int) $count) {
            throw new \RuntimeException(
                sprintf(
                    'Expects %d sql stored requests, got %d instead',
                    (int) $count,
                    (int) $realCount
                )
            );
        }
    }

    /**
     * @When I add the SQL request :sqlRequest with name :name
     */
    public function addSqlRequest($sqlRequest, $name)
    {
        $commandBus = $this::getContainer()->get('prestashop.core.command_bus');

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
            throw new \RuntimeException(
                sprintf(
                    'Expects %s, got %s instead',
                    $expected,
                    $subject
                )
            );
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

        throw new \RuntimeException(sprintf(
            'Expected database field %s in given set',
            $expected
        ));
    }

    /**
     * @return \Symfony\Component\DependencyInjection\ContainerInterface
     */
    abstract protected static function getContainer();
}

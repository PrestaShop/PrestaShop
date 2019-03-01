<?php

namespace Tests\Integration\Behaviour\Features\Context;

use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Query\GetDatabaseTableFieldsList;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\DatabaseTableFields;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\ValueObject\DatabaseTableField;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Command\AddSqlRequestCommand;
use Behat\Gherkin\Node\TableNode;

/**
 * SqlManagerFeatureContext provides behat steps to perform actions related to prestashop SQL management
 * and validate returned outputs
 */
class SqlManagerFeatureContext extends AbstractPrestaShopFeatureContext
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
     */
    public function resetStoredSqlRequest($count)
    {
        $legacyDatabaseSingleton = \Db::getInstance(_PS_USE_SQL_SLAVE_);
        $legacyDatabaseSingleton->delete('request_sql');
    }

    /**
     * @Then there should be :arg1 stored SQL request
     */
    public function assertStoredSqlRequestCount($count)
    {
        $legacyDatabaseSingleton = \Db::getInstance(_PS_USE_SQL_SLAVE_);
        $realCountResults = $legacyDatabaseSingleton->executeS('SELECT COUNT(*) AS result FROM ' . _DB_PREFIX_ . 'request_sql');

        $realCount = current($realCountResults)['result'];

        if ((int)$realCount !== (int)$count) {
            throw new \RuntimeException(
                sprintf(
                    'Expects %d sql stored requests, got %d instead',
                    (int)$count,
                    (int)$realCount
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
}

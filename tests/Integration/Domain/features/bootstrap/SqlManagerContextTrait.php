<?php

use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Query\GetDatabaseTableFieldsList;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\DatabaseTableFields;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\ValueObject\DatabaseTableField;
use Behat\Gherkin\Node\TableNode;


trait SqlManagerContextTrait
{
    /**
     * @When I request the database fields from table :tableName
     */
    public function iRequestTheDatabaseFieldsFromTable($tableName)
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
    public function iShouldGetASetOfDatabaseFieldsThatContain(TableNode $table)
    {
        $result = $this->latestResult;

        $this->assertInstanceOf(DatabaseTableFields::class, $result);

        foreach ($table->getRows() as $row) {
            $this->assertDatabaseFieldsContain($result, current($row));
        };
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
    abstract static protected function getContainer();
}

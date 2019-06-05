<?php
/**
 * 2007-2019 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

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
     * "When" steps perform actions, and some of them store the latest result
     * in this variable so that "Then" action can check its content
     *
     * @var mixed
     */
    protected $latestResult;

    /** @var bool */
    protected $flagPerformDatabaseCleanHard = false;

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

<?php
/**
 * 2007-2018 PrestaShop
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace Tests\Domain;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\DatabaseTableFields;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Query\GetDatabaseTableFieldsList;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Query\GetSqlRequestSettings;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\ValueObject\DatabaseTableField;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Tests\PrestaShopBundle\Utils\DatabaseCreator as Database;
use AppKernel;

/**
 * This is a Proof of Concept
 * Topic is: how to test Commands and Queries of SQL Manager ?
 */
class POCTest extends TestCase
{
    /**
     * PrestaShop Symfony Kernel
     *
     * @var AppKernel
     */
    protected $kernel;

    /**
     * PrestaShop Symfony Container, use it to access the services you want to test
     *
     * @var ContainerInterface
     */
    protected $container;

    protected function setUp()
    {
        parent::setUp();

        Database::restoreTestDB();
        require_once(__DIR__ . '/../../config/config.inc.php');
        require_once __DIR__ . '/../../app/AppKernel.php';

        $this->kernel = new AppKernel(_PS_MODE_DEV_ ? 'dev' : 'prod', _PS_MODE_DEV_);
        $this->kernel->boot();
        $this->container = $this->kernel->getContainer();
    }

    public function test_query_GetDatabaseTableFieldsList()
    {
        $commandBus = $this->container->get('prestashop.core.command_bus');

        $query = new GetDatabaseTableFieldsList('ps_carrier'); // is it always ps_carrier ?
        $result = $commandBus->handle($query);

        $this->assertInstanceOf(DatabaseTableFields::class, $result);

        $this->assertDatabaseFieldsContain($result, 'id_carrier');
        $this->assertDatabaseFieldsContain($result, 'name');
        $this->assertDatabaseFieldsContain($result, 'shipping_method');
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

        $this->fail("Expected database field $expected");
    }
}

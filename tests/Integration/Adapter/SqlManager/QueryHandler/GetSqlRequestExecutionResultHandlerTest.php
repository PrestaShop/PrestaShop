<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */
declare(strict_types=1);

namespace Tests\Integration\Adapter\SqlManager\QueryHandler;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Command\AddSqlRequestCommand;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Exception\SqlRequestConstraintException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Resources\DatabaseDump;

class GetSqlRequestExecutionResultHandlerTest extends KernelTestCase
{
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

        $this->commandBus = self::$container->get('prestashop.core.command_bus');
    }

    public function testUnauthorizedFunctionInSelect(): void
    {
        $this->expectException(SqlRequestConstraintException::class);
        $this->commandBus->handle(new AddSqlRequestCommand('request1', 'SELECT load_file(\'/etc/passwd\') FROM ps_zone;'));
    }
}

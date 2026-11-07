<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Core\Stock;

use Context;
use Db;
use Employee;
use PrestaShop\PrestaShop\Core\Stock\StockManager;
use Product;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Resources\DatabaseDump;

class StockMovementAuthorTest extends KernelTestCase
{
    private int $productId;

    protected function setUp(): void
    {
        parent::setUp();

        self::bootKernel();
        Context::getContext()->container = self::getContainer();
        // The movement is written through the Symfony container, which StockManager reads from the
        // global kernel.
        $GLOBALS['kernel'] = self::$kernel;

        $this->productId = (int) Db::getInstance()->getValue(
            'SELECT id_product FROM ' . _DB_PREFIX_ . 'stock_available WHERE id_product_attribute = 0'
        );
    }

    protected function tearDown(): void
    {
        DatabaseDump::restoreTables(['stock_available', 'stock_mvt']);
        parent::tearDown();
    }

    public function testItRecordsTheAuthorGivenByTheCaller(): void
    {
        $this->updateQuantityWithEmployee(null);

        $this->assertSame(['7', 'Import', 'Script'], $this->getLastMovementAuthor());
    }

    public function testTheAuthorGivenByTheCallerWinsOverTheContextEmployee(): void
    {
        $this->updateQuantityWithEmployee(new Employee(1));

        $this->assertSame(['7', 'Import', 'Script'], $this->getLastMovementAuthor());
    }

    private function updateQuantityWithEmployee(?Employee $employee): void
    {
        $previousEmployee = Context::getContext()->employee;
        Context::getContext()->employee = $employee;

        try {
            (new StockManager())->updateQuantity(
                new Product($this->productId),
                0,
                1,
                null,
                true,
                [
                    'id_employee' => 7,
                    'employee_firstname' => 'Import',
                    'employee_lastname' => 'Script',
                ]
            );
        } finally {
            Context::getContext()->employee = $previousEmployee;
        }
    }

    /**
     * @return array{0: string, 1: string, 2: string}
     */
    private function getLastMovementAuthor(): array
    {
        $movement = Db::getInstance()->getRow(
            'SELECT id_employee, employee_firstname, employee_lastname
             FROM ' . _DB_PREFIX_ . 'stock_mvt ORDER BY id_stock_mvt DESC'
        );

        return [(string) $movement['id_employee'], (string) $movement['employee_firstname'], (string) $movement['employee_lastname']];
    }
}

<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Core\Stock;

use Context;
use Db;
use PrestaShop\PrestaShop\Core\Stock\StockManager;
use Product;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

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

    /**
     * A cron or a console command has no employee in the context, so without this the movement is
     * recorded against employee 0 with no name and the caller cannot say who performed it.
     */
    public function testItRecordsTheAuthorGivenByTheCaller(): void
    {
        $previousEmployee = Context::getContext()->employee;
        Context::getContext()->employee = null;

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

        $movement = Db::getInstance()->getRow(
            'SELECT id_employee, employee_firstname, employee_lastname
             FROM ' . _DB_PREFIX_ . 'stock_mvt ORDER BY id_stock_mvt DESC'
        );

        $this->assertSame('7', (string) $movement['id_employee']);
        $this->assertSame('Import', $movement['employee_firstname']);
        $this->assertSame('Script', $movement['employee_lastname']);
    }
}

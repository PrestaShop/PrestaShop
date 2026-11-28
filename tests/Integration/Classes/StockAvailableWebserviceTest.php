<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes;

use Db;
use StockAvailable;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class StockAvailableWebserviceTest extends KernelTestCase
{
    private const PRODUCT_ID = 999405;

    private int $stockId;

    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();

        $db = Db::getInstance();
        $db->insert('stock_available', [
            'id_product' => self::PRODUCT_ID,
            'id_product_attribute' => 0,
            'id_shop' => 1,
            'id_shop_group' => 0,
            'quantity' => 10,
            'physical_quantity' => 10,
            'reserved_quantity' => 0,
        ]);
        $this->stockId = (int) $db->Insert_ID();
    }

    protected function tearDown(): void
    {
        Db::getInstance()->delete('stock_available', 'id_product = ' . self::PRODUCT_ID);
        parent::tearDown();
    }

    public function testUpdatingAnotherFieldKeepsAnOrderPlacedInTheMeantime(): void
    {
        $request = new StockAvailable($this->stockId);
        Db::getInstance()->update('stock_available', ['quantity' => 9], 'id_stock_available = ' . $this->stockId);

        $request->location = 'A1';
        $this->assertTrue($request->updateWs());

        $this->assertEquals(['quantity' => 9, 'location' => 'A1'], $this->getStoredRow());
    }

    public function testNewQuantityIsStored(): void
    {
        $request = new StockAvailable($this->stockId);
        $request->quantity = 25;
        $request->location = 'B2';

        $this->assertTrue($request->updateWs());

        $this->assertEquals(['quantity' => 25, 'location' => 'B2'], $this->getStoredRow());
    }

    /**
     * @return array{quantity: int|string, location: string}
     */
    private function getStoredRow(): array
    {
        return Db::getInstance()->getRow(
            'SELECT quantity, location FROM ' . _DB_PREFIX_ . 'stock_available WHERE id_stock_available = ' . $this->stockId,
            false
        );
    }
}

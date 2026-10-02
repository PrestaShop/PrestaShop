<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Tests\Integration\Classes;

use AdminImportControllerCore;
use Db;
use ReflectionClass;
use ReflectionMethod;
use SpecificPrice;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Importing a zero discount removes the specific price the importer owns, and only that one.
 */
class SpecificPriceImportDeletionTest extends KernelTestCase
{
    private const PRODUCT_ID = 1;
    private const SHOP_ID = 1;

    /** @var int[] */
    private array $createdIds = [];

    protected function tearDown(): void
    {
        foreach ($this->createdIds as $id) {
            $specificPrice = new SpecificPrice($id);
            if ($specificPrice->id) {
                $specificPrice->delete();
            }
        }
        $this->createdIds = [];
        parent::tearDown();
    }

    public function testItRemovesTheOneTheImporterWroteAndLeavesTheOthers(): void
    {
        $imported = $this->createSpecificPrice([]);
        $forACustomer = $this->createSpecificPrice(['id_customer' => 1]);
        $forAQuantity = $this->createSpecificPrice(['from_quantity' => 10]);
        $fromARule = $this->createSpecificPrice(['id_specific_price_rule' => 1]);

        $this->deleteImported();

        // demo data carries an all-shops price on this product (id_shop = 0); the importer never
        // writes one, so the delete must not reach it
        $allShops = (int) Db::getInstance()->getValue(
            'SELECT id_specific_price FROM ' . _DB_PREFIX_ . 'specific_price
             WHERE id_product = ' . self::PRODUCT_ID . ' AND id_shop = 0'
        );

        $this->assertFalse($this->exists($imported), 'the imported specific price was not removed');
        if ($allShops) {
            $this->assertTrue($this->exists($allShops), 'an all-shops specific price was removed');
        }
        $this->assertTrue($this->exists($forACustomer), 'a customer specific price was removed');
        $this->assertTrue($this->exists($forAQuantity), 'a quantity tier was removed');
        $this->assertTrue($this->exists($fromARule), 'a catalog price rule price was removed');
    }

    /**
     * @dataProvider discountRemovalRows
     */
    public function testItOnlyTreatsAnExplicitZeroAsRemoval(array $row, bool $expected, string $message): void
    {
        $method = new ReflectionMethod(AdminImportControllerCore::class, 'csvRemovesTheDiscount');
        $method->setAccessible(true);

        $this->assertSame($expected, $method->invoke(null, $row), $message);
    }

    public function discountRemovalRows(): array
    {
        return [
            [['reduction_percent' => '0'], true, 'a zero percent asks for the discount to go'],
            [['reduction_percent' => 0], true, 'the same written as an integer'],
            [['reduction_price' => '0.00'], true, 'a zero amount asks for it too'],
            [['reduction_percent' => ''], false, 'an empty cell means the column was left alone'],
            [['reduction_price' => ''], false, 'an empty amount means the same'],
            [[], false, 'a file without the column must never delete anything'],
            [['reduction_percent' => '25'], false, 'a real discount is not a removal'],
            [['reduction_price' => '9.99'], false, 'nor is a real amount'],
            [['reduction_percent' => '', 'reduction_price' => '0'], true, 'one explicit zero is enough'],
        ];
    }

    public function testItRemovesAPriceThatNeverExpires(): void
    {
        // what the importer writes when the CSV carries no discount dates, and what a delete filtered
        // on "to >= now()" would miss
        $endless = $this->createSpecificPrice(['from' => '0000-00-00 00:00:00', 'to' => '0000-00-00 00:00:00']);

        $this->deleteImported();

        $this->assertFalse($this->exists($endless));
    }

    private function deleteImported(): void
    {
        $method = new ReflectionMethod(AdminImportControllerCore::class, 'deleteImportedSpecificPrices');
        $method->setAccessible(true);
        $method->invoke(
            (new ReflectionClass(AdminImportControllerCore::class))->newInstanceWithoutConstructor(),
            self::PRODUCT_ID,
            [self::SHOP_ID]
        );
    }

    private function createSpecificPrice(array $overrides): int
    {
        $specificPrice = new SpecificPrice();
        $specificPrice->id_product = self::PRODUCT_ID;
        $specificPrice->id_shop = self::SHOP_ID;
        $specificPrice->id_product_attribute = 0;
        $specificPrice->id_currency = 0;
        $specificPrice->id_country = 0;
        $specificPrice->id_group = 0;
        $specificPrice->id_customer = 0;
        $specificPrice->id_specific_price_rule = 0;
        $specificPrice->price = -1;
        $specificPrice->from_quantity = 1;
        $specificPrice->reduction = 0.25;
        $specificPrice->reduction_type = 'percentage';
        $specificPrice->reduction_tax = 1;
        $specificPrice->from = '0000-00-00 00:00:00';
        $specificPrice->to = '0000-00-00 00:00:00';

        foreach ($overrides as $field => $value) {
            $specificPrice->{$field} = $value;
        }

        $specificPrice->add();
        $this->createdIds[] = (int) $specificPrice->id;

        return (int) $specificPrice->id;
    }

    private function exists(int $id): bool
    {
        return (bool) Db::getInstance()->getValue(
            'SELECT id_specific_price FROM ' . _DB_PREFIX_ . 'specific_price WHERE id_specific_price = ' . $id
        );
    }
}

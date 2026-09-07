<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes\Product;

use Combination;
use Db;
use Pack;
use PHPUnit\Framework\TestCase;
use Product;
use Tests\Integration\Utility\ContextMockerTrait;

/**
 * The webservice declares the product_bundle association with the fields id, id_product_attribute and
 * quantity, and getWsProductBundle() returns exactly those. A payload built from a GET response, or from
 * the documented field list, therefore carries id_product_attribute, and setWsProductBundle() has to read
 * the same name back.
 *
 * When it does not, Pack::addItem() receives 0 and substitutes the item's default combination, so every
 * item of a pack collapses onto one row. ps_pack is keyed on
 * (id_product_pack, id_product_item, id_product_attribute_item), which turns the second item into a
 * duplicate entry error and the first into silently wrong data.
 */
class WebserviceProductBundleTest extends TestCase
{
    use ContextMockerTrait;

    private Product $pack;

    /** @var int[] */
    private array $combinationIds = [];

    private int $itemProductId;

    protected function setUp(): void
    {
        parent::setUp();
        self::mockContext();

        $this->pack = new Product(null, false, 1);
        $this->pack->name = 'Bundle host';
        $this->pack->price = 10.0;
        $this->pack->link_rewrite = 'bundle-host-' . uniqid();
        $this->pack->save();

        $item = new Product(null, false, 1);
        $item->name = 'Bundled item';
        $item->price = 5.0;
        $item->link_rewrite = 'bundled-item-' . uniqid();
        $item->save();
        $this->itemProductId = (int) $item->id;

        // Three combinations, so the default one is not the only candidate.
        foreach (range(1, 3) as $i) {
            $combination = new Combination();
            $combination->id_product = $this->itemProductId;
            $combination->reference = 'bundle-combination-' . $i;
            $combination->default_on = (1 === $i) ? true : null;
            $combination->save();
            $this->combinationIds[] = (int) $combination->id;
        }
    }

    protected function tearDown(): void
    {
        Pack::deleteItems((int) $this->pack->id);
        foreach ($this->combinationIds as $id) {
            (new Combination($id))->delete();
        }
        (new Product($this->itemProductId))->delete();
        $this->pack->delete();

        parent::tearDown();
    }

    public function testEachRequestedCombinationIsStored(): void
    {
        $this->pack->setWsProductBundle($this->bundlePayload('id_product_attribute'));

        self::assertSame($this->combinationIds, $this->storedCombinationIds());
    }

    /**
     * getWsProductBundle() is what a client reads before writing back, so the two have to agree.
     */
    public function testTheFieldNamesMatchWhatTheGetterReturns(): void
    {
        $this->pack->setWsProductBundle($this->bundlePayload('id_product_attribute'));

        $returned = $this->pack->getWsProductBundle();

        self::assertCount(3, $returned);
        self::assertSame(['id', 'id_product_attribute', 'quantity'], array_keys($returned[0]));
    }

    /**
     * product_attribute_id was never part of the declared association, but it was the only name this
     * method read, so anything written against it has to keep working.
     */
    public function testTheUndocumentedFieldNameIsStillAccepted(): void
    {
        $this->pack->setWsProductBundle($this->bundlePayload('product_attribute_id'));

        self::assertSame($this->combinationIds, $this->storedCombinationIds());
    }

    /**
     * The combination is optional, and an item sent without one still has to fall back to the default.
     */
    public function testAnItemWithoutACombinationFallsBackToTheDefault(): void
    {
        $this->pack->setWsProductBundle([
            ['id' => $this->itemProductId, 'quantity' => 1],
        ]);

        self::assertSame([(int) Product::getDefaultAttribute($this->itemProductId)], $this->storedCombinationIds());
    }

    /**
     * @return array<int, array<string, int>>
     */
    private function bundlePayload(string $combinationField): array
    {
        $items = [];
        foreach ($this->combinationIds as $combinationId) {
            $items[] = [
                'id' => $this->itemProductId,
                $combinationField => $combinationId,
                'quantity' => 1,
            ];
        }

        return $items;
    }

    /**
     * @return int[]
     */
    private function storedCombinationIds(): array
    {
        $rows = Db::getInstance()->executeS(
            'SELECT id_product_attribute_item FROM ' . _DB_PREFIX_ . 'pack WHERE id_product_pack = ' . (int) $this->pack->id
        );
        $ids = array_map(static fn (array $row): int => (int) $row['id_product_attribute_item'], $rows ?: []);
        sort($ids);

        return $ids;
    }
}

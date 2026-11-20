<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes;

use Cache;
use Context;
use Db;
use Hook;
use PHPUnit\Framework\TestCase;
use Product;
use StockAvailable;
use Tests\Integration\Utility\ContextMockerTrait;

class StockAvailablePrefetchTest extends TestCase
{
    use ContextMockerTrait;

    private const OVERRIDE_HOOK = 'actionOverrideQuantityAvailableByProduct';

    private int $productId;
    private int $combinationId;
    /** @var array{0: int, 1: int}|null */
    private ?array $hookModuleRow = null;
    private int $createdHookId = 0;

    protected function setUp(): void
    {
        parent::setUp();
        self::mockContext();

        $row = Db::getInstance()->getRow(
            'SELECT `id_product`, `id_product_attribute` FROM `' . _DB_PREFIX_ . 'stock_available`
            WHERE `id_product_attribute` > 0 AND `quantity` > 0 ORDER BY `id_product`, `id_product_attribute`'
        );
        self::assertIsArray($row);
        $this->productId = (int) $row['id_product'];
        $this->combinationId = (int) $row['id_product_attribute'];

        Cache::clean('StockAvailable::getQuantityAvailableByProduct_*');
    }

    protected function tearDown(): void
    {
        if ($this->hookModuleRow) {
            Db::getInstance()->delete('hook_module', '`id_module` = ' . $this->hookModuleRow[0] . ' AND `id_hook` = ' . $this->hookModuleRow[1]);
            Cache::clean(Hook::MODULE_LIST_BY_HOOK_KEY . '*');
        }
        if ($this->createdHookId) {
            (new Hook($this->createdHookId))->delete();
        }
        Cache::clean('StockAvailable::getQuantityAvailableByProduct_*');
        parent::tearDown();
    }

    public function testPrefetchMatchesPerProductResolution(): void
    {
        $pairs = [
            [$this->productId, 0],
            [$this->productId, $this->combinationId],
            // No stock row: the per-product read returns 0.
            [$this->productId, 999999],
        ];
        $expected = [];
        foreach ($pairs as [$productId, $combinationId]) {
            $expected[] = StockAvailable::getQuantityAvailableByProduct($productId, $combinationId);
        }
        self::assertGreaterThan(0, $expected[1]);
        self::assertSame(0, $expected[2]);

        Cache::clean('StockAvailable::getQuantityAvailableByProduct_*');
        StockAvailable::prefetchQuantityAvailableByProducts($pairs);

        self::assertSame($expected, array_map([$this, 'getCachedQuantity'], $pairs));
    }

    public function testPrefetchUsesTheRowCombinationOrTheDefaultOne(): void
    {
        Product::prefetchPropertiesQuantities([
            ['id_product' => (string) $this->productId, 'id_product_attribute' => '0', 'cache_default_attribute' => (string) $this->combinationId],
        ]);

        self::assertTrue(Cache::isStored($this->getCacheKey($this->productId, 0)));
        self::assertTrue(Cache::isStored($this->getCacheKey($this->productId, $this->combinationId)));
    }

    public function testPrefetchLoadsNothingWhenAModuleOverridesTheQuantity(): void
    {
        $hookId = (int) Hook::getIdByName(self::OVERRIDE_HOOK, false);
        if (!$hookId) {
            $hook = new Hook();
            $hook->name = self::OVERRIDE_HOOK;
            $hook->title = self::OVERRIDE_HOOK;
            self::assertTrue($hook->add());
            $hookId = (int) $hook->id;
            $this->createdHookId = $hookId;
        }
        $moduleId = (int) Db::getInstance()->getValue(
            'SELECT m.`id_module` FROM `' . _DB_PREFIX_ . 'module` m
            INNER JOIN `' . _DB_PREFIX_ . 'module_shop` ms ON ms.`id_module` = m.`id_module` AND ms.`id_shop` = ' . (int) Context::getContext()->shop->id . '
            WHERE m.`active` = 1 ORDER BY m.`id_module`'
        );
        self::assertGreaterThan(0, $moduleId);
        self::assertTrue(Db::getInstance()->insert('hook_module', [
            'id_module' => $moduleId,
            'id_shop' => (int) Context::getContext()->shop->id,
            'id_hook' => $hookId,
            'position' => 1,
        ]));
        $this->hookModuleRow = [$moduleId, $hookId];
        Cache::clean(Hook::MODULE_LIST_BY_HOOK_KEY . '*');
        self::assertNotEmpty(Hook::getHookModuleExecList(self::OVERRIDE_HOOK));

        StockAvailable::prefetchQuantityAvailableByProducts([
            [$this->productId, 0],
            [$this->productId, $this->combinationId],
        ]);

        self::assertFalse(Cache::isStored($this->getCacheKey($this->productId, 0)));
        self::assertFalse(Cache::isStored($this->getCacheKey($this->productId, $this->combinationId)));
    }

    /**
     * @param array{0: int, 1: int} $pair
     */
    private function getCachedQuantity(array $pair)
    {
        $key = $this->getCacheKey($pair[0], $pair[1]);
        self::assertTrue(Cache::isStored($key), $key);

        return Cache::retrieve($key);
    }

    private function getCacheKey(int $productId, int $combinationId): string
    {
        return 'StockAvailable::getQuantityAvailableByProduct_' . $productId . '-' . $combinationId . '-0';
    }
}

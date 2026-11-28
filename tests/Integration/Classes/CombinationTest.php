<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes;

use Combination;
use Db;
use PHPUnit\Framework\TestCase;
use Product;
use Tests\Integration\Utility\ContextMockerTrait;

class CombinationTest extends TestCase
{
    use ContextMockerTrait;

    private Product $product;

    protected function setUp(): void
    {
        parent::setUp();
        self::mockContext();

        $this->product = new Product(null, false, 1);
        $this->product->name = 'Default combination flag';
        $this->product->price = 10.0;
        $this->product->link_rewrite = 'default-combination-flag';
        $this->product->add();
    }

    protected function tearDown(): void
    {
        $this->product->delete();
        parent::tearDown();
    }

    public function testAddingADefaultCombinationReleasesThePreviousOne(): void
    {
        $first = $this->addCombination(true);
        $second = $this->addCombination(true);

        $this->assertSame([(int) $second->id], $this->defaultCombinationIds('product_attribute'));
        $this->assertSame([(int) $second->id], $this->defaultCombinationIds('product_attribute_shop'));
        // The released row must be NULL and not 0: the unique index counts 0 as a value, so a second
        // zeroed row would collide exactly like a second 1.
        $this->assertNull($this->storedDefaultOn((int) $first->id));
    }

    public function testPromotingACombinationReleasesThePreviousDefault(): void
    {
        $first = $this->addCombination(true);
        $second = $this->addCombination(false);

        $second->default_on = true;
        $this->assertTrue($second->update());

        $this->assertSame([(int) $second->id], $this->defaultCombinationIds('product_attribute'));
        $this->assertSame([(int) $second->id], $this->defaultCombinationIds('product_attribute_shop'));
        $this->assertNull($this->storedDefaultOn((int) $first->id));
    }

    public function testResavingTheDefaultCombinationKeepsItDefault(): void
    {
        $first = $this->addCombination(true);
        $second = $this->addCombination(false);

        // A webservice PUT arrives with default_on hydrated from the database, so an edit that only
        // means to change the price re-saves the flag too. That must not disturb it.
        $first->price = 5.0;
        $this->assertTrue($first->update());

        $this->assertSame([(int) $first->id], $this->defaultCombinationIds('product_attribute'));
        $this->assertSame([(int) $first->id], $this->defaultCombinationIds('product_attribute_shop'));
        $this->assertNull($this->storedDefaultOn((int) $second->id));
    }

    private function addCombination(bool $isDefault): Combination
    {
        $combination = new Combination();
        $combination->id_product = (int) $this->product->id;
        $combination->minimal_quantity = 1;
        $combination->default_on = $isDefault ? true : null;
        $this->assertTrue($combination->add());

        return $combination;
    }

    /**
     * @return int[]
     */
    private function defaultCombinationIds(string $table): array
    {
        $rows = Db::getInstance()->executeS(
            'SELECT `id_product_attribute` FROM `' . _DB_PREFIX_ . $table . '`
            WHERE `default_on` = 1 AND `id_product` = ' . (int) $this->product->id . '
            ORDER BY `id_product_attribute`'
        );

        return array_map('intval', array_column($rows, 'id_product_attribute'));
    }

    private function storedDefaultOn(int $combinationId): ?string
    {
        $rows = Db::getInstance()->executeS(
            'SELECT `default_on` FROM `' . _DB_PREFIX_ . 'product_attribute`
            WHERE `id_product_attribute` = ' . $combinationId
        );

        return $rows[0]['default_on'];
    }
}

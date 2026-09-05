<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes;

use Combination;
use PHPUnit\Framework\TestCase;
use Product;
use Tests\Integration\Utility\ContextMockerTrait;

class ProductTest extends TestCase
{
    use ContextMockerTrait;

    protected function setUp(): void
    {
        parent::setUp();
        self::mockContext();
    }

    public function testSaveActiveRecordStyle(): void
    {
        $product = new Product(null, false, 1);
        $product->name = 'A Product';
        $product->price = 42.42;
        $product->link_rewrite = 'a-product';
        $this->assertTrue($product->save());
    }

    /**
     * When the combinations of a product do not all use the same attribute groups, no combination matches
     * the requested attributes exactly and getIdProductAttributeByIdAttributes falls back to a best guess.
     * That fallback used to drop attributes by group position, so it could only recover a combination that
     * omits a late group and otherwise answered with an unrelated combination.
     *
     * @dataProvider provideAsymmetricCombinations
     *
     * @param int[] $requestedAttributes
     */
    public function testItFindsTheMostSpecificCombinationWithinTheRequestedAttributes(
        array $combinations,
        array $requestedAttributes,
        int $expectedIndex
    ): void {
        $product = new Product(null, false, 1);
        $product->name = 'Asymmetric combinations';
        $product->price = 100.0;
        $product->link_rewrite = 'asymmetric-combinations';
        $product->save();

        $combinationIds = [];
        foreach ($combinations as $attributeIds) {
            $combination = new Combination();
            $combination->id_product = (int) $product->id;
            $combination->add();
            $combination->setAttributes($attributeIds);
            $combinationIds[] = (int) $combination->id;
        }

        self::assertSame(
            $combinationIds[$expectedIndex],
            Product::getIdProductAttributeByIdAttributes((int) $product->id, $requestedAttributes, true)
        );

        $product->delete();
    }

    /**
     * Attribute ids come from the default install: 1 = S and 2 = M in Size, 10 = Red and 14 = Blue in
     * Color, 19 = 40x60cm and 20 = 60x90cm in Dimension. Size sorts before Color, which sorts before
     * Dimension, which is what the old positional fallback depended on.
     */
    public function provideAsymmetricCombinations(): iterable
    {
        // two groups
        yield 'exact match still wins' => [[[1, 14], [2, 14], [3]], [1, 14], 0];
        yield 'combination omitting the last group' => [[[1, 14], [2, 14], [3]], [3, 14], 2];
        yield 'combination omitting the first group' => [[[1, 14], [2, 14], [10]], [1, 10], 2];

        // three groups: a combination can omit the middle group too, which two groups cannot express
        $threeGroups = [[1, 14, 19], [2, 14], [10, 19], [20], [1, 19]];
        yield 'three groups, exact match' => [$threeGroups, [1, 14, 19], 0];
        yield 'three groups, only the last group matches' => [$threeGroups, [1, 14, 20], 3];
        yield 'three groups, two candidates, the widest wins' => [$threeGroups, [2, 14, 20], 1];
    }
}

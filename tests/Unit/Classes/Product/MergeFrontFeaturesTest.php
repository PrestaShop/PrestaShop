<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Classes\Product;

use PHPUnit\Framework\TestCase;
use Product;

/**
 * Covers the pure merge logic used by the front office to combine product-level and
 * combination-level features (Product::getFrontFeaturesMergedStatic delegates to it).
 */
class MergeFrontFeaturesTest extends TestCase
{
    public function testEmptyCombinationReturnsProductFeaturesUnchanged(): void
    {
        $productFeatures = [
            $this->feature(1, 'Color', 'Red', 0),
            $this->feature(2, 'Size', 'M', 1),
        ];

        $this->assertSame($productFeatures, Product::mergeFrontFeatures($productFeatures, []));
    }

    public function testCombinationOnlyFeaturesAreKept(): void
    {
        $combinationFeatures = [$this->feature(3, 'Material', 'Cotton', 0)];

        $this->assertEquals($combinationFeatures, Product::mergeFrontFeatures([], $combinationFeatures));
    }

    public function testDisjointFeaturesAreMergedAndOrderedByPosition(): void
    {
        $productFeatures = [$this->feature(1, 'Color', 'Red', 2)];
        $combinationFeatures = [$this->feature(2, 'Size', 'M', 0)];

        $merged = Product::mergeFrontFeatures($productFeatures, $combinationFeatures);

        // Ordered by feature position (0 then 2)
        $this->assertSame([2, 1], array_column($merged, 'id_feature'));
    }

    public function testCombinationOverridesProductForSameFeature(): void
    {
        $productFeatures = [
            $this->feature(1, 'Color', 'Red', 0),
            $this->feature(2, 'Size', 'M', 1),
        ];
        $combinationFeatures = [
            $this->feature(1, 'Color', 'Blue', 0),
        ];

        $merged = Product::mergeFrontFeatures($productFeatures, $combinationFeatures);

        $valuesByFeature = [];
        foreach ($merged as $row) {
            $valuesByFeature[(int) $row['id_feature']][] = $row['value'];
        }

        // Feature 1: the combination value wins, the product value is dropped
        $this->assertSame(['Blue'], $valuesByFeature[1]);
        // Feature 2: only defined at product level, kept as-is
        $this->assertSame(['M'], $valuesByFeature[2]);
    }

    public function testAllProductValuesOfAnOverriddenFeatureAreReplaced(): void
    {
        $productFeatures = [
            $this->feature(1, 'Color', 'Red', 0),
            $this->feature(1, 'Color', 'Green', 0),
        ];
        $combinationFeatures = [
            $this->feature(1, 'Color', 'Blue', 0),
            $this->feature(1, 'Color', 'Yellow', 0),
        ];

        $merged = Product::mergeFrontFeatures($productFeatures, $combinationFeatures);

        $values = array_column($merged, 'value');
        sort($values);
        // Only the combination values remain for the overridden feature
        $this->assertSame(['Blue', 'Yellow'], $values);
    }

    /**
     * @return array{name: string, value: string, id_feature: int, position: int, id_feature_value: int}
     */
    private function feature(int $idFeature, string $name, string $value, int $position): array
    {
        return [
            'name' => $name,
            'value' => $value,
            'id_feature' => $idFeature,
            'position' => $position,
            'id_feature_value' => $idFeature * 10,
        ];
    }
}

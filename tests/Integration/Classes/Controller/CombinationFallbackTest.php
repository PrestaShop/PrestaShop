<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes\Controller;

use Configuration;
use PHPUnit\Framework\TestCase;
use Product;
use ProductController;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;

/**
 * With "Display unavailable attributes" off, picking an attribute whose combination is out of stock
 * makes the controller fall back to a neighbouring combination.
 *
 * The dropdowns are washed from left to right: a group only offers values that still combine, in
 * stock, with the groups before it. An unavailable combination therefore means an earlier group was
 * just changed and the groups after it hold stale values, so the fallback has to keep the leading
 * groups and re-pick the trailing ones. Keeping the wrong side makes the dropdown appear to snap
 * back to its previous value.
 */
class CombinationFallbackTest extends TestCase
{
    private const COLOUR_GROUP = 2;
    private const SIZE_GROUP = 1;
    private const WAREHOUSE_GROUP = 9;
    private const MATERIAL_GROUP = 3;
    private const FINISH_GROUP = 4;

    private const WHITE = 801;
    private const BLACK = 802;
    private const SIZE_40 = 401;
    private const SIZE_44 = 441;
    private const SIZE_45 = 451;
    private const WAREHOUSE_A = 901;
    private const WAREHOUSE_B = 902;
    private const COTTON = 501;
    private const LINEN = 502;
    private const MATT = 601;
    private const GLOSS = 602;
    private const HANDLE_GROUP = 5;
    private const WITH_HANDLE = 701;
    private const WITHOUT_HANDLE = 702;
    private const PACK_1 = 111;
    private const PACK_4 = 114;
    private const PACK_15 = 115;

    /** @var array<string, string> */
    private array $originalConfiguration = [];

    protected function setUp(): void
    {
        parent::setUp();

        foreach (['PS_DISP_UNAVAILABLE_ATTR' => '0', 'PS_ORDER_OUT_OF_STOCK' => '0'] as $key => $value) {
            $this->originalConfiguration[$key] = (string) Configuration::get($key);
            Configuration::updateValue($key, $value);
        }
    }

    protected function tearDown(): void
    {
        foreach ($this->originalConfiguration as $key => $value) {
            Configuration::updateValue($key, $value);
        }
        $this->originalConfiguration = [];

        parent::tearDown();
    }

    /**
     * @dataProvider getFallbackScenarios
     *
     * @param array<int, array{attributes: array<int, int>, quantity: int, default: int}> $combinations
     * @param array<int, int> $groups
     */
    public function testTheFallbackKeepsTheAttributeThatWasPicked(
        array $combinations,
        array $groups,
        int $requestedIdProductAttribute,
        int $pickedAttribute,
        int $expectedIdProductAttribute,
        string $message
    ): void {
        $landedOn = $this->fallbackFor($combinations, $groups, $requestedIdProductAttribute);

        self::assertSame($expectedIdProductAttribute, $landedOn, $message);
        self::assertContains(
            $pickedAttribute,
            $combinations[$landedOn]['attributes'],
            'the combination it fell back to dropped the attribute the visitor picked'
        );
    }

    /**
     * Nothing in the requested colour is in stock, so there is no way to honour the pick. Falling
     * back to another colour is correct here, and pins that the fix did not turn into "never move".
     */
    public function testItStillMovesWhenThePickCannotBeHonoured(): void
    {
        $combinations = [
            3001 => ['attributes' => [self::WHITE, self::SIZE_40], 'quantity' => 1, 'default' => 1],
            3002 => ['attributes' => [self::BLACK, self::SIZE_40], 'quantity' => 0, 'default' => 0],
            3003 => ['attributes' => [self::WHITE, self::SIZE_44], 'quantity' => 2, 'default' => 0],
            3004 => ['attributes' => [self::BLACK, self::SIZE_44], 'quantity' => 0, 'default' => 0],
        ];

        $landedOn = $this->fallbackFor($combinations, [self::COLOUR_GROUP, self::SIZE_GROUP], 3002);

        self::assertContains($landedOn, [3001, 3003], 'it should still land on an in-stock combination');
        self::assertSame(self::WHITE, $combinations[$landedOn]['attributes'][0]);
    }

    /**
     * Issue #33720. Handle group first, package size second. Exactly the reporter's stock table:
     * with a handle only the 4-pack is in stock, without a handle only the 1- and 15-packs are.
     * Standing on "with handle / 4" and picking "without handle" asks for a combination that does
     * not exist, and the visitor must not be pushed back to "with handle".
     */
    public function testPickingTheOtherHandleKeepsItAndMovesThePackSize(): void
    {
        $combinations = [
            4001 => ['attributes' => [self::WITH_HANDLE, self::PACK_1], 'quantity' => 0, 'default' => 0],
            4002 => ['attributes' => [self::WITH_HANDLE, self::PACK_4], 'quantity' => 4, 'default' => 1],
            4003 => ['attributes' => [self::WITH_HANDLE, self::PACK_15], 'quantity' => 0, 'default' => 0],
            4004 => ['attributes' => [self::WITHOUT_HANDLE, self::PACK_1], 'quantity' => 3, 'default' => 0],
            4005 => ['attributes' => [self::WITHOUT_HANDLE, self::PACK_4], 'quantity' => 0, 'default' => 0],
            4006 => ['attributes' => [self::WITHOUT_HANDLE, self::PACK_15], 'quantity' => 2, 'default' => 0],
        ];

        $landedOn = $this->fallbackFor($combinations, [self::HANDLE_GROUP, self::SIZE_GROUP], 4005);

        self::assertContains($landedOn, [4004, 4006], 'it should stay without a handle and move the pack size');
        self::assertSame(
            self::WITHOUT_HANDLE,
            $combinations[$landedOn]['attributes'][0],
            'the handle the visitor picked has to survive the fallback'
        );
    }

    /**
     * The trailing groups are the stale ones, so they are the ones that move. Reaching this request
     * through the dropdowns is not possible: the size dropdown is washed to sizes that are in stock
     * for the selected colour, so White/40 could only come from a hand-written query string.
     */
    public function testTheLeadingGroupWinsWhenALaterGroupIsStale(): void
    {
        $combinations = self::colourFirstCatalogue();

        $landedOn = $this->fallbackFor($combinations, [self::COLOUR_GROUP, self::SIZE_GROUP], 2001);

        self::assertSame(2003, $landedOn);
        self::assertSame(self::WHITE, $combinations[$landedOn]['attributes'][0], 'the leading group is kept');
    }

    /**
     * Colour first, size second. In stock: Black/40 (2), White/44 (1), Black/45 (1).
     *
     * @return array<int, array{attributes: array<int, int>, quantity: int, default: int}>
     */
    private static function colourFirstCatalogue(): array
    {
        return [
            2001 => ['attributes' => [self::WHITE, self::SIZE_40], 'quantity' => 0, 'default' => 0],
            2002 => ['attributes' => [self::BLACK, self::SIZE_40], 'quantity' => 2, 'default' => 0],
            2003 => ['attributes' => [self::WHITE, self::SIZE_44], 'quantity' => 1, 'default' => 1],
            2004 => ['attributes' => [self::BLACK, self::SIZE_44], 'quantity' => 0, 'default' => 0],
            2005 => ['attributes' => [self::WHITE, self::SIZE_45], 'quantity' => 0, 'default' => 0],
            2006 => ['attributes' => [self::BLACK, self::SIZE_45], 'quantity' => 1, 'default' => 0],
        ];
    }

    public static function getFallbackScenarios(): iterable
    {
        yield 'picking a colour keeps that colour' => [
            self::colourFirstCatalogue(),
            [self::COLOUR_GROUP, self::SIZE_GROUP],
            2004,
            self::BLACK,
            2002,
            'picking Black on an out-of-stock size fell back to White, discarding the colour',
        ];

        // Two plain groups, the shape reported in #32125. In stock: 40/B (2), 44/A (1), 45/B (1).
        $sizeFirst = [
            1001 => ['attributes' => [self::SIZE_40, self::WAREHOUSE_A], 'quantity' => 0, 'default' => 0],
            1002 => ['attributes' => [self::SIZE_40, self::WAREHOUSE_B], 'quantity' => 2, 'default' => 0],
            1003 => ['attributes' => [self::SIZE_44, self::WAREHOUSE_A], 'quantity' => 1, 'default' => 1],
            1004 => ['attributes' => [self::SIZE_44, self::WAREHOUSE_B], 'quantity' => 0, 'default' => 0],
            1005 => ['attributes' => [self::SIZE_45, self::WAREHOUSE_A], 'quantity' => 0, 'default' => 0],
            1006 => ['attributes' => [self::SIZE_45, self::WAREHOUSE_B], 'quantity' => 1, 'default' => 0],
        ];

        yield 'picking a size keeps that size with two plain groups' => [
            $sizeFirst,
            [self::SIZE_GROUP, self::WAREHOUSE_GROUP],
            1001,
            self::SIZE_40,
            1002,
            'picking size 40 should move the second group, not the size',
        ];

        // Same, but the combination that keeps only the untouched warehouse holds far more stock.
        $sizeFirstRestocked = $sizeFirst;
        $sizeFirstRestocked[1003]['quantity'] = 10;

        yield 'stock elsewhere does not outweigh the picked attribute' => [
            $sizeFirstRestocked,
            [self::SIZE_GROUP, self::WAREHOUSE_GROUP],
            1001,
            self::SIZE_40,
            1002,
            'a restock on another size pulled the visitor off the size they picked',
        ];

        // Three groups. The visitor changed the first one, so the other two are stale. The
        // combination keeping both stale values must not outrank the one keeping the fresh pick.
        $threeGroups = [
            7001 => ['attributes' => [self::SIZE_40, self::COTTON, self::MATT], 'quantity' => 0, 'default' => 0],
            7002 => ['attributes' => [self::SIZE_40, self::LINEN, self::GLOSS], 'quantity' => 5, 'default' => 0],
            7003 => ['attributes' => [self::SIZE_44, self::COTTON, self::MATT], 'quantity' => 9, 'default' => 1],
        ];

        yield 'two stale attributes do not outvote the one just picked' => [
            $threeGroups,
            [self::SIZE_GROUP, self::MATERIAL_GROUP, self::FINISH_GROUP],
            7001,
            self::SIZE_40,
            7002,
            'the combination keeping both stale attributes won over the one keeping the fresh pick',
        ];
    }

    /**
     * @param array<int, array{attributes: array<int, int>, quantity: int, default: int}> $combinations
     * @param array<int, int> $groups attribute group ids, in attribute group position order
     */
    private function fallbackFor(array $combinations, array $groups, int $requestedIdProductAttribute): int
    {
        $product = new class() extends Product {
            /** @var array<int, array<string, mixed>> */
            public array $combinationRows = [];

            public function getAttributeCombinations($id_lang = null, $groupByIdAttributeGroup = true)
            {
                return $this->combinationRows;
            }
        };
        $product->id = 999999;
        $product->out_of_stock = 0;
        $product->combinationRows = $this->buildRows($combinations, $groups);

        $controller = (new ReflectionClass(ProductController::class))->newInstanceWithoutConstructor();

        $productProperty = new ReflectionProperty(ProductController::class, 'product');
        $productProperty->setAccessible(true);
        $productProperty->setValue($controller, $product);

        $method = new ReflectionMethod(ProductController::class, 'tryToGetAvailableIdProductAttribute');
        $method->setAccessible(true);

        return (int) $method->invoke($controller, $requestedIdProductAttribute);
    }

    /**
     * getAttributeCombinations() groups by combination and attribute group and orders by the group
     * position, so a combination yields one row per group in the order the dropdowns are washed.
     * Identifiers are rendered as strings, the way the database hands them back.
     *
     * @param array<int, array{attributes: array<int, int>, quantity: int, default: int}> $combinations
     * @param array<int, int> $groups
     *
     * @return array<int, array<string, mixed>>
     */
    private function buildRows(array $combinations, array $groups): array
    {
        $rows = [];
        foreach ($combinations as $idProductAttribute => $combination) {
            foreach ($combination['attributes'] as $slot => $idAttribute) {
                $rows[] = [
                    'id_product_attribute' => (string) $idProductAttribute,
                    'id_attribute' => (string) $idAttribute,
                    'id_attribute_group' => (string) $groups[$slot],
                    'is_color_group' => (string) (int) (self::COLOUR_GROUP === $groups[$slot]),
                    'quantity' => $combination['quantity'],
                    'default_on' => (string) $combination['default'],
                ];
            }
        }

        return $rows;
    }
}

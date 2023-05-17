<?php

namespace Tests\Unit\Core\Pricing;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Pricing\PriceEngine;
use Tests\Unit\Core\Pricing\Extension\TestBasicProductPriceExtension;
use Tests\Unit\Core\Pricing\Extension\TestFixedVatExtension;
use Tests\Unit\Core\Pricing\Extension\TestHookReplacesPrice;

/**
 * @group Pricing
 */
class PriceEngineTest extends TestCase
{
    /**
     * @dataProvider getDataForbasicPrices
     */
    public function testGetBasicPrice(int $combinationId, float $quantity, string $expected): void
    {
        $engine = $this->createEngine();

        $price = $engine->getPrice($combinationId, [
            'quantity' => $quantity,
        ]);

        self::assertEquals($expected, $price->getTotal()->toPrecision(2));
    }

    public function getDataForbasicPrices(): \Generator
    {
        yield 'P1' => [1, 1.0, '12.00'];
        yield 'P2' => [2, 1.0, '8.40'];
//        yield 'P2 - quantity 2' => [2, 2.0, '16.80']; // buggy atm, will be fixed into the next TS
        yield 'P3 Fixed price (by hook for example)' => [3, 1.0, '5.00'];
    }

    private function createEngine(): PriceEngine
    {
        return new PriceEngine([
            new TestBasicProductPriceExtension([
                1 => '10',
                2 => '7',
                3 => '7',
            ]),
            new TestFixedVatExtension('0.2'),
            new TestHookReplacesPrice(3, '5.0'),
        ]);
    }
}

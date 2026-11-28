<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Pdf;

use PHPUnit\Framework\TestCase;
use Smarty;

/**
 * A shipment delivery slip lists what left the warehouse in that shipment, so its quantities come
 * from the shipment and not from the order. The refunded count on the row is the order's, which is
 * why it must not be subtracted here: two shipments of two units against a four unit line, with two
 * units refunded afterwards, would otherwise print nothing on either slip.
 *
 * The order based slip in delivery-slip.product-tab.tpl does subtract it, and should: there the
 * quantity is the order's too.
 */
/**
 * Carries the two constants the template reads, so the render needs no legacy autoloader.
 */
class CustomizationFieldTypes
{
    public const CUSTOMIZE_FILE = 0;
    public const CUSTOMIZE_TEXTFIELD = 1;
}

class ShipmentDeliverySlipQuantityTest extends TestCase
{
    private const TEMPLATE = __DIR__ . '/../../../pdf/shipment-delivery-slip.product-tab.tpl';

    public function testAShippedLineIsPrintedEvenWhenTheOrderHasRefundsAgainstIt(): void
    {
        $html = $this->render(['product_quantity' => 2, 'product_quantity_refunded' => 2]);

        $this->assertSame(1, $this->countProductRows($html));
        $this->assertSame(['2'], $this->quantityCells($html));
    }

    public function testTheQuantityPrintedIsTheShipmentQuantity(): void
    {
        $html = $this->render(['product_quantity' => 3, 'product_quantity_refunded' => 1]);

        $this->assertSame(['3'], $this->quantityCells($html));
    }

    public function testACustomizedLineAlsoKeepsItsShipmentQuantity(): void
    {
        $html = $this->render([
            'product_quantity' => 2,
            'product_quantity_refunded' => 2,
            'customizedDatas' => [
                'address' => [
                    'customization-1' => [
                        'quantity' => 2,
                        'datas' => [CustomizationFieldTypes::CUSTOMIZE_TEXTFIELD => [
                            ['name' => 'Engraving', 'value' => 'Probe'],
                        ]],
                    ],
                ],
            ],
        ]);

        $this->assertStringContainsString('customization_data', $html);
        $this->assertSame(['(2)'], $this->customizationQuantityCells($html));
    }

    public function testALineWithNothingInTheShipmentIsNotPrinted(): void
    {
        $html = $this->render(['product_quantity' => 0, 'product_quantity_refunded' => 0]);

        $this->assertSame(0, $this->countProductRows($html));
    }

    private function render(array $overrides): string
    {
        $smarty = new Smarty();
        $smarty->setCompileDir(sys_get_temp_dir() . '/ps-smarty-' . getmypid());
        $smarty->setCaching(Smarty::CACHING_OFF);
        $smarty->setErrorReporting(E_ALL & ~E_NOTICE & ~E_WARNING);
        // The translation plugin the template calls; the wording is not what is under test.
        $smarty->registerPlugin('function', 'l', static fn (array $params): string => (string) ($params['s'] ?? ''));
        // The template reads two Product constants in its customization branch, and Smarty wants any
        // class it resolves statically to be registered.
        $smarty->registerClass('Product', CustomizationFieldTypes::class);

        // The template also reads this flag; images are not what is under test.
        $smarty->assign('display_product_images', false);
        $smarty->assign('products', [$overrides + [
            'product_name' => 'Probe product',
            'product_reference' => 'PROBE',
            'customizedDatas' => [],
        ]]);

        return $smarty->fetch(self::TEMPLATE);
    }

    /**
     * @return string[]
     */
    private function customizationQuantityCells(string $html): array
    {
        preg_match_all('~<td class="center">\s*(\([0-9-]*\))\s*</td>~', $html, $m);

        return $m[1];
    }

    private function countProductRows(string $html): int
    {
        return preg_match_all('~<tr class="product~', $html);
    }

    /**
     * @return string[]
     */
    private function quantityCells(string $html): array
    {
        preg_match_all('~<td class="product center">\s*([0-9-]*)\s*</td>~', $html, $m);

        return $m[1];
    }
}

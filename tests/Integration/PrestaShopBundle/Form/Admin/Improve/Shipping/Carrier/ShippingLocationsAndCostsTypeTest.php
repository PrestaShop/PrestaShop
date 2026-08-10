<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\PrestaShopBundle\Form\Admin\Improve\Shipping\Carrier;

use PrestaShop\PrestaShop\Core\Domain\Carrier\ValueObject\OutOfRangeBehavior;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ValueObject\ShippingMethod;
use PrestaShopBundle\Form\Admin\Improve\Shipping\Carrier\ShippingLocationsAndCostsType;
use Symfony\Component\Form\FormInterface;
use Tests\Integration\PrestaShopBundle\Form\AbstractFormTester;

class ShippingLocationsAndCostsTypeTest extends AbstractFormTester
{
    public function testANegativePriceIsRejectedWhenShippingIsPaid(): void
    {
        $form = $this->submitWithPrice(false, '-5');

        $this->assertFalse($form->isValid());
        $this->assertCount(1, $form->get('ranges_costs')->get('0')->get('ranges')->get('0')->get('price')->getErrors());
    }

    public function testTheHiddenCostsAreNotValidatedWhenShippingIsFree(): void
    {
        $form = $this->submitWithPrice(true, '-5');

        $this->assertTrue($form->isValid(), (string) $form->getErrors(true));
    }

    private function submitWithPrice(bool $isFree, string $price): FormInterface
    {
        $form = $this->createForm(ShippingLocationsAndCostsType::class, ['csrf_protection' => false]);
        $form->submit([
            'zones' => ['1'],
            'is_free' => $isFree ? '1' : '0',
            'has_additional_handling_fee' => '0',
            'shipping_method' => (string) ShippingMethod::BY_PRICE,
            'range_behavior' => (string) OutOfRangeBehavior::USE_HIGHEST_RANGE,
            'ranges_costs' => [
                [
                    'zoneId' => '1',
                    'ranges' => [
                        ['from' => '0', 'to' => '10', 'price' => $price],
                    ],
                ],
            ],
        ]);

        return $form;
    }
}

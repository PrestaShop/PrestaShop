<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Core\Form\IdentifiableObject\DataProvider;

use PrestaShop\PrestaShop\Core\Context\LanguageContextBuilder;
use PrestaShop\PrestaShop\Core\Context\ShopContextBuilder;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider\CarrierFormDataProvider;
use Tests\Integration\PrestaShopBundle\Form\FormListenerTestCase;

/**
 * A range covers its lower bound and stops before the upper one. Labelling two consecutive rows
 * "0 - 4" and "4 - 10" leaves the merchant with no way to know what a cart of exactly 4 pays.
 *
 * @see https://github.com/PrestaShop/PrestaShop/issues/37180
 */
class CarrierFormDataProviderRangeLabelTest extends FormListenerTestCase
{
    private const CARRIER_ID = 2;

    protected function setUp(): void
    {
        parent::setUp();

        /** @var ShopContextBuilder $shopContextBuilder */
        $shopContextBuilder = self::getContainer()->get('test_shop_context_builder');
        $shopContextBuilder->setShopId(1);
        $shopContextBuilder->setShopConstraint(ShopConstraint::shop(1));

        /** @var LanguageContextBuilder $languageContextBuilder */
        $languageContextBuilder = self::getContainer()->get('test_language_context_builder');
        $languageContextBuilder->setLanguageId(1);
    }

    public function testEveryRangeStatesWhichBoundItIncludes(): void
    {
        $labels = $this->rangeLabels();

        $this->assertNotEmpty($labels, 'The fixture carrier is expected to have ranges.');

        foreach ($labels as $label) {
            $this->assertMatchesRegularExpression('/^>= .+ - < .+$/', $label);
        }
    }

    public function testBothBoundsAreStillShown(): void
    {
        foreach ($this->rangeLabels() as $label) {
            // Nothing is lost compared to the plain dash: a value remains on each side.
            [$lowerBound, $upperBound] = explode(' - < ', $label);

            $this->assertMatchesRegularExpression('/\d/', $lowerBound);
            $this->assertMatchesRegularExpression('/\d/', $upperBound);
        }
    }

    /**
     * @return string[]
     */
    private function rangeLabels(): array
    {
        /** @var CarrierFormDataProvider $dataProvider */
        $dataProvider = self::getContainer()->get(CarrierFormDataProvider::class);
        $data = $dataProvider->getData(self::CARRIER_ID);

        $labels = [];
        foreach ($data['shipping_settings']['ranges_costs'] as $zone) {
            foreach ($zone['ranges'] as $range) {
                $labels[] = $range['range'];
            }
        }

        return $labels;
    }
}

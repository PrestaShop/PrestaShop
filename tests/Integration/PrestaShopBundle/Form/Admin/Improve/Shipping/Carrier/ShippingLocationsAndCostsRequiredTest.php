<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\PrestaShopBundle\Form\Admin\Improve\Shipping\Carrier;

use PrestaShop\PrestaShop\Core\Context\LanguageContextBuilder;
use PrestaShop\PrestaShop\Core\Context\ShopContextBuilder;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use Symfony\Component\Form\FormConfigInterface;
use Tests\Integration\PrestaShopBundle\Form\FormListenerTestCase;

/**
 * Both choices always carry a selected value, so the merchant has nothing to fill in and the
 * required marker on their label is misleading. Turning `required` off makes Symfony add an empty
 * choice unless `placeholder` is pinned, which is what the second assertion of each case guards.
 *
 * @see https://github.com/PrestaShop/PrestaShop/issues/37178
 */
class ShippingLocationsAndCostsRequiredTest extends FormListenerTestCase
{
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

    /**
     * @dataProvider getPreselectedFields
     */
    public function testAPreselectedChoiceIsNotAdvertisedAsMandatory(string $fieldName): void
    {
        $this->assertFalse($this->fieldConfig($fieldName)->getOption('required'));
    }

    /**
     * @dataProvider getPreselectedFields
     */
    public function testTurningTheMarkerOffDoesNotAddAnEmptyChoice(string $fieldName): void
    {
        $config = $this->fieldConfig($fieldName);

        // Symfony defaults `placeholder` to an empty entry as soon as `required` is false, and
        // normalises an explicit `false` to null, which is the "no empty entry" state.
        $this->assertNull($config->getOption('placeholder'));
        $this->assertCount(2, $config->getOption('choices'));
    }

    /**
     * @return array<int, array<int, string>>
     */
    public function getPreselectedFields(): array
    {
        return [['shipping_method'], ['range_behavior']];
    }

    private function fieldConfig(string $fieldName): FormConfigInterface
    {
        $form = self::getContainer()
            ->get('prestashop.core.form.identifiable_object.builder.carrier_form_builder')
            ->getForm();

        return $form->get('shipping_settings')->get($fieldName)->getConfig();
    }
}

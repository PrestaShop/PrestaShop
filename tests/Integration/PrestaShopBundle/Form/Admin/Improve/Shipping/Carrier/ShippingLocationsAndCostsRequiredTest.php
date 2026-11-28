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
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Tests\Integration\PrestaShopBundle\Form\FormListenerTestCase;

/**
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
        // Building the view reads the session of the current request
        $request = new Request();
        $request->setSession(new Session(new MockArraySessionStorage()));
        $requestStack = self::getContainer()->get('request_stack');
        $requestStack->push($request);
        try {
            $view = $this->getForm()->createView()['shipping_settings'][$fieldName];
        } finally {
            $requestStack->pop();
        }

        // The rendered field offers no empty entry (an extra radio, or an empty option of the select)
        $this->assertNull($view->vars['placeholder']);
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
        return $this->getForm()->get('shipping_settings')->get($fieldName)->getConfig();
    }

    private function getForm(): FormInterface
    {
        return self::getContainer()
            ->get('prestashop.core.form.identifiable_object.builder.carrier_form_builder')
            ->getForm();
    }
}

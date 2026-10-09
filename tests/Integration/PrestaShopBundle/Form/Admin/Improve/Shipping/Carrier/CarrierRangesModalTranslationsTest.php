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
use Tests\Integration\PrestaShopBundle\Form\FormListenerTestCase;

/**
 * The ranges modal receives its wording through the `data-translations` attribute of the button that
 * opens it. A message the component asks for but the form does not send renders as nothing at all,
 * which is how the invalid range case ended up silent.
 *
 * @see https://github.com/PrestaShop/PrestaShop/issues/37182
 */
class CarrierRangesModalTranslationsTest extends FormListenerTestCase
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
     * @dataProvider getAlertsTheModalCanRaise
     */
    public function testTheModalIsGivenAMessageForEveryAlertItCanRaise(string $translationKey): void
    {
        $translations = $this->modalTranslations();

        $this->assertArrayHasKey($translationKey, $translations);
        $this->assertNotSame('', trim($translations[$translationKey]));
    }

    /**
     * @return array<int, array<int, string>>
     */
    public function getAlertsTheModalCanRaise(): array
    {
        return [
            ['modal.overlappingAlert'],
            ['modal.negativeRangeAlert'],
            ['modal.invalidRangeAlert'],
        ];
    }

    /**
     * @return array<string, string>
     */
    private function modalTranslations(): array
    {
        $form = self::getContainer()
            ->get('prestashop.core.form.identifiable_object.builder.carrier_form_builder')
            ->getForm();

        $attributes = $form->get('shipping_settings')->get('ranges')->get('show_modal')
            ->getConfig()->getOption('attr');

        return json_decode($attributes['data-translations'], true, 512, JSON_THROW_ON_ERROR);
    }
}

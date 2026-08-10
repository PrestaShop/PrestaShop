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
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Validator\Constraints\Length;
use Tests\Integration\PrestaShopBundle\Form\FormListenerTestCase;

/**
 * The `name` column of the carrier is 64 characters, and the adapter turns an over long value into
 * a CarrierConstraintException. The controller catches it and shows a generic failure with no field
 * attached, so the form has to refuse the value first.
 *
 * @see https://github.com/PrestaShop/PrestaShop/issues/37183
 */
class CarrierNameConstraintTest extends FormListenerTestCase
{
    private const MAX_NAME_LENGTH = 64;

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

    public function testItAcceptsANameOfTheMaximumLength(): void
    {
        $this->assertCount(0, $this->submitName(str_repeat('a', self::MAX_NAME_LENGTH))->getErrors());
    }

    public function testItRefusesANameLongerThanTheColumn(): void
    {
        $errors = $this->submitName(str_repeat('a', self::MAX_NAME_LENGTH + 1))->getErrors();

        $this->assertCount(1, $errors);
        $this->assertInstanceOf(Length::class, $errors[0]->getCause()->getConstraint());
    }

    private function submitName(string $name): FormInterface
    {
        $form = self::getContainer()
            ->get('prestashop.core.form.identifiable_object.builder.carrier_form_builder')
            ->getForm();

        // Only the name is submitted, every other field keeps the data the builder provided.
        $form->submit(['general_settings' => ['name' => $name]], false);

        return $form->get('general_settings')->get('name');
    }
}

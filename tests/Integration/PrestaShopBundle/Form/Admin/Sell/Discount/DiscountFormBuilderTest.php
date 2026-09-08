<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\PrestaShopBundle\Form\Admin\Sell\Discount;

use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Builder\FormBuilderInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;

class DiscountFormBuilderTest extends KernelTestCase
{
    private FormBuilderInterface $discountFormBuilder;

    protected function setUp(): void
    {
        self::bootKernel();

        $shopContextBuilder = self::getContainer()->get('test_shop_context_builder');
        $shopContextBuilder->setShopConstraint(ShopConstraint::shop(1));
        $shopContextBuilder->setShopId(1);

        $languageContextBuilder = self::getContainer()->get('test_language_context_builder');
        $languageContextBuilder->setLanguageId(1);
        $languageContextBuilder->setDefaultLanguageId(1);

        $currencyContextBuilder = self::getContainer()->get('test_currency_context_builder');
        $currencyContextBuilder->setCurrencyId(1);

        $this->discountFormBuilder = self::getContainer()->get('prestashop.core.form.identifiable_object.builder.discount_form_builder');
    }

    /**
     * The "Add new discount" page first GET has no type selected. The controller now passes an
     * empty string for that state; the whole discount form (and its nested types, which all
     * require a string discount_type) must build for it. See #40344.
     */
    public function testCreateFormBuildsWithNoDiscountTypeSelected(): void
    {
        $form = $this->discountFormBuilder->getForm([], ['discount_type' => '']);

        $this->assertInstanceOf(FormInterface::class, $form);
        $this->assertTrue($form->has('information'));
        $this->assertTrue($form->has('usability'));
    }

    /**
     * Documents why the controller must not pass null: the form types are typed to string, so a
     * null discount_type (the previous first-GET behaviour) fatals the page.
     */
    public function testBuildingWithNullDiscountTypeIsRejected(): void
    {
        $this->expectException(InvalidOptionsException::class);

        $this->discountFormBuilder->getForm([], ['discount_type' => null]);
    }
}

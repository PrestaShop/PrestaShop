<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\Form\Admin\Sell\Discount;

use PrestaShop\PrestaShop\Adapter\Discount\Repository\DiscountTypeRepository;
use PrestaShop\PrestaShop\Core\Context\LanguageContext;
use PrestaShop\PrestaShop\Core\Domain\Discount\ValueObject\DiscountType;
use PrestaShopBundle\Form\Admin\Sell\Discount\DiscountTypeSelectorType;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Contracts\Translation\TranslatorInterface;

class DiscountTypeSelectorTypeTest extends TypeTestCase
{
    private const LANG_ID = 1;

    protected function getExtensions(): array
    {
        $discountTypeRepository = $this->createMock(DiscountTypeRepository::class);
        $discountTypeRepository->method('getAllTypes')->willReturn([
            1 => $this->type(1, DiscountType::FREE_SHIPPING, 'Free shipping', true),
            2 => $this->type(2, DiscountType::CART_LEVEL, 'On cart amount', true),
            3 => $this->type(3, DiscountType::ORDER_LEVEL, 'On total order', true),
            4 => $this->type(4, DiscountType::PRODUCT_LEVEL, 'On catalog products', true),
            5 => $this->type(5, DiscountType::FREE_GIFT, 'Free gift', false),
        ]);

        $languageContext = $this->createMock(LanguageContext::class);
        $languageContext->method('getId')->willReturn(self::LANG_ID);

        $translator = $this->createMock(TranslatorInterface::class);
        $translator->method('trans')->willReturnArgument(0);

        return [
            new PreloadedExtension(
                [new DiscountTypeSelectorType($discountTypeRepository, $languageContext, $translator, [])],
                []
            ),
        ];
    }

    /**
     * Every type the shop declares enabled is offered. order_level used to be skipped by name on top
     * of the enabled flag, which is what made it reachable through the CQRS command and the Admin API
     * but not from the back office.
     */
    public function testItOffersEveryEnabledType(): void
    {
        $view = $this->factory->create(DiscountTypeSelectorType::class)->createView();

        $this->assertSame(
            [
                DiscountType::FREE_SHIPPING,
                DiscountType::CART_LEVEL,
                DiscountType::ORDER_LEVEL,
                DiscountType::PRODUCT_LEVEL,
            ],
            array_map(
                static fn ($choiceView) => $choiceView->value,
                $view['discount_type_selector']->vars['choices']
            )
        );
    }

    /**
     * A type marked inactive in ps_cart_rule_type is the supported way to withdraw one.
     */
    public function testItSkipsADisabledType(): void
    {
        $view = $this->factory->create(DiscountTypeSelectorType::class)->createView();

        $values = array_map(
            static fn ($choiceView) => $choiceView->value,
            $view['discount_type_selector']->vars['choices']
        );

        $this->assertNotContains(DiscountType::FREE_GIFT, $values);
    }

    private function type(int $id, string $discountType, string $name, bool $enabled): array
    {
        return [
            'id_cart_rule_type' => $id,
            'discount_type' => $discountType,
            'is_core' => true,
            'enabled' => $enabled,
            'names' => [self::LANG_ID => $name],
            'descriptions' => [self::LANG_ID => $name],
        ];
    }
}
